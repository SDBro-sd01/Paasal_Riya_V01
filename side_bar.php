<?php
// side_bar.php - Enhanced Responsive Sidebar with Auto-Hide Toggle & Active Page Highlight

$cookie_data = [];

// Check if 'abc' cookie exists
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
}

$user_id = 0;
$isLogged = false;
$username = '';
$user_type = '';

// Get user_id safely
if (isset($cookie_data['user_id'])) {
    $user_id = intval($cookie_data['user_id']);
    
    if (isset($cookie_data['islogged']) && $cookie_data['islogged'] == 1) {
        $isLogged = true;
    }

    // Fetch username from 'users' table
    $sql = "SELECT username, user_type FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($username, $user_type);
        $stmt->fetch();
        $stmt->close();
    }

    if (empty($username)) {
        $username = "User" . $user_id;
    }

    $avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=667eea&color=fff&size=45";
} else {
    $username = "Guest";
    $avatar_url = "https://ui-avatars.com/api/?name=Guest&background=667eea&color=fff&size=45";
}
?>
<head>

<link rel="icon" type="image/png" href="assets/logo.png">

</head>
<!-- External CSS Libraries (include only if not already present) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Custom Sidebar CSS -->
<style>
    /* CSS Variables for highlight colours – change these easily */
    :root {
        --main-highlight-bg: rgba(255, 255, 255, 0.25);   /* main menu items */
        --main-highlight-color: #ffffff;
        --sub-highlight-bg: rgba(243, 8, 212, 0.4);     /* sub‑menu items – adjust as you like */
        --sub-highlight-color: #ffffff;
    }

    /* Desktop Toggle Button (Show Menu) */
    .desktop-toggle {
        position: fixed;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        color: white;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        z-index: 997;
        transition: all 0.3s ease;
    }

    .desktop-toggle:hover {
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
    }

    .desktop-toggle.hidden {
        opacity: 0;
        pointer-events: none;
    }

    /* Tooltip */
    .desktop-toggle::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 50px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .desktop-toggle:hover::after {
        opacity: 1;
    }

    /* Left Sidebar */
    .right-sidebar {
        position: fixed;
        left: -320px;
        top: 0;
        width: 320px;
        height: 100vh;
        background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
        box-shadow: 5px 0 25px rgba(0, 0, 0, 0.3);
        transition: left 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        z-index: 999;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .right-sidebar.active {
        left: 0;
    }

    /* Close Button Inside Sidebar */
    .sidebar-close-btn {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        width: 35px;
        height: 35px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 6px;
        color: white;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        z-index: 1001;
    }

    .sidebar-close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-50%) scale(1.1);
    }

    .sidebar-close-btn::after {
        content: attr(data-tooltip);
        position: absolute;
        right: 45px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .sidebar-close-btn:hover::after {
        opacity: 1;
    }

    /* Active/highlight states for MAIN menu items (top‑level links) */
    .menu-link.active,
    .menu-link.has-submenu.active {
        background: var(--main-highlight-bg);
        color: var(--main-highlight-color);
        font-weight: 600;
    }

    .menu-link.has-submenu.active:before {
        background: var(--main-highlight-color);
    }

    /* User dropdown items – also use the main highlight colour */
    .user-dropdown-item.active {
        background: var(--main-highlight-bg);
        color: var(--main-highlight-color);
        font-weight: 600;
    }

    /* Active/highlight state for SUB‑menu items */
    .submenu-item.active {
        background: var(--sub-highlight-bg);
        color: var(--sub-highlight-color);
        font-weight: 600;
    }

    /* Locked menu item style */
    .submenu-item.locked {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: auto;
    }
    .submenu-item.locked:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: none;
    }
    .submenu-item.locked i.fa-lock {
        margin-left: auto;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.8);
    }

    /* Header */
    .sidebar-header {
        padding: 25px 20px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        cursor: pointer;
        transition: background 0.3s ease;
        text-decoration: none;
        display: block;
        position: relative;
    }

    .sidebar-header:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .logo-container {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .logo-img {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .project-title {
        color: white;
        font-size: 20px;
        font-weight: 600;
        margin: 0;
        letter-spacing: 0.5px;
    }

    /* Menu Section */
    .sidebar-menu {
        flex: 1;
        overflow-y: auto;
        padding: 20px 0;
    }

    .sidebar-menu::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-menu::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }

    .sidebar-menu::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
    }

    /* Menu Items */
    .menu-item {
        margin: 5px 15px;
    }

    .menu-link {
        display: flex;
        align-items: center;
        padding: 14px 18px;
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.3s ease;
        font-size: 15px;
        position: relative;
    }

    .menu-link:hover {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        transform: translateX(5px);
    }

    .menu-link i {
        width: 25px;
        font-size: 18px;
        margin-right: 12px;
    }

    .menu-link .fa-chevron-down {
        margin-left: auto;
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    .menu-link.active .fa-chevron-down {
        transform: rotate(180deg);
    }

    /* Submenu – space between main item and first submenu item */
    .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease;
        margin-left: 15px;
        padding-top: 5px;   /* gap between main link and submenu items */
    }

    .submenu.show {
        max-height: 500px !important;
    }

    .submenu-item {
        padding: 10px 18px;
        padding-left: 50px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        display: flex;
        align-items: center;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-size: 14px;
        position: relative;
        margin-bottom: 3px;  /* space between submenu items */
    }

    .submenu-item:last-child {
        margin-bottom: 0;    /* no extra space after the last item */
    }

    .submenu-item:before {
        content: '';
        position: absolute;
        left: 30px;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
    }

    .submenu-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transform: translateX(3px);
    }

    /* User Section */
    .sidebar-footer {
        padding: 20px 15px;
        background: rgba(0, 0, 0, 0.2);
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .user-profile {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .user-profile:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.4);
        margin-right: 12px;
        object-fit: cover;
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        color: white;
        font-weight: 600;
        font-size: 15px;
        margin: 0;
    }

    .user-status {
        color: rgba(255, 255, 255, 0.7);
        font-size: 12px;
        margin: 0;
    }

    .user-dropdown-icon {
        color: rgba(255, 255, 255, 0.7);
        font-size: 14px;
        transition: transform 0.3s ease;
    }

    .user-profile.active .user-dropdown-icon {
        transform: rotate(180deg);
    }

    /* User Dropdown Menu */
    .user-dropdown {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        margin-top: 10px;
    }

    .user-dropdown.show {
        max-height: 300px;
    }

    .user-dropdown-item {
        display: flex;
        align-items: center;
        padding: 12px 18px;
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-size: 14px;
        margin: 3px 0;
    }

    .user-dropdown-item:hover {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        transform: translateX(3px);
    }

    .user-dropdown-item i {
        width: 25px;
        margin-right: 10px;
        font-size: 16px;
    }

    .user-dropdown-item.logout {
        color: #ff6b6b;
    }

    .user-dropdown-item.logout:hover {
        background: rgba(255, 107, 107, 0.2);
    }

    .user-dropdown-item.delete_account {
        color: #ff6b6b;
    }

    .user-dropdown-item.delete_account:hover {
        background: rgba(255, 107, 107, 0.2);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .right-sidebar {
            left: -100%;
            width: 100%;
            max-width: 320px;
        }
        .sidebar-close-btn {
            display: flex;
        }
    }

    .menu-link i:first-child {
        margin-right: 8px;
    }

    .submenu-item i {
        margin-right: 6px;
    }
