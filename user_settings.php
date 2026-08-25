<?php
include "includes/db_connection.php";

$Page_Name = "User Settings";

// ==================== USER SETTINGS COOKIE LOGIC ====================
$cookie_name = 'user_settings';
$default_settings = [
    'user_language' => 'English',
    'user_theme' => 'Dark'
];

// Check if cookie exists; if not, create it with defaults (never expire = 20 years)
if (!isset($_COOKIE[$cookie_name])) {
    $cookie_value = json_encode($default_settings);
    // 20 years expiration = practically never expires
    setcookie($cookie_name, $cookie_value, time() + (20 * 365 * 24 * 60 * 60), '/');
    $current_settings = $default_settings;
} else {
    $current_settings = json_decode($_COOKIE[$cookie_name], true);
    // Fallback if JSON is corrupted
    if (!is_array($current_settings)) {
        $current_settings = $default_settings;
    }
    // Ensure all keys exist
    $current_settings = array_merge($default_settings, $current_settings);
}

// Handle Apply button submission
$show_toast = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_settings'])) {
    $new_settings = [
        'user_language' => $_POST['user_language'] ?? $default_settings['user_language'],
        'user_theme' => $_POST['user_theme'] ?? $default_settings['user_theme']
    ];
    // Validate inputs
    if (!in_array($new_settings['user_language'], ['English', 'Sinhala'])) {
        $new_settings['user_language'] = $default_settings['user_language'];
    }
    if (!in_array($new_settings['user_theme'], ['Dark', 'Light'])) {
        $new_settings['user_theme'] = $default_settings['user_theme'];
    }
    $cookie_value = json_encode($new_settings);
    setcookie($cookie_name, $cookie_value, time() + (20 * 365 * 24 * 60 * 60), '/');
    $current_settings = $new_settings;
    // Redirect to reflect changes & trigger toast
    header('Location: ' . $_SERVER['PHP_SELF'] . '?updated=1');
    exit;
}

// Check for toast trigger
if (isset($_GET['updated']) && $_GET['updated'] == '1') {
    $show_toast = true;
}

// ==================== TRANSLATIONS ====================
$lang = $current_settings['user_language'];
$t = [
    'page_title' => ($lang === 'Sinhala') ? 'පරිශීලක සැකසුම්' : 'User Settings',
    'current_settings' => ($lang === 'Sinhala') ? 'වත්මන් සැකසුම්' : 'Current Settings',
    'language_label' => ($lang === 'Sinhala') ? 'භාෂාව' : 'Language',
    'theme_label' => ($lang === 'Sinhala') ? 'තේමාව' : 'Theme',
    'apply_btn' => ($lang === 'Sinhala') ? 'යොදන්න' : 'Apply Settings',
    'english' => ($lang === 'Sinhala') ? 'ඉංග්‍රීසි' : 'English',
    'sinhala' => ($lang === 'Sinhala') ? 'සිංහල' : 'Sinhala',
    'dark' => ($lang === 'Sinhala') ? 'අඳුරු' : 'Dark',
    'light' => ($lang === 'Sinhala') ? 'ආලෝක' : 'Light',
    'toast_success' => ($lang === 'Sinhala') ? '✅ සැකසුම් සාර්ථකව යාවත්කාලීන කරන ලදී!' : '✅ Settings updated successfully!',
    'current_lang' => ($lang === 'Sinhala') ? 'වත්මන් භාෂාව' : 'Current Language',
    'current_theme' => ($lang === 'Sinhala') ? 'වත්මන් තේමාව' : 'Current Theme',
    'select_lang' => ($lang === 'Sinhala') ? 'භාෂාව තෝරන්න' : 'Select Language',
    'select_theme' => ($lang === 'Sinhala') ? 'තේමාව තෝරන්න' : 'Select Theme',
    'card_subtitle' => ($lang === 'Sinhala') ? 'ඔබගේ භාෂාව සහ තේමා මනාපයන් සකසන්න' : 'Customize your language and theme preferences',
];

