<?php
session_start();
require 'vendor/autoload.php';
include "includes/db_connection.php";
include "includes/audit_helper.php";

$Page_Name = "Delete Account";

// Cookie handling
$cookie_data = [];
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
}
$user_id = isset($cookie_data['user_id']) ? intval($cookie_data['user_id']) : 0;
$isLogged = isset($cookie_data['islogged']) && $cookie_data['islogged'] == 1;

// Backend processing (sets session steps/alerts)
include "includes/delete_account_backend_user_side.php";

// Retrieve alert from session
$alert = null;
if (function_exists('getAlert')) {
    $alert = getAlert();
}

// Current step from session
$step = $_SESSION['delete_step'] ?? 'verify_password';

// Map step for the indicator (combine send_code & verify_code)
$step_map = $step;
if (in_array($step, ['send_code', 'verify_code'])) {
    $step_map = 'code';
}
$steps_order = [
    'verify_password' => 'Password',
    'code'            => 'Verification Code',
    'confirm_delete'  => 'Confirm Delete'
];
$step_keys = array_keys($steps_order);
$current_index = array_search($step_map, $step_keys);
if ($current_index === false) $current_index = 0;

include "side_bar.php";
?>
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
           DESIGN TOKENS — same as provided theme
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

        /* =============================================
           PAGE WRAPPER
        ============================================= */
        .pw-wrapper {
            max-width: 520px;
            margin: 0 auto;
            padding: 40px 20px 80px;
        }

        /* =============================================
           PAGE HEADER
        ============================================= */
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

        /* =============================================
           STEP INDICATOR
        ============================================= */
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

        /* =============================================
           FORM CARD
        ============================================= */
        .pw-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            overflow: hidden;
        }

        .card-body {
            padding: 28px;
        }

        /* =============================================
           ALERTS
        ============================================= */
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

        /* =============================================
           EMAIL CHIP
        ============================================= */
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

        /* =============================================
           FORM CONTROLS
        ============================================= */
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

        /* =============================================
           INFO NOTE
        ============================================= */
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

        /* =============================================
           BUTTONS
        ============================================= */
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

        /* =============================================
           CARD FOOTER
        ============================================= */
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

        /* =============================================
           RESPONSIVE
        ============================================= */
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

<?php include "stars_bg.php"; ?>

