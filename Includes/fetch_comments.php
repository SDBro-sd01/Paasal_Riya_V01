<?php
session_start();
header('Content-Type: application/json');

include "db_connection.php";

$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;
if (!$service_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid service']);
    exit;
}

// Get logged-in user from cookie
$cookie_data = [];
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
}
$current_user_id = isset($cookie_data['user_id']) ? intval($cookie_data['user_id']) : 0;
$current_user_username = null;

// Fetch current user's username from DB if logged in
if ($current_user_id > 0) {
    $userStmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
    $userStmt->bind_param("i", $current_user_id);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    if ($userRow = $userResult->fetch_assoc()) {
        $current_user_username = $userRow['username'];
    }
    $userStmt->close();
}

$sql = "SELECT c.comment_id, c.user_id, c.comment, c.created_at, c.updated_at, c.parent_comment_id,
               u.username, u.fullname,
               s.user_id AS service_owner_id
        FROM comments c
        JOIN users u ON c.user_id = u.user_id
        JOIN services s ON c.service_id = s.service_id AND s.service_id = ?
        WHERE c.service_id = ?
        ORDER BY c.created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $service_id, $service_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $row['can_edit_delete'] = ($current_user_id > 0 && $current_user_id == $row['user_id']);
    // Any logged user can reply (Facebook style)
    $row['can_reply'] = ($current_user_id > 0);
    $row['is_edited'] = ($row['updated_at'] != $row['created_at']);
    $comments[] = $row;
}

echo json_encode([
    'success' => true,
    'comments' => $comments,
    'current_user_username' => $current_user_username,
    'current_user_id' => $current_user_id
]);
?>