<?php
session_start();
include('db.php'); // Include your DB connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get user details from database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get recent bookings or other data for the dashboard
$stmt_bookings = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt_bookings->bind_param("i", $user_id);
$stmt_bookings->execute();
$recent_bookings = $stmt_bookings->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Font Awesome Icons -->
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0072ff, #00c6ff); /* Gradient Background */
            color: #fff;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-image: url(images/body.jpg);
            background-size: cover;
        }

        /* Sidebar */
        .sidebar {
            width: 200px;
            height: 100%;
           
            position: fixed;
            top: 0;
            left: 20px;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: left;
            border-right: 2px solid #fff;
            backdrop-filter: blur(10px);
        }

        .sidebar h2 {
            color: #00c6ff; /* Light blue */
            margin-bottom: 30px;
            font-size: 24px;
            letter-spacing: 2px;
        }

        .sidebar a {
            text-decoration: none;
            color: #fff;
            font-size: 18px;
            padding: 15px;
            margin: 10px 0;
            
            text-align: left;
            transition: background-color 0.3s ease;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background-color: #00c6ff; /* Hover Effect */
            color: #000;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 30px;
            
            border-radius: 8px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
        }

        .header {
            background-color: #1f1f1f;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #fff;
            margin: 0;
            font-size: 28px;
        }

        .header .user-info {
            color: #00c6ff;
            font-size: 18px;
            cursor: pointer;
        }

        .dashboard-welcome {
            font-size: 26px;
            margin-bottom: 30px;
            color: #fff;
            font-weight: bold;
        }

        .card {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 25px;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            color: #fff;
        }

        .card h3 {
            font-size: 22px;
            color: #00c6ff;
            margin-bottom: 15px;
        }

        .card ul {
            list-style-type: none;
            padding: 0;
        }

        .card ul li {
            margin: 10px 0;
        }

        .card .btn {
            padding: 10px 15px;
            background-color: #00c6ff;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .card .btn:hover {
            background-color: #0072ff;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .profile-dropdown-content {
            display: none;
            position: absolute;
            background-color: rgba(0, 0, 0, 0.8);
            min-width: 160px;
            color: #fff;
            border-radius: 8px;
            z-index: 1;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.4);
            padding: 10px;
        }

        .profile-dropdown:hover .profile-dropdown-content {
            display: block;
        }

        .profile-dropdown-content a {
            color: #fff;
            padding: 12px;
            text-decoration: none;
            display: block;
        }

        .profile-dropdown-content a:hover {
            background-color: #00c6ff;
            color: #000;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>SAFARI-RENTALS</h2>
    <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
     <a href="available_cars.php"><i class="fas fa-user"></i> Book Car</a>
    <a href="bookings_made.php"><i class="fas fa-calendar-check"></i> My Bookings</a>
    <a href="pay.php"><i class="fas fa-credit-card"></i> Pay </a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">

    <div class="header">
        <h1></h1>
        
        <!-- Profile Dropdown -->
        <div class="profile-dropdown">
            <div class="user-info">
                <i class="fas fa-user"></i> Welcome, <?php echo $user_name; ?>
            </div>
            <div class="profile-dropdown-content">
                <p>Name: <?php echo $user['name']; ?></p>
                <p>Email: <?php echo $user['email']; ?></p>
                <p>Phone: <?php echo $user['phone']; ?></p>
                <a href="edit_profile.php" class="btn">Edit Profile</a>
            </div>
        </div>
    </div>

    <div class="dashboard-welcome">
        Welcome back, <?php echo $user_name; ?>! Here's your personalized dashboard.
    </div>

    <!-- Recent Bookings Card -->
   <div class="card">
    <h3>Recent Bookings</h3>
    <?php
    // Assuming you already have a query to get the recent bookings
    // Modify the query as per your requirements
    $sql = "SELECT b.id, c.make AS car_make, c.model AS car_model, b.pickup_date, b.created_at
            FROM bookings b
            JOIN cars c ON b.car_id = c.id
            WHERE b.user_id = $user_id
            ORDER BY b.created_at DESC
            LIMIT 5"; // Limit to 5 recent bookings

    $recent_bookings = $conn->query($sql);
    
    if ($recent_bookings->num_rows > 0): ?>
        <ul>
            <?php while ($booking = $recent_bookings->fetch_assoc()): ?>
                <li>
                    <span class="activity-text">Car: <?php echo $booking['car_make'] . ' ' . $booking['car_model']; ?> - Pickup: <?php echo $booking['pickup_date']; ?></span>
                    <span class="activity-date"><?php echo $booking['created_at']; ?></span>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No recent bookings found.</p>
    <?php endif; ?>
</div>


</div>

</body>
</html>
