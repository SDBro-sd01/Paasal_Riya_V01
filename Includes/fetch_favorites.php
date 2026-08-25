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
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Always show only approved posts in favorites (no admin exception)
$sql = "SELECT s.*, 
        (SELECT COUNT(*) FROM comments WHERE service_id = s.service_id) as comments_count
        FROM services s 
        INNER JOIN favorites f ON s.service_id = f.service_id 
        WHERE f.user_id = ? AND s.status = 'approved'
        ORDER BY f.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$services = [];
while ($row = $result->fetch_assoc()) {
    $service_id = $row['service_id'];
    $row['schools'] = fetchMulti($conn, $service_id, 'service_schools', 'school_name');
    $row['images'] = getServiceImages($conn, $service_id);
    
    // Ratings summary
    $ratingQuery = "SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM ratings WHERE service_id = ?";
    $rStmt = $conn->prepare($ratingQuery);
    $rStmt->bind_param("i", $service_id);
    $rStmt->execute();
    $ratingData = $rStmt->get_result()->fetch_assoc();
    $row['avg_rating'] = $ratingData['avg_rating'] ? round($ratingData['avg_rating'], 1) : 0;
    $row['total_ratings'] = (int)$ratingData['total'];
    
    // User's own rating
    $row['user_rating'] = null;
    $userRatingQuery = "SELECT rating FROM ratings WHERE user_id = ? AND service_id = ?";
    $urStmt = $conn->prepare($userRatingQuery);
    $urStmt->bind_param("ii", $user_id, $service_id);
    $urStmt->execute();
    $urRes = $urStmt->get_result();
    if ($ur = $urRes->fetch_assoc()) $row['user_rating'] = $ur['rating'];
    
    $services[] = $row;
}
echo json_encode(['success' => true, 'services' => $services]);

// Helper functions (unchanged)
function fetchMulti($conn, $service_id, $table, $column) {
    $data = [];
    $stmt = $conn->prepare("SELECT $column FROM $table WHERE service_id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $data[] = $row[$column];
    return $data;
}
function getServiceImages($conn, $service_id) {
    $stmt = $conn->prepare("SELECT image_id, image_path, is_mandatory FROM service_images WHERE service_id = ? ORDER BY is_mandatory ASC, display_order ASC");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $images = [];
    while ($row = $res->fetch_assoc()) $images[] = $row;
    return $images;
}
?>