<?php
include "db_connection.php";

$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;
if (!$service_id) {
    echo json_encode(["success" => false, "message" => "Invalid service ID"]);
    exit;
}

// Fetch all reports for this service
$reportQuery = "SELECT id, selected_options, custom_reason, created_at FROM reports WHERE service_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($reportQuery);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all report options once
$optQuery = "SELECT id, option_text FROM report_options";
$optResult = mysqli_query($conn, $optQuery);
$optionsMap = [];
while ($row = mysqli_fetch_assoc($optResult)) {
    $optionsMap[$row['id']] = $row['option_text'];
}

$reports = [];
while ($row = $result->fetch_assoc()) {
    $selectedIds = json_decode($row['selected_options'], true);
    $selectedTexts = [];
    if (is_array($selectedIds)) {
        foreach ($selectedIds as $optId) {
            if (isset($optionsMap[$optId])) {
                $selectedTexts[] = $optionsMap[$optId];
            }
        }
    }
    $reports[] = [
        'id' => $row['id'],
        'selected_options_text' => $selectedTexts,
        'custom_reason' => $row['custom_reason'],
        'created_at' => $row['created_at']
    ];
}

echo json_encode(["success" => true, "reports" => $reports]);
?>