$current_theme = $current_settings['user_theme']; // 'Dark' or 'Light'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['page_title']; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            /* Dark Theme (Default) */
            --bg-primary: #0a0e17;
            --bg-secondary: #111827;
            --bg-card: rgba(22, 27, 40, 0.85);
            --bg-card-solid: #161b28;
            --bg-input: rgba(30, 36, 52, 0.9);
            --border-color: rgba(255, 255, 255, 0.08);
            --border-glow: rgba(99, 200, 130, 0.3);
            --text-primary: #e8eaef;
            --text-secondary: #a0a8b8;
            --text-muted: #6b7280;
            --accent-green: #2ecc71;
            --accent-green-hover: #27ae60;
            --accent-green-glow: rgba(46, 204, 113, 0.4);
            --badge-lang-bg: rgba(52, 152, 219, 0.2);
            --badge-lang-text: #5dade2;
            --badge-theme-bg: rgba(155, 89, 182, 0.2);
            --badge-theme-text: #bb8fce;
            --card-shadow: 0 8px 40px rgba(0, 0, 0, 0.5);
            --card-border: 1px solid rgba(255, 255, 255, 0.06);
            --glass-blur: blur(18px);
            --gradient-bg: linear-gradient(135deg, #0a0e17 0%, #111827 40%, #0d1520 70%, #0a0f1a 100%);
            --select-arrow: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23a0a8b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            --toast-bg: rgba(22, 27, 40, 0.95);
            --icon-sun-moon: '🌙';
            --badge-current-bg: rgba(46, 204, 113, 0.15);
            --badge-current-text: #2ecc71;
        }

        /* Light Theme Overrides */
        body.light-theme {
            --bg-primary: #f4f6f9;
            --bg-secondary: #e8ecf1;
            --bg-card: rgba(255, 255, 255, 0.82);
            --bg-card-solid: #ffffff;
            --bg-input: rgba(240, 242, 245, 0.95);
            --border-color: rgba(0, 0, 0, 0.08);
            --border-glow: rgba(39, 174, 96, 0.35);
            --text-primary: #1a1d28;
            --text-secondary: #4a5568;
            --text-muted: #8892a0;
            --accent-green: #27ae60;
            --accent-green-hover: #219a52;
            --accent-green-glow: rgba(39, 174, 96, 0.5);
            --badge-lang-bg: rgba(41, 128, 185, 0.15);
            --badge-lang-text: #2471a3;
            --badge-theme-bg: rgba(142, 68, 173, 0.15);
            --badge-theme-text: #7d3c98;
            --card-shadow: 0 8px 40px rgba(0, 0, 0, 0.12);
            --card-border: 1px solid rgba(0, 0, 0, 0.06);
            --glass-blur: blur(16px);
            --gradient-bg: linear-gradient(135deg, #f4f6f9 0%, #e8ecf1 40%, #eef1f5 70%, #f4f6f9 100%);
            --select-arrow: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%234a5568' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            --toast-bg: rgba(255, 255, 255, 0.96);
            --icon-sun-moon: '☀️';
            --badge-current-bg: rgba(39, 174, 96, 0.12);
            --badge-current-text: #219a52;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--gradient-bg);
            background-attachment: fixed;
            font-family: 'Segoe UI', 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text-primary);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            transition: background 0.5s ease, color 0.4s ease;
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle animated background orbs */
        body::before {
            content: '';
            position: fixed;
            top: -180px;
            right: -180px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(46, 204, 113, 0.07) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
            pointer-events: none;
            animation: floatOrb 14s ease-in-out infinite;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -150px;
            left: -150px;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(52, 152, 219, 0.06) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
            pointer-events: none;
            animation: floatOrb 18s ease-in-out infinite reverse;
        }

        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -25px) scale(1.08); }
            50% { transform: translate(-15px, 20px) scale(0.95); }
            75% { transform: translate(20px, 10px) scale(1.04); }
        }

        .main-container {
            position: relative;
            z-index: 1;
            padding: 30px 20px 50px;
            max-width: 750px;
            margin: 0 auto;
        }

        /* Page Header */
        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .page-header .icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(46, 204, 113, 0.1);
            border: 2px solid rgba(46, 204, 113, 0.25);
            font-size: 28px;
            margin-bottom: 12px;
            animation: pulseIcon 3s ease-in-out infinite;
        }
        @keyframes pulseIcon {
            0%, 100% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.35); }
            50% { box-shadow: 0 0 0 18px rgba(46, 204, 113, 0); }
        }
        .page-header h2 {
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: -0.3px;
            margin: 0;
            color: var(--text-primary);
        }
        .page-header .subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-top: 4px;
        }

        /* Glass Card */
        .glass-card {
            background: var(--bg-card);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: var(--card-border);
            border-radius: 20px;
            padding: 28px 30px;
            margin-bottom: 22px;
            box-shadow: var(--card-shadow);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--border-glow), transparent);
            opacity: 0.7;
        }
        .glass-card:hover {
            box-shadow: var(--card-shadow), 0 0 60px rgba(46, 204, 113, 0.06);
        }
        .card-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            margin-bottom: 6px;
            font-weight: 600;
        }
        .card-title-sm {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--text-primary);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Current Settings Badges */
        .current-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: center;
        }
        .setting-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 18px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.35s ease;
        }
        .badge-lang {
            background: var(--badge-lang-bg);
            color: var(--badge-lang-text);
            border: 1px solid rgba(52, 152, 219, 0.2);
        }
        .badge-theme {
            background: var(--badge-theme-bg);
            color: var(--badge-theme-text);
            border: 1px solid rgba(155, 89, 182, 0.2);
        }
        .badge-current-indicator {
            background: var(--badge-current-bg);
            color: var(--badge-current-text);
            border: 1px solid rgba(46, 204, 113, 0.25);
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .setting-badge i {
            font-size: 1.1rem;
        }

        /* Form Styles */
        .form-group-custom {
            margin-bottom: 20px;
        }
        .form-group-custom label {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-primary);
            margin-bottom: 7px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .form-group-custom label i {
            font-size: 1.1rem;
            color: var(--accent-green);
        }
        .custom-select-wrapper {
            position: relative;
        }
        .custom-select-wrapper select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 100%;
            padding: 13px 44px 13px 16px;
            background: var(--bg-input);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            outline: none;
            font-family: inherit;
        }
        .custom-select-wrapper select:hover {
            border-color: rgba(46, 204, 113, 0.35);
            box-shadow: 0 0 0 6px rgba(46, 204, 113, 0.04);
        }
        .custom-select-wrapper select:focus {
            border-color: var(--accent-green);
            box-shadow: 0 0 0 8px rgba(46, 204, 113, 0.08);
        }
        .custom-select-wrapper::after {
            content: '';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            background-image: var(--select-arrow);
            background-repeat: no-repeat;
            background-position: center;
            pointer-events: none;
            transition: transform 0.3s ease;
        }
        .custom-select-wrapper:has(select:focus)::after {
            transform: translateY(-50%) rotate(180deg);
        }
        select option {
            background: var(--bg-card-solid);
            color: var(--text-primary);
            padding: 10px;
        }

        /* Apply Button */
        .btn-apply {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: 100%;
            padding: 14px 28px;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            color: #fff;
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 60%, #1e8449 100%);
            border: none;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.35s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 24px var(--accent-green-glow);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-apply::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .btn-apply:hover::before {
            opacity: 1;
        }
        .btn-apply:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 36px var(--accent-green-glow);
            background: linear-gradient(135deg, #3ddb84 0%, #2ecc71 60%, #27ae60 100%);
        }
        .btn-apply:active {
            transform: translateY(-1px) scale(0.98);
            box-shadow: 0 4px 16px var(--accent-green-glow);
            transition: all 0.1s ease;
        }
        .btn-apply i {
            font-size: 1.2rem;
        }

        /* Toast Notification */
        .toast-container-custom {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            pointer-events: none;
        }
        .toast-custom {
            background: var(--toast-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: var(--card-border);
            border-left: 4px solid #2ecc71;
            border-radius: 14px;
            padding: 16px 22px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.4);
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
            opacity: 0;
            transform: translateX(120%);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            pointer-events: auto;
            min-width: 260px;
            max-width: 90vw;
        }
        .toast-custom.show {
            opacity: 1;
            transform: translateX(0);
        }
        .toast-custom .toast-icon {
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        .toast-custom .toast-close {
            margin-left: auto;
            cursor: pointer;
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.2rem;
            padding: 4px;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .toast-custom .toast-close:hover {
            color: var(--text-primary);
            background: rgba(255,255,255,0.06);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                padding: 18px 12px 40px;
            }
            .glass-card {
                padding: 20px 16px;
                border-radius: 16px;
            }
            .page-header h2 {
                font-size: 1.4rem;
            }
            .page-header .icon-circle {
                width: 48px;
                height: 48px;
                font-size: 22px;
            }
            .current-badges {
                gap: 8px;
            }
            .setting-badge {
                padding: 8px 14px;
                font-size: 0.8rem;
            }
            .btn-apply {
                padding: 12px 20px;
                font-size: 0.9rem;
                border-radius: 12px;
            }
            .custom-select-wrapper select {
                padding: 11px 38px 11px 14px;
                font-size: 0.88rem;
            }
            .toast-container-custom {
                top: 12px;
                right: 10px;
                left: 10px;
            }
            .toast-custom {
                min-width: auto;
                font-size: 0.85rem;
                padding: 12px 16px;
            }
        }
        @media (max-width: 400px) {
            .current-badges {
                flex-direction: column;
                align-items: flex-start;
            }
            .glass-card {
                padding: 16px 12px;
            }
        }
    </style>
</head>
<body class="<?php echo ($current_theme === 'Light') ? 'light-theme' : ''; ?>">

<?php include "stars_bg.php"; ?>

    <div class="main-container">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="icon-circle">⚙️</div>
            <h2><?php echo $t['page_title']; ?></h2>
            <p class="subtitle"><?php echo $t['card_subtitle']; ?></p>
        </div>

        <!-- Current Settings Card -->
        <div class="glass-card">
            <div class="card-label"><?php echo $t['current_settings']; ?></div>
            <div class="current-badges">
                <span class="setting-badge badge-lang">
                    <i class="bi bi-translate"></i>
                    <span><?php echo $t['current_lang']; ?>:</span>
                    <strong><?php echo ($current_settings['user_language'] === 'Sinhala') ? $t['sinhala'] : $t['english']; ?></strong>
                </span>
                <span class="setting-badge badge-theme">
                    <i class="bi bi-<?php echo ($current_settings['user_theme'] === 'Dark') ? 'moon-stars-fill' : 'sun-fill'; ?>"></i>
                    <span><?php echo $t['current_theme']; ?>:</span>
                    <strong><?php echo ($current_settings['user_theme'] === 'Dark') ? $t['dark'] : $t['light']; ?></strong>
                </span>
                <span class="badge-current-indicator">
                    <i class="bi bi-dot"></i> Active
                </span>
            </div>
        </div>

        <!-- Settings Form Card -->
        <div class="glass-card">
            <div class="card-title-sm">
                <i class="bi bi-sliders2-vertical" style="color: var(--accent-green);"></i>
                <?php echo $t['page_title']; ?>
            </div>
            <form method="POST" action="" id="settingsForm">
                <!-- Language Select -->
                <div class="form-group-custom">
                    <label for="user_language">
                        <i class="bi bi-globe2"></i> <?php echo $t['language_label']; ?>
                    </label>
                    <div class="custom-select-wrapper">
                        <select name="user_language" id="user_language">
                            <option value="English" <?php echo ($current_settings['user_language'] === 'English') ? 'selected' : ''; ?>>
                                🇬🇧 <?php echo $t['english']; ?>
                            </option>
                            <option value="Sinhala" <?php echo ($current_settings['user_language'] === 'Sinhala') ? 'selected' : ''; ?>>
                                🇱🇰 <?php echo $t['sinhala']; ?>
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Theme Select -->
                <div class="form-group-custom">
                    <label for="user_theme">
                        <i class="bi bi-palette2"></i> <?php echo $t['theme_label']; ?>
                    </label>
                    <div class="custom-select-wrapper">
                        <select name="user_theme" id="user_theme">
                            <option value="Dark" <?php echo ($current_settings['user_theme'] === 'Dark') ? 'selected' : ''; ?>>
                                🌙 <?php echo $t['dark']; ?>
                            </option>
                            <option value="Light" <?php echo ($current_settings['user_theme'] === 'Light') ? 'selected' : ''; ?>>
                                ☀️ <?php echo $t['light']; ?>
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Apply Button -->
                <button type="submit" name="apply_settings" class="btn-apply" style="margin-top: 10px;">
                    <i class="bi bi-check2-circle"></i> <?php echo $t['apply_btn']; ?>
                </button>
            </form>
        </div>

        <!-- Info: Cookie Status -->
        <div style="text-align:center; margin-top:16px;">
            <small style="color: var(--text-muted); font-size: 0.78rem;">
                <i class="bi bi-shield-check"></i> 
                <?php echo ($lang === 'Sinhala') ? 'සැකසුම් කුකී තුළ සුරක්ෂිතව ගබඩා කර ඇත • කිසිදා කල් ඉකුත් නොවේ' : 'Settings stored securely in cookie • Never expires'; ?>
            </small>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-container-custom">
        <div class="toast-custom" id="successToast">
            <span class="toast-icon">✅</span>
            <span><?php echo $t['toast_success']; ?></span>
            <button class="toast-close" onclick="hideToast()" title="Close">✕</button>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    
    <script>
        // ==================== TOAST LOGIC ====================
        const toastEl = document.getElementById('successToast');
        let toastTimer;

        function showToast() {
            toastEl.classList.add('show');
            // Auto-hide after 4 seconds
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                hideToast();
            }, 4000);
        }

        function hideToast() {
            toastEl.classList.remove('show');
            clearTimeout(toastTimer);
        }

        // Show toast if redirected after successful update
        <?php if ($show_toast): ?>
            showToast();
            // Clean URL without page reload (remove ?updated=1)
            if (window.history && window.history.replaceState) {
                const cleanUrl = window.location.href.split('?')[0];
                window.history.replaceState({}, document.title, cleanUrl);
            }
        <?php endif; ?>

        // ==================== LIVE PREVIEW (Optional subtle feedback) ====================
        const themeSelect = document.getElementById('user_theme');
        const bodyEl = document.body;

        // Preview theme change instantly on selection
        themeSelect.addEventListener('change', function() {
            if (this.value === 'Light') {
                bodyEl.classList.add('light-theme');
            } else {
                bodyEl.classList.remove('light-theme');
            }
        });

        // ==================== CONFIRM BEFORE LEAVING IF CHANGES UNSAVED ====================
        const formEl = document.getElementById('settingsForm');
        const originalLang = document.getElementById('user_language').value;
        const originalTheme = document.getElementById('user_theme').value;
        let isDirty = false;

        formEl.addEventListener('change', function(e) {
            const currentLang = document.getElementById('user_language').value;
            const currentTheme = document.getElementById('user_theme').value;
            isDirty = (currentLang !== originalLang || currentTheme !== originalTheme);
        });

        // Reset dirty flag on submit
        formEl.addEventListener('submit', function() {
            isDirty = false;
        });

        // Optional: warn on navigation (uncomment if needed)
        // window.addEventListener('beforeunload', function(e) {
        //     if (isDirty) {
        //         e.preventDefault();
        //         e.returnValue = 'You have unsaved changes!';
        //         return e.returnValue;
        //     }
        // });

        // ==================== KEYBOARD SHORTCUT: Ctrl+Enter to submit ====================
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                const activeEl = document.activeElement;
                if (activeEl && (activeEl.id === 'user_language' || activeEl.id === 'user_theme' || activeEl.closest('form'))) {
                    e.preventDefault();
                    formEl.querySelector('button[type="submit"]').click();
                }
            }
        });

        console.log('✅ User Settings Page Ready | Theme:', '<?php echo $current_theme; ?>', '| Language:', '<?php echo $current_settings['user_language']; ?>');
    </script>

</body>
</html>

<?php include "side_bar.php"; ?>