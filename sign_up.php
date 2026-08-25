<?php
session_start();

include "Includes/db_connection.php";
include "Cookie_Managements/cookie_management.php";

$Page_Name = "Sign Up";

// Retrieve old input and field errors from session (if any)
$old_input = $_SESSION['old_input'] ?? [];
$form_errors = $_SESSION['form_errors'] ?? [];

// Clear them so they don't show again on refresh
unset($_SESSION['old_input'], $_SESSION['form_errors']);

$Session_Messages_Helper = [
    "add_user_success" => [
        "class" => "session-success",
        "icon"  => "fa-check-circle"
    ],
    "add_user_caution" => [
        "class" => "session-caution",
        "icon"  => "fa-exclamation-triangle"
    ],
    "add_user_error" => [
        "class" => "session-error",
        "icon"  => "fa-times-circle"
    ],

    // Custom examples
    "sample01" => [
        "custom_color" => "rgb(0, 229, 255)",
        "icon" => "fa-info-circle"
    ],

    "sample02" => [
        "custom_color" => "#5a05f7",
        "icon" => "fa-bell"
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/logo.png" sizes="32x32">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet CSS for map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body {
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .session-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 5px solid #28a745;
            font-size: 14px;
            font-weight: 500;
        }
        
        .signup-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 1200px;
            width: 90%;
            height: 100%;
            backdrop-filter: blur(10px);
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .signup-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 35px 30px;
            text-align: center;
            color: white;
        }
        
        .signup-logo-container {
            margin-bottom: 15px;
        }
        
        .signup-logo-container img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }
        
        .brand-name {
            font-size: 26px;
            font-weight: 700;
            margin: 12px 0 8px 0;
            letter-spacing: 1px;
        }
        
        .signup-header h2 {
            font-size: 20px;
            font-weight: 500;
            margin: 0;
            opacity: 0.95;
        }
        
        .signup-body {
            padding: 40px 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-label {
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        
        .form-label i {
            margin-right: 8px;
            color: #667eea;
            width: 18px;
        }
        
        .input-group {
            position: relative;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }
        
        /* Error state */
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .invalid-feedback {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        
        .password-field {
            position: relative;
        }
        
        .password-field .form-control {
            padding-right: 45px;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            transition: color 0.3s ease;
            background: none;
            border: none;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .password-toggle:hover {
            color: #667eea;
        }
        
        .password-match {
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
        
        .match-success {
            color: #00c851;
        }
        
        .match-error {
            color: #ff4444;
        }
        
        .btn-signup {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-signup:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        
        .btn-signup:active {
            transform: translateY(0);
        }
        
        .signup-footer {
            text-align: center;
            padding: 20px 30px 30px;
            font-size: 14px;
            color: #666;
        }
        
        .signup-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .signup-footer a:hover {
            color: #764ba2;
        }
        
        .session-message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 500;
            border-left: 5px solid;
        }

        /* Success - Green */
        .session-success {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }

        /* Caution - Yellow */
        .session-caution {
            background: #fff3cd;
            color: #856404;
            border-color: #ffc107;
        }

        /* Error - Red */
        .session-error {
            background: #f8d7da;
            color: #721c24;
            border-color: #dc3545;
        }

        #map {
            width: 100%;
            height: 400px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        @media (max-width: 768px) {
            #map {
                height: 300px;
            }
        }
        
        @media (max-width: 576px) {
            .signup-header {
                padding: 30px 20px;
            }
            
            .logo-container img {
                width: 70px;
                height: 70px;
            }
            
            .brand-name {
                font-size: 22px;
            }
            
            .signup-header h2 {
                font-size: 18px;
            }
            
            .signup-body {
                padding: 30px 20px;
            }
        }
    </style>

    <title>Paasal Riya - Sign Up</title>
</head>
<body>
<?php include "side_bar.php"; ?>

<div class="signup-container">
    <div class="signup-header">
        <div class="signup-logo-container">
            <img src="Assets/logo.png" alt="Paasal Riya Logo">
        </div>
        <div class="brand-name">Paasal Riya</div>
        <h2>Create Account</h2>
    </div>
    
    <div class="signup-body">

    <?php
    // Display session messages (general success/caution/error)
    foreach ($Session_Messages_Helper as $session_key => $settings) {
        if (isset($_SESSION[$session_key])) {
            $message = $_SESSION[$session_key];
            $class = $settings["class"] ?? "";
            $icon  = $settings["icon"] ?? "fa-info-circle";

            $style = "";
            if (isset($settings["custom_color"])) {
                $style = "background: {$settings['custom_color']}20;
                          border-left: 5px solid {$settings['custom_color']};
                          color: {$settings['custom_color']};";
            }

            echo "<div class='session-message {$class}' style='{$style}'>
                    <i class='fas {$icon}'></i> {$message}
                  </div>";

            unset($_SESSION[$session_key]);
        }
    }
    ?>
        
        <form id="signupForm" action="Includes/sign_up_inc.php" method="post">

            <!-- Username -->
            <div class="form-group">
                <label class="form-label" for="username">
                    <i class="fas fa-user"></i> Username
                </label>
                <input 
                    type="text" 
                    class="form-control <?php echo isset($form_errors['username']) ? 'is-invalid' : ''; ?>" 
                    id="username" 
                    name="username"
                    placeholder="Enter your username"
                    value="<?php echo htmlspecialchars($old_input['username'] ?? ''); ?>"
                    required
                >
                <?php if (isset($form_errors['username'])): ?>
                    <div class="invalid-feedback"><?php echo $form_errors['username']; ?></div>
                <?php endif; ?>
            </div>

            <!-- Full Name -->
            <div class="form-group">
                <label class="form-label" for="fullname">
                    <i class="fas fa-id-card"></i> Full Name
                </label>
                <input 
                    type="text" 
                    class="form-control <?php echo isset($form_errors['fullname']) ? 'is-invalid' : ''; ?>" 
                    id="fullname" 
                    name="fullname"
                    placeholder="Enter your full name"
                    value="<?php echo htmlspecialchars($old_input['fullname'] ?? ''); ?>"
                    required
                >
                <?php if (isset($form_errors['fullname'])): ?>
                    <div class="invalid-feedback"><?php echo $form_errors['fullname']; ?></div>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label class="form-label" for="email">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <input 
                    type="email" 
                    class="form-control <?php echo isset($form_errors['email']) ? 'is-invalid' : ''; ?>" 
                    id="email" 
                    name="email"
                    placeholder="Enter your email"
                    value="<?php echo htmlspecialchars($old_input['email'] ?? ''); ?>"
                    required
                >
                <?php if (isset($form_errors['email'])): ?>
                    <div class="invalid-feedback"><?php echo $form_errors['email']; ?></div>
                <?php endif; ?>
            </div>

            <!-- Mobile Number -->
            <div class="form-group">
                <label class="form-label" for="mobile">
                    <i class="fas fa-phone"></i> Mobile Number
                </label>
                <input 
                    type="tel" 
                    class="form-control <?php echo isset($form_errors['mobile']) ? 'is-invalid' : ''; ?>" 
                    id="mobile" 
                    name="mobile"
                    placeholder="Enter your mobile number"
                    value="<?php echo htmlspecialchars($old_input['mobile'] ?? ''); ?>"
                    required
                >
                <?php if (isset($form_errors['mobile'])): ?>
                    <div class="invalid-feedback"><?php echo $form_errors['mobile']; ?></div>
                <?php endif; ?>
            </div>

            <!-- NIC -->
            <div class="form-group">
                <label class="form-label" for="nic">
                    <i class="fas fa-id-card"></i> NIC
                </label>
                <input 
                    type="text" 
                    class="form-control <?php echo isset($form_errors['nic']) ? 'is-invalid' : ''; ?>" 
                    id="nic" 
                    name="nic"
                    placeholder="Enter your NIC"
                    value="<?php echo htmlspecialchars($old_input['nic'] ?? ''); ?>"
                    required
                >
                <?php if (isset($form_errors['nic'])): ?>
                    <div class="invalid-feedback"><?php echo $form_errors['nic']; ?></div>
                <?php endif; ?>
            </div>

            <!-- District Map Section -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-map-marked-alt"></i> Select Your District
                </label>
                <div id="map" style="height: 400px; border-radius: 8px; margin-bottom: 10px;"></div>
                <div id="selectedDistrictDisplay" style="font-size: 14px; color: #555; margin-top: 5px;">
                    <?php if (!empty($old_input['district'])): ?>
                        <i class="fas fa-check-circle" style="color:#28a745;"></i> Selected: <?php echo $old_input['district']; ?> (<?php echo $old_input['province'] ?? ''; ?> Province)
                    <?php else: ?>
                        <i class="fas fa-info-circle"></i> Click a district on the map.
                    <?php endif; ?>
                </div>
                <!-- Hidden fields to submit district and province -->
                <input type="hidden" name="district" id="selectedDistrict" value="<?php echo htmlspecialchars($old_input['district'] ?? ''); ?>">
                <input type="hidden" name="province" id="selectedProvince" value="<?php echo htmlspecialchars($old_input['province'] ?? ''); ?>">
                <?php if (isset($form_errors['district'])): ?>
                    <div class="invalid-feedback d-block"><?php echo $form_errors['district']; ?></div>
                <?php endif; ?>
            </div>

            <!-- Address -->
            <div class="form-group">
                <label class="form-label" for="address">
                    <i class="fas fa-map-marker-alt"></i> Address
                </label>
                <input 
                    type="text" 
                    class="form-control <?php echo isset($form_errors['address']) ? 'is-invalid' : ''; ?>" 
                    id="address" 
                    name="address"
                    placeholder="Enter your address"
                    value="<?php echo htmlspecialchars($old_input['address'] ?? ''); ?>"
                    required
                >
                <?php if (isset($form_errors['address'])): ?>
                    <div class="invalid-feedback"><?php echo $form_errors['address']; ?></div>
                <?php endif; ?>
            </div>

            <!-- User Type -->
            <div class="form-group">
                <label class="form-label" for="user_type">
                    <i class="fas fa-users"></i> Who Are You? (User Type)
                </label>
                <select 
                    class="form-control <?php echo isset($form_errors['user_type']) ? 'is-invalid' : ''; ?>" 
                    id="user_type" 
                    name="user_type" 
                    required
                >
                    <option value="" <?php echo !isset($old_input['user_type']) ? 'selected' : ''; ?> disabled>-- Select User Type --</option>
                    <option value="Parents" <?php echo ($old_input['user_type'] ?? '') == 'Parents' ? 'selected' : ''; ?>>Parents</option>
                    <option value="Vehicle Owner" <?php echo ($old_input['user_type'] ?? '') == 'Vehicle Owner' ? 'selected' : ''; ?>>Vehicle Owner</option>
                </select>
                <?php if (isset($form_errors['user_type'])): ?>
                    <div class="invalid-feedback"><?php echo $form_errors['user_type']; ?></div>
                <?php endif; ?>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">
                    <i class="fas fa-lock"></i> Password
                </label>
                <div class="password-field">
                    <input 
                        type="password" 
                        class="form-control <?php echo isset($form_errors['password']) ? 'is-invalid' : ''; ?>" 
                        id="password" 
                        name="password"
                        placeholder="Enter your password"
                        required
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <?php if (isset($form_errors['password'])): ?>
                    <div class="invalid-feedback"><?php echo $form_errors['password']; ?></div>
                <?php endif; ?>
                <!-- Password strength bar removed -->
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label class="form-label" for="confirmPassword">
                    <i class="fas fa-lock"></i> Confirm Password
                </label>
                <div class="password-field">
                    <input 
                        type="password" 
                        class="form-control <?php echo isset($form_errors['confirmPassword']) ? 'is-invalid' : ''; ?>" 
                        id="confirmPassword" 
                        name="confirmPassword"
                        placeholder="Confirm your password"
                        required
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <?php if (isset($form_errors['confirmPassword'])): ?>
                    <div class="invalid-feedback"><?php echo $form_errors['confirmPassword']; ?></div>
                <?php endif; ?>
                <div class="password-match" id="passwordMatch"></div>
            </div>

            <button type="submit" class="btn-signup" name="btn-signup">
                <i class="fas fa-user-check"></i> Sign Up
            </button>
        </form>
    </div>
    
    <div class="signup-footer">
        Already have an account? <a href="log_in.php">Log In</a>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Toggle Password Visibility
    function togglePassword(fieldId, button) {
        const field = document.getElementById(fieldId);
        const icon = button.querySelector('i');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Password Strength Indicator removed

    // Password Match Indicator
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const passwordInput = document.getElementById('password');
    const passwordMatch = document.getElementById('passwordMatch');

    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        if (confirmPassword.length === 0) {
            passwordMatch.style.display = 'none';
            return;
        }

        passwordMatch.style.display = 'block';

        if (password === confirmPassword) {
            passwordMatch.className = 'password-match match-success';
            passwordMatch.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
        } else {
            passwordMatch.className = 'password-match match-error';
            passwordMatch.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match';
        }
    }

    confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    // ========== MAP INTEGRATION ==========
    // Pass old district value to JavaScript (if any)
    var oldDistrict = "<?php echo addslashes($old_input['district'] ?? ''); ?>";

    // Initialize map centered on Sri Lanka with zoom and drag disabled
    var map = L.map('map', {
        center: [7.8731, 80.7718],
        zoom: 7,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        boxZoom: false,
        touchZoom: false,
        zoomControl: false,
        dragging: false
    });

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // GeoJSON data for Sri Lanka districts (simplified)
    var districtsGeoJSON = {
        "type": "FeatureCollection",
        "features": [
            { "type": "Feature", "properties": { "name": "Colombo", "province": "Western" }, "geometry": { "type": "Polygon", "coordinates": [[[79.85,6.95],[79.91,7.05],[80.0,7.02],[79.95,6.88],[79.85,6.95]]] }},
            { "type": "Feature", "properties": { "name": "Gampaha", "province": "Western" }, "geometry": { "type": "Polygon", "coordinates": [[[79.95,7.15],[80.05,7.25],[80.15,7.15],[80.05,7.05],[79.95,7.15]]] }},
            { "type": "Feature", "properties": { "name": "Kalutara", "province": "Western" }, "geometry": { "type": "Polygon", "coordinates": [[[79.95,6.55],[80.05,6.65],[80.15,6.55],[80.05,6.45],[79.95,6.55]]] }},
            { "type": "Feature", "properties": { "name": "Kandy", "province": "Central" }, "geometry": { "type": "Polygon", "coordinates": [[[80.55,7.25],[80.65,7.35],[80.75,7.25],[80.65,7.15],[80.55,7.25]]] }},
            { "type": "Feature", "properties": { "name": "Matale", "province": "Central" }, "geometry": { "type": "Polygon", "coordinates": [[[80.55,7.45],[80.65,7.55],[80.75,7.45],[80.65,7.35],[80.55,7.45]]] }},
            { "type": "Feature", "properties": { "name": "Nuwara Eliya", "province": "Central" }, "geometry": { "type": "Polygon", "coordinates": [[[80.75,6.95],[80.85,7.05],[80.95,6.95],[80.85,6.85],[80.75,6.95]]] }},
            { "type": "Feature", "properties": { "name": "Galle", "province": "Southern" }, "geometry": { "type": "Polygon", "coordinates": [[[80.15,6.05],[80.25,6.15],[80.35,6.05],[80.25,5.95],[80.15,6.05]]] }},
            { "type": "Feature", "properties": { "name": "Matara", "province": "Southern" }, "geometry": { "type": "Polygon", "coordinates": [[[80.45,5.95],[80.55,6.05],[80.65,5.95],[80.55,5.85],[80.45,5.95]]] }},
            { "type": "Feature", "properties": { "name": "Hambantota", "province": "Southern" }, "geometry": { "type": "Polygon", "coordinates": [[[80.95,6.15],[81.05,6.25],[81.15,6.15],[81.05,6.05],[80.95,6.15]]] }},
            { "type": "Feature", "properties": { "name": "Jaffna", "province": "Northern" }, "geometry": { "type": "Polygon", "coordinates": [[[80.0,9.65],[80.1,9.75],[80.2,9.65],[80.1,9.55],[80.0,9.65]]] }},
            { "type": "Feature", "properties": { "name": "Kilinochchi", "province": "Northern" }, "geometry": { "type": "Polygon", "coordinates": [[[80.1,9.35],[80.2,9.45],[80.3,9.35],[80.2,9.25],[80.1,9.35]]] }},
            { "type": "Feature", "properties": { "name": "Mannar", "province": "Northern" }, "geometry": { "type": "Polygon", "coordinates": [[[79.85,8.95],[79.95,9.05],[80.05,8.95],[79.95,8.85],[79.85,8.95]]] }},
            { "type": "Feature", "properties": { "name": "Vavuniya", "province": "Northern" }, "geometry": { "type": "Polygon", "coordinates": [[[80.4,8.65],[80.5,8.75],[80.6,8.65],[80.5,8.55],[80.4,8.65]]] }},
            { "type": "Feature", "properties": { "name": "Mullaitivu", "province": "Northern" }, "geometry": { "type": "Polygon", "coordinates": [[[80.4,9.15],[80.5,9.25],[80.6,9.15],[80.5,9.05],[80.4,9.15]]] }},
            { "type": "Feature", "properties": { "name": "Batticaloa", "province": "Eastern" }, "geometry": { "type": "Polygon", "coordinates": [[[81.55,7.65],[81.65,7.75],[81.75,7.65],[81.65,7.55],[81.55,7.65]]] }},
            { "type": "Feature", "properties": { "name": "Ampara", "province": "Eastern" }, "geometry": { "type": "Polygon", "coordinates": [[[81.55,7.15],[81.65,7.25],[81.75,7.15],[81.65,7.05],[81.55,7.15]]] }},
            { "type": "Feature", "properties": { "name": "Trincomalee", "province": "Eastern" }, "geometry": { "type": "Polygon", "coordinates": [[[81.15,8.45],[81.25,8.55],[81.35,8.45],[81.25,8.35],[81.15,8.45]]] }},
            { "type": "Feature", "properties": { "name": "Kurunegala", "province": "North Western" }, "geometry": { "type": "Polygon", "coordinates": [[[80.25,7.65],[80.35,7.75],[80.45,7.65],[80.35,7.55],[80.25,7.65]]] }},
            { "type": "Feature", "properties": { "name": "Puttalam", "province": "North Western" }, "geometry": { "type": "Polygon", "coordinates": [[[79.85,8.05],[79.95,8.15],[80.05,8.05],[79.95,7.95],[79.85,8.05]]] }},
            { "type": "Feature", "properties": { "name": "Anuradhapura", "province": "North Central" }, "geometry": { "type": "Polygon", "coordinates": [[[80.35,8.25],[80.45,8.35],[80.55,8.25],[80.45,8.15],[80.35,8.25]]] }},
            { "type": "Feature", "properties": { "name": "Polonnaruwa", "province": "North Central" }, "geometry": { "type": "Polygon", "coordinates": [[[80.95,7.85],[81.05,7.95],[81.15,7.85],[81.05,7.75],[80.95,7.85]]] }},
            { "type": "Feature", "properties": { "name": "Badulla", "province": "Uva" }, "geometry": { "type": "Polygon", "coordinates": [[[80.95,6.85],[81.05,6.95],[81.15,6.85],[81.05,6.75],[80.95,6.85]]] }},
            { "type": "Feature", "properties": { "name": "Moneragala", "province": "Uva" }, "geometry": { "type": "Polygon", "coordinates": [[[81.25,6.65],[81.35,6.75],[81.45,6.65],[81.35,6.55],[81.25,6.65]]] }},
            { "type": "Feature", "properties": { "name": "Ratnapura", "province": "Sabaragamuwa" }, "geometry": { "type": "Polygon", "coordinates": [[[80.35,6.65],[80.45,6.75],[80.55,6.65],[80.45,6.55],[80.35,6.65]]] }},
            { "type": "Feature", "properties": { "name": "Kegalle", "province": "Sabaragamuwa" }, "geometry": { "type": "Polygon", "coordinates": [[[80.25,7.15],[80.35,7.25],[80.45,7.15],[80.35,7.05],[80.25,7.15]]] }}
        ]
    };

    // Variable to store the currently selected layer (for highlight removal)
    var selectedLayer = null;

    // Style for districts (default)
    function getDistrictStyle() {
        return {
            fillColor: '#3388ff',
            weight: 2,
            opacity: 1,
            color: 'white',
            fillOpacity: 0.5
        };
    }

    // Style for highlighted district
    function getHighlightStyle() {
        return {
            fillColor: '#ff7800',
            weight: 3,
            opacity: 1,
            color: '#ff7800',
            fillOpacity: 0.7
        };
    }

    // Add GeoJSON layer to map
    var geojsonLayer = L.geoJSON(districtsGeoJSON, {
        style: getDistrictStyle,
        onEachFeature: function(feature, layer) {
            // Bind click event
            layer.on('click', function(e) {
                // Reset previous highlight
                if (selectedLayer) {
                    geojsonLayer.resetStyle(selectedLayer);
                }
                
                // Highlight new selection
                layer.setStyle(getHighlightStyle());
                selectedLayer = layer;
                
                // Get district and province from properties
                var districtName = feature.properties.name;
                var provinceName = feature.properties.province;
                
                // Set hidden inputs
                document.getElementById('selectedDistrict').value = districtName;
                document.getElementById('selectedProvince').value = provinceName;
                
                // Update display message
                document.getElementById('selectedDistrictDisplay').innerHTML = 
                    '<i class="fas fa-check-circle" style="color:#28a745;"></i> Selected: ' + 
                    districtName + ' (' + provinceName + ' Province)';
            });
            
            // Optional: add tooltip on hover
            layer.bindTooltip(feature.properties.name, { sticky: true });
        }
    }).addTo(map);

    // Fit map to district boundaries
    map.fitBounds(geojsonLayer.getBounds());

    // If there is an old district, highlight it
    if (oldDistrict) {
        geojsonLayer.eachLayer(function(layer) {
            if (layer.feature.properties.name === oldDistrict) {
                // Simulate a click to trigger the same logic
                layer.fire('click');
            }
        });
    }
</script>

</body>
</html>