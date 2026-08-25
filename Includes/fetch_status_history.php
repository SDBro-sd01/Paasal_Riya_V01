<?php
header('Content-Type: application/json');
include "db_connection.php";

$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;
if ($service_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$query = "SELECT 
            psl.log_id, psl.action, psl.changed_at,
            u.user_id AS admin_id, u.username, u.fullname
          FROM post_status_log psl
          JOIN users u ON psl.admin_id = u.user_id
          WHERE psl.service_id = ?
          ORDER BY psl.changed_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}
echo json_encode(['success' => true, 'history' => $history]);
$conn->close();