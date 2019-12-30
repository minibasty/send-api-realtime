<?php
$servername = "27.254.81.41";
$username = "root";
$password = "Ple01010!@#";
$db="traccar_1";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);
mysqli_query($conn, "SET NAMES UTF8");
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>