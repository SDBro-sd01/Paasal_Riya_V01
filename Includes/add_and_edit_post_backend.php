<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

ob_clean();

include "db_connection.php";

// Admin ID retrieve from cookie (same way as admin page)
$admin_id = 0;
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
    if (isset($cookie_data['user_id'])) {
        $admin_id = intval($cookie_data['user_id']);
    }
}
// Optionally check if user is admin
if ($admin_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Admin not logged in.']);
    exit;
}

// ============================================================
// !! IMPORTANT : Absolute path to the uploads directory !!
// ============================================================
$baseUploadPath = 'C:/xampp/htdocs/Assignments Projects/Sisu Seriya/09 - Copy - Copy/uploads/';
// ============================================================

$cookie_data = [];
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
}
$user_id = isset($cookie_data['user_id']) ? intval($cookie_data['user_id']) : 0;
$isLogged = (isset($cookie_data['islogged']) && $cookie_data['islogged'] == 1);

if (!$isLogged || $user_id == 0) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'fetch':
            fetchServices($conn, $user_id);
            break;
        case 'get':
            getService($conn, $user_id);
            break;
        case 'add':
            addService($conn, $user_id);
            break;
        case 'edit':
            editService($conn, $user_id);
            break;
        case 'delete':
            deleteService($conn, $user_id);
            break;
        case 'delete_optional_image':
            deleteOptionalImage($conn, $user_id);
            break;
        case 'change_status':
            changeStatus($conn, $user_id);
            break;
        case 'admin_fetch':
            adminFetchPosts($conn, $user_id);
            break;
        case 'get_user':
            getUserDetails($conn, $user_id);
            break;
        case 'delete_post_admin':
            deletePostAdmin($conn, $user_id);
            break;
        case 'fetch_req_documents':
            fetchReqDocuments($conn);
            break;
        case 'get_document_images':
            getDocumentImages($conn, $user_id);
            break;
        case 'delete_document_image':
            deleteDocumentImage($conn, $user_id);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
$conn->close();
exit;

