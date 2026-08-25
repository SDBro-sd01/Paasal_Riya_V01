<?php
session_start();

// Autoload (vendor/autoload.php must be present)
require 'vendor/autoload.php';

// Load .env from the same folder
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

include "includes/db_connection.php";
include "includes/audit_helper.php";

$Page_Name = "Change Password (No Login Required)";

// ==================== HELPER FUNCTIONS ====================
function maskEmail($email) {
    $parts = explode("@", $email);
    if (count($parts) == 2) {
        $name = $parts[0];
        $domain = $parts[1];
        $len = strlen($name);
        if ($len <= 3) {
            $masked_name = substr($name, 0, 1) . str_repeat('*', $len-1);
        } else {
            $masked_name = substr($name, 0, 3) . str_repeat('*', $len-3);
        }
        $domain_parts = explode(".", $domain);
        if (count($domain_parts) >= 2) {
            $domain_name = $domain_parts[0];
            $domain_tld  = $domain_parts[count($domain_parts)-1];
            $domain_mid  = implode(".", array_slice($domain_parts, 1, -1));
            $masked_domain = substr($domain_name, 0, min(3, strlen($domain_name))) .
                             str_repeat('*', max(0, strlen($domain_name)-3));
            if ($domain_mid) {
                $masked_domain .= "." . $domain_mid;
            }
            $masked_domain .= "." . $domain_tld;
        } else {
            $masked_domain = $domain;
        }
        return $masked_name . "@" . $masked_domain;
    }
    return $email;
}

function generateCode($length = 6) {
    return str_pad(random_int(0, pow(10, $length)-1), $length, '0', STR_PAD_LEFT);
}

function setAlert($type, $message) {
    $_SESSION['alert'] = ['type' => $type, 'message' => $message];
}
function getAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
        return $alert;
    }
    return null;
}

