<?php
include 'db.php'; // Include database connection

// Assuming you have a session for logged-in users
session_start();
$user_id = $_SESSION['user_id']; // Get the logged-in user's ID from session

// Fetch user's bookings from the database
$sql = "SELECT b.id, c.make AS car_make, c.model AS car_model, b.pickup_date, b.return_date, 
               b.pickup_status, b.return_status
        FROM bookings b
        JOIN cars c ON b.car_id = c.id
        WHERE b.user_id = $user_id
        ORDER BY b.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Bookings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            background-image: url(images/login.jpg);
            background-size: cover;
        }
        .container {
            max-width: 1000px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #42c8d7ff;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f4f4f4;
        }
        .status {
            font-weight: bold;
        }
        .status.pending {
            color: red;
        }
        .status.picked_up {
            color: green;
        }
        .status.returned {
            color: blue;
        }
        .print-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .print-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Your Bookings</h1>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Car</th>
                        <th>Pickup Date</th>
                        <th>Return Date</th>
                        <th>Pickup Status</th>
                        <th>Return Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['car_make'] . ' ' . $row['car_model']) ?></td>
                            <td><?= htmlspecialchars($row['pickup_date']) ?></td>
                            <td><?= htmlspecialchars($row['return_date']) ?></td>
                            <td class="status <?= htmlspecialchars($row['pickup_status']) ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $row['pickup_status']))) ?></td>
                            <td class="status <?= htmlspecialchars($row['return_status']) ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $row['return_status']))) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No bookings found.</p>
        <?php endif; ?>

        <button class="print-btn" onclick="window.print()">Print Bookings</button>
    </div>

</body>
</html>

<?php $conn->close(); // Close database connection ?>
