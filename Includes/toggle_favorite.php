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
    echo json_encode(['success' => false, 'message' => 'Please login to favorite']);
    exit;
}

$service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
if (!$service_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid service']);
    exit;
}

// Check if already favorited
$check = $conn->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND service_id = ?");
$check->bind_param("ii", $user_id, $service_id);
$check->execute();
$check->store_result();
$exists = $check->num_rows > 0;
$check->close();

if ($exists) {
    // Remove favorite
    $del = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND service_id = ?");
    $del->bind_param("ii", $user_id, $service_id);
    $del->execute();
    echo json_encode(['success' => true, 'action' => 'removed']);
} else {
    // Add favorite
    $ins = $conn->prepare("INSERT INTO favorites (user_id, service_id) VALUES (?, ?)");
    $ins->bind_param("ii", $user_id, $service_id);
    $ins->execute();
    echo json_encode(['success' => true, 'action' => 'added']);
}
$conn->close();
?>