function sendVerificationEmail($email, $username, $code) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'];
        $mail->Password   = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'] ?? 'tls';
        $mail->Port       = $_ENV['SMTP_PORT'] ?? 587;

        $mail->setFrom($_ENV['SMTP_USER'], 'Sisu Seriya');
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = "Password Change Verification Code";
        $mail->Body    = "<p>Hello <b>" . htmlspecialchars($username) . "</b>,</p>
                          <p>Your verification code is: <b>$code</b></p>
                          <p>This code expires in 5 minutes.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// ==================== POST HANDLING ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = isset($_POST['step']) ? $_POST['step'] : 'enter_email';

    // Step 0: Enter email manually and verify it exists in DB
    if ($step === 'enter_email') {
        $entered_email = isset($_POST['email']) ? trim($_POST['email']) : '';

        if (empty($entered_email)) {
            setAlert('warning', 'Please enter your email address.');
            $_SESSION['next_step'] = 'enter_email';
        } else {
            // Check if email exists in users table
            $stmt = $conn->prepare("SELECT user_id, username, email FROM users WHERE email = ?");
            $stmt->bind_param("s", $entered_email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 0) {
                setAlert('danger', 'No account found with that email address.');
                $_SESSION['next_step'] = 'enter_email';
            } else {
                $stmt->bind_result($db_user_id, $db_username, $db_email);
                $stmt->fetch();

                // Store user info in session for later steps
                $_SESSION['pwd_reset_user_id']   = $db_user_id;
                $_SESSION['pwd_reset_username']  = $db_username;
                $_SESSION['pwd_reset_email']     = $db_email;
                $_SESSION['email_verified']      = true;

                // Generate and send verification code
                $code = generateCode();
                $_SESSION['pwd_reset_code']      = $code;
                $_SESSION['pwd_reset_code_time'] = time();

                if (sendVerificationEmail($db_email, $db_username, $code)) {
                    setAlert('success', 'Verification code sent to: ' . maskEmail($db_email));
                    $_SESSION['next_step'] = 'verify_code';
                } else {
                    setAlert('danger', 'Failed to send email. Please try again later.');
                    // Clean up session variables on failure
                    unset($_SESSION['pwd_reset_user_id'], $_SESSION['pwd_reset_username'],
                          $_SESSION['pwd_reset_email'], $_SESSION['email_verified'],
                          $_SESSION['pwd_reset_code'], $_SESSION['pwd_reset_code_time']);
                    $_SESSION['next_step'] = 'enter_email';
                }
            }
            $stmt->close();
        }
        header("Location: change_password_not_logged_in.php");
        exit();
    }

    // Step 2: Verify the code
    elseif ($step === 'verify_code') {
        $entered_code = isset($_POST['verification_code']) ? trim($_POST['verification_code']) : '';
        $stored_code = isset($_SESSION['pwd_reset_code']) ? $_SESSION['pwd_reset_code'] : '';
        $code_time   = isset($_SESSION['pwd_reset_code_time']) ? $_SESSION['pwd_reset_code_time'] : 0;

        if (empty($stored_code) || (time() - $code_time) > 300) {
            setAlert('warning', 'Verification code expired. Please request a new code.');
            // Reset the whole process
            unset($_SESSION['pwd_reset_user_id'], $_SESSION['pwd_reset_username'],
                  $_SESSION['pwd_reset_email'], $_SESSION['email_verified'],
                  $_SESSION['pwd_reset_code'], $_SESSION['pwd_reset_code_time']);
            $_SESSION['next_step'] = 'enter_email';
        } elseif ($entered_code === $stored_code) {
            $_SESSION['code_verified'] = true;
            setAlert('success', 'Code verified. Please enter your new password.');
            $_SESSION['next_step'] = 'change_password';
        } else {
            setAlert('danger', 'Invalid verification code. Please try again.');
            $_SESSION['next_step'] = 'verify_code';
        }
        header("Location: change_password_not_logged_in.php");
        exit();
    }

    // Step 3: Change the password
    elseif ($step === 'change_password') {
        if (!isset($_SESSION['code_verified']) || $_SESSION['code_verified'] !== true) {
            setAlert('danger', 'Unauthorized access. Please start the process again.');
            $_SESSION['next_step'] = 'enter_email';
        } else {
            $new_password     = isset($_POST['new_password']) ? $_POST['new_password'] : '';
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            $user_id          = $_SESSION['pwd_reset_user_id'];

            if (empty($new_password) || empty($confirm_password)) {
                setAlert('warning', 'Both password fields are required.');
                $_SESSION['next_step'] = 'change_password';
            } elseif (strlen($new_password) < 6) {
                setAlert('warning', 'Password must be at least 6 characters.');
                $_SESSION['next_step'] = 'change_password';
            } elseif ($new_password !== $confirm_password) {
                setAlert('warning', 'Passwords do not match.');
                $_SESSION['next_step'] = 'change_password';
            } else {
                $hashed = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $stmt->bind_param("si", $hashed, $user_id);
                if ($stmt->execute()) {
                    // Audit log: self password change (user_id is the user themselves)
                    $oldData = ['password' => '********'];
                    $newData = ['password' => '********', 'note' => 'Self password changed via email verification (not logged in)'];
                    insertAuditLog($conn, 'users', $user_id, 'UPDATE', $oldData, $newData, $user_id);

                    setAlert('success', 'Password changed successfully! You can now login with your new password.');
                    // Destroy the reset session
                    unset($_SESSION['pwd_reset_user_id'], $_SESSION['pwd_reset_username'],
                          $_SESSION['pwd_reset_email'], $_SESSION['email_verified'],
                          $_SESSION['pwd_reset_code'], $_SESSION['pwd_reset_code_time'],
                          $_SESSION['code_verified']);
                    $_SESSION['next_step'] = 'enter_email';
                } else {
                    setAlert('danger', 'Database error. Could not update password.');
                    $_SESSION['next_step'] = 'change_password';
                }
                $stmt->close();
            }
        }
        header("Location: change_password_not_logged_in.php");
        exit();
    }
}

// ==================== GET REQUEST: DETERMINE CURRENT STEP ====================
$step = 'enter_email'; // default first step
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['next_step'])) {
        $step = $_SESSION['next_step'];
        unset($_SESSION['next_step']);
    }
}

// Security: if step is change_password but code not verified, reset
if ($step === 'change_password' && (!isset($_SESSION['code_verified']) || $_SESSION['code_verified'] !== true)) {
    $step = 'enter_email';
}

// ==================== UI RENDERING ====================
// Note: side_bar.php is excluded because this page is for non-logged-in users.
// If you need a consistent header, include your own non-authenticated header.
?>

