<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();

include "db_connection.php";

// Absolute upload path – ඔබේ නිවැරදි path එක යොදන්න
$baseUploadPath = 'C:/xampp/htdocs/Assignments Projects/Sisu Seriya/09 - Copy - Copy/uploads/';

if (!file_exists($baseUploadPath)) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Upload path does not exist: ' . $baseUploadPath]);
    exit;
}

$cookie_data = [];
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
}
$user_id = isset($cookie_data['user_id']) ? intval($cookie_data['user_id']) : 0;
$isLogged = (isset($cookie_data['islogged']) && $cookie_data['islogged'] == 1);

if (!$isLogged || $user_id == 0) {
    ob_end_clean();
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

if ($action === 'download_documents') {
    downloadUserDocuments($user_id, $baseUploadPath, $conn);
} else {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
$conn->close();
exit;

function downloadUserDocuments($user_id, $baseUploadPath, $conn) {
    $service_id = intval($_GET['service_id'] ?? 0);
    if ($service_id === 0) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid service ID']);
        return;
    }

    $stmt = $conn->prepare("SELECT service_name, province, district FROM services WHERE service_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $service_id, $user_id);
    $stmt->execute();
    $service = $stmt->get_result()->fetch_assoc();
    if (!$service) {
        ob_end_clean();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Service not found or access denied']);
        return;
    }

    $imgStmt = $conn->prepare("SELECT image_path FROM service_document_images WHERE service_id = ?");
    $imgStmt->bind_param("i", $service_id);
    $imgStmt->execute();
    $images = $imgStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (empty($images)) {
        ob_end_clean();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'No document images found']);
        return;
    }

    // Temp directory
    $tempBase = __DIR__ . '/../temp/';
    if (!is_dir($tempBase)) {
        mkdir($tempBase, 0777, true);
    }
    $tempDir = $tempBase . 'doc_zip_' . uniqid() . '/';
    if (!mkdir($tempDir, 0777, true)) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create temporary folder: ' . $tempDir]);
        return;
    }

    if (!class_exists('ZipArchive')) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'ZipArchive class not available. Enable php_zip extension.']);
        return;
    }

    $zip = new ZipArchive();
    $zipFileName = $tempDir . 'documents.zip';
    if ($zip->open($zipFileName, ZipArchive::CREATE) !== true) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create ZIP archive']);
        rmdir($tempDir);
        return;
    }

    $addedCount = 0;
    foreach ($images as $img) {
        $relativePath = $img['image_path'];
        $fullPath = $baseUploadPath . str_replace('uploads/', '', $relativePath);
        if (file_exists($fullPath)) {
            $zip->addFile($fullPath, basename($fullPath));
            $addedCount++;
        }
    }

    if ($addedCount === 0) {
        $zip->close();
        array_map('unlink', glob("$tempDir*.*"));
        rmdir($tempDir);
        ob_end_clean();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'No valid document files found on server']);
        return;
    }

    $zip->close();

    // 🔥 Custom filename: Province_District_ServiceName_ServiceID_Documents.zip
    $safeProvince = preg_replace('/[^A-Za-z0-9_\-]/', '_', $service['province']);
    $safeDistrict = preg_replace('/[^A-Za-z0-9_\-]/', '_', $service['district']);
    $safeName     = preg_replace('/[^A-Za-z0-9_\-]/', '_', $service['service_name']);
    $downloadFilename = $safeProvince . '_' . $safeDistrict . '_' . $safeName . '_' . $service_id . '_Documents.zip';

    ob_end_clean();
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
    header('Content-Length: ' . filesize($zipFileName));
    header('Pragma: no-cache');
    header('Expires: 0');
    
    readfile($zipFileName);

    unlink($zipFileName);
    rmdir($tempDir);
    exit;
}