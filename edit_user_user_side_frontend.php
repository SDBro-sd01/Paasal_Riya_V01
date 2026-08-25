<?php
session_start();
include "includes/db_connection.php";
include "includes/sri_lanka_provinces_districts.php";

$Page_Name = "Edit Profile";

// --- Cookie Login Check ---
$cookie_data = [];
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
}
$isLogged = isset($cookie_data['islogged']) && $cookie_data['islogged'] == 1;
$user_id = isset($cookie_data['user_id']) ? intval($cookie_data['user_id']) : 0;

if (!$isLogged || $user_id === 0) {
    header("Location: login.php");
    exit();
}

// --- Fetch current user data ---
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: login.php");
    exit();
}

// Old values for form
$old_username  = htmlspecialchars($user['username']);
$old_fullname  = htmlspecialchars($user['fullname']);
$old_mobile    = htmlspecialchars($user['mobile']);
$old_email     = htmlspecialchars($user['email']);
$old_nic       = htmlspecialchars($user['nic']);
$old_address   = htmlspecialchars($user['address']);
$old_user_type = htmlspecialchars($user['user_type']);
$old_province  = htmlspecialchars($user['province']);
$old_district  = htmlspecialchars($user['district']);
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

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* =============================================
           DESIGN TOKENS
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

            --warning-text: #fb923c;

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

        /* =============================================
           GLOBAL RESET / BASE
        ============================================= */
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
        .profile-wrapper {
            max-width: 860px;
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
           ALERT MESSAGES
        ============================================= */
        .alert-custom {
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

        .alert-success {
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            color: var(--success-text);
        }

        .alert-error {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            color: var(--error-text);
        }

        .alert-icon { font-size: 16px; margin-top: 1px; flex-shrink: 0; }

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

        /* =============================================
           FORM CONTROLS
        ============================================= */
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 18px;
            margin-bottom: 18px;
        }

        .form-row.full { grid-template-columns: 1fr; }

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

        .field-label i { font-size: 13px; color: var(--text-muted); }

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
           WARNING TEXT
        ============================================= */
        .warning-text {
            display: none;
            align-items: center;
            gap: 6px;
            color: var(--warning-text);
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 6px;
            padding: 8px 12px;
            background: rgba(251,146,60,0.08);
            border: 1px solid rgba(251,146,60,0.2);
            border-radius: var(--radius-sm);
        }

        .warning-text.visible { display: flex; }

        /* =============================================
           FORM FOOTER / ACTIONS
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
            color: #fff;
        }

        .btn-primary-custom:active { transform: translateY(0); }

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
           RESPONSIVE
        ============================================= */
        @media (max-width: 600px) {
            .profile-wrapper { padding: 24px 14px 60px; }
            .form-section { padding: 20px 16px 0; }
            .form-section:last-of-type { padding-bottom: 20px; }
            .form-divider { margin: 20px 0 0; }
            .form-row { grid-template-columns: 1fr; gap: 14px; margin-bottom: 14px; }
            .form-actions { padding: 16px; flex-direction: column; align-items: stretch; }
            .btn-primary-custom,
            .btn-secondary-custom { justify-content: center; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { transition: none !important; animation: none !important; }
        }
    </style>
</head>
<body>

<?php include "stars_bg.php"; ?>

<div class="profile-wrapper">

    <!-- ── PAGE HEADER ───────────────────────────── -->
    <div class="page-header">
        <div class="page-header-icon"><i class="bi bi-person-gear"></i></div>
        <div class="page-header-text">
            <h1><?php echo $Page_Name; ?></h1>
            <p>Update your account information and preferences.</p>
        </div>
    </div>

    <!-- ── SESSION MESSAGES ──────────────────────── -->
    <?php
    if (isset($_SESSION['success'])) {
        echo '<div class="alert-custom alert-success">
                <i class="bi bi-check-circle-fill alert-icon"></i>
                <span>' . htmlspecialchars($_SESSION['success']) . '</span>
              </div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        $errors = $_SESSION['error'];
        if (is_array($errors)) {
            foreach ($errors as $err) {
                echo '<div class="alert-custom alert-error">
                        <i class="bi bi-exclamation-circle-fill alert-icon"></i>
                        <span>' . htmlspecialchars($err) . '</span>
                      </div>';
            }
        } else {
            echo '<div class="alert-custom alert-error">
                    <i class="bi bi-exclamation-circle-fill alert-icon"></i>
                    <span>' . htmlspecialchars($errors) . '</span>
                  </div>';
        }
        unset($_SESSION['error']);
    }
    ?>

    <!-- ── FORM CARD ──────────────────────────────── -->
    <form action="includes/edit_user_user_side_backend.php" method="POST">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

        <div class="form-card">

            <!-- Section: Account Info -->
            <div class="form-section">
                <div class="section-label"><i class="bi bi-person"></i> Account Info</div>

                <div class="form-row">
                    <div class="field-group">
                        <label class="field-label" for="username">
                            <i class="bi bi-at"></i> Username
                        </label>
                        <input type="text" id="username" name="username" class="field-input"
                               value="<?php echo $old_username; ?>" required
                               placeholder="your_username">
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="fullname">
                            <i class="bi bi-person-badge"></i> Full Name
                        </label>
                        <input type="text" id="fullname" name="fullname" class="field-input"
                               value="<?php echo $old_fullname; ?>" required
                               placeholder="Your full name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="field-group">
                        <label class="field-label" for="email">
                            <i class="bi bi-envelope"></i> Email
                        </label>
                        <input type="email" id="email" name="email" class="field-input"
                               value="<?php echo $old_email; ?>" required
                               placeholder="you@example.com">
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="mobile">
                            <i class="bi bi-phone"></i> Mobile
                        </label>
                        <input type="text" id="mobile" name="mobile" class="field-input"
                               value="<?php echo $old_mobile; ?>" required
                               placeholder="+94 7X XXX XXXX">
                    </div>
                </div>

                <div class="form-row">
                    <div class="field-group">
                        <label class="field-label" for="nic">
                            <i class="bi bi-credit-card-2-front"></i> NIC
                        </label>
                        <input type="text" id="nic" name="nic" class="field-input"
                               value="<?php echo $old_nic; ?>" required
                               placeholder="123456789V">
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="user_type">
                            <i class="bi bi-shield-check"></i> Account Type
                        </label>
                        <select id="user_type" name="user_type" class="field-input" required>
                            <?php if ($old_user_type === 'admin'): ?>
                                <option value="admin" <?php echo ($old_user_type == 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <?php endif; ?>
                            <option value="Parents" <?php echo ($old_user_type == 'Parents') ? 'selected' : ''; ?>>Parents</option>
                            <option value="Vehicle Owner" <?php echo ($old_user_type == 'Vehicle Owner') ? 'selected' : ''; ?>>Vehicle Owner</option>
                        </select>
                        <?php if ($old_user_type === 'admin'): ?>
                        <div id="admin-warning" class="warning-text">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            This can't be undone — please reconsider before changing.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div><!-- /section -->

            <div class="form-divider"></div>

            <!-- Section: Location -->
            <div class="form-section">
                <div class="section-label"><i class="bi bi-geo-alt"></i> Location</div>

                <div class="form-row">
                    <div class="field-group">
                        <label class="field-label" for="province">
                            <i class="bi bi-map"></i> Province
                        </label>
                        <select id="province" name="province" class="field-input" required>
                            <option value="">— Select Province —</option>
                            <?php
                            foreach ($sl_provinces as $prov => $districts) {
                                $selected = ($prov === $old_province) ? 'selected' : '';
                                echo "<option value=\"$prov\" $selected>$prov</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="district">
                            <i class="bi bi-pin-map"></i> District
                        </label>
                        <select id="district" name="district" class="field-input" required>
                            <option value="">— Select District —</option>
                        </select>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="field-group">
                        <label class="field-label" for="address">
                            <i class="bi bi-house"></i> Address
                        </label>
                        <textarea id="address" name="address" class="field-input" required
                                  placeholder="Your full address…"><?php echo $old_address; ?></textarea>
                    </div>
                </div>
            </div><!-- /section -->

            <!-- ── FORM ACTIONS ──────────────────────── -->
            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">
                    <i class="bi bi-floppy"></i> Save Changes
                </button>
                <a href="index.php" class="btn-secondary-custom">
                    <i class="bi bi-x"></i> Cancel
                </a>
            </div>

        </div><!-- /form-card -->
    </form>

</div><!-- /profile-wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Province → District cascade -->
<script>
const districtsByProvince = <?php echo json_encode($sl_provinces); ?>;
const oldDistrict = <?php echo json_encode($old_district); ?>;

function populateDistricts(province) {
    const districtSelect = document.getElementById('district');
    districtSelect.innerHTML = '<option value="">— Select District —</option>';
    if (province && districtsByProvince[province]) {
        districtsByProvince[province].forEach(function(dist) {
            const option = document.createElement('option');
            option.value = dist;
            option.textContent = dist;
            if (dist === oldDistrict) option.selected = true;
            districtSelect.appendChild(option);
        });
    }
}

window.addEventListener('DOMContentLoaded', function () {
    const provinceSelect = document.getElementById('province');
    populateDistricts(provinceSelect.value);
    provinceSelect.addEventListener('change', function () {
        populateDistricts(this.value);
    });

    <?php if ($old_user_type === 'admin'): ?>
    const userTypeSelect = document.getElementById('user_type');
    const adminWarning   = document.getElementById('admin-warning');
    function toggleWarning() {
        if (userTypeSelect.value !== 'admin') {
            adminWarning.classList.add('visible');
        } else {
            adminWarning.classList.remove('visible');
        }
    }
    userTypeSelect.addEventListener('change', toggleWarning);
    toggleWarning();
    <?php endif; ?>
});
</script>

</body>
</html>