<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $car_id = $_POST['car_id'];
    $pickup_date = $_POST['pickup_date'];
    $return_date = $_POST['return_date'];

    // Ensure the car is still available
    $check_sql = "SELECT id FROM bookings WHERE car_id = ? AND (status = 'pending' OR status = 'approved')";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "This car is already booked!";
        exit();
    }

    // Insert the booking
    $sql = "INSERT INTO bookings (user_id, car_id, pickup_date, return_date, status) VALUES (?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $user_id, $car_id, $pickup_date, $return_date);

    if ($stmt->execute()) {
        echo "Booking request sent!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