<?php include "side_bar.php"; ?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <title><?php echo $Page_Name; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* =============================================
           DESIGN TOKENS — same system as edit_profile
        ============================================= */
        :root {
            --bg-base:       #0d0f14;
            --bg-surface:    #13161e;
            --bg-card:       #181c27;
            --bg-elevated:   #1e2333;
            --border:        #272d3f;
            --border-focus:  #4f7ef8;

            --accent:        #4f7ef8;
            --accent-glow:   rgba(79,126,248,0.18);
            --accent-hover:  #6b93ff;

            --success-bg:    #0d2318;
            --success-border:#1a4d30;
            --success-text:  #4ade80;

            --error-bg:      #230d0d;
            --error-border:  #4d1a1a;
            --error-text:    #f87171;

            --warning-bg:    #1c1500;
            --warning-border:#4d3800;
            --warning-text:  #fbbf24;

            --text-primary:   #e8eaf2;
            --text-secondary: #8892a4;
            --text-muted:     #4e5769;

            --font-display: 'Space Grotesk', sans-serif;
            --font-body:    'Inter', sans-serif;

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;

            --transition: 0.2s cubic-bezier(0.4,0,0.2,1);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            background-color: var(--bg-base);
            color: var(--text-primary);
            font-family: var(--font-body);
            font-size: 15px;
            line-height: 1.6;
            min-height: 100vh;
        }

        .pw-wrapper {
            max-width: 520px;
            margin: 0 auto;
            padding: 40px 20px 80px;
        }

        .page-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 32px;
        }

        .page-header-icon {
            width: 48px;
            height: 48px;
            background: var(--accent-glow);
            border: 1px solid rgba(79,126,248,0.3);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--accent);
            flex-shrink: 0;
        }

        .page-header-text h1 {
            font-family: var(--font-display);
            font-size: clamp(1.35rem, 4vw, 1.75rem);
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .page-header-text p {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-top: 3px;
        }

        .step-track {
            display: flex;
            align-items: center;
            margin-bottom: 28px;
        }

        .step-node {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .step-circle.done {
            background: #0d2318;
            border: 2px solid #1a4d30;
            color: var(--success-text);
        }

        .step-circle.active {
            background: var(--accent-glow);
            border: 2px solid var(--accent);
            color: var(--accent);
            box-shadow: 0 0 0 4px rgba(79,126,248,0.1);
            animation: stepPulse 2s infinite;
        }

        .step-circle.idle {
            background: var(--bg-elevated);
            border: 2px solid var(--border);
            color: var(--text-muted);
        }

        @keyframes stepPulse {
            0%, 100% { box-shadow: 0 0 0 4px rgba(79,126,248,0.1); }
            50%       { box-shadow: 0 0 0 8px rgba(79,126,248,0.06); }
        }

        .step-label {
            font-size: 0.68rem;
            font-weight: 500;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .step-label.done   { color: var(--success-text); }
        .step-label.active { color: var(--accent); }
        .step-label.idle   { color: var(--text-muted); }

        .step-connector {
            flex: 1;
            height: 2px;
            border-radius: 2px;
            margin: 0 6px;
            margin-bottom: 22px;
            transition: background 0.3s;
        }

        .step-connector.done   { background: #1a4d30; }
        .step-connector.idle   { background: var(--border); }

        .pw-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            overflow: hidden;
        }

        .card-body {
            padding: 28px;
        }

        .alert-custom {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 13px 16px;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 22px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .alert-success { background: var(--success-bg); border: 1px solid var(--success-border); color: var(--success-text); }
        .alert-danger  { background: var(--error-bg);   border: 1px solid var(--error-border);   color: var(--error-text); }
        .alert-warning { background: var(--warning-bg); border: 1px solid var(--warning-border); color: var(--warning-text); }

        .alert-icon { font-size: 15px; margin-top: 1px; flex-shrink: 0; }

        .email-chip {
            display: flex;
            align-items: center;
            gap: 14px;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 14px 16px;
            margin-bottom: 20px;
        }

        .email-chip-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            background: var(--accent-glow);
            border: 1px solid rgba(79,126,248,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--accent);
            flex-shrink: 0;
        }

        .email-chip-label {
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .email-chip-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
            word-break: break-all;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 18px;
        }

        .field-label {
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .field-label i { font-size: 12px; color: var(--text-muted); }

        .field-input {
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-family: var(--font-body);
            font-size: 0.9rem;
            padding: 11px 14px;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
            width: 100%;
            outline: none;
        }

        .field-input::placeholder { color: var(--text-muted); }

        .field-input:hover {
            border-color: #353d54;
            background: #222736;
        }

        .field-input:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px var(--accent-glow);
            background: #1a2038;
        }

        .field-input.otp-input {
            text-align: center;
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 10px;
            font-family: var(--font-display);
            padding: 14px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .field-input {
            padding-right: 44px;
        }

        .toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 16px;
            padding: 4px;
            transition: color var(--transition);
        }

        .toggle-pw:hover { color: var(--text-secondary); }

        .strength-track {
            height: 3px;
            background: var(--border);
            border-radius: 3px;
            margin-top: 8px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            border-radius: 3px;
            width: 0%;
            transition: width 0.35s ease, background 0.35s ease;
        }

        .strength-fill.s1 { width: 25%; background: #ef4444; }
        .strength-fill.s2 { width: 50%; background: #f59e0b; }
        .strength-fill.s3 { width: 75%; background: #3b82f6; }
        .strength-fill.s4 { width: 100%; background: #10b981; }

        .strength-hint {
            font-size: 0.72rem;
            color: var(--text-muted);
            margin-top: 4px;
            min-height: 16px;
            transition: color 0.2s;
        }

        .match-hint {
            font-size: 0.72rem;
            margin-top: 4px;
            min-height: 16px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .match-hint.ok    { color: var(--success-text); }
        .match-hint.fail  { color: var(--error-text); }
        .match-hint.empty { color: transparent; }

        .info-note {
            font-size: 0.83rem;
            color: var(--text-secondary);
            text-align: center;
            margin-bottom: 20px;
        }

        .info-note strong { color: var(--text-primary); }

        .expiry-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .form-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 4px;
        }

        .form-actions-row {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-primary-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--accent);
            color: #fff;
            font-family: var(--font-body);
            font-size: 0.875rem;
            font-weight: 600;
            padding: 11px 24px;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
            text-decoration: none;
            flex: 1;
        }

        .btn-primary-custom:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(79,126,248,0.35);
            color: #fff;
        }

        .btn-primary-custom:active { transform: translateY(0); }

        .btn-ghost-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: transparent;
            color: var(--text-secondary);
            font-family: var(--font-body);
            font-size: 0.875rem;
            font-weight: 500;
            padding: 11px 18px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all var(--transition);
            text-decoration: none;
        }

        .btn-ghost-custom:hover {
            background: var(--bg-elevated);
            color: var(--text-primary);
            border-color: #353d54;
        }

        .btn-text-custom {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: none;
            color: var(--accent);
            font-family: var(--font-body);
            font-size: 0.83rem;
            font-weight: 500;
            cursor: pointer;
            padding: 0;
            text-decoration: none;
            transition: color var(--transition);
        }

        .btn-text-custom:hover { color: var(--accent-hover); }

        .card-footer-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 28px;
            background: var(--bg-surface);
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
        }

        .footer-note {
            font-size: 0.78rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        @media (max-width: 560px) {
            .pw-wrapper { padding: 24px 14px 60px; }
            .card-body  { padding: 20px 16px; }
            .card-footer-strip { padding: 14px 16px; }
            .step-label { display: none; }
            .form-actions-row { flex-direction: column; }
            .btn-primary-custom,
            .btn-ghost-custom  { width: 100%; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { transition: none !important; animation: none !important; }
        }
    </style>
</head>
<body>



<div class="pw-wrapper">

    <div class="page-header">
        <div class="page-header-icon"><i class="bi bi-shield-lock"></i></div>
        <div class="page-header-text">
            <h1><?php echo $Page_Name; ?></h1>
            <p>Reset your password in four simple steps.</p>
        </div>
    </div>

    <?php
    $steps = [
        'enter_email'     => ['label' => 'Email',       'icon' => 'bi-envelope'],
        'send_code'       => ['label' => 'Send Code',   'icon' => 'bi-send'],
        'verify_code'     => ['label' => 'Verify',      'icon' => 'bi-shield-check'],
        'change_password' => ['label' => 'New Password', 'icon' => 'bi-key'],
    ];
    $step_keys     = array_keys($steps);
    $current_index = array_search($step, $step_keys);

    // Adjust for skipped 'send_code' visual step
    if ($step === 'verify_code' && isset($_SESSION['email_verified'])) {
        $current_index = 2; // email + send done -> verify active
    } elseif ($step === 'change_password' && isset($_SESSION['code_verified'])) {
        $current_index = 3;
    }
    ?>

    <div class="step-track">
        <?php foreach ($step_keys as $i => $sk):
            if ($i < $current_index)      $state = 'done';
            elseif ($i === $current_index) $state = 'active';
            else                           $state = 'idle';
        ?>
            <div class="step-node">
                <div class="step-circle <?php echo $state; ?>">
                    <?php if ($state === 'done'): ?>
                        <i class="bi bi-check-lg"></i>
                    <?php else: ?>
                        <i class="<?php echo $steps[$sk]['icon']; ?>"></i>
                    <?php endif; ?>
                </div>
                <span class="step-label <?php echo $state; ?>"><?php echo $steps[$sk]['label']; ?></span>
            </div>
            <?php if ($i < count($step_keys) - 1): ?>
                <div class="step-connector <?php echo ($i < $current_index) ? 'done' : 'idle'; ?>"></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="pw-card">
        <div class="card-body">

            <?php $alert = getAlert(); ?>
            <?php if ($alert): ?>
                <?php
                $icon_map = [
                    'success' => 'check-circle-fill',
                    'danger'  => 'exclamation-circle-fill',
                    'warning' => 'exclamation-triangle-fill',
                ];
                $icon_class = $icon_map[$alert['type']] ?? 'info-circle-fill';
                ?>
                <div class="alert-custom alert-<?php echo htmlspecialchars($alert['type']); ?>">
                    <i class="bi bi-<?php echo $icon_class; ?> alert-icon"></i>
                    <span><?php echo htmlspecialchars($alert['message']); ?></span>
                </div>
            <?php endif; ?>

            <!-- STEP 1: Enter Email -->
            <?php if ($step === 'enter_email'): ?>
                <p class="info-note">
                    Enter the <strong>email address</strong> associated with your account.
                </p>
                <form method="post">
                    <input type="hidden" name="step" value="enter_email">
                    <div class="field-group">
                        <label class="field-label" for="email">
                            <i class="bi bi-envelope"></i> Enter your Email here
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="field-input"
                            placeholder="Enter your Email here"
                            required
                            autocomplete="email"
                        >
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary-custom">
                            <i class="bi bi-send-fill"></i> Send Verification Code
                        </button>
                    </div>
                </form>

            <!-- STEP 3: Verify Code -->
            <?php elseif ($step === 'verify_code'): ?>
                <?php
                $masked = isset($_SESSION['pwd_reset_email']) ? maskEmail($_SESSION['pwd_reset_email']) : 'your email';
                ?>
                <div class="email-chip">
                    <div class="email-chip-icon"><i class="bi bi-envelope-check"></i></div>
                    <div>
                        <div class="email-chip-label">Code sent to</div>
                        <div class="email-chip-value"><?php echo htmlspecialchars($masked); ?></div>
                    </div>
                </div>

                <p class="info-note">Enter the <strong>6-digit code</strong> from your email.</p>
                <div class="expiry-note">
                    <i class="bi bi-clock"></i> Code expires in 5 minutes
                </div>

                <form method="post" style="margin-top: 20px;">
                    <input type="hidden" name="step" value="verify_code">
                    <div class="field-group">
                        <label class="field-label" for="verification_code">
                            <i class="bi bi-hash"></i> Verification Code
                        </label>
                        <input
                            type="text"
                            id="verification_code"
                            name="verification_code"
                            class="field-input otp-input"
                            pattern="\d{6}"
                            maxlength="6"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            required
                            placeholder="000000"
                        >
                    </div>
                    <div class="form-actions-row" style="margin-top: 4px;">
                        <button type="submit" class="btn-primary-custom">
                            <i class="bi bi-shield-check"></i> Verify Code
                        </button>
                        <a href="change_password_not_logged_in.php" class="btn-ghost-custom">
                            <i class="bi bi-arrow-repeat"></i> Resend
                        </a>
                    </div>
                </form>

            <!-- STEP 4: Change Password -->
            <?php elseif ($step === 'change_password'): ?>
                <?php
                $masked = isset($_SESSION['pwd_reset_email']) ? maskEmail($_SESSION['pwd_reset_email']) : 'your email';
                ?>
                <div class="email-chip">
                    <div class="email-chip-icon"><i class="bi bi-patch-check"></i></div>
                    <div>
                        <div class="email-chip-label">Identity confirmed</div>
                        <div class="email-chip-value"><?php echo htmlspecialchars($masked); ?></div>
                    </div>
                </div>

                <p class="info-note">Create a <strong>strong new password</strong> for your account.</p>

                <form method="post" id="changePasswordForm" style="margin-top: 20px;">
                    <input type="hidden" name="step" value="change_password">

                    <div class="field-group">
                        <label class="field-label" for="new_password">
                            <i class="bi bi-lock"></i> New Password
                        </label>
                        <div class="input-wrap">
                            <input
                                type="password"
                                id="new_password"
                                name="new_password"
                                class="field-input"
                                minlength="6"
                                required
                                placeholder="Min. 6 characters"
                                autocomplete="new-password"
                            >
                            <button type="button" class="toggle-pw" data-target="new_password" aria-label="Toggle password visibility">
                                <i class="bi bi-eye" id="icon-new_password"></i>
                            </button>
                        </div>
                        <div class="strength-track">
                            <div class="strength-fill" id="strengthBar"></div>
                        </div>
                        <div class="strength-hint" id="strengthHint"></div>
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="confirm_password">
                            <i class="bi bi-lock-fill"></i> Confirm New Password
                        </label>
                        <div class="input-wrap">
                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                minlength="6"
                                required
                                class="field-input"
                                placeholder="Re-enter your password"
                                autocomplete="new-password"
                            >
                            <button type="button" class="toggle-pw" data-target="confirm_password" aria-label="Toggle password visibility">
                                <i class="bi bi-eye" id="icon-confirm_password"></i>
                            </button>
                        </div>
                        <div class="match-hint empty" id="matchHint"></div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary-custom">
                            <i class="bi bi-floppy"></i> Update Password
                        </button>
                    </div>
                </form>

                <script>
                document.querySelectorAll('.toggle-pw').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var targetId = this.getAttribute('data-target');
                        var input    = document.getElementById(targetId);
                        var icon     = document.getElementById('icon-' + targetId);
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.className = 'bi bi-eye-slash';
                        } else {
                            input.type = 'password';
                            icon.className = 'bi bi-eye';
                        }
                    });
                });

                var newPass     = document.getElementById('new_password');
                var strengthBar = document.getElementById('strengthBar');
                var strengthHint = document.getElementById('strengthHint');
                var labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
                var colors = ['', '#ef4444', '#f59e0b', '#3b82f6', '#10b981'];

                if (newPass && strengthBar) {
                    newPass.addEventListener('input', function() {
                        var val = this.value;
                        var s = 0;
                        if (val.length >= 6)  s = 1;
                        if (val.length >= 8  && /[A-Z]/.test(val)) s = 2;
                        if (val.length >= 10 && /[0-9]/.test(val) && /[^A-Za-z0-9]/.test(val)) s = 3;
                        if (val.length >= 12 && /[A-Z]/.test(val) && /[0-9]/.test(val) && /[^A-Za-z0-9]/.test(val)) s = 4;
                        strengthBar.className = 'strength-fill' + (s ? ' s' + s : '');
                        strengthHint.textContent = val.length ? labels[s] : '';
                        strengthHint.style.color = val.length ? colors[s] : '';
                        checkMatch();
                    });
                }

                var confirmPass = document.getElementById('confirm_password');
                var matchHint   = document.getElementById('matchHint');

                function checkMatch() {
                    if (!confirmPass.value) {
                        matchHint.className = 'match-hint empty';
                        matchHint.innerHTML = '';
                        return;
                    }
                    if (newPass.value === confirmPass.value) {
                        matchHint.className = 'match-hint ok';
                        matchHint.innerHTML = '<i class="bi bi-check-circle-fill"></i> Passwords match';
                    } else {
                        matchHint.className = 'match-hint fail';
                        matchHint.innerHTML = '<i class="bi bi-x-circle-fill"></i> Passwords do not match';
                    }
                }

                if (confirmPass) {
                    confirmPass.addEventListener('input', checkMatch);
                }

                document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
                    if (newPass.value !== confirmPass.value) {
                        e.preventDefault();
                        matchHint.className = 'match-hint fail';
                        matchHint.innerHTML = '<i class="bi bi-x-circle-fill"></i> Passwords do not match';
                        confirmPass.focus();
                    }
                });
                </script>

            <?php endif; ?>

        </div>

        <div class="card-footer-strip">
            <span class="footer-note"><i class="bi bi-lock"></i> Secured with BCRYPT encryption</span>
            <a href="login.php" class="btn-text-custom">
                <i class="bi bi-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>

</div>

</body>
</html>