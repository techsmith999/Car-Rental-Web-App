<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // If not logged in, redirect to the login page
    header("Location: admin_auth.php");
    exit();
}

// Include your database connection
include('db.php'); // Make sure this file contains your database connection details

// Query to get the total number of cars
$totalCarsQuery = "SELECT COUNT(*) as total_cars FROM cars";
$totalCarsResult = mysqli_query($conn, $totalCarsQuery);
$totalCarsData = mysqli_fetch_assoc($totalCarsResult);
$totalCars = $totalCarsData['total_cars'];

// Query to get the number of available cars
$availableCarsQuery = "SELECT COUNT(*) as available_cars FROM cars WHERE availability_status = 'available'";
$availableCarsResult = mysqli_query($conn, $availableCarsQuery);
$availableCarsData = mysqli_fetch_assoc($availableCarsResult);
$availableCars = $availableCarsData['available_cars'];

// Query to get the total number of bookings
$totalBookingsQuery = "SELECT COUNT(*) as total_bookings FROM bookings";
$totalBookingsResult = mysqli_query($conn, $totalBookingsQuery);
$totalBookingsData = mysqli_fetch_assoc($totalBookingsResult);
$totalBookings = $totalBookingsData['total_bookings'];

// Query to get the number of pending bookings
$pendingBookingsQuery = "SELECT COUNT(*) as pending_bookings FROM bookings WHERE pickup_status = 'pending'";
$pendingBookingsResult = mysqli_query($conn, $pendingBookingsQuery);
$pendingBookingsData = mysqli_fetch_assoc($pendingBookingsResult);
$pendingBookings = $pendingBookingsData['pending_bookings'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Dealership Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #1e3c72;
            --secondary-color: #2a5298;
            --accent-color: #4a90e2;
            --dark-bg: rgba(0, 0, 0, 0.8);
            --card-bg: rgba(255, 255, 255, 0.15);
            --text-light: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --border-radius: 10px;
            --sidebar-width: 280px;
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--dark-bg);
            padding: 20px 0;
            position: fixed;
            z-index: 100;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .sidebar-header h2 {
            margin-left: 10px;
            font-size: 1.5rem;
        }

        .sidebar-header i {
            font-size: 1.8rem;
            color: var(--accent-color);
        }

        .sidebar ul {
            list-style: none;
            padding: 0 10px;
        }

        .sidebar ul li {
            padding: 12px 15px;
            border-radius: var(--border-radius);
            margin-bottom: 5px;
        }

        .sidebar ul li:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .sidebar ul li.active {
            background: var(--accent-color);
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            color: var(--text-light);
            text-decoration: none;
        }

        .sidebar ul li i {
            margin-right: 12px;
            width: 24px;
            text-align: center;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            width: calc(100% - var(--sidebar-width));
        }

        .navbar {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
        }

        .analytics-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            color: var(--text-light);
            text-align: center;
        }

        .analytics-card h5 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .analytics-card .card-title {
            font-size: 1rem;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-car-front-fill"></i>
            <h2>AutoAdmin Pro</h2>
        </div>
        <ul>
            <li class="active"><a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="manage_cars.php"><i class="bi bi-car-front"></i> Vehicle Inventory</a></li>
            <li><a href="manage_users.php"><i class="bi bi-people"></i> Customers</a></li>
            <li><a href="bookings_analysis.php"><i class="bi bi-graph-up"></i> Sales Analytics</a></li>
            <li><a href="manage_bookings.php"><i class="bi bi-book"></i> manage bookings</a></li>
            <li><a href="logout.php"><i class="bi bi-lock"></i> logout </a></li>
            <li><a href="transactions.php"><i class="bi bi-lock"></i> transactions </a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="navbar">
            <h3>Dashboard Overview</h3>
        </div>
        <!-- Analytics Container -->
        <div class="row">
            <!-- Total Cars -->
            <div class="col-md-3">
                <div class="analytics-card">
                    <h5><?php echo $totalCars; ?></h5>
                    <div class="card-title">Total Cars</div>
                </div>
            </div>
            <!-- Available Cars -->
            <div class="col-md-3">
                <div class="analytics-card">
                    <h5><?php echo $availableCars; ?></h5>
                    <div class="card-title">Available Cars</div>
                </div>
            </div>
            <!-- Total Bookings -->
            <div class="col-md-3">
                <div class="analytics-card">
                    <h5><?php echo $totalBookings; ?></h5>
                    <div class="card-title">Total Bookings</div>
                </div>
            </div>
            <!-- Pending Bookings -->
            <div class="col-md-3">
                <div class="analytics-card">
                    <h5><?php echo $pendingBookings; ?></h5>
                    <div class="card-title">Pending Bookings</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
