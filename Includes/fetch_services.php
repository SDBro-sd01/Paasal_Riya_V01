<?php
session_start();
header('Content-Type: application/json');

include "db_connection.php";

$cookie_data = [];
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
}
$current_user_id = isset($cookie_data['user_id']) ? intval($cookie_data['user_id']) : 0;
$isLogged = isset($cookie_data['islogged']) && $cookie_data['islogged'] == 1;

// Check if current user is admin
$isAdmin = false;
if ($isLogged && $current_user_id > 0) {
    $adminCheck = $conn->prepare("SELECT user_type FROM users WHERE user_id = ?");
    $adminCheck->bind_param("i", $current_user_id);
    $adminCheck->execute();
    $adminRes = $adminCheck->get_result();
    if ($adminRes->num_rows > 0 && $adminRes->fetch_assoc()['user_type'] === 'admin') {
        $isAdmin = true;
    }
}

// If single service_id requested (for view details)
if (isset($_GET['service_id'])) {
    $service_id = intval($_GET['service_id']);
    $sql = "SELECT * FROM services WHERE service_id = ?";
    if (!$isAdmin) {
        $sql .= " AND status = 'approved'";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $row['schools'] = fetchMulti($conn, $service_id, 'service_schools', 'school_name');
        $row['assistants'] = fetchMulti($conn, $service_id, 'service_assistants', 'assistant_name');
        $row['telephones'] = fetchMulti($conn, $service_id, 'service_telephones', 'telephone');
        $row['emails'] = fetchMulti($conn, $service_id, 'service_emails', 'email');
        $row['websites'] = fetchMulti($conn, $service_id, 'service_websites', 'website');
        $row['images'] = getServiceImages($conn, $service_id);

        $sch = $conn->prepare("SELECT label, place, time FROM service_schedules WHERE service_id = ? ORDER BY sort_order");
        $sch->bind_param("i", $service_id);
        $sch->execute();
        $schRes = $sch->get_result();
        $schedules = [];
        while ($s = $schRes->fetch_assoc()) {
            $schedules[] = $s;
        }
        $row['schedules'] = $schedules;

        $docImg = $conn->prepare("SELECT id, image_path FROM service_document_images WHERE service_id = ?");
        $docImg->bind_param("i", $service_id);
        $docImg->execute();
        $docRes = $docImg->get_result();
        $docImages = [];
        while ($d = $docRes->fetch_assoc()) {
            $docImages[] = $d;
        }
        $row['document_images'] = $docImages;

        echo json_encode(['success' => true, 'services' => [$row]]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Not found']);
    }
    $conn->close();
    exit;
}

// Otherwise fetch list with filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$province = isset($_GET['province']) ? trim($_GET['province']) : '';
$district = isset($_GET['district']) ? trim($_GET['district']) : '';
$service_type = isset($_GET['service_type']) ? trim($_GET['service_type']) : ''; // NEW

$sql = "SELECT s.*, 
        (SELECT GROUP_CONCAT(school_name SEPARATOR ' ') FROM service_schools WHERE service_id = s.service_id) as all_schools,
        (SELECT COUNT(*) FROM comments WHERE service_id = s.service_id) as comments_count
        FROM services s WHERE s.status = 'approved'";
$params = [];
$types = "";

if (!empty($province)) {
    $sql .= " AND s.province = ?";
    $params[] = $province;
    $types .= "s";
}
if (!empty($district)) {
    $sql .= " AND s.district = ?";
    $params[] = $district;
    $types .= "s";
}
// NEW: service_type filter
if (!empty($service_type)) {
    $sql .= " AND s.service_type = ?";
    $params[] = $service_type;
    $types .= "s";
}
if (!empty($search)) {
    $escapedSearch = addcslashes($search, '%_');
    $sql .= " AND (s.service_name LIKE ? OR 
                    (SELECT GROUP_CONCAT(school_name SEPARATOR ' ') FROM service_schools WHERE service_id = s.service_id) LIKE ? OR
                    s.areas_covered LIKE ?)";
    $like = "%$escapedSearch%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}
$sql .= " ORDER BY s.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$services = [];
while ($row = $result->fetch_assoc()) {
    $service_id = $row['service_id'];
    $row['schools'] = fetchMulti($conn, $service_id, 'service_schools', 'school_name');
    $row['images'] = getServiceImages($conn, $service_id);
    
    $ratingQuery = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM ratings WHERE service_id = ?";
    $ratingStmt = $conn->prepare($ratingQuery);
    $ratingStmt->bind_param("i", $service_id);
    $ratingStmt->execute();
    $ratingRes = $ratingStmt->get_result();
    $ratingData = $ratingRes->fetch_assoc();
    $row['avg_rating'] = $ratingData['avg_rating'] ? round($ratingData['avg_rating'], 1) : 0;
    $row['total_ratings'] = (int)$ratingData['total_ratings'];
    
    $row['user_rating'] = null;
    if ($isLogged && $current_user_id > 0) {
        $userRatingQuery = "SELECT rating FROM ratings WHERE user_id = ? AND service_id = ?";
        $uStmt = $conn->prepare($userRatingQuery);
        $uStmt->bind_param("ii", $current_user_id, $service_id);
        $uStmt->execute();
        $uRes = $uStmt->get_result();
        if ($uRow = $uRes->fetch_assoc()) $row['user_rating'] = (int)$uRow['rating'];
    }
    
    $row['is_favorited'] = false;
    if ($isLogged && $current_user_id > 0) {
        $favCheck = $conn->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND service_id = ?");
        $favCheck->bind_param("ii", $current_user_id, $service_id);
        $favCheck->execute();
        $favCheck->store_result();
        if ($favCheck->num_rows > 0) $row['is_favorited'] = true;
        $favCheck->close();
    }
    $services[] = $row;
}

echo json_encode(['success' => true, 'services' => $services]);
$conn->close();

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