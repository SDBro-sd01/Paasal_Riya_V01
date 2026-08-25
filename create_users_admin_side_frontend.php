<?php
// Start session (if not already)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include backend logic (form processing)
include "Includes/create_users_admin_side_backend.php";

$Page_Name = "Create Users";

// Session messages helper (exactly as requested)
$Session_Messages_Helper = [
    "create_user_success" => [
        "class" => "session-success",
        "icon"  => "fa-check-circle"
    ],
    "create_user_error" => [
        "class" => "session-error",
        "icon"  => "fa-times-circle"
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $Page_Name; ?></title>

    <!-- Font Awesome (sidebar එකට අවශ්‍යයි) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* =============================================
           DESIGN TOKENS – modern dark theme
        ============================================= */
        :root {
            --bg-base:      #0d0f14;
            --bg-surface:   #13161e;
            --bg-card:      #181c27;
            --bg-elevated:  #1e2333;
            --border:       #272d3f;
            --border-focus: #4f7ef8;

            --accent:       #4f7ef8;
            --accent-glow:  rgba(79,126,248,0.18);
            --accent-hover: #6b93ff;

            --success-bg:   #0d2318;
            --success-border:#1a4d30;
            --success-text: #4ade80;

            --error-bg:     #230d0d;
            --error-border: #4d1a1a;
            --error-text:   #f87171;

            --text-primary:   #e8eaf2;
            --text-secondary: #8892a4;
            --text-muted:     #4e5769;

            --font-display: 'Space Grotesk', sans-serif;
            --font-body:    'Inter', sans-serif;

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;

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
           MAIN WRAPPER
        ============================================= */
        .profile-wrapper {
            max-width: 860px;
            margin: 0 auto;
            padding: 40px 20px 80px;
            position: relative;
              z-index: 1; 
        }

        /* =============================================
           PAGE HEADER
        ============================================= */
        .page-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 36px;
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
            font-size: clamp(1.35rem, 3vw, 1.75rem);
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
           SESSION MESSAGES
        ============================================= */
        .session-success,
        .session-error {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 18px;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 20px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .session-success {
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            color: var(--success-text);
        }

        .session-error {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            color: var(--error-text);
        }

        /* =============================================
           FORM CARD
        ============================================= */
        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .form-section {
            padding: 28px 28px 0;
        }
        .form-section:last-of-type {
            padding-bottom: 28px;
        }

        .section-label {
            font-family: var(--font-display);
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .form-divider {
            height: 1px;
            background: var(--border);
            margin: 28px 0 0;
        }

        /* Grid rows */
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 18px;
            margin-bottom: 18px;
        }
        .form-row.full {
            grid-template-columns: 1fr;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field-label {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .field-label i {
            font-size: 13px;
            color: var(--text-muted);
        }

        .field-input {
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-family: var(--font-body);
            font-size: 0.9rem;
            padding: 10px 14px;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
            width: 100%;
            outline: none;
            -webkit-appearance: none;
        }
        .field-input::placeholder {
            color: var(--text-muted);
        }
        .field-input:hover {
            border-color: #353d54;
            background: #222736;
        }
        .field-input:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px var(--accent-glow);
            background: #1a2038;
        }

        /* Select arrow */
        select.field-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%238892a4' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
            cursor: pointer;
        }
        select.field-input option {
            background: var(--bg-card);
            color: var(--text-primary);
        }

        textarea.field-input {
            resize: vertical;
            min-height: 90px;
        }

        /* =============================================
           FORM ACTIONS
        ============================================= */
        .form-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 28px;
            background: var(--bg-surface);
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
        }

        .btn-primary-custom {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--accent);
            color: #fff;
            font-family: var(--font-body);
            font-size: 0.875rem;
            font-weight: 600;
            padding: 10px 22px;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
            text-decoration: none;
        }
        .btn-primary-custom:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(79,126,248,0.35);
        }
        .btn-primary-custom:active {
            transform: translateY(0);
        }

        .btn-secondary-custom {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: var(--text-secondary);
            font-family: var(--font-body);
            font-size: 0.875rem;
            font-weight: 500;
            padding: 10px 18px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all var(--transition);
            text-decoration: none;
        }
        .btn-secondary-custom:hover {
            background: var(--bg-elevated);
            color: var(--text-primary);
            border-color: #353d54;
        }

        /* =============================================
           VALIDATION (Bootstrap-style without Bootstrap)
        ============================================= */
        .invalid-feedback {
            display: none;
            color: #f87171;
            font-size: 0.8rem;
            margin-top: 4px;
        }
        .was-validated .field-input:invalid {
            border-color: #f87171;
        }
        .was-validated .field-input:invalid ~ .invalid-feedback {
            display: block;
        }

        /* =============================================
           RESPONSIVE
        ============================================= */
        @media (max-width: 600px) {
            .profile-wrapper {
                padding: 24px 14px 60px;
            }
            .form-section {
                padding: 20px 16px 0;
            }
            .form-section:last-of-type {
                padding-bottom: 20px;
            }
            .form-divider {
                margin: 20px 0 0;
            }
            .form-row {
                grid-template-columns: 1fr;
                gap: 14px;
                margin-bottom: 14px;
            }
            .form-actions {
                padding: 16px;
                flex-direction: column;
                align-items: stretch;
            }
            .btn-primary-custom,
            .btn-secondary-custom {
                justify-content: center;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar (unchanged) -->
<?php include "side_bar.php"; ?>
<?php include "stars_bg.php"; ?>

<!-- 
    ⚠️ Force dark background even if side_bar loads Bootstrap or other styles 
    This does NOT break any sidebar functionality
-->
<style>
    body {
        background-color: #0d0f14 !important;
        color: #e8eaf2 !important;
    }
</style>

<!-- Main content wrapper -->
<div class="profile-wrapper">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-icon"><i class="fas fa-user-plus"></i></div>
        <div class="page-header-text">
            <h1><?php echo $Page_Name; ?></h1>
            <p>Create a new user account with all required details.</p>
        </div>
    </div>

    <!-- Session Messages -->
    <?php
    if (isset($_SESSION['create_user_success'])) {
        $msg = $_SESSION['create_user_success'];
        echo '<div class="session-success"><i class="fa fa-check-circle"></i> ' . htmlspecialchars($msg) . '</div>';
        unset($_SESSION['create_user_success']);
    }
    if (isset($_SESSION['create_user_error'])) {
        $msg = $_SESSION['create_user_error'];
        echo '<div class="session-error"><i class="fa fa-times-circle"></i> ' . htmlspecialchars($msg) . '</div>';
        unset($_SESSION['create_user_error']);
    }
    ?>

    <!-- Form Card -->
    <form method="POST" action="" class="needs-validation" novalidate>
        <div class="form-card">

            <!-- Section: Account Info -->
            <div class="form-section">
                <div class="section-label"><i class="fas fa-user"></i> Account Info</div>

                <div class="form-row">
                    <div class="field-group">
                        <label class="field-label" for="username">
                            <i class="fas fa-at"></i> Username <span style="color:var(--error-text)">*</span>
                        </label>
                        <input type="text" id="username" name="username" class="field-input" required
                               maxlength="50" placeholder="Enter username">
                        <div class="invalid-feedback">Username is required.</div>
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="fullname">
                            <i class="fas fa-id-badge"></i> Full Name <span style="color:var(--error-text)">*</span>
                        </label>
                        <input type="text" id="fullname" name="fullname" class="field-input" required
                               maxlength="100" placeholder="Enter full name">
                        <div class="invalid-feedback">Full name is required.</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="field-group">
                        <label class="field-label" for="mobile">
                            <i class="fas fa-mobile-alt"></i> Mobile <span style="color:var(--error-text)">*</span>
                        </label>
                        <input type="text" id="mobile" name="mobile" class="field-input" required
                               maxlength="15" placeholder="07XXXXXXXX">
                        <div class="invalid-feedback">Mobile number is required.</div>
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="email">
                            <i class="fas fa-envelope"></i> Email <span style="color:var(--error-text)">*</span>
                        </label>
                        <input type="email" id="email" name="email" class="field-input" required
                               maxlength="100" placeholder="example@email.com">
                        <div class="invalid-feedback">Valid email is required.</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="field-group">
                        <label class="field-label" for="nic">
                            <i class="fas fa-id-card"></i> NIC <span style="color:var(--error-text)">*</span>
                        </label>
                        <input type="text" id="nic" name="nic" class="field-input" required
                               maxlength="20" placeholder="National Identity Card number">
                        <div class="invalid-feedback">NIC is required.</div>
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="user_type">
                            <i class="fas fa-shield-alt"></i> User Type <span style="color:var(--error-text)">*</span>
                        </label>
                        <select id="user_type" name="user_type" class="field-input" required>
                            <option value="">Select User Type</option>
                            <option value="Parents">Parents</option>
                            <option value="Vehicle Owner">Vehicle Owner</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div class="invalid-feedback">Please select a user type.</div>
                    </div>
                </div>
            </div>

            <div class="form-divider"></div>

            <!-- Section: Location -->
            <div class="form-section">
                <div class="section-label"><i class="fas fa-map-marker-alt"></i> Location</div>

                <div class="form-row">
                    <div class="field-group">
                        <label class="field-label" for="province">
                            <i class="fas fa-map"></i> Province <span style="color:var(--error-text)">*</span>
                        </label>
                        <select id="province" name="province" class="field-input" required>
                            <option value="">Select Province</option>
                            <option value="Western">Western</option>
                            <option value="Central">Central</option>
                            <option value="Southern">Southern</option>
                            <option value="Northern">Northern</option>
                            <option value="Eastern">Eastern</option>
                            <option value="North Western">North Western</option>
                            <option value="North Central">North Central</option>
                            <option value="Uva">Uva</option>
                            <option value="Sabaragamuwa">Sabaragamuwa</option>
                        </select>
                        <div class="invalid-feedback">Please select a province.</div>
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="district">
                            <i class="fas fa-pin"></i> District <span style="color:var(--error-text)">*</span>
                        </label>
                        <select id="district" name="district" class="field-input" required>
                            <option value="">Select Province first</option>
                        </select>
                        <div class="invalid-feedback">Please select a district.</div>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="field-group">
                        <label class="field-label" for="address">
                            <i class="fas fa-home"></i> Address <span style="color:var(--error-text)">*</span>
                        </label>
                        <textarea id="address" name="address" class="field-input" rows="3" required
                                  placeholder="Enter full address"></textarea>
                        <div class="invalid-feedback">Address is required.</div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">
                    <i class="fa fa-user-plus"></i> Create User
                </button>
                <a href="index.php" class="btn-secondary-custom">
                    <i class="fa fa-times"></i> Cancel
                </a>
            </div>

        </div><!-- /form-card -->
    </form>
</div><!-- /profile-wrapper -->

<!-- JavaScript -->
<script>
// Province → District mapping
const districtsByProvince = {
    "Western":        ["Colombo", "Gampaha", "Kalutara"],
    "Central":        ["Kandy", "Matale", "Nuwara Eliya"],
    "Southern":       ["Galle", "Matara", "Hambantota"],
    "Northern":       ["Jaffna", "Kilinochchi", "Mannar", "Vavuniya", "Mullaitivu"],
    "Eastern":        ["Batticaloa", "Ampara", "Trincomalee"],
    "North Western":  ["Kurunegala", "Puttalam"],
    "North Central":  ["Anuradhapura", "Polonnaruwa"],
    "Uva":            ["Badulla", "Moneragala"],
    "Sabaragamuwa":   ["Ratnapura", "Kegalle"]
};

document.getElementById('province').addEventListener('change', function() {
    var province = this.value;
    var districtSelect = document.getElementById('district');
    districtSelect.innerHTML = '<option value="">Select District</option>';
    if (province && districtsByProvince[province]) {
        districtsByProvince[province].forEach(function(district) {
            var option = document.createElement('option');
            option.value = district;
            option.textContent = district;
            districtSelect.appendChild(option);
        });
    }
});

// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

</body>
</html>