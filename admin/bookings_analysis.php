<?php
// Database connection
$servername = "localhost";
$username = "root";  // your MySQL username
$password = "";      // your MySQL password
$dbname = "car_rental";  // your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Default: show the rentals in the last 6 months
$start_date = date('Y-m-01', strtotime('-6 months')); // 6 months ago from the first of the month
$end_date = date('Y-m-t'); // Current month, last day

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update the date range based on admin input
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
}

// Fetch car rental data (number of rentals per month)
$query = "
    SELECT 
        DATE_FORMAT(pickup_date, '%Y-%m') AS month,
        COUNT(*) AS rental_count
    FROM bookings
    WHERE pickup_date BETWEEN '$start_date' AND '$end_date'
    GROUP BY month
    ORDER BY month ASC
";
$result = mysqli_query($conn, $query);

// Prepare data for Chart.js
$months = [];
$rental_counts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $months[] = $row['month'];
    $rental_counts[] = $row['rental_count'];
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Rental Analysis</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 50px auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .date-range-form {
            margin-bottom: 30px;
            text-align: center;
        }

        .date-range-form input {
            padding: 8px;
            margin: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .chart-container {
            width: 100%;
            height: 400px;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #333;
            color: white;
            margin-top: 50px;
        }

        .go-home-btn {
            display: block;
            margin: 30px auto;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            font-size: 18px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            width: 200px;
        }

        .go-home-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Car Rental Analysis</h1>

        <!-- Date Range Filter Form -->
        <div class="date-range-form">
            <form action="" method="POST">
                <input type="date" name="start_date" value="<?php echo $start_date; ?>" required>
                <input type="date" name="end_date" value="<?php echo $end_date; ?>" required>
                <button type="submit">Filter</button>
            </form>
        </div>

        <!-- Chart Container -->
        <div class="chart-container">
            <canvas id="rentalChart"></canvas>
        </div>

        <!-- Go Back Home Button -->
        <a href="index.php" class="go-home-btn">Go Back Home</a>
    </div>

    <footer>
        <p>&copy; 2025 Car Rental. All rights reserved.</p>
    </footer>

    <script>
        // Chart.js configuration
        var ctx = document.getElementById('rentalChart').getContext('2d');
        var rentalChart = new Chart(ctx, {
            type: 'bar', // You can change this to 'line' for a line chart
            data: {
                labels: <?php echo json_encode($months); ?>,  // Months
                datasets: [{
                    label: 'Cars Rented',
                    data: <?php echo json_encode($rental_counts); ?>,  // Rental counts
                    backgroundColor: 'rgba(0, 123, 255, 0.6)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html>
