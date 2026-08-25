<?php
session_start();
header('Content-Type: application/json');
include "db_connection.php";

$cookie_data = json_decode($_COOKIE['abc'] ?? '{}', true);
$user_id = intval($cookie_data['user_id'] ?? 0);
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

if ($action === 'fetch') {
    $post_id = intval($_GET['post_id'] ?? 0);
    
    // Check if user is post owner or admin
    $postOwner = $conn->prepare("SELECT user_id FROM services WHERE service_id = ?");
    $postOwner->bind_param("i", $post_id);
    $postOwner->execute();
    $ownerRow = $postOwner->get_result()->fetch_assoc();
    if (!$ownerRow) {
        echo json_encode(['success'=>false,'message'=>'Post not found']);
        exit;
    }
    
    $isAdmin = false;
    $adminCheck = $conn->prepare("SELECT user_type FROM users WHERE user_id = ?");
    $adminCheck->bind_param("i", $user_id);
    $adminCheck->execute();
    $adminRes = $adminCheck->get_result();
    if ($adminRes->num_rows > 0 && $adminRes->fetch_assoc()['user_type'] === 'admin') {
        $isAdmin = true;
    }
    
    if ($user_id != $ownerRow['user_id'] && !$isAdmin) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
    
    $sql = "SELECT pm.*, u.fullname as sender_name 
            FROM post_messages pm 
            JOIN users u ON pm.sender_id = u.user_id 
            WHERE pm.post_id = ? 
            ORDER BY pm.created_at ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = [];
    while ($row = $result->fetch_assoc()) $messages[] = $row;
    
    echo json_encode(['success' => true, 'messages' => $messages]);

} elseif ($action === 'send') {
    $post_id = intval($_POST['post_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    if (!$message) {
        echo json_encode(['success'=>false,'message'=>'Empty message']);
        exit;
    }
    
    // Get post owner
    $post = $conn->prepare("SELECT user_id FROM services WHERE service_id = ?");
    $post->bind_param("i", $post_id);
    $post->execute();
    $postRes = $post->get_result()->fetch_assoc();
    if (!$postRes) {
        echo json_encode(['success'=>false,'message'=>'Post not found']);
        exit;
    }
    $owner_id = $postRes['user_id'];
    
    // Determine if sender is admin
    $isAdmin = false;
    $adminCheck = $conn->prepare("SELECT user_type FROM users WHERE user_id = ?");
    $adminCheck->bind_param("i", $user_id);
    $adminCheck->execute();
    if ($adminCheck->get_result()->fetch_assoc()['user_type'] === 'admin') {
        $isAdmin = true;
    }
    
    $receiver_id = 0;
    if ($user_id == $owner_id) {
        // Sender is post owner, receiver = admin (pick first admin)
        $adminQuery = $conn->query("SELECT user_id FROM users WHERE user_type = 'admin' LIMIT 1");
        $adminRow = $adminQuery->fetch_assoc();
        $receiver_id = $adminRow ? $adminRow['user_id'] : 0;
    } elseif ($isAdmin) {
        // Sender is admin, receiver = post owner
        $receiver_id = $owner_id;
    } else {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    if ($receiver_id == 0) {
        echo json_encode(['success' => false, 'message' => 'No admin available']);
        exit;
    }
    
    $stmt = $conn->prepare("INSERT INTO post_messages (post_id, sender_id, receiver_id, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $post_id, $user_id, $receiver_id, $message);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'DB Error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>