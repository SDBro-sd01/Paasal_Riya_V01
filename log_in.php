<?php

session_start();

include "Includes/db_connection.php";
include "Cookie_Managements/cookie_management.php";

$Page_Name = "Log In";

$Session_Messages_Helper = [
    "login_error" => [
        "class" => "session-error",
        "icon"  => "fa-times-circle"
    ],
    "login_success" => [
        "class" => "session-success",
        "icon"  => "fa-check-circle"
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

    <style>
        body {
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: #f5f5f5;
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
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 500px;
            width: 90%;
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
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 35px 30px;
            text-align: center;
            color: white;
        }
        
        .login-logo-container {
            margin-bottom: 15px;
        }
        
        .login-logo-container img {
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
        
        .login-header h2 {
            font-size: 20px;
            font-weight: 500;
            margin: 0;
            opacity: 0.95;
        }
        
        .login-body {
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
        
        .btn-login {
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
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .login-footer {
            text-align: center;
            padding: 20px 30px 30px;
            font-size: 14px;
            color: #666;
        }
        
        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .login-footer a:hover {
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

        .session-success {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }

        .session-caution {
            background: #fff3cd;
            color: #856404;
            border-color: #ffc107;
        }

        .session-error {
            background: #f8d7da;
            color: #721c24;
            border-color: #dc3545;
        }

        .extra-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .extra-links a {
            color: #667eea;
            text-decoration: none;
        }

        .extra-links a:hover {
            text-decoration: underline;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
        }

        .remember-me input {
            width: 16px;
            height: 16px;
            accent-color: #667eea;
        }
        
        @media (max-width: 576px) {
            .login-header {
                padding: 30px 20px;
            }
            
            .logo-container img {
                width: 70px;
                height: 70px;
            }
            
            .brand-name {
                font-size: 22px;
            }
            
            .login-header h2 {
                font-size: 18px;
            }
            
            .login-body {
                padding: 30px 20px;
            }
        }
    </style>

    <title>Paasal Riya - Log In</title>
</head>
<body>
    <?php include "side_bar.php";?>

    <div class="login-container">
        <div class="login-header">
            <div class="login-logo-container">
                <img src="Assets/logo.png" alt="Paasal Riya Logo">
            </div>
            <div class="brand-name">Paasal Riya</div>
            <h2>Log In</h2>
        </div>
        
        <div class="login-body">

            <?php
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
            
            <form id="loginForm" action="Includes/log_in_inc.php" method="post">

                <div class="form-group">
                    <label class="form-label" for="login_identifier">
                        <i class="fas fa-user"></i>
                        Username / Email / Mobile / NIC
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="login_identifier" 
                        name="login_identifier"
                        placeholder="Enter your username, email, mobile or NIC"
                        required
                        autofocus
                    >
                </div>

               
                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="fas fa-lock"></i>
                        Password
                    </label>
                    <div class="password-field">
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password"
                            placeholder="Enter your password"
                            required
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                
                <!-- <div class="extra-links">
                    <label class="remember-me">
                        <input type="checkbox" name="remember_me"> Remember Me
                    </label>
                    <a href="forgot_password.php">Forgot Password?</a>
                </div> -->
                
                <button type="submit" class="btn-login" name="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Log In
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            Don't have an account? <a href="sign_up.php">Sign Up</a>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // මුරපදය පෙන්වීම/සැඟවීම
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
    </script>
</body>
</html>