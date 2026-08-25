<?php
session_start();
include "Includes/db_connection.php";
include "Cookie_Managements/cookie_management.php";

$message = '';
$type = 'success';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // Get username before deleting
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
    
    // Delete user
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        if (!empty($username)) {
            $message = "User '$username' deleted successfully.";
        } else {
            $message = "User ID $user_id deleted successfully.";
        }
    } else {
        $message = "Error deleting user: " . $conn->error;
        $type = 'error';
    }
    $stmt->close();
} else {
    $message = "Invalid user ID.";
    $type = 'error';
}

// Store message in session
$_SESSION['flash_message'] = $message;
$_SESSION['flash_type'] = $type;

// Redirect back to user management page with same filters and page
$redirect = "user_management.php?page=" . urlencode($_GET['page'] ?? 1) .
            "&search=" . urlencode($_GET['search'] ?? '') .
            "&user_type=" . urlencode($_GET['user_type'] ?? '');
header("Location: $redirect");
exit;
?>