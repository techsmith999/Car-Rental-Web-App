<?php
$host = "localhost";
$user = "root"; // Default user for XAMPP
$pass = "";
$dbname = "car_rental";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
