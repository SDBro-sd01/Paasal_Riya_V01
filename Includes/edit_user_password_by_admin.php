<?php
session_start();
include "db_connection.php";
include "Cookie_Managements/cookie_management.php";
include "audit_helper.php"; // ✅ added

$message = '';
$is_success = true; // track success/failure

// ✅ Get logged admin ID from cookie (used for audit)
$admin_id = 0;
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
    $admin_id = isset($cookie_data['user_id']) ? intval($cookie_data['user_id']) : 0;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // Reset password to Temppass123 (hashed)
    $temp_password = "Temppass123";
    $hashed_password = password_hash($temp_password, PASSWORD_BCRYPT);
    
    // First get username for the message
    $user_sql = "SELECT username FROM users WHERE user_id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $username = '';
    if ($user_row = $user_result->fetch_assoc()) {
        $username = $user_row['username'];
    }
    $user_stmt->close();
    
    $sql = "UPDATE users SET password = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashed_password, $user_id);
    
    if ($stmt->execute()) {
        if (!empty($username)) {
            $message = "Password for user '$username' has been reset to Temppass123.";
        } else {
            $message = "Password for user ID $user_id has been reset to Temppass123.";
        }

        // ✅ AUDIT LOG – password reset by admin
        $oldData = ['password' => '********'];
        $newData = ['password' => '********', 'note' => 'Reset by admin to Temppass123'];
        insertAuditLog($conn, 'users', $user_id, 'UPDATE', $oldData, $newData, $admin_id);
    } else {
        $message = "Error resetting password: " . $conn->error;
        $is_success = false;
    }
    $stmt->close();
} else {
    $message = "Invalid user ID.";
    $is_success = false;
}

// Use session keys that match user_management.php's Session_Messages_Helper
if ($is_success) {
    $_SESSION['edit_user_success'] = $message;
} else {
    $_SESSION['edit_user_error'] = $message;
}

// Redirect back to user management page with same filters and page
$redirect = "../user_management.php?page=" . urlencode($_GET['page'] ?? 1) .
            "&search=" . urlencode($_GET['search'] ?? '') .
            "&user_type=" . urlencode($_GET['user_type'] ?? '');
header("Location: $redirect");
exit;
?>