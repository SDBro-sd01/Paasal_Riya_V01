<?php 

$host = "localhost";
$username = "root";
$password = "(QR2r;I%v(DR84T";
$db_name = "PAASAL_RIYA_DB_01";


$conn = new mysqli($host,$username,$password,$db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>