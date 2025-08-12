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

// Initialize variables
$errors = [];
$success = '';
$vehicle = null;

// Get vehicle ID from URL
$car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch vehicle data
if ($car_id > 0) {
    $query = "SELECT * FROM cars WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicle = $result->fetch_assoc();
    $stmt->close();
    
    if (!$vehicle) {
        header("Location: manage_cars.php");
        exit();
    }
} else {
    header("Location: manage_cars.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $make = trim($_POST['make']);
    $model = trim($_POST['model']);
    $year = intval($_POST['year']);
    $color = trim($_POST['color']);
    $price_per_day = floatval($_POST['price_per_day']);
    $availability_status = $_POST['availability_status'];
    $mileage = intval($_POST['mileage']);
    $fuel_type = $_POST['fuel_type'];
    $car_type = strtolower(trim($_POST['car_type']));
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    
    // Validate required fields
    if (empty($make)) $errors[] = "Make is required";
    if (empty($model)) $errors[] = "Model is required";
    if ($year < 1900 || $year > date('Y') + 1) $errors[] = "Invalid year";
    if (empty($color)) $errors[] = "Color is required";
    if ($price_per_day <= 0) $errors[] = "Price per day must be positive";
    if ($mileage < 0) $errors[] = "Mileage cannot be negative";
    if (empty($location)) $errors[] = "Location is required";
    
    // Validate car type against allowed values
    $allowed_types = ['sedan', 'suv', 'coupe', 'hatchback', 'convertible', 'minivan', 'truck'];
    if (!in_array($car_type, $allowed_types)) {
        $errors[] = "Invalid vehicle type. Please choose from: " . implode(', ', $allowed_types);
    }
    
    // Handle file upload
    $image = $vehicle['image']; // Keep existing image by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed";
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $errors[] = "Image size must be less than 5MB";
        } else {
            $image = file_get_contents($_FILES['image']['tmp_name']);
        }
    }
    
    // If no errors, update database
    if (empty($errors)) {
        $query = "UPDATE cars SET 
                  make = ?, model = ?, year = ?, color = ?, 
                  price_per_day = ?, availability_status = ?, 
                  mileage = ?, fuel_type = ?, car_type = ?, 
                  image = ?, description = ?, location = ? 
                  WHERE id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssisdssissssi", 
            $make, $model, $year, $color, $price_per_day, 
            $availability_status, $mileage, $fuel_type, $car_type, 
            $image, $description, $location, $car_id);
        
        if ($stmt->execute()) {
            $success = "Vehicle updated successfully!";
            // Refresh vehicle data
            $query = "SELECT * FROM cars WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $car_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $vehicle = $result->fetch_assoc();
            $stmt->close();
        } else {
            $errors[] = "Error updating vehicle: " . $db->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vehicle | AutoAdmin Pro</title>
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
            max-width: 800px;
            margin: 0 auto;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--accent-color);
            text-decoration: none;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-col {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--border-radius);
            background: rgba(0, 0, 0, 0.3);
            color: var(--text-light);
            font-size: 1rem;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--accent-color);
            background: rgba(0, 0, 0, 0.5);
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .image-preview-container {
            margin-bottom: 20px;
        }

        .image-preview {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            margin-top: 10px;
            border-radius: var(--border-radius);
        }

        .no-image {
            height: 200px;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--border-radius);
            margin-top: 10px;
        }

        .btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #3a9cf5;
            transform: translateY(-2px);
        }

        .btn-block {
            display: block;
            width: 100%;
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

        .error-list {
            margin-bottom: 20px;
            color: var(--danger-color);
        }

        .error-list li {
            margin-left: 20px;
        }

        .text-muted {
            color: var(--text-muted);
            font-size: 0.85rem;
            display: block;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="manage_cars.php" class="back-btn">
            <i class="bi bi-arrow-left"></i> Back to Vehicles
        </a>
        
        <h1><i class="bi bi-car-front"></i> Edit Vehicle</h1>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="edit_car.php?id=<?php echo $car_id; ?>" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="make">Make*</label>
                        <input type="text" id="make" name="make" 
                               value="<?php echo htmlspecialchars($vehicle['make']); ?>" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="model">Model*</label>
                        <input type="text" id="model" name="model" 
                               value="<?php echo htmlspecialchars($vehicle['model']); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="year">Year*</label>
                        <input type="number" id="year" name="year" 
                               value="<?php echo htmlspecialchars($vehicle['year']); ?>" 
                               min="1900" max="<?php echo date('Y') + 1; ?>" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="color">Color*</label>
                        <input type="text" id="color" name="color" 
                               value="<?php echo htmlspecialchars($vehicle['color']); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="price_per_day">Price Per Day ($)*</label>
                        <input type="number" id="price_per_day" name="price_per_day" 
                               value="<?php echo htmlspecialchars($vehicle['price_per_day']); ?>" 
                               step="0.01" min="0.01" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="mileage">Mileage (km)*</label>
                        <input type="number" id="mileage" name="mileage" 
                               value="<?php echo htmlspecialchars($vehicle['mileage']); ?>" 
                               min="0" required>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="fuel_type">Fuel Type*</label>
                        <select id="fuel_type" name="fuel_type" required>
                            <option value="petrol" <?php echo $vehicle['fuel_type'] == 'petrol' ? 'selected' : ''; ?>>Petrol</option>
                            <option value="diesel" <?php echo $vehicle['fuel_type'] == 'diesel' ? 'selected' : ''; ?>>Diesel</option>
                            <option value="electric" <?php echo $vehicle['fuel_type'] == 'electric' ? 'selected' : ''; ?>>Electric</option>
                            <option value="hybrid" <?php echo $vehicle['fuel_type'] == 'hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                        </select>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="car_type">Vehicle Type*</label>
                        <input type="text" id="car_type" name="car_type" 
                               value="<?php echo htmlspecialchars($vehicle['car_type']); ?>" 
                               list="car_type_examples" required>
                        <datalist id="car_type_examples">
                            <option value="sedan">
                            <option value="suv">
                            <option value="coupe">
                            <option value="hatchback">
                            <option value="convertible">
                            <option value="minivan">
                            <option value="truck">
                        </datalist>
                        <small class="text-muted">Examples: sedan, suv, coupe, hatchback, convertible, minivan, truck</small>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="availability_status">Availability Status*</label>
                        <select id="availability_status" name="availability_status" required>
                            <option value="available" <?php echo $vehicle['availability_status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                            <option value="rented" <?php echo $vehicle['availability_status'] == 'rented' ? 'selected' : ''; ?>>Rented</option>
                            <option value="maintenance" <?php echo $vehicle['availability_status'] == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="location">Location*</label>
                        <input type="text" id="location" name="location" 
                               value="<?php echo htmlspecialchars($vehicle['location']); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" 
                          placeholder="Enter vehicle features, condition, etc."><?php echo htmlspecialchars($vehicle['description']); ?></textarea>
            </div>
            
            <div class="form-group image-preview-container">
                <label>Current Image</label>
                <?php if (!empty($vehicle['image'])): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($vehicle['image']); ?>" 
                         class="image-preview" alt="Current vehicle image">
                <?php else: ?>
                    <div class="no-image">
                        <i class="bi bi-car-front" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="image">Update Image (optional)</label>
                <input type="file" id="image" name="image" accept="image/*">
                <small class="text-muted">Leave blank to keep current image</small>
            </div>
            
            <button type="submit" class="btn btn-block">
                <i class="bi bi-save"></i> Update Vehicle
            </button>
        </form>
    </div>
</body>
</html>
<?php
$db->close();
?>