<div class="pw-wrapper">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-icon"><i class="bi bi-trash3"></i></div>
        <div class="page-header-text">
            <h1><?php echo $Page_Name; ?></h1>
            <p>This action is permanent. Please proceed with caution.</p>
        </div>
    </div>

    <!-- Step Indicator -->
    <div class="step-track">
        <?php foreach ($steps_order as $key => $label):
            $i = array_search($key, $step_keys);
            if ($i < $current_index) $state = 'done';
            elseif ($i == $current_index) $state = 'active';
            else $state = 'idle';
            $icon = ($key == 'verify_password') ? 'bi-key' : (($key == 'code') ? 'bi-shield-check' : 'bi-exclamation-triangle');
        ?>
            <div class="step-node">
                <div class="step-circle <?php echo $state; ?>">
                    <?php if ($state === 'done'): ?>
                        <i class="bi bi-check-lg"></i>
                    <?php else: ?>
                        <i class="<?php echo $icon; ?>"></i>
                    <?php endif; ?>
                </div>
                <span class="step-label <?php echo $state; ?>"><?php echo $label; ?></span>
            </div>
            <?php if ($i < count($step_keys)-1): ?>
                <div class="step-connector <?php echo ($i < $current_index) ? 'done' : 'idle'; ?>"></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Card -->
    <div class="pw-card">
        <div class="card-body">

            <!-- Alert -->
            <?php if ($alert): ?>
                <?php
                $icon_map = ['success'=>'check-circle-fill','danger'=>'exclamation-circle-fill','warning'=>'exclamation-triangle-fill'];
                $icon_class = $icon_map[$alert['type']] ?? 'info-circle-fill';
                ?>
                <div class="alert-custom alert-<?php echo htmlspecialchars($alert['type']); ?>">
                    <i class="bi bi-<?php echo $icon_class; ?> alert-icon"></i>
                    <span><?php echo htmlspecialchars($alert['message']); ?></span>
                </div>
            <?php endif; ?>

            <!-- Step: verify_password -->
            <?php if ($step === 'verify_password'): ?>
                <p class="info-note">Enter your <strong>current password</strong> to confirm your identity.</p>
                <form method="post">
                    <input type="hidden" name="step" value="verify_password">
                    <div class="field-group">
                        <label class="field-label" for="password"><i class="bi bi-lock"></i> Password</label>
                        <div class="input-wrap">
                            <input type="password" id="password" name="password" class="field-input" required placeholder="Your current password" autocomplete="current-password">
                            <button type="button" class="toggle-pw" data-target="password"><i class="bi bi-eye" id="icon-password"></i></button>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary-custom"><i class="bi bi-arrow-right"></i> Verify Password</button>
                    </div>
                </form>

            <!-- Step: send_code -->
            <?php elseif ($step === 'send_code'): ?>
                <div class="email-chip">
                    <div class="email-chip-icon"><i class="bi bi-envelope"></i></div>
                    <div>
                        <div class="email-chip-label">Sending code to</div>
                        <div class="email-chip-value"><?php echo maskEmail($user_email); ?></div>
                    </div>
                </div>
                <p class="info-note">We'll send a <strong>6-digit code</strong> to your email for verification.</p>
                <form method="post">
                    <input type="hidden" name="step" value="send_code">
                    <div class="form-actions">
                        <button type="submit" class="btn-primary-custom"><i class="bi bi-send-fill"></i> Send Code</button>
                    </div>
                </form>

            <!-- Step: verify_code -->
            <?php elseif ($step === 'verify_code'): ?>
                <div class="email-chip">
                    <div class="email-chip-icon"><i class="bi bi-envelope-check"></i></div>
                    <div>
                        <div class="email-chip-label">Code sent to</div>
                        <div class="email-chip-value"><?php echo maskEmail($user_email); ?></div>
                    </div>
                </div>
                <p class="info-note">Enter the <strong>6-digit code</strong> from your email.</p>
                <div class="expiry-note"><i class="bi bi-clock"></i> Code expires in 5 minutes</div>
                <form method="post" style="margin-top:20px;">
                    <input type="hidden" name="step" value="verify_code">
                    <div class="field-group">
                        <label class="field-label" for="verification_code"><i class="bi bi-hash"></i> Verification Code</label>
                        <input type="text" id="verification_code" name="verification_code" class="field-input otp-input" pattern="\d{6}" maxlength="6" inputmode="numeric" autocomplete="one-time-code" required placeholder="000000">
                    </div>
                    <div class="form-actions-row">
                        <button type="submit" class="btn-primary-custom"><i class="bi bi-shield-check"></i> Verify Code</button>
                        <button type="submit" class="btn-ghost-custom" name="step" value="send_code"><i class="bi bi-arrow-repeat"></i> Resend</button>
                    </div>
                </form>

            <!-- Step: confirm_delete -->
            <?php elseif ($step === 'confirm_delete'): ?>
                <div class="email-chip">
                    <div class="email-chip-icon" style="background:rgba(248,79,79,0.18);border-color:rgba(248,79,79,0.3);color:#f87171;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <div class="email-chip-label">Account Deletion</div>
                        <div class="email-chip-value">This action cannot be undone</div>
                    </div>
                </div>
                <p class="info-note" style="color:var(--error-text);">All your data will be permanently removed.</p>
                <form method="post" id="deleteConfirmForm">
                    <input type="hidden" name="step" value="confirm_delete">
                    <div class="field-group" style="flex-direction:row;align-items:center;gap:10px;">
                        <input type="checkbox" id="confirmCheck" name="confirm" value="1" style="width:18px;height:18px;accent-color:var(--accent);">
                        <label for="confirmCheck" style="font-size:0.9rem;color:var(--text-secondary);">
                            I understand that my account will be permanently deleted and all data will be lost.
                        </label>
                    </div>
                    <div class="form-actions">
                        <button type="submit" id="deleteBtn" class="btn-primary-custom" style="background:#e11d48;border-color:#e11d48;" disabled>
                            <i class="bi bi-trash"></i> Delete My Account
                        </button>
                    </div>
                </form>
                <script>
                    document.getElementById('confirmCheck').addEventListener('change', function(){
                        document.getElementById('deleteBtn').disabled = !this.checked;
                    });
                </script>
            <?php endif; ?>

        </div><!-- /card-body -->

        <!-- Card Footer -->
        <div class="card-footer-strip">
            <span class="footer-note"><i class="bi bi-shield-exclamation"></i> Please be certain before proceeding</span>
            <a href="index.php" class="btn-text-custom"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div><!-- /pw-card -->

</div><!-- /pw-wrapper -->

<!-- Password toggle script -->
<script>
    document.querySelectorAll('.toggle-pw').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            var icon = document.getElementById('icon-' + targetId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    });
</script>

</body>
</html>