// ── HELPER FUNCTIONS ──
function getAbsolutePath($baseUploadPath, $relativePath) {
    return $baseUploadPath . str_replace('uploads/', '', $relativePath);
}
function fetchMulti($conn, $service_id, $table, $column) {
    $data = [];
    $stmt = $conn->prepare("SELECT $column FROM $table WHERE service_id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $data[] = $row[$column];
    return $data;
}
function saveMulti($conn, $service_id, $table, $column, $values) {
    if (empty($values)) return;
    $stmt = $conn->prepare("INSERT INTO $table (service_id, $column) VALUES (?, ?)");
    foreach ($values as $val) {
        $val = trim($val);
        if ($val !== '') {
            $stmt->bind_param("is", $service_id, $val);
            $stmt->execute();
        }
    }
}
function deleteMulti($conn, $service_id, $table) {
    $stmt = $conn->prepare("DELETE FROM $table WHERE service_id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
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
function removeEmptyParentDirs($baseUploadPath, $province, $district) {
    $districtPath = $baseUploadPath . $province . '/' . $district;
    if (is_dir($districtPath)) {
        $files = scandir($districtPath);
        if (count($files) == 2) { // only . and ..
            rmdir($districtPath);
            $provincePath = $baseUploadPath . $province;
            if (is_dir($provincePath)) {
                $provFiles = scandir($provincePath);
                if (count($provFiles) == 2) rmdir($provincePath);
            }
        }
    }
}

// ── REQ DOCUMENTS ──
function fetchReqDocuments($conn) {
    $res = $conn->query("SELECT id, document_name FROM req_documents ORDER BY sort_order");
    $docs = [];
    while ($row = $res->fetch_assoc()) $docs[] = $row;
    echo json_encode(['success' => true, 'documents' => $docs]);
}

// ── DOCUMENT IMAGES ──
function getDocumentImages($conn, $user_id) {
    $service_id = intval($_GET['service_id'] ?? 0);
    $stmt = $conn->prepare("SELECT sdi.id, sdi.image_path FROM service_document_images sdi JOIN services s ON sdi.service_id = s.service_id WHERE sdi.service_id = ? AND s.user_id = ?");
    $stmt->bind_param("ii", $service_id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $images = [];
    while ($row = $res->fetch_assoc()) $images[] = $row;
    echo json_encode(['success' => true, 'images' => $images]);
}
function deleteDocumentImage($conn, $user_id) {
    global $baseUploadPath;
    $image_id = intval($_POST['image_id'] ?? 0);
    $stmt = $conn->prepare("SELECT sdi.image_path, s.user_id FROM service_document_images sdi JOIN services s ON sdi.service_id = s.service_id WHERE sdi.id = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($img = $res->fetch_assoc()) {
        if ($img['user_id'] != $user_id) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $full = getAbsolutePath($baseUploadPath, $img['image_path']);
        if (file_exists($full)) unlink($full);
        $del = $conn->prepare("DELETE FROM service_document_images WHERE id = ?");
        $del->bind_param("i", $image_id);
        $del->execute();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Image not found']);
    }
}

// ── FETCH SERVICES ──
function fetchServices($conn, $user_id) {
    $sql = "SELECT s.*, 
            (SELECT COUNT(*) FROM comments WHERE service_id = s.service_id) as comments_count,
            (SELECT AVG(rating) FROM ratings WHERE service_id = s.service_id) as avg_rating,
            (SELECT COUNT(*) FROM ratings WHERE service_id = s.service_id) as total_ratings
            FROM services s WHERE s.user_id = ? ORDER BY s.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $services = [];
    while ($row = $result->fetch_assoc()) {
        $row['schools'] = fetchMulti($conn, $row['service_id'], 'service_schools', 'school_name');
        $row['images'] = getServiceImages($conn, $row['service_id']);
        $row['avg_rating'] = $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
        $row['total_ratings'] = (int)$row['total_ratings'];
        $services[] = $row;
    }
    echo json_encode(['success' => true, 'posts' => $services]);
}

// ── GET SINGLE SERVICE ──
function getService($conn, $user_id) {
    $service_id = intval($_GET['post_id'] ?? 0);
    if ($service_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        return;
    }
    $sql = "SELECT * FROM services WHERE service_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $service_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($service = $result->fetch_assoc()) {
        $service['assistants'] = fetchMulti($conn, $service_id, 'service_assistants', 'assistant_name');
        $service['schools'] = fetchMulti($conn, $service_id, 'service_schools', 'school_name');
        $service['telephones'] = fetchMulti($conn, $service_id, 'service_telephones', 'telephone');
        $service['emails'] = fetchMulti($conn, $service_id, 'service_emails', 'email');
        $service['websites'] = fetchMulti($conn, $service_id, 'service_websites', 'website');
        $service['images'] = getServiceImages($conn, $service_id);
        $sch = $conn->prepare("SELECT label, place, time FROM service_schedules WHERE service_id = ? ORDER BY sort_order");
        $sch->bind_param("i", $service_id);
        $sch->execute();
        $schRes = $sch->get_result();
        $schedules = [];
        while ($s = $schRes->fetch_assoc()) $schedules[] = $s;
        $service['schedules'] = $schedules;
        echo json_encode(['success' => true, 'post' => $service]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Service not found']);
    }
}

// ── ADD SERVICE ──
function addService($conn, $user_id) {
    global $baseUploadPath;
    $service_name = trim($_POST['service_name'] ?? '');
    $reg_no = trim($_POST['reg_no'] ?? '');
    $vehicle_type = $_POST['vehicle_type'] ?? '';
    $service_type = $_POST['service_type'] ?? '';
    $owner = trim($_POST['owner'] ?? '');
    $driver = trim($_POST['driver'] ?? '');
    $driver_reg_no = trim($_POST['driver_reg_no'] ?? '');
    $province = $_POST['province'] ?? '';
    $district = $_POST['district'] ?? '';
    $home_town = trim($_POST['home_town'] ?? '');
    $areas_covered = trim($_POST['areas_covered'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $road_description = trim($_POST['road_description'] ?? '');

    $schedule_places = $_POST['schedule_place'] ?? [];
    $schedule_times = $_POST['schedule_time'] ?? [];
    $schedule_labels = $_POST['schedule_label'] ?? [];
    if (empty($schedule_places) || empty($schedule_times)) {
        echo json_encode(['success' => false, 'message' => 'At least one schedule entry is required.']);
        return;
    }

    if (empty($service_name) || empty($reg_no) || empty($vehicle_type) || empty($service_type) || empty($owner) || empty($driver) || empty($driver_reg_no) || empty($province) || empty($district) || empty($home_town) || empty($areas_covered) || empty($address)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        return;
    }
    if (empty($_POST['schools']) || !is_array($_POST['schools']) || empty(array_filter($_POST['schools'], 'trim'))) {
        echo json_encode(['success' => false, 'message' => 'At least one school/institute is required']);
        return;
    }
    if (empty($_POST['telephones']) || !is_array($_POST['telephones']) || empty(array_filter($_POST['telephones'], 'trim'))) {
        echo json_encode(['success' => false, 'message' => 'At least one telephone number is required']);
        return;
    }
    $check = $conn->prepare("SELECT service_id FROM services WHERE reg_no = ?");
    $check->bind_param("s", $reg_no);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Registration number already exists']);
        return;
    }

    $sql = "INSERT INTO services (user_id, service_name, reg_no, vehicle_type, service_type, owner, driver, driver_reg_no, 
            province, district, home_town, areas_covered, address, description, road_description, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssssssssss", 
        $user_id, $service_name, $reg_no, $vehicle_type, $service_type, $owner, $driver, $driver_reg_no,
        $province, $district, $home_town, $areas_covered, $address, $description, $road_description);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'DB Error']);
        return;
    }
    $service_id = $conn->insert_id;

    $imageResult = handleImageUploadAdd($conn, $service_id, $province, $district, $service_name, $_FILES, $baseUploadPath);
    if ($imageResult !== true) {
        $conn->query("DELETE FROM services WHERE service_id = $service_id");
        echo json_encode(['success' => false, 'message' => $imageResult]);
        return;
    }

    foreach ($schedule_places as $i => $place) {
        if (trim($place) === '' || empty($schedule_times[$i])) continue;
        $label = $schedule_labels[$i] ?? '';
        $time = $schedule_times[$i];
        $stmt2 = $conn->prepare("INSERT INTO service_schedules (service_id, label, place, time, sort_order) VALUES (?, ?, ?, ?, ?)");
        $order = $i + 1;
        $stmt2->bind_param("isssi", $service_id, $label, $place, $time, $order);
        $stmt2->execute();
    }

    saveMulti($conn, $service_id, 'service_schools', 'school_name', $_POST['schools'] ?? []);
    saveMulti($conn, $service_id, 'service_assistants', 'assistant_name', $_POST['assistants'] ?? []);
    saveMulti($conn, $service_id, 'service_telephones', 'telephone', $_POST['telephones'] ?? []);
    saveMulti($conn, $service_id, 'service_emails', 'email', $_POST['emails'] ?? []);
    saveMulti($conn, $service_id, 'service_websites', 'website', $_POST['websites'] ?? []);

    if (!empty($_FILES['document_images']['name'][0])) {
        $docResult = handleDocumentImageUpload($conn, $service_id, $province, $district, $service_name, $_FILES['document_images'], $baseUploadPath);
        if ($docResult !== true) {
            echo json_encode(['success' => false, 'message' => $docResult]);
            return;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Please upload at least one document image.']);
        return;
    }

    echo json_encode(['success' => true, 'message' => 'Service added successfully']);
}

// ── EDIT SERVICE (Fixed folder rename/move logic) ──
function editService($conn, $user_id) {
    global $baseUploadPath;
    $service_id = intval($_POST['service_id'] ?? 0);
    if (!$service_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid service ID']);
        return;
    }
    $current = $conn->prepare("SELECT province, district, service_name, user_id FROM services WHERE service_id = ?");
    $current->bind_param("i", $service_id);
    $current->execute();
    $currentData = $current->get_result()->fetch_assoc();
    if (!$currentData || $currentData['user_id'] != $user_id) {
        echo json_encode(['success' => false, 'message' => 'Service not found or access denied']);
        return;
    }

    $oldProvince = $currentData['province'];
    $oldDistrict = $currentData['district'];
    $oldServiceName = $currentData['service_name'];

    $service_name = trim($_POST['service_name'] ?? '');
    $reg_no = trim($_POST['reg_no'] ?? '');
    $vehicle_type = $_POST['vehicle_type'] ?? '';
    $service_type = $_POST['service_type'] ?? '';
    $owner = trim($_POST['owner'] ?? '');
    $driver = trim($_POST['driver'] ?? '');
    $driver_reg_no = trim($_POST['driver_reg_no'] ?? '');
    $province = $_POST['province'] ?? '';
    $district = $_POST['district'] ?? '';
    $home_town = trim($_POST['home_town'] ?? '');
    $areas_covered = trim($_POST['areas_covered'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $road_description = trim($_POST['road_description'] ?? '');

    $schedule_places = $_POST['schedule_place'] ?? [];
    $schedule_times = $_POST['schedule_time'] ?? [];
    $schedule_labels = $_POST['schedule_label'] ?? [];
    if (empty($schedule_places) || empty($schedule_times)) {
        echo json_encode(['success' => false, 'message' => 'At least one schedule entry is required.']);
        return;
    }

    if (empty($service_name) || empty($reg_no) || empty($vehicle_type) || empty($service_type) || 
        empty($owner) || empty($driver) || empty($driver_reg_no) || empty($province) || 
        empty($district) || empty($home_town) || empty($areas_covered) || empty($address)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        return;
    }
    if (empty($_POST['schools']) || !is_array($_POST['schools']) || empty(array_filter($_POST['schools'], 'trim'))) {
        echo json_encode(['success' => false, 'message' => 'At least one school/institute is required']);
        return;
    }
    if (empty($_POST['telephones']) || !is_array($_POST['telephones']) || empty(array_filter($_POST['telephones'], 'trim'))) {
        echo json_encode(['success' => false, 'message' => 'At least one telephone number is required']);
        return;
    }
    $dupCheck = $conn->prepare("SELECT service_id FROM services WHERE reg_no = ? AND service_id != ?");
    $dupCheck->bind_param("si", $reg_no, $service_id);
    $dupCheck->execute();
    if ($dupCheck->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Registration number already exists']);
        return;
    }

// පැරණි UPDATE ප්‍රකාශය වෙනුවට
$sql = "UPDATE services SET service_name=?, reg_no=?, vehicle_type=?, service_type=?, owner=?, driver=?, driver_reg_no=?,
        province=?, district=?, home_town=?, areas_covered=?, address=?, description=?, road_description=?,
        status = 'pending', edited_after_approval = 1
        WHERE service_id=? AND user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssssssssii", 
    $service_name, $reg_no, $vehicle_type, $service_type, $owner, $driver, $driver_reg_no,
    $province, $district, $home_town, $areas_covered, $address, $description, $road_description,
    $service_id, $user_id);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'DB update failed']);
        return;
    }

    // ────────── SMART FOLDER HANDLING ──────────
    $oldServiceFolder = $baseUploadPath . $oldProvince . '/' . $oldDistrict . '/' . $oldServiceName . '_' . $service_id;
    $newServiceFolder = $baseUploadPath . $province . '/' . $district . '/' . $service_name . '_' . $service_id;
    $oldDocFolder = $oldServiceFolder . '_documents';
    $newDocFolder = $newServiceFolder . '_documents';
    $folderChanged = ($oldServiceFolder !== $newServiceFolder);
    $districtChanged = ($oldProvince !== $province) || ($oldDistrict !== $district);

    if ($folderChanged) {
        if (!$districtChanged) {
            // Only service name changed – simple rename inside same district
            if (is_dir($oldServiceFolder)) {
                rename($oldServiceFolder, $newServiceFolder);
            }
            if (is_dir($oldDocFolder)) {
                rename($oldDocFolder, $newDocFolder);
            }
        } else {
            // District/province changed
            $oldDistrictPath = $baseUploadPath . $oldProvince . '/' . $oldDistrict;
            $newDistrictPath = $baseUploadPath . $province . '/' . $district;
            $districtContents = @scandir($oldDistrictPath);
            $serviceFolders = [];
            if ($districtContents) {
                foreach ($districtContents as $item) {
                    if ($item != '.' && $item != '..' && is_dir($oldDistrictPath . '/' . $item)) {
                        $serviceFolders[] = $item;
                    }
                }
            }
            $onlyOurService = (count($serviceFolders) == 2) &&
                              in_array($oldServiceName . '_' . $service_id, $serviceFolders) &&
                              in_array($oldServiceName . '_' . $service_id . '_documents', $serviceFolders);

            if ($onlyOurService) {
                // Rename entire district folder
                if (!is_dir(dirname($newDistrictPath))) {
                    mkdir(dirname($newDistrictPath), 0777, true);
                }
                rename($oldDistrictPath, $newDistrictPath);
                // Check old province (might be empty now)
                $oldProvincePath = $baseUploadPath . $oldProvince;
                $provContents = @scandir($oldProvincePath);
                if ($provContents && count($provContents) == 2) {
                    rmdir($oldProvincePath);
                }
            } else {
                // Move only our service folders to new district
                if (!is_dir($newDistrictPath)) {
                    mkdir($newDistrictPath, 0777, true);
                }
                if (is_dir($oldServiceFolder)) {
                    rename($oldServiceFolder, $newServiceFolder);
                }
                if (is_dir($oldDocFolder)) {
                    rename($oldDocFolder, $newDocFolder);
                }
                // Cleanup empty old directories
                removeEmptyParentDirs($baseUploadPath, $oldProvince, $oldDistrict);
            }
        }

        // Update database paths
        $oldBaseRel = "uploads/$oldProvince/$oldDistrict/$oldServiceName" . "_" . $service_id;
        $newBaseRel = "uploads/$province/$district/$service_name" . "_" . $service_id;
        $conn->query("UPDATE service_images SET image_path = REPLACE(image_path, '$oldBaseRel', '$newBaseRel') WHERE service_id = $service_id");
        $conn->query("UPDATE service_document_images SET image_path = REPLACE(image_path, '$oldBaseRel" . "_documents', '$newBaseRel" . "_documents') WHERE service_id = $service_id");
    }

    // Handle mandatory replacements / new uploads
    handleMandatoryReplacements($conn, $service_id, $province, $district, $service_name, $_FILES, $baseUploadPath);
    if (isset($_FILES['optional_images']) && !empty($_FILES['optional_images']['name'][0])) {
        handleOptionalImageUpload($conn, $service_id, $province, $district, $service_name, $_FILES['optional_images'], $baseUploadPath);
    }

    // Update schedules
    deleteMulti($conn, $service_id, 'service_schedules');
    foreach ($schedule_places as $i => $place) {
        if (trim($place) === '' || empty($schedule_times[$i])) continue;
        $label = $schedule_labels[$i] ?? '';
        $time = $schedule_times[$i];
        $stmt2 = $conn->prepare("INSERT INTO service_schedules (service_id, label, place, time, sort_order) VALUES (?, ?, ?, ?, ?)");
        $order = $i + 1;
        $stmt2->bind_param("isssi", $service_id, $label, $place, $time, $order);
        $stmt2->execute();
    }

    // Update multi fields
    deleteMulti($conn, $service_id, 'service_assistants');
    deleteMulti($conn, $service_id, 'service_schools');
    deleteMulti($conn, $service_id, 'service_telephones');
    deleteMulti($conn, $service_id, 'service_emails');
    deleteMulti($conn, $service_id, 'service_websites');
    saveMulti($conn, $service_id, 'service_assistants', 'assistant_name', $_POST['assistants'] ?? []);
    saveMulti($conn, $service_id, 'service_schools', 'school_name', $_POST['schools'] ?? []);
    saveMulti($conn, $service_id, 'service_telephones', 'telephone', $_POST['telephones'] ?? []);
    saveMulti($conn, $service_id, 'service_emails', 'email', $_POST['emails'] ?? []);
    saveMulti($conn, $service_id, 'service_websites', 'website', $_POST['websites'] ?? []);

    if (!empty($_FILES['document_images']['name'][0])) {
        handleDocumentImageUpload($conn, $service_id, $province, $district, $service_name, $_FILES['document_images'], $baseUploadPath);
    }

    echo json_encode(['success' => true, 'message' => 'Service updated successfully']);
}

