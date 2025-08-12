<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_auth.php");
    exit();
}

// Database connection
$db = new mysqli('localhost', 'root', '', 'car_rental');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Fetch user ID from the URL
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = intval($_GET['user_id']);

// Fetch user details
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows == 0) {
    header("Location: admin_dashboard.php");
    exit();
}

$user = $user_result->fetch_assoc();

// Fetch booking data and car details
$booking_data = [];
$booking_query = "SELECT b.*, c.make, c.model, c.car_type, c.color, c.year, c.price_per_day FROM bookings b JOIN cars c ON b.car_id = c.id WHERE b.user_id = ?";
$stmt = $db->prepare($booking_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings_result = $stmt->get_result();

while ($row = $bookings_result->fetch_assoc()) {
    $booking_data[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Details | Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        /* Your custom CSS goes here */
        body {
            font-family: 'Arial', sans-serif;
            background: #f4f4f9;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
        }
        .user-details {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .user-details h3 {
            margin: 0;
            font-size: 20px;
        }
        .back-btn {
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
        .booking-details {
            margin-top: 20px;
        }
        .booking-card {
            background: #fff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .booking-card h4 {
            margin: 0;
            font-size: 18px;
        }
        .booking-card p {
            margin: 5px 0;
        }
        .booking-card .status {
            font-weight: bold;
        }
        .view-more-btn {
            margin-top: 10px;
            padding: 8px 15px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .view-more-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>Admin Dashboard - Customer Booking Details</h1>
            <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>

        <div class="user-details">
            <h3>Customer: <?php echo htmlspecialchars($user['name']); ?></h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>

            <!-- Display user bookings with more details -->
            <div class="booking-details">
                <h4>Bookings:</h4>
                <?php if (count($booking_data) > 0): ?>
                    <?php foreach ($booking_data as $booking): ?>
                        <div class="booking-card">
                            <h4>Car: <?php echo htmlspecialchars($booking['make']) . ' ' . htmlspecialchars($booking['model']); ?></h4>
                            <p><strong>Car Type:</strong> <?php echo htmlspecialchars($booking['car_type']); ?></p>
                            <p><strong>Color:</strong> <?php echo htmlspecialchars($booking['color']); ?></p>
                            <p><strong>Year:</strong> <?php echo htmlspecialchars($booking['year']); ?></p>
                            <p><strong>Price per Day:</strong> $<?php echo number_format($booking['price_per_day'], 2); ?></p>
                            <p><strong>Pickup Date:</strong> <?php echo htmlspecialchars($booking['pickup_date']); ?></p>
                            <p><strong>Return Date:</strong> <?php echo htmlspecialchars($booking['return_date']); ?></p>
                            <p class="status"><strong>Status:</strong> <?php echo htmlspecialchars($booking['pickup_status']); ?> (Pickup) / <?php echo htmlspecialchars($booking['return_status']); ?> (Return)</p>
                            <a href="view_booking_details.php?booking_id=<?php echo $booking['id']; ?>" class="view-more-btn">View More Details</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No bookings found for this customer.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>

<?php
$stmt->close();
$db->close();
?>
