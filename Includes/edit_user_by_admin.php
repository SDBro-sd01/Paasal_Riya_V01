<?php
session_start();
include "db_connection.php";   // adjust path if needed
include "audit_helper.php"; // ✅ added

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $user_id = (int)$_POST['user_id'];
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $nic = trim($_POST['nic']);
    $district = trim($_POST['district']);
    $province = trim($_POST['province']);
    $address = trim($_POST['address']);
    $user_type = trim($_POST['user_type']);

    // Filter parameters to preserve after redirect
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $user_type_filter = isset($_POST['user_type_filter']) ? $_POST['user_type_filter'] : '';
    $creation_method_filter = isset($_POST['creation_method']) ? $_POST['creation_method'] : '';

    $errors = [];

    // 1. Required field check
    $required = [
        'username' => $username,
        'fullname' => $fullname,
        'mobile'   => $mobile,
        'email'    => $email,
        'nic'      => $nic,
        'district' => $district,
        'province' => $province,
        'address'  => $address,
        'user_type'=> $user_type
    ];
    foreach ($required as $field => $value) {
        if (empty($value) && $value !== '0') {
            $errors[] = ucfirst($field) . " is required.";
        }
    }

    // 2. Email format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // 3. Duplicate checks (exclude current user)
    if (empty($errors)) {
        // Check username
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
        $stmt->bind_param("si", $username, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Username already taken.";
        }
        $stmt->close();

        // Check email
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email already in use.";
        }
        $stmt->close();

        // Check NIC
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE nic = ? AND user_id != ?");
        $stmt->bind_param("si", $nic, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "NIC already registered.";
        }
        $stmt->close();

        // Check mobile
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE mobile = ? AND user_id != ?");
        $stmt->bind_param("si", $mobile, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Mobile number already in use.";
        }
        $stmt->close();
    }

    // If errors, store in session and redirect back to modal
    if (!empty($errors)) {
        $_SESSION['edit_errors'] = $errors;
        $_SESSION['edit_old_input'] = [
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

        // Build redirect URL (edit modal open again)
        $redirect = "../user_management.php?edit_modal=1&edit_user_id=$user_id";
        if ($page > 1) $redirect .= "&page=$page";
        if (!empty($search)) $redirect .= "&search=" . urlencode($search);
        if (!empty($user_type_filter)) $redirect .= "&user_type=" . urlencode($user_type_filter);
        if (!empty($creation_method_filter)) $redirect .= "&creation_method=" . urlencode($creation_method_filter);

        header("Location: $redirect");
        exit();
    }

    // ✅ Fetch OLD data for audit before update
    $old_stmt = $conn->prepare("SELECT username, fullname, mobile, email, nic, district, province, address, user_type FROM users WHERE user_id = ?");
    $old_stmt->bind_param("i", $user_id);
    $old_stmt->execute();
    $oldResult = $old_stmt->get_result();
    $oldData = $oldResult->fetch_assoc();
    $old_stmt->close();

    // 4. Update user data
    $stmt = $conn->prepare("UPDATE users SET username=?, fullname=?, mobile=?, email=?, nic=?, district=?, province=?, address=?, user_type=? WHERE user_id=?");
    $stmt->bind_param("sssssssssi", $username, $fullname, $mobile, $email, $nic, $district, $province, $address, $user_type, $user_id);

    if ($stmt->execute()) {
        // ✅ AUDIT LOG – admin edit user
        // Get admin ID from cookie
        $admin_id = 0;
        if (isset($_COOKIE['abc'])) {
            $cookie_data = json_decode($_COOKIE['abc'], true);
            $admin_id = isset($cookie_data['user_id']) ? intval($cookie_data['user_id']) : 0;
        }

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
        insertAuditLog($conn, 'users', $user_id, 'UPDATE', $oldData, $newData, $admin_id);

        // Success message
        $_SESSION['edit_user_success'] = "User updated successfully.";
    } else {
        $_SESSION['edit_user_error'] = "Database error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();

    // Build clean redirect URL (no modal parameters)
    $redirect = "../user_management.php";
    $queryParams = [];
    if ($page > 1) $queryParams['page'] = $page;
    if (!empty($search)) $queryParams['search'] = $search;
    if (!empty($user_type_filter)) $queryParams['user_type'] = $user_type_filter;
    if (!empty($creation_method_filter)) $queryParams['creation_method'] = $creation_method_filter;

    if (!empty($queryParams)) {
        $redirect .= '?' . http_build_query($queryParams);
    }

    header("Location: $redirect");
    exit();
}
?>