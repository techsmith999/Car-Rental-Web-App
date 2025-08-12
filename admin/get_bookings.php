<?php
include 'db.php'; // Database connection

header('Content-Type: application/json');

$sql = "SELECT bookings.id, users.name AS user_name, cars.name AS car_name, bookings.pickup_date, bookings.return_date, 
               bookings.pickup_status, bookings.return_status 
        FROM bookings
        JOIN users ON bookings.user_id = users.id
        JOIN cars ON bookings.car_id = cars.id
        ORDER BY bookings.created_at DESC";

$result = $conn->query($sql);
$bookings = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

echo json_encode($bookings);
$conn->close();
?>
