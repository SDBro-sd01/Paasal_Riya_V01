<?php
include "db_connection.php";

$query = "SELECT id, option_text FROM report_options ORDER BY sort_order ASC";
$result = mysqli_query($conn, $query);
$options = [];
while ($row = mysqli_fetch_assoc($result)) {
    $options[] = $row;
}
echo json_encode(["success" => true, "options" => $options]);
?>