</style>

<?php
// --- Language from cookie ---
$cookie_name = 'user_settings';
$default_lang = 'English';

$lang = $default_lang;
if (isset($_COOKIE[$cookie_name])) {
    $settings = json_decode($_COOKIE[$cookie_name], true);
    if (is_array($settings) && isset($settings['user_language']) &&
        in_array($settings['user_language'], ['English', 'Sinhala'])) {
        $lang = $settings['user_language'];
    }
}

// --- Load JSON translations ---
$translation_file = __DIR__ . '/Languages_Files/side_bar.json';
$translations = [];

if (file_exists($translation_file)) {
    $json = file_get_contents($translation_file);
    $all_lang = json_decode($json, true);
    if (isset($all_lang[$lang])) {
        $translations = $all_lang[$lang];
    } else {
        $translations = isset($all_lang['English']) ? $all_lang['English'] : [];
    }
}

// Helper function
function __t($key, $default = '') {
    global $translations;
    return isset($translations[$key]) ? $translations[$key] : $default;
}
?>

<!-- Desktop Toggle Button -->
<button class="desktop-toggle hidden" id="desktopToggle" data-tooltip="<?php echo __t('toggle_show', 'Show Menu'); ?>">
    <i class="fas fa-chevron-right"></i>
</button>

<!-- Left Sidebar -->
<div class="right-sidebar" id="rightSidebar">
    <!-- Close Button -->
    <button class="sidebar-close-btn" id="sidebarCloseBtn" data-tooltip="<?php echo __t('toggle_hide', 'Hide Menu'); ?>">
        <i class="fas fa-chevron-left"></i>
    </button>

    <!-- Header with Logo -->
    <a href="index.php" class="sidebar-header">
        <div class="logo-container">
            <img src="Assets/logo.png" alt="Logo" class="logo-img" onerror="this.src='https://via.placeholder.com/45/667eea/ffffff?text=MP'">
            <h2 class="project-title">Paasal Riya</h2>
        </div>
    </a>

    <!-- Menu Section -->
    <div class="sidebar-menu">
        <!-- Dashboard -->
        <div class="menu-item">
            <a href="index.php" class="menu-link <?php if(isset($Page_Name) && $Page_Name=="Dashboard") echo 'active'; ?>">
                <i class="fas fa-home"></i>
                <span><?php echo __t('dashboard', 'Dashboard'); ?></span>
            </a>
        </div>

        <!-- Tools -->
        <div class="menu-item">
            <a href="#" class="menu-link has-submenu" data-submenu="tools">
                <i class="fas fa-tools"></i>
                <span><?php echo __t('tools', 'Tools'); ?></span>
                <i class="fas fa-chevron-down"></i>
            </a>
            <div class="submenu" id="tools-submenu">
                <?php if ($isLogged): ?>
                    <a href="favorite_posts.php" class="submenu-item <?php if(isset($Page_Name) && $Page_Name=="Favorite") echo 'active'; ?>">
                        <i class="fas fa-star"></i>
                        <span><?php echo __t('favorite', 'Favorite'); ?></span>
                    </a>
                <?php else: ?>
                    <a href="#" class="submenu-item locked" title="<?php echo __t('favorite_locked', 'Please log in to your account to unlock this'); ?>">
                        <i class="fas fa-star"></i>
                        <span><?php echo __t('favorite', 'Favorite'); ?></span>
                        <i class="fas fa-lock"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($isLogged && $user_type === 'Vehicle Owner'): ?>
        <!-- Tools For Vehicle Owners -->
        <div class="menu-item">
            <a href="#" class="menu-link has-submenu" data-submenu="vehicle-tools">
                <i class="fas fa-car"></i>
                <span><?php echo __t('vehicle_tools', 'Tools For Vehicle Owners'); ?></span>
                <i class="fas fa-chevron-down"></i>
            </a>
            <div class="submenu" id="vehicle-tools-submenu">
                <a href="add_and_edit_post.php" class="submenu-item <?php if(isset($Page_Name) && $Page_Name=="Add/Edit Post") echo 'active'; ?>">
                    <i class="fas fa-plus-circle"></i>
                    <span><?php echo __t('add_edit_post', 'Add/Edit Post'); ?></span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($isLogged && $user_type === 'admin'): ?>
        <!-- Tools For Admin -->
        <div class="menu-item">
            <a href="#" class="menu-link has-submenu" data-submenu="admin-tools">
                <i class="fas fa-user-shield"></i>
                <span><?php echo __t('admin_tools', 'Tools For Admin'); ?></span>
                <i class="fas fa-chevron-down"></i>
            </a>
            <div class="submenu" id="admin-tools-submenu">
                <a href="add_and_edit_post.php" class="submenu-item <?php if(isset($Page_Name) && $Page_Name=="Add/Edit Post") echo 'active'; ?>">
                    <i class="fas fa-plus-circle"></i>
                    <span><?php echo __t('add_edit_post', 'Add/Edit Post'); ?></span>
                </a>
                <a href="create_users_admin_side_frontend.php" class="submenu-item <?php if(isset($Page_Name) && $Page_Name=="Create Users") echo 'active'; ?>">
                    <i class="fas fa-plus-circle"></i>
                    <span><?php echo __t('create_users', 'Create Users'); ?></span>
                </a>
                <a href="user_management.php" class="submenu-item <?php if(isset($Page_Name) && $Page_Name=="Users Management") echo 'active'; ?>">
                    <i class="fas fa-users"></i>
                    <span><?php echo __t('manage_users', 'Manage Users'); ?></span>
                </a>
                <a href="user_posts.php" class="submenu-item <?php if(isset($Page_Name) && $Page_Name=="User Posts Management") echo 'active'; ?>">
                    <i class="fas fa-file-alt"></i>
                    <span><?php echo __t('user_posts', 'User Posts Management'); ?></span>
                </a>
                <a href="contact_us_and_about_us_backend.php" class="submenu-item <?php if(isset($Page_Name) && $Page_Name=="About Us and Contact Us Page Management") echo 'active'; ?>">
                    <i class="fas fa-file-alt"></i>
                    <span><?php echo __t('about_contact_mgmt', 'About Us and Contact Us Page Management'); ?></span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Contact Us / About Us -->
        <div class="menu-item">
            <a href="contact_us_and_about_us_front_end.php" class="menu-link <?php if(isset($Page_Name) && $Page_Name=="About & Contact") echo 'active'; ?>">
                <i class="fas fa-address-book"></i>
                <span><?php echo __t('about_contact', 'About Us / Contact Us'); ?></span>
            </a>
        </div>
    </div><!-- /sidebar-menu -->

    <!-- User Section -->
    <div class="sidebar-footer">
        <div class="user-profile" id="userProfile">
            <img src="<?php echo $avatar_url; ?>" alt="User" class="user-avatar">
            <div class="user-info">
                <p class="user-name" id="userName"><?php echo htmlspecialchars($username); ?></p>
                <p class="user-status" id="userStatus"><?php echo __t('online', 'Online'); ?></p>
            </div>
            <i class="fas fa-chevron-down user-dropdown-icon"></i>
        </div>
        
        <div class="user-dropdown" id="userDropdown">
            <!-- Logged In Menu -->
            <div id="loggedInMenu">
                <a href="user_settings.php" class="user-dropdown-item <?php if(isset($Page_Name) && $Page_Name=="User Settings") echo 'active'; ?>">
                    <i class="fas fa-sliders-h"></i>
                    <span><?php echo __t('settings', 'Settings'); ?></span>
                </a>
                <a href="edit_user_user_side_frontend.php" class="user-dropdown-item <?php if(isset($Page_Name) && $Page_Name=="Edit Profile") echo 'active'; ?>">
                    <i class="fas fa-user-edit"></i>
                    <span><?php echo __t('edit_profile', 'Edit Profile'); ?></span>
                </a>
                <a href="edit_user_password.php" class="user-dropdown-item <?php if(isset($Page_Name) && $Page_Name=="Change Password") echo 'active'; ?>">
                    <i class="fas fa-key"></i>
                    <span><?php echo __t('change_password', 'Change Password'); ?></span>
                </a>
                <a href="delete_account_frontend_user_side.php" class="user-dropdown-item delete_account <?php if(isset($Page_Name) && $Page_Name=="Delete Account") echo 'active'; ?>">
                    <i class="fas fa-user-times"></i>
                    <span><?php echo __t('delete_account', 'Delete Account'); ?></span>
                </a>
                <a href="log_out.php" id="logoutBtn" class="user-dropdown-item logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span><?php echo __t('logout', 'LogOut'); ?></span>
                </a>
            </div>

            <!-- Guest Menu -->
            <div id="guestMenu" style="display:none;">
                <p style="padding:10px; font-size:14px; color:red;">
                    <?php echo __t('guest_message', 'Please log into your account or register to unlock more features....'); ?>
                </p>
                <a href="user_settings.php" class="user-dropdown-item <?php if(isset($Page_Name) && $Page_Name=="User Settings") echo 'active'; ?>">
                    <i class="fas fa-sliders-h"></i>
                    <span><?php echo __t('settings', 'Settings'); ?></span>
                </a>
                <a href="change_password_not_logged_in.php" class="user-dropdown-item <?php if(isset($Page_Name) && $Page_Name=="Change Password (No Login Required)") echo 'active'; ?>">
                    <i class="fas fa-key"></i>
                    <span><?php echo __t('change_password_no_login', 'Change Password'); ?></span>
                </a>
                <a href="Sign_up.php" class="user-dropdown-item <?php if(isset($Page_Name) && $Page_Name=="Sign Up") echo 'active'; ?>">
                    <i class="fas fa-user-plus"></i>
                    <span><?php echo __t('sign_up', 'Sign Up (Register)'); ?></span>
                </a>
                <a href="log_in.php" class="user-dropdown-item <?php if(isset($Page_Name) && $Page_Name=="Log In") echo 'active'; ?>">
                    <i class="fas fa-sign-in-alt"></i>
                    <span><?php echo __t('log_in', 'Log In'); ?></span>
                </a>
            </div>
        </div>
    </div>
