<?php
include "db_connection.php";

// Read user ID from cookie (adjust based on your auth)
$cookie_data = json_decode($_COOKIE['abc'] ?? '{}', true);
$user_id = $cookie_data['user_id'] ?? null; // ensure your cookie contains user_id

if (!$user_id) {
    echo json_encode(["success" => false, "message" => "Please login to report."]);
    exit;
}

$service_id = $_POST['service_id'] ?? '';
$selected_options = $_POST['selected_options'] ?? '[]'; // JSON array of option IDs
$custom_reason = $_POST['custom_reason'] ?? '';

if (empty($service_id) || empty($selected_options) || $selected_options == '[]') {
    echo json_encode(["success" => false, "message" => "Please select at least one reason."]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO reports (service_id, user_id, selected_options, custom_reason) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $service_id, $user_id, $selected_options, $custom_reason);
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Report submitted successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Database error. Try again."]);
}
?>