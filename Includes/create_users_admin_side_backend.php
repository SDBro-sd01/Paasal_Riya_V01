<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include "db_connection.php";
include "audit_helper.php"; // ✅ added

// ✅ Get logged admin ID from cookie
$admin_id = 0;
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
    $admin_id = isset($cookie_data['user_id']) ? intval($cookie_data['user_id']) : 0;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Required fields
    $required = ['username', 'fullname', 'mobile', 'email', 'nic', 'district', 'province', 'address', 'user_type'];
    $missing = [];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        $_SESSION['create_user_error'] = "Please fill all required fields: " . implode(', ', $missing);
        header("Location: create_users_admin_side_frontend.php");
        exit();
    }

    // Sanitize input
    $username  = trim($_POST['username']);
    $fullname  = trim($_POST['fullname']);
    $mobile    = trim($_POST['mobile']);
    $email     = trim($_POST['email']);
    $nic       = trim($_POST['nic']);
    $district  = trim($_POST['district']);
    $province  = trim($_POST['province']);
    $address   = trim($_POST['address']);
    $user_type = trim($_POST['user_type']);

    // Check for duplicate entries (username, email, nic, mobile)
    $duplicate_error = '';

    // Check username
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $duplicate_error .= "Username already exists. ";
    }
    $stmt->close();

    // Check email
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $duplicate_error .= "Email already exists. ";
    }
    $stmt->close();

    // Check NIC
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE nic = ?");
    $stmt->bind_param("s", $nic);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $duplicate_error .= "NIC already exists. ";
    }
    $stmt->close();

    // Check mobile
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE mobile = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $duplicate_error .= "Mobile already exists. ";
    }
    $stmt->close();

    // If any duplicate found, redirect with error
    if (!empty($duplicate_error)) {
        $_SESSION['create_user_error'] = trim($duplicate_error);
        header("Location: create_users_admin_side_frontend.php");
        exit();
    }

    // Hash the fixed temporary password
    $password_hashed = password_hash("Temppass123", PASSWORD_BCRYPT);

    // Insert into users table
    $insert_user = $conn->prepare("INSERT INTO users (username, fullname, mobile, email, nic, district, province, address, user_type, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert_user->bind_param("ssssssssss", $username, $fullname, $mobile, $email, $nic, $district, $province, $address, $user_type, $password_hashed);

    if ($insert_user->execute()) {
        $new_user_id = $conn->insert_id;

        // Insert into user_created_method with method = 'Custom'
        $insert_method = $conn->prepare("INSERT INTO user_created_method (user_id, method) VALUES (?, 'Custom')");
        $insert_method->bind_param("i", $new_user_id);
        $insert_method->execute();
        $insert_method->close();

        // ✅ AUDIT LOG – admin creates user
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
        insertAuditLog($conn, 'users', $new_user_id, 'INSERT', null, $newData, $admin_id);

        $_SESSION['create_user_success'] = "User created successfully! Temporary password is: Temppass123";
    } else {
        $_SESSION['create_user_error'] = "Database error: " . $insert_user->error;
    }

    $insert_user->close();

    // Redirect to avoid form resubmission
    header("Location: create_users_admin_side_frontend.php");
    exit();
}
?>