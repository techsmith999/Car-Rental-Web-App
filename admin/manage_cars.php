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

// Handle delete action
if (isset($_GET['delete'])) {
    $car_id = intval($_GET['delete']);
    $query = "DELETE FROM cars WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $car_id);
    if ($stmt->execute()) {
        $success = "Vehicle deleted successfully!";
    } else {
        $error = "Error deleting vehicle: " . $db->error;
    }
    $stmt->close();
}

// Fetch all vehicles from database
$vehicles = [];
$query = "SELECT * FROM cars ORDER BY created_at DESC";
$result = $db->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vehicles | AutoAdmin Pro</title>
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
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --border-radius: 10px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            min-height: 100vh;
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

        h1 {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .add-btn {
            background-color: var(--success-color);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .add-btn:hover {
            opacity: 0.9;
        }

        .vehicles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .vehicle-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 20px;
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }

        .vehicle-card:hover {
            transform: translateY(-5px);
        }

        .vehicle-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
        }

        .no-image {
            height: 180px;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
        }

        .vehicle-card h3 {
            margin-bottom: 10px;
            color: var(--accent-color);
        }

        .vehicle-card p {
            margin-bottom: 8px;
        }

        .status-available {
            color: var(--success-color);
            font-weight: 500;
        }

        .status-rented {
            color: var(--warning-color);
            font-weight: 500;
        }

        .status-maintenance {
            color: var(--danger-color);
            font-weight: 500;
        }

        .vehicle-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .edit-btn {
            background-color: var(--accent-color);
            color: white;
        }

        .delete-btn {
            background-color: var(--danger-color);
            color: white;
        }

        .alert {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid var(--success-color);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid var(--danger-color);
        }

        @media (max-width: 768px) {
            .vehicles-grid {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="bi bi-car-front"></i> Manage Vehicles</h1>
            <a href="add_car.php" class="add-btn">
                <i class="bi bi-plus-circle"></i> Add New Vehicle
            </a>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (count($vehicles) > 0): ?>
            <div class="vehicles-grid">
                <?php foreach ($vehicles as $vehicle): ?>
                    <div class="vehicle-card">
                        <?php if (!empty($vehicle['image'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($vehicle['image']); ?>" 
                                 class="vehicle-image" alt="<?php echo htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model']); ?>">
                        <?php else: ?>
                            <div class="no-image">
                                <i class="bi bi-car-front" style="font-size: 3rem; opacity: 0.5;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <h3><?php echo htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model'] . ' (' . $vehicle['year'] . ')'); ?></h3>
                        <p><strong>Color:</strong> <?php echo htmlspecialchars($vehicle['color']); ?></p>
                        <p><strong>Price/Day:</strong> $<?php echo number_format($vehicle['price_per_day'], 2); ?></p>
                        <p><strong>Status:</strong> 
                            <span class="status-<?php echo $vehicle['availability_status']; ?>">
                                <?php echo ucfirst($vehicle['availability_status']); ?>
                            </span>
                        </p>
                        <p><strong>Type:</strong> <?php echo ucfirst($vehicle['car_type']); ?></p>
                        <p><strong>Fuel:</strong> <?php echo ucfirst($vehicle['fuel_type']); ?></p>
                        <p><strong>Mileage:</strong> <?php echo number_format($vehicle['mileage']); ?> km</p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($vehicle['location']); ?></p>
                        
                        <?php if (!empty($vehicle['description'])): ?>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($vehicle['description']); ?></p>
                        <?php endif; ?>
                        
                        <div class="vehicle-actions">
                            <a href="edit_car.php?id=<?php echo $vehicle['id']; ?>" class="action-btn edit-btn">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <button class="action-btn delete-btn" 
                                    onclick="confirmDelete(<?php echo $vehicle['id']; ?>)">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px;">
                <i class="bi bi-car-front" style="font-size: 3rem; opacity: 0.5;"></i>
                <h3>No vehicles found</h3>
                <p>Add your first vehicle to get started</p>
                <a href="add_car.php" class="add-btn" style="display: inline-flex; margin-top: 20px;">
                    <i class="bi bi-plus-circle"></i> Add Vehicle
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function confirmDelete(carId) {
            if (confirm('Are you sure you want to delete this vehicle? This action cannot be undone.')) {
                window.location.href = 'manage_cars.php?delete=' + carId;
            }
        }
    </script>
</body>
</html>
<?php
$db->close();
?>