// ── IMAGE HANDLING (using absolute paths) ──
function handleImageUploadAdd($conn, $service_id, $province, $district, $service_name, $files, $baseUploadPath) {
    $folder = $baseUploadPath . $province . '/' . $district . '/' . $service_name . "_" . $service_id . '/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);
    $mandatoryMap = [
        'mandatory_front'=>1, 'mandatory_back'=>2, 'mandatory_left'=>3, 'mandatory_right'=>4,
        'mandatory_seats1'=>5, 'mandatory_seats2'=>6
    ];
    $uploaded = 0; $errors = [];
    foreach ($mandatoryMap as $field => $type) {
        if (!isset($files[$field]) || $files[$field]['error'] !== UPLOAD_ERR_OK) { $errors[] = "$field required"; continue; }
        $file = $files[$field];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) { $errors[] = "Invalid $field format"; continue; }
        if ($file['size'] > 5*1024*1024) { $errors[] = "$field too large"; continue; }
        $newName = $type . '_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $dest = $folder . $newName;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $rel = "uploads/$province/$district/$service_name" . "_" . $service_id . "/$newName";
            $stmt = $conn->prepare("INSERT INTO service_images (service_id, image_path, is_mandatory, display_order) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isii", $service_id, $rel, $type, $type);
            $stmt->execute();
            $uploaded++;
        } else $errors[] = "Failed move $field";
    }
    if ($uploaded < 6) return "Mandatory images missing: " . implode(", ", $errors);
    if (isset($files['optional_images']) && is_array($files['optional_images']['name'])) {
        $order = 10;
        for ($i=0; $i<count($files['optional_images']['name']); $i++) {
            if ($files['optional_images']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $ext = strtolower(pathinfo($files['optional_images']['name'][$i], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp'])) continue;
            if ($files['optional_images']['size'][$i] > 5*1024*1024) continue;
            $newName = 'opt_' . time() . '_' . $i . '_' . rand(1000,9999) . '.' . $ext;
            $dest = $folder . $newName;
            if (move_uploaded_file($files['optional_images']['tmp_name'][$i], $dest)) {
                $rel = "uploads/$province/$district/$service_name" . "_" . $service_id . "/$newName";
                $stmt = $conn->prepare("INSERT INTO service_images (service_id, image_path, is_mandatory, display_order) VALUES (?, ?, 0, ?)");
                $stmt->bind_param("isi", $service_id, $rel, $order);
                $stmt->execute();
                $order++;
            }
        }
    }
    return true;
}
function handleMandatoryReplacements($conn, $service_id, $province, $district, $service_name, $files, $baseUploadPath) {
    $folder = $baseUploadPath . $province . '/' . $district . '/' . $service_name . "_" . $service_id . '/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);
    for ($i = 1; $i <= 6; $i++) {
        $fieldName = "mandatory_replace_$i";
        if (isset($files[$fieldName]) && $files[$fieldName]['error'] === UPLOAD_ERR_OK) {
            $old = $conn->prepare("SELECT image_id, image_path FROM service_images WHERE service_id = ? AND is_mandatory = ?");
            $old->bind_param("ii", $service_id, $i);
            $old->execute();
            $oldRes = $old->get_result();
            if ($oldRow = $oldRes->fetch_assoc()) {
                $oldFullPath = getAbsolutePath($baseUploadPath, $oldRow['image_path']);
                if (file_exists($oldFullPath)) unlink($oldFullPath);
                $conn->query("DELETE FROM service_images WHERE image_id = {$oldRow['image_id']}");
            }
            $file = $files[$fieldName];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $newName = $i . '_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $dest = $folder . $newName;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $rel = "uploads/$province/$district/$service_name" . "_" . $service_id . "/$newName";
                $stmt = $conn->prepare("INSERT INTO service_images (service_id, image_path, is_mandatory, display_order) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isii", $service_id, $rel, $i, $i);
                $stmt->execute();
            }
        }
    }
}
function handleOptionalImageUpload($conn, $service_id, $province, $district, $service_name, $optionalFiles, $baseUploadPath) {
    $folder = $baseUploadPath . $province . '/' . $district . '/' . $service_name . "_" . $service_id . '/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);
    $orderQ = $conn->prepare("SELECT MAX(display_order) as max FROM service_images WHERE service_id = ? AND is_mandatory = 0");
    $orderQ->bind_param("i", $service_id);
    $orderQ->execute();
    $max = $orderQ->get_result()->fetch_assoc()['max'] ?? 10;
    $order = $max + 1;
    for ($i=0; $i<count($optionalFiles['name']); $i++) {
        if ($optionalFiles['error'][$i] !== UPLOAD_ERR_OK) continue;
        $ext = strtolower(pathinfo($optionalFiles['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) continue;
        if ($optionalFiles['size'][$i] > 5*1024*1024) continue;
        $newName = 'opt_' . time() . '_' . $i . '_' . rand(1000,9999) . '.' . $ext;
        $dest = $folder . $newName;
        if (move_uploaded_file($optionalFiles['tmp_name'][$i], $dest)) {
            $rel = "uploads/$province/$district/$service_name" . "_" . $service_id . "/$newName";
            $stmt = $conn->prepare("INSERT INTO service_images (service_id, image_path, is_mandatory, display_order) VALUES (?, ?, 0, ?)");
            $stmt->bind_param("isi", $service_id, $rel, $order);
            $stmt->execute();
            $order++;
        }
    }
}
function handleDocumentImageUpload($conn, $service_id, $province, $district, $service_name, $filesArray, $baseUploadPath) {
    $docFolder = $baseUploadPath . $province . '/' . $district . '/' . $service_name . "_" . $service_id . '_documents/';
    if (!is_dir($docFolder)) mkdir($docFolder, 0777, true);
    $uploaded = false;
    for ($i = 0; $i < count($filesArray['name']); $i++) {
        if ($filesArray['error'][$i] !== UPLOAD_ERR_OK) continue;
        $ext = strtolower(pathinfo($filesArray['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) continue;
        if ($filesArray['size'][$i] > 5*1024*1024) continue;
        $newName = 'doc_' . time() . '_' . $i . '_' . rand(1000,9999) . '.' . $ext;
        $dest = $docFolder . $newName;
        if (move_uploaded_file($filesArray['tmp_name'][$i], $dest)) {
            $rel = "uploads/$province/$district/$service_name" . "_" . $service_id . "_documents/$newName";
            $stmt = $conn->prepare("INSERT INTO service_document_images (service_id, document_type_id, image_path) VALUES (?, NULL, ?)");
            $stmt->bind_param("is", $service_id, $rel);
            $stmt->execute();
            $uploaded = true;
        }
    }
    return $uploaded ? true : false;
}

// ── DELETE OPTIONAL IMAGE ──
function deleteOptionalImage($conn, $user_id) {
    global $baseUploadPath;
    $image_id = intval($_POST['image_id'] ?? 0);
    if (!$image_id) { echo json_encode(['success' => false, 'message' => 'Invalid image ID']); return; }
    $imgQuery = $conn->prepare("SELECT si.image_path, si.service_id, s.user_id FROM service_images si JOIN services s ON si.service_id = s.service_id WHERE si.image_id = ?");
    $imgQuery->bind_param("i", $image_id);
    $imgQuery->execute();
    $imgRes = $imgQuery->get_result();
    if ($img = $imgRes->fetch_assoc()) {
        if ($img['user_id'] != $user_id) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); return; }
        $fullPath = getAbsolutePath($baseUploadPath, $img['image_path']);
        if (file_exists($fullPath)) unlink($fullPath);
        $del = $conn->prepare("DELETE FROM service_images WHERE image_id = ?");
        $del->bind_param("i", $image_id);
        $del->execute();
        echo json_encode(['success' => true]);
    } else { echo json_encode(['success' => false, 'message' => 'Image not found']); }
}

// ── DELETE SERVICE ──
function deleteService($conn, $user_id) {
    global $baseUploadPath;
    $service_id = intval($_POST['post_id'] ?? 0);
    if (!$service_id) { echo json_encode(['success' => false, 'message' => 'Invalid ID']); return; }

    $imgQuery = $conn->prepare("SELECT image_path FROM service_images WHERE service_id = ?");
    $imgQuery->bind_param("i", $service_id);
    $imgQuery->execute();
    $imgRes = $imgQuery->get_result();
    while ($img = $imgRes->fetch_assoc()) {
        $full = getAbsolutePath($baseUploadPath, $img['image_path']);
        if (file_exists($full)) unlink($full);
    }
    $docImgQuery = $conn->prepare("SELECT image_path FROM service_document_images WHERE service_id = ?");
    $docImgQuery->bind_param("i", $service_id);
    $docImgQuery->execute();
    $docRes = $docImgQuery->get_result();
    while ($doc = $docRes->fetch_assoc()) {
        $full = getAbsolutePath($baseUploadPath, $doc['image_path']);
        if (file_exists($full)) unlink($full);
    }

    $serviceData = $conn->prepare("SELECT province, district, service_name FROM services WHERE service_id = ?");
    $serviceData->bind_param("i", $service_id);
    $serviceData->execute();
    $sData = $serviceData->get_result()->fetch_assoc();

    $conn->query("DELETE FROM service_images WHERE service_id = $service_id");
    $conn->query("DELETE FROM service_document_images WHERE service_id = $service_id");

    if ($sData) {
        $folder = $baseUploadPath . $sData['province'] . '/' . $sData['district'] . '/' . $sData['service_name'] . "_" . $service_id;
        $docFolder = $folder . '_documents';
        if (is_dir($docFolder)) {
            array_map('unlink', glob($docFolder . "/*"));
            rmdir($docFolder);
        }
        if (is_dir($folder)) {
            array_map('unlink', glob($folder . "/*"));
            rmdir($folder);
        }
        removeEmptyParentDirs($baseUploadPath, $sData['province'], $sData['district']);
    }

    $sql = "DELETE FROM services WHERE service_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $service_id, $user_id);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Deleted']);
}

// ── ADMIN FUNCTIONS ──
function changeStatus($conn, $user_id) {
    // Admin check
    $adminCheck = $conn->prepare("SELECT user_type FROM users WHERE user_id = ?");
    $adminCheck->bind_param("i", $user_id);
    $adminCheck->execute();
    $res = $adminCheck->get_result();
    if ($res->num_rows === 0 || $res->fetch_assoc()['user_type'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized. Admins only.']);
        return;
    }

    $service_id = intval($_POST['service_id'] ?? 0);
    $new_status = $_POST['status'] ?? '';

    if (!in_array($new_status, ['pending', 'approved', 'rejected', 'hold'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        return;
    }

    // Update post status
    if ($new_status === 'approved') {
        $stmt = $conn->prepare("UPDATE services SET status = ?, edited_after_approval = 0 WHERE service_id = ?");
        $stmt->bind_param("si", $new_status, $service_id);
    } else {
        $stmt = $conn->prepare("UPDATE services SET status = ? WHERE service_id = ?");
        $stmt->bind_param("si", $new_status, $service_id);
    }

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        // Audit log entry (admin ID = $user_id)
        $logStmt = $conn->prepare("INSERT INTO post_status_log (service_id, admin_id, action) VALUES (?, ?, ?)");
        $logStmt->bind_param("iis", $service_id, $user_id, $new_status);
        $logStmt->execute();

        echo json_encode(['success' => true, 'message' => 'Status updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'DB update failed or no changes']);
    }
}

function adminFetchPosts($conn, $user_id) {
    $adminCheck = $conn->prepare("SELECT user_type FROM users WHERE user_id = ?");
    $adminCheck->bind_param("i", $user_id);
    $adminCheck->execute();
    $res = $adminCheck->get_result();
    if ($res->num_rows === 0 || $res->fetch_assoc()['user_type'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    $status = $_GET['status'] ?? 'all';
    $province = isset($_GET['province']) ? trim($_GET['province']) : '';
    $district = isset($_GET['district']) ? trim($_GET['district']) : '';
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $sql = "SELECT s.*,
            (SELECT COUNT(*) FROM comments WHERE service_id = s.service_id) as comments_count,
            (SELECT AVG(rating) FROM ratings WHERE service_id = s.service_id) as avg_rating,
            (SELECT COUNT(*) FROM ratings WHERE service_id = s.service_id) as total_ratings,
            (SELECT COUNT(*) FROM reports WHERE service_id = s.service_id) as report_count
            FROM services s WHERE 1=1";
    $params = []; $types = "";
    if ($status !== 'all') { $sql .= " AND s.status = ?"; $params[] = $status; $types .= "s"; }
    if (!empty($province)) { $sql .= " AND s.province = ?"; $params[] = $province; $types .= "s"; }
    if (!empty($district)) { $sql .= " AND s.district = ?"; $params[] = $district; $types .= "s"; }
    if (!empty($search)) {
        $escapedSearch = addcslashes($search, '%_');
        $like = "%$escapedSearch%";
        $sql .= " AND (s.service_name LIKE ? OR s.areas_covered LIKE ? OR s.reg_no LIKE ? OR EXISTS (SELECT 1 FROM service_schools WHERE service_id = s.service_id AND school_name LIKE ?) OR EXISTS (SELECT 1 FROM service_telephones WHERE service_id = s.service_id AND telephone LIKE ?))";
        array_push($params, $like, $like, $like, $like, $like);
        $types .= "sssss";
    }
    $sql .= " ORDER BY s.created_at DESC";
    $stmt = $conn->prepare($sql);
    if (!empty($params)) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $row['schools'] = fetchMulti($conn, $row['service_id'], 'service_schools', 'school_name');
        $row['images'] = getServiceImages($conn, $row['service_id']);
        $row['avg_rating'] = $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
        $row['total_ratings'] = (int)$row['total_ratings'];
        $row['report_count'] = (int)$row['report_count'];
        $posts[] = $row;
    }
    echo json_encode(['success' => true, 'posts' => $posts]);
}
function getUserDetails($conn, $user_id) {
    $adminCheck = $conn->prepare("SELECT user_type FROM users WHERE user_id = ?");
    $adminCheck->bind_param("i", $user_id);
    $adminCheck->execute();
    if ($adminCheck->get_result()->fetch_assoc()['user_type'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']); return;
    }
    $service_id = intval($_GET['service_id'] ?? 0);
    $stmt = $conn->prepare("SELECT user_id FROM services WHERE service_id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if ($row) {
        $userStmt = $conn->prepare("SELECT user_id, username, fullname, mobile, email, nic, district, province, address, user_type, created_at FROM users WHERE user_id = ?");
        $userStmt->bind_param("i", $row['user_id']);
        $userStmt->execute();
        $user = $userStmt->get_result()->fetch_assoc();
        unset($user['password']);
        echo json_encode(['success' => true, 'user' => $user]);
    } else echo json_encode(['success' => false, 'message' => 'Service not found']);
}
function deletePostAdmin($conn, $user_id) {
    global $baseUploadPath;
    $adminCheck = $conn->prepare("SELECT user_type, password FROM users WHERE user_id = ?");
    $adminCheck->bind_param("i", $user_id);
    $adminCheck->execute();
    $res = $adminCheck->get_result();
    if ($res->num_rows === 0) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); return; }
    $adminData = $res->fetch_assoc();
    if ($adminData['user_type'] !== 'admin' || !password_verify($_POST['password'] ?? '', $adminData['password'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized or wrong password']); return;
    }
    $service_id = intval($_POST['service_id'] ?? 0);
    $imgQuery = $conn->prepare("SELECT image_path FROM service_images WHERE service_id = ?");
    $imgQuery->bind_param("i", $service_id); $imgQuery->execute();
    $imgRes = $imgQuery->get_result();
    while ($img = $imgRes->fetch_assoc()) {
        $full = getAbsolutePath($baseUploadPath, $img['image_path']);
        if (file_exists($full)) unlink($full);
    }
    $docImgQuery = $conn->prepare("SELECT image_path FROM service_document_images WHERE service_id = ?");
    $docImgQuery->bind_param("i", $service_id); $docImgQuery->execute();
    $docRes = $docImgQuery->get_result();
    while ($doc = $docRes->fetch_assoc()) {
        $full = getAbsolutePath($baseUploadPath, $doc['image_path']);
        if (file_exists($full)) unlink($full);
    }
    $serviceData = $conn->prepare("SELECT province, district, service_name FROM services WHERE service_id = ?");
    $serviceData->bind_param("i", $service_id); $serviceData->execute();
    $sData = $serviceData->get_result()->fetch_assoc();
    $conn->query("DELETE FROM service_images WHERE service_id = $service_id");
    $conn->query("DELETE FROM service_document_images WHERE service_id = $service_id");
    if ($sData) {
        $folder = $baseUploadPath . $sData['province'] . '/' . $sData['district'] . '/' . $sData['service_name'] . "_" . $service_id;
        $docFolder = $folder . '_documents';
        if (is_dir($docFolder)) { array_map('unlink', glob($docFolder . "/*")); rmdir($docFolder); }
        if (is_dir($folder)) { array_map('unlink', glob($folder . "/*")); rmdir($folder); }
        removeEmptyParentDirs($baseUploadPath, $sData['province'], $sData['district']);
    }
    $delStmt = $conn->prepare("DELETE FROM services WHERE service_id = ?");
    $delStmt->bind_param("i", $service_id); $delStmt->execute();
    echo json_encode(['success' => true, 'message' => 'Service deleted permanently']);
}
?>