<?php
// Backend processing for account deletion
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load environment variables from project root
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Helper functions (same as password change page)
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
    $_SESSION['delete_alert'] = ['type' => $type, 'message' => $message];
}

function getAlert() {
    if (isset($_SESSION['delete_alert'])) {
        $alert = $_SESSION['delete_alert'];
        unset($_SESSION['delete_alert']);
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
        $mail->Subject = "Account Deletion Verification Code";
        $mail->Body    = "<p>Hello <b>" . htmlspecialchars($username) . "</b>,</p>
                          <p>Your verification code to delete your account is: <b>$code</b></p>
                          <p>This code expires in 5 minutes. If you did not request this, please ignore.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Login check (variables $isLogged, $user_id, $conn come from the frontend include)
if (!$isLogged || $user_id === 0) {
    header("Location: login.php");
    exit();
}

// Get user details
$stmt = $conn->prepare("SELECT username, email, password FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $user_email, $hashed_password);
$stmt->fetch();
$stmt->close();

if (empty($user_email)) {
    die("User email not found.");
}

// Determine current step from session
$step = isset($_SESSION['delete_step']) ? $_SESSION['delete_step'] : 'verify_password';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_step = isset($_POST['step']) ? $_POST['step'] : '';

    if ($post_step === 'verify_password') {
        $password = $_POST['password'] ?? '';
        if (password_verify($password, $hashed_password)) {
            $_SESSION['delete_pw_verified'] = true;
            setAlert('success', 'Password verified. Proceed to email verification.');
            $_SESSION['delete_step'] = 'send_code';
        } else {
            setAlert('danger', 'Incorrect password. Please try again.');
        }
        header("Location: delete_account_frontend_user_side.php");
        exit();
    }
    elseif ($post_step === 'send_code') {
        if (!isset($_SESSION['delete_pw_verified']) || !$_SESSION['delete_pw_verified']) {
            setAlert('danger', 'Please verify your password first.');
            $_SESSION['delete_step'] = 'verify_password';
            header("Location: delete_account_frontend_user_side.php");
            exit();
        }
        $code = generateCode();
        $_SESSION['delete_code'] = $code;
        $_SESSION['delete_code_time'] = time();
        $_SESSION['delete_email'] = $user_email;

        if (sendVerificationEmail($user_email, $username, $code)) {
            setAlert('success', 'Verification code sent to: ' . maskEmail($user_email));
            $_SESSION['delete_step'] = 'verify_code';
        } else {
            setAlert('danger', 'Failed to send email. Please try again later.');
        }
        header("Location: delete_account_frontend_user_side.php");
        exit();
    }
    elseif ($post_step === 'verify_code') {
        $entered_code = trim($_POST['verification_code'] ?? '');
        $stored_code = $_SESSION['delete_code'] ?? '';
        $code_time   = $_SESSION['delete_code_time'] ?? 0;

        if (empty($stored_code) || (time() - $code_time) > 300) {
            setAlert('warning', 'Verification code expired. Please request a new code.');
            unset($_SESSION['delete_code'], $_SESSION['delete_code_time'], $_SESSION['delete_email']);
            $_SESSION['delete_step'] = 'send_code';
        } elseif ($entered_code === $stored_code) {
            $_SESSION['delete_code_verified'] = true;
            setAlert('success', 'Code verified. Please confirm account deletion.');
            $_SESSION['delete_step'] = 'confirm_delete';
        } else {
            setAlert('danger', 'Invalid verification code. Please try again.');
            $_SESSION['delete_step'] = 'verify_code';
        }
        header("Location: delete_account_frontend_user_side.php");
        exit();
    }
    elseif ($post_step === 'confirm_delete') {
        if (!isset($_SESSION['delete_code_verified']) || $_SESSION['delete_code_verified'] !== true) {
            setAlert('danger', 'Unauthorized. Please complete verification first.');
            $_SESSION['delete_step'] = 'verify_password';
            header("Location: delete_account_frontend_user_side.php");
            exit();
        }

        $confirmed = isset($_POST['confirm']) && $_POST['confirm'] == '1';
        if (!$confirmed) {
            setAlert('warning', 'You must confirm that you understand the consequences by ticking the checkbox.');
            $_SESSION['delete_step'] = 'confirm_delete';
            header("Location: delete_account_frontend_user_side.php");
            exit();
        }

        // Delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            // Audit log (if function exists)
            if (function_exists('insertAuditLog')) {
                $oldData = ['user_id' => $user_id, 'username' => $username, 'email' => $user_email];
                insertAuditLog($conn, 'users', $user_id, 'DELETE', $oldData, [], $user_id);
            }

            // Clear all deletion-related session data
            unset($_SESSION['delete_pw_verified'], $_SESSION['delete_code'], $_SESSION['delete_code_time'],
                  $_SESSION['delete_email'], $_SESSION['delete_code_verified'], $_SESSION['delete_step'],
                  $_SESSION['delete_alert']);

            // Delete the login cookie
            setcookie('abc', '', time() - 3600, '/');

            // Redirect to log_out.php (which will clear the rest and redirect to login)
            header("Location: log_out.php");
            exit();
        } else {
            setAlert('danger', 'Failed to delete account. Please try again or contact support.');
            $_SESSION['delete_step'] = 'confirm_delete';
            header("Location: delete_account_frontend_user_side.php");
            exit();
        }
    }
}

// On GET requests, the step is already set from session; nothing extra needed.
?>