<?php
session_start();

include "db_connection.php";
include "audit_helper.php"; // ✅ added

if(isset($_POST["btn-signup"])){

    // Retrieve all fields
    $username = trim($_POST["username"]);
    $fullname = trim($_POST["fullname"]);
    $mobile = trim($_POST["mobile"]);
    $email = trim($_POST["email"]);
    $nic = trim($_POST["nic"]);
    $district = trim($_POST['district']);
    $province = trim($_POST['province']);
    $address = trim($_POST['address']);
    $user_type = trim($_POST["user_type"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];

    // Store old input (except passwords) in session
    $_SESSION['old_input'] = [
        'username'   => $username,
        'fullname'   => $fullname,
        'mobile'     => $mobile,
        'email'      => $email,
        'nic'        => $nic,
        'district'   => $district,
        'province'   => $province,
        'address'    => $address,
        'user_type'  => $user_type
    ];

    $errors = []; // Field-specific errors

    // 1️⃣ Check empty fields
    $required_fields = [
        'username'   => 'Username',
        'fullname'   => 'Full name',
        'mobile'     => 'Mobile number',
        'email'      => 'Email address',
        'nic'        => 'NIC',
        'district'   => 'District',
        'province'   => 'Province',
        'address'    => 'Address',
        'user_type'  => 'User type',
        'password'   => 'Password',
        'confirmPassword' => 'Confirm password'
    ];

    foreach ($required_fields as $field => $label) {
        // For password fields, check the direct $_POST because they are not in old_input
        if ($field == 'password' || $field == 'confirmPassword') {
            if (empty($_POST[$field])) {
                $errors[$field] = "$label is required.";
            }
        } else {
            if (empty(${$field}) && ${$field} !== '0') {
                $errors[$field] = "$label is required.";
            }
        }
    }

    // If any required field is empty, set errors and redirect (no generic caution)
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        header("Location: ../sign_up.php");
        exit();
    }

    // 2️⃣ Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    // 3️⃣ Check password match
    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = "Passwords do not match.";
    }

    // 4️⃣ If no basic validation errors, proceed with database checks
    if (empty($errors)) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Check if email, username or NIC already exists - individually
        $dupErrors = [];

        // Check email
        $stmtEmail = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmtEmail->bind_param("s", $email);
        $stmtEmail->execute();
        $stmtEmail->store_result();
        if ($stmtEmail->num_rows > 0) {
            $dupErrors['email'] = "Email already registered.";
        }
        $stmtEmail->close();

        // Check username
        $stmtUser = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmtUser->bind_param("s", $username);
        $stmtUser->execute();
        $stmtUser->store_result();
        if ($stmtUser->num_rows > 0) {
            $dupErrors['username'] = "Username already taken.";
        }
        $stmtUser->close();

        // Check NIC
        $stmtNic = $conn->prepare("SELECT user_id FROM users WHERE nic = ?");
        $stmtNic->bind_param("s", $nic);
        $stmtNic->execute();
        $stmtNic->store_result();
        if ($stmtNic->num_rows > 0) {
            $dupErrors['nic'] = "NIC already registered.";
        }
        $stmtNic->close();

        // If any duplicate found
        if (!empty($dupErrors)) {
            $_SESSION['form_errors'] = array_merge($errors, $dupErrors);
            $_SESSION["add_user_caution"] = "Some fields are already registered.";
            header("Location: ../sign_up.php");
            exit();
        }

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users 
            (username, fullname, mobile, email, nic, district, province, address, user_type, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "ssssssssss",
            $username,
            $fullname,
            $mobile,
            $email,
            $nic,
            $district,
            $province,
            $address,
            $user_type,
            $hashedPassword
        );

        if ($stmt->execute()) {
            // ✅ User create වුණා. දැන් user_created_method table එකට record එකක් දාමු
            $newUserId = $conn->insert_id;  // අලුතින් හැදුන user ගේ ID එක

            // method = 'Normal' ලෙස insert කරන්න
            $methodStmt = $conn->prepare("INSERT INTO user_created_method (user_id, method) VALUES (?, 'Normal')");
            $methodStmt->bind_param("i", $newUserId);
            $methodStmt->execute();
            $methodStmt->close();

            // ✅ AUDIT LOG – insert new user
            $newData = [
                'username'  => $username,
                'fullname'  => $fullname,
                'mobile'    => $mobile,
                'email'     => $email,
                'nic'       => $nic,
                'district'  => $district,
                'province'  => $province,
                'address'   => $address,
                'user_type' => $user_type,
            ];
            insertAuditLog($conn, 'users', $newUserId, 'INSERT', null, $newData, $newUserId);

            // Clear old input on success
            unset($_SESSION['old_input']);
            unset($_SESSION['form_errors']);
            $_SESSION["add_user_success"] = "Account created successfully!";
            header("Location: ../sign_up.php");
            exit();
        } else {
            // Database error - general message
            $_SESSION["add_user_error"] = "Something went wrong. Please try again.";
            header("Location: ../sign_up.php");
            exit();
        }
        $stmt->close();
        $conn->close();
    }

    // If we reach here, there were validation errors (email, password, etc.)
    $_SESSION['form_errors'] = $errors;
    header("Location: ../sign_up.php");
    exit();
}
?>