</div><!-- /right-sidebar -->

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
// PHP translations to JS (make sure $translations is already set)
window.__sidebarTrans = <?php echo json_encode($translations); ?>;
</script>

<script>
// Logout confirmation
document.getElementById('logoutBtn')?.addEventListener('click', function(e) {
    e.preventDefault();
    
    // Get translated strings from global object
    const t = window.__sidebarTrans || {};
    
    Swal.fire({
        title: t.logout_confirm_title || 'Are you sure?',
        text: t.logout_confirm_text || 'You will be logged out!',
        icon: 'warning',
        background: '#1e1e1e',
        color: '#ffffff',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: t.yes_logout || 'Yes, log me out!',
        cancelButtonText: t.cancel || 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = this.href;
        }
    });
});

// Cookie helper
function getCookie(name) {
    let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) return match[2];
    return null;
}

// Update user menu based on cookie
let abcCookie = getCookie("abc");
let cookieData = null;
if (abcCookie) {
    try {
        cookieData = JSON.parse(decodeURIComponent(abcCookie));
    } catch(e) {
        cookieData = null;
    }
}
let loggedInMenu = document.getElementById("loggedInMenu");
let guestMenu = document.getElementById("guestMenu");
let userStatus = document.getElementById("userStatus");

if (!cookieData || cookieData.islogged !== 1) {
    loggedInMenu.style.display = "none";
    guestMenu.style.display = "block";
    if (userStatus) userStatus.innerText = "Offline";
} else {
    loggedInMenu.style.display = "block";
    guestMenu.style.display = "none";
    if (userStatus) userStatus.innerText = "Online";
}

