<?php
session_start();
include "db_connection.php";
include "sri_lanka_provinces_districts.php";
include "audit_helper.php"; // ✅ added

// --- Cookie Login Check ---
$cookie_data = [];
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
}
$isLogged = isset($cookie_data['islogged']) && $cookie_data['islogged'] == 1;
$logged_user_id = isset($cookie_data['user_id']) ? intval($cookie_data['user_id']) : 0;

if (!$isLogged || $logged_user_id === 0) {
    $_SESSION['error'] = "You are not logged in.";
    header("Location: ../login.php");
    exit();
}

// --- POST request check ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../edit_user_user_side_frontend.php");
    exit();
}

// --- Verify user identity ---
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
if ($user_id !== $logged_user_id) {
    $_SESSION['error'] = "Invalid user identity.";
    header("Location: ../edit_user_user_side_frontend.php");
    exit();
}

// --- Fetch current user_type from database ---
$stmt = $conn->prepare("SELECT user_type FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_user_type);
$stmt->fetch();
$stmt->close();

// --- Input sanitization ---
$username   = trim($_POST['username']);
$fullname   = trim($_POST['fullname']);
$mobile     = trim($_POST['mobile']);
$email      = trim($_POST['email']);
$nic        = trim($_POST['nic']);
$province   = trim($_POST['province']);
$district   = trim($_POST['district']);
$address    = trim($_POST['address']);
$user_type  = trim($_POST['user_type']);

$errors = [];

// --- Basic validations ---
if (empty($username))   $errors[] = "Username is required.";
if (empty($fullname))   $errors[] = "Full name is required.";
if (empty($mobile))     $errors[] = "Mobile number is required.";
if (empty($email))      $errors[] = "Email is required.";
if (empty($nic))        $errors[] = "NIC is required.";
if (empty($address))    $errors[] = "Address is required.";
if (empty($province) || empty($district)) $errors[] = "Province and District are required.";

// Email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

// Mobile (Sri Lankan 10 digits starting with 0)
if (!preg_match('/^0[1-9][0-9]{8}$/', $mobile)) {
    $errors[] = "Invalid mobile number. Should be 10 digits starting with 0.";
}

// NIC (old 9 digits + V/X or new 12 digits)
if (!preg_match('/^[0-9]{9}[vVxX]|[0-9]{12}$/', $nic)) {
    $errors[] = "Invalid NIC format.";
}

// Province & district validation
if (!array_key_exists($province, $sl_provinces)) {
    $errors[] = "Invalid province selected.";
} else {
    if (!in_array($district, $sl_provinces[$province])) {
        $errors[] = "Invalid district for the selected province.";
    }
}

// --- User type validation (dynamic allowed list) ---
if ($current_user_type === 'admin') {
    $allowed_user_types = ['admin', 'Parents', 'Vehicle Owner'];
} else {
    $allowed_user_types = ['Parents', 'Vehicle Owner'];
}

if (!in_array($user_type, $allowed_user_types)) {
    $errors[] = "Invalid user type selected.";
}

// Prevent non-admin from becoming admin
if ($user_type === 'admin' && $current_user_type !== 'admin') {
    $errors[] = "You cannot change your user type to Admin. This option is not available.";
}

if (!empty($errors)) {
    $_SESSION['error'] = $errors;
    header("Location: ../edit_user_user_side_frontend.php");
    exit();
}

// --- Duplicate checks (excluding current user) ---
// Username
$stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
$stmt->bind_param("si", $username, $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $errors[] = "Username already taken by another user.";
}
$stmt->close();

// Email
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
$stmt->bind_param("si", $email, $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $errors[] = "Email already registered to another user.";
}
$stmt->close();

// NIC
$stmt = $conn->prepare("SELECT user_id FROM users WHERE nic = ? AND user_id != ?");
$stmt->bind_param("si", $nic, $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $errors[] = "NIC already used by another user.";
}
$stmt->close();

// Mobile
$stmt = $conn->prepare("SELECT user_id FROM users WHERE mobile = ? AND user_id != ?");
$stmt->bind_param("si", $mobile, $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $errors[] = "Mobile number already used by another user.";
}
$stmt->close();

if (!empty($errors)) {
    $_SESSION['error'] = $errors;
    header("Location: ../edit_user_user_side_frontend.php");
    exit();
}

// --- Fetch OLD data for audit ---
$old_stmt = $conn->prepare("SELECT username, fullname, mobile, email, nic, province, district, address, user_type FROM users WHERE user_id = ?");
$old_stmt->bind_param("i", $user_id);
$old_stmt->execute();
$oldResult = $old_stmt->get_result();
$oldData = $oldResult->fetch_assoc();
$old_stmt->close();

// --- Update user (password excluded) ---
$update_stmt = $conn->prepare("UPDATE users SET 
    username = ?, 
    fullname = ?, 
    mobile = ?, 
    email = ?, 
    nic = ?, 
    province = ?, 
    district = ?, 
    address = ?, 
    user_type = ? 
    WHERE user_id = ?");

$update_stmt->bind_param("sssssssssi", 
    $username, $fullname, $mobile, $email, $nic, 
    $province, $district, $address, $user_type, 
    $user_id
);

if ($update_stmt->execute()) {
    // ✅ AUDIT LOG – self update
    $newData = [
        'username'  => $username,
        'fullname'  => $fullname,
        'mobile'    => $mobile,
        'email'     => $email,
        'nic'       => $nic,
        'province'  => $province,
        'district'  => $district,
        'address'   => $address,
        'user_type' => $user_type,
    ];
    insertAuditLog($conn, 'users', $user_id, 'UPDATE', $oldData, $newData, $logged_user_id);

    $_SESSION['success'] = "Profile updated successfully!";
} else {
    $_SESSION['error'] = "Database update failed. Please try again.";
}

$update_stmt->close();
$conn->close();

header("Location: ../edit_user_user_side_frontend.php");
exit();
?>