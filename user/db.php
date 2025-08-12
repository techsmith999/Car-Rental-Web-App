<?php
$host = "localhost"; // Change if using a different host
$user = "root"; // Default for XAMPP/MAMP/WAMP
$pass = ""; // Default is empty for XAMPP/MAMP
$db = "car_rental"; // Your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set character encoding to UTF-8
$conn->set_charset("utf8");
?>