// Sidebar & Toggle Elements
const sidebar = document.getElementById('rightSidebar');
const desktopToggle = document.getElementById('desktopToggle');
const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');

// --- Auto-hide/show toggle button based on mouse movement ---
let hideToggleTimer;

function showToggleTemporarily() {
    if (!sidebar.classList.contains('active')) {
        desktopToggle.classList.remove('hidden');
        clearTimeout(hideToggleTimer);
        hideToggleTimer = setTimeout(() => {
            desktopToggle.classList.add('hidden');
        }, 2500); // hide after 2.5s of inactivity
    }
}

// Initial state: hidden
desktopToggle.classList.add('hidden');

document.addEventListener('mousemove', function(e) {
    showToggleTemporarily();
});

desktopToggle.addEventListener('mouseenter', function() {
    clearTimeout(hideToggleTimer);
});
desktopToggle.addEventListener('mouseleave', function() {
    if (!sidebar.classList.contains('active')) {
        hideToggleTimer = setTimeout(() => {
            desktopToggle.classList.add('hidden');
        }, 1000);
    }
});

// Toggle click handlers
desktopToggle.addEventListener('click', function() {
    sidebar.classList.add('active');
    desktopToggle.classList.add('hidden');
    clearTimeout(hideToggleTimer);
});

sidebarCloseBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    sidebar.classList.remove('active');
    desktopToggle.classList.remove('hidden');
    // Start timer to hide again after movement
    showToggleTemporarily();
});

// Close sidebar when clicking outside (works on all screen sizes)
document.addEventListener('click', function(e) {
    if (!sidebar.contains(e.target) && !desktopToggle.contains(e.target)) {
        if (sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            desktopToggle.classList.remove('hidden');
            showToggleTemporarily(); // restart the auto-hide
        }
    }
});

// Submenu Toggle
const menuLinks = document.querySelectorAll('.menu-link.has-submenu');
menuLinks.forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const submenuId = this.getAttribute('data-submenu') + '-submenu';
        const submenu = document.getElementById(submenuId);
        
        document.querySelectorAll('.submenu').forEach(sub => {
            if (sub.id !== submenuId) {
                sub.classList.remove('show');
            }
        });
        document.querySelectorAll('.menu-link.has-submenu').forEach(l => {
            if (l !== this) {
                l.classList.remove('active');
            }
        });
        
        submenu.classList.toggle('show');
        this.classList.toggle('active');
    });
});

// User Dropdown Toggle
const userProfile = document.getElementById('userProfile');
const userDropdown = document.getElementById('userDropdown');

if (userProfile) {
    userProfile.addEventListener('click', function(e) {
        e.stopPropagation();
        userDropdown.classList.toggle('show');
        userProfile.classList.toggle('active');
    });
}

