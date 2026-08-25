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
    echo json_encode(['success' => false, 'message' => 'Please login to rate']);
    exit;
}

$service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

if ($service_id <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid rating or service']);
    exit;
}

// Check if service exists
$checkService = $conn->prepare("SELECT service_id FROM services WHERE service_id = ?");
$checkService->bind_param("i", $service_id);
$checkService->execute();
$checkService->store_result();
if ($checkService->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Service not found']);
    exit;
}
$checkService->close();

// Insert or update (ON DUPLICATE KEY UPDATE)
$sql = "INSERT INTO ratings (user_id, service_id, rating) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE rating = VALUES(rating), updated_at = CURRENT_TIMESTAMP";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $service_id, $rating);

if ($stmt->execute()) {
    // Fetch updated average & total for this service
    $avgQuery = "SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM ratings WHERE service_id = ?";
    $avgStmt = $conn->prepare($avgQuery);
    $avgStmt->bind_param("i", $service_id);
    $avgStmt->execute();
    $avgRes = $avgStmt->get_result();
    $stats = $avgRes->fetch_assoc();
    echo json_encode([
        'success' => true,
        'avg_rating' => round($stats['avg_rating'], 1),
        'total_ratings' => (int)$stats['total'],
        'user_rating' => $rating
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
$conn->close();
?>