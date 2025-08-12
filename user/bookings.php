<?php
session_start();
include 'db.php'; // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get logged-in user ID

// Fetch user bookings
$query = $conn->prepare("
    SELECT bookings.id, cars.model AS car_model, bookings.pickup_date, 
           bookings.return_date, bookings.status
    FROM bookings 
    JOIN cars ON bookings.car_id = cars.id
    WHERE bookings.user_id = ?
    ORDER BY bookings.pickup_date ASC
");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Car Bookings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: linear-gradient(to right, #1e3c72, #2a5298);
            font-family: Arial, sans-serif;
            color: white;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            padding: 20px;
            border-radius: 15px;
            background: white;
            color: black;
            animation: fadeIn 1s ease-in-out;
        }
        .countdown {
            font-size: 18px;
            font-weight: bold;
            color: #ff5733;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">My Car Bookings</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <table class="table table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Car</th>
                                <th>Pickup Date</th>
                                <th>Return Date</th>
                                <th>Time Remaining</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0) : ?>
                                <?php $count = 1; while ($row = $result->fetch_assoc()) : ?>
                                    <tr>
                                        <td><?= $count++; ?></td>
                                        <td><?= htmlspecialchars($row['car_model']); ?></td>
                                        <td><?= htmlspecialchars($row['pickup_date']); ?></td>
                                        <td><?= htmlspecialchars($row['return_date']); ?></td>
                                        <td class="countdown" data-end="<?= $row['return_date']; ?>"></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5">No bookings found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateCountdown() {
            document.querySelectorAll(".countdown").forEach(function(el) {
                let endDate = new Date(el.getAttribute("data-end")).getTime();
                let now = new Date().getTime();
                let diff = endDate - now;

                if (diff <= 0) {
                    el.innerHTML = "<span class='text-danger'>Expired</span>";
                } else {
                    let days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    let hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    el.innerHTML = `<span class='text-success'>${days}d ${hours}h ${minutes}m ${seconds}s</span>`;
                }
            });
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    </script>
</body>
</html>
