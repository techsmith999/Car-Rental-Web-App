<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Car Rental System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Chart.js for Real-Time Animations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <style>
        :root {
            --bg-color: #f8f9fa;
            --text-color: #343a40;
            --card-bg: white;
            --sidebar-bg: #343a40;
            --sidebar-text: white;
            --primary-color: #007bff;
            --hover-color: #495057;
        }

        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --text-color: #f8f9fa;
            --card-bg: #2d2d2d;
            --sidebar-bg: #212529;
            --sidebar-text: #f8f9fa;
            --primary-color: #0d6efd;
            --hover-color: #3d3d3d;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar {
            height: 100vh;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            padding: 20px;
            position: fixed;
            width: 250px;
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar a {
            color: var(--sidebar-text);
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background: var(--hover-color);
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .card {
            background: var(--card-bg);
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, background-color 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-icon {
            font-size: 2rem;
            color: var(--primary-color);
        }

        .chart-container {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s;
        }

        .theme-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body data-theme="light">
    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center mb-4">Car Rental Admin</h3>
        <a href="dashboard.php" class="active">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="manage_cars.php">
            <i class="fas fa-car"></i> Manage Cars
        </a>
        <a href="manage_bookings.php">
            <i class="fas fa-calendar-check"></i> Manage Bookings
        </a>
        <a href="manage_users.php">
            <i class="fas fa-users"></i> Manage Users
        </a>
        <a href="reports.php">
            <i class="fas fa-chart-line"></i> Reports
        </a>
        <a href="settings.php">
            <i class="fas fa-cog"></i> Settings
        </a>
        <a href="logout.php" class="mt-5">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Dashboard</h1>
            <div class="btn-group">
                <button class="btn btn-primary"><i class="fas fa-bell"></i></button>
                <button class="btn btn-primary"><i class="fas fa-user"></i> Admin</button>
            </div>
        </div>