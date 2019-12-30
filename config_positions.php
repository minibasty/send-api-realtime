<?php
// connect database 
$servername = ""; 
$username = "";
$password = "";
$db="";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);
mysqli_query($conn, "SET NAMES UTF8");
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>