// On page load: expand parent menus / dropdowns if an active item exists
document.addEventListener('DOMContentLoaded', function() {
    // Reset all submenus
    document.querySelectorAll('.submenu').forEach(sub => sub.classList.remove('show'));
    document.querySelectorAll('.menu-link.has-submenu').forEach(link => link.classList.remove('active'));

    // If a submenu-item is active, open its parent submenu and highlight parent link
    const activeSubItem = document.querySelector('.submenu-item.active');
    if (activeSubItem) {
        const parentSubmenu = activeSubItem.closest('.submenu');
        if (parentSubmenu) {
            parentSubmenu.classList.add('show');
            const submenuId = parentSubmenu.id;
            if (submenuId) {
                const parentMenuId = submenuId.replace(/-submenu$/, '');
                const parentLink = document.querySelector(`.menu-link[data-submenu="${parentMenuId}"]`);
                if (parentLink) {
                    parentLink.classList.add('active');
                }
            }
        }
    }

    // If any user-dropdown-item is active, expand the user dropdown
    if (document.querySelector('.user-dropdown-item.active')) {
        userDropdown.classList.add('show');
        userProfile.classList.add('active');
    }
});

// When window resizes, ensure toggle button shows appropriately
window.addEventListener('resize', function() {
    if (sidebar.classList.contains('active')) {
        // keep sidebar open; toggle stays hidden
    } else {
        showToggleTemporarily();
    }
});
</script>