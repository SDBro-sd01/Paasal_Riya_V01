<?php
session_start();
header('Content-Type: application/json');

include "db_connection.php";

$cookie_data = [];
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
}
$user_id = isset($cookie_data['user_id']) ? intval($cookie_data['user_id']) : 0;
$isLogged = isset($cookie_data['islogged']) && $cookie_data['islogged'] == 1;

if (!$isLogged || $user_id == 0) {
    echo json_encode(['success' => false, 'message' => 'Please login to manage comments']);
    exit;
}

$action = $_POST['action'] ?? '';
$service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

if (!$service_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid service']);
    exit;
}

switch ($action) {
    case 'add':
        if (empty($comment)) {
            echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
            exit;
        }
        $parent_id = isset($_POST['parent_comment_id']) ? intval($_POST['parent_comment_id']) : 0;

        // 🔥 Only verify that parent exists and belongs to same service
        if ($parent_id > 0) {
            $pCheck = $conn->prepare("SELECT comment_id FROM comments WHERE comment_id = ? AND service_id = ?");
            $pCheck->bind_param("ii", $parent_id, $service_id);
            $pCheck->execute();
            $pCheck->store_result();
            if ($pCheck->num_rows == 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid parent comment']);
                exit;
            }
            $pCheck->close();
        }

        $stmt = $conn->prepare("INSERT INTO comments (user_id, service_id, comment, parent_comment_id) VALUES (?, ?, ?, ?)");
        $nullParam = $parent_id > 0 ? $parent_id : null;
        $stmt->bind_param("iisi", $user_id, $service_id, $comment, $nullParam);
        if ($stmt->execute()) {
            $new_id = $conn->insert_id;
            $userQuery = $conn->prepare("SELECT username, fullname FROM users WHERE user_id = ?");
            $userQuery->bind_param("i", $user_id);
            $userQuery->execute();
            $user = $userQuery->get_result()->fetch_assoc();
            echo json_encode([
                'success' => true,
                'comment' => [
                    'comment_id'   => $new_id,
                    'user_id'      => $user_id,
                    'username'     => $user['username'],
                    'fullname'     => $user['fullname'],
                    'comment'      => $comment,
                    'parent_comment_id' => $parent_id > 0 ? $parent_id : null,
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                    'can_edit_delete' => true,
                    'can_reply'    => true,
                    'is_edited'    => false
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
        break;

    case 'edit':
        $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
        if (!$comment_id || empty($comment)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit;
        }
        $check = $conn->prepare("SELECT user_id FROM comments WHERE comment_id = ?");
        $check->bind_param("i", $comment_id);
        $check->execute();
        $check->bind_result($owner_id);
        $check->fetch();
        $check->close();
        if ($owner_id != $user_id) {
            echo json_encode(['success' => false, 'message' => 'You can only edit your own comments']);
            exit;
        }
        $update = $conn->prepare("UPDATE comments SET comment = ?, updated_at = NOW() WHERE comment_id = ?");
        $update->bind_param("si", $comment, $comment_id);
        if ($update->execute()) {
            echo json_encode(['success' => true, 'comment' => $comment]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed']);
        }
        break;

    case 'delete':
        $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
        if (!$comment_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid comment']);
            exit;
        }
        $check = $conn->prepare("SELECT user_id FROM comments WHERE comment_id = ?");
        $check->bind_param("i", $comment_id);
        $check->execute();
        $check->bind_result($owner_id);
        $check->fetch();
        $check->close();
        if ($owner_id != $user_id) {
            echo json_encode(['success' => false, 'message' => 'You can only delete your own comments']);
            exit;
        }
        $delete = $conn->prepare("DELETE FROM comments WHERE comment_id = ?");
        $delete->bind_param("i", $comment_id);
        if ($delete->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Delete failed']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>