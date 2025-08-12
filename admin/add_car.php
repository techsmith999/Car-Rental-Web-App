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

// Process form submission
$errors = [];
$success = '';

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
    $car_type = $_POST['car_type'];
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
    
    // Handle file upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        // Check if the file is an image
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed";
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) { // 5MB max
            $errors[] = "Image size must be less than 5MB";
        } else {
            $image = file_get_contents($_FILES['image']['tmp_name']);
        }
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        $query = "INSERT INTO cars (make, model, year, color, price_per_day, 
                  availability_status, mileage, fuel_type, car_type, image, 
                  description, location) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssisdssissss", 
            $make, $model, $year, $color, $price_per_day, 
            $availability_status, $mileage, $fuel_type, $car_type, 
            $image, $description, $location);
        
        if ($stmt->execute()) {
            $success = "Vehicle added successfully!";
            // Clear form fields
            $_POST = [];
        } else {
            $errors[] = "Error adding vehicle: " . $db->error;
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Vehicle | AutoAdmin Pro</title>
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

        .image-preview {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            margin-top: 10px;
            border-radius: var(--border-radius);
            display: none;
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
        <h1><i class="bi bi-car-front"></i> Add New Vehicle</h1>
        
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
        
        <form method="POST" action="add_car.php" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="make">Make*</label>
                        <input type="text" id="make" name="make" 
                               value="<?php echo htmlspecialchars($_POST['make'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="model">Model*</label>
                        <input type="text" id="model" name="model" 
                               value="<?php echo htmlspecialchars($_POST['model'] ?? ''); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="year">Year*</label>
                        <input type="number" id="year" name="year" 
                               value="<?php echo htmlspecialchars($_POST['year'] ?? date('Y')); ?>" 
                               min="1900" max="<?php echo date('Y') + 1; ?>" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="color">Color*</label>
                        <input type="text" id="color" name="color" 
                               value="<?php echo htmlspecialchars($_POST['color'] ?? ''); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="price_per_day">Price Per Day ($)*</label>
                        <input type="number" id="price_per_day" name="price_per_day" 
                               value="<?php echo htmlspecialchars($_POST['price_per_day'] ?? ''); ?>" 
                               step="0.01" min="0.01" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="mileage">Mileage (km)*</label>
                        <input type="number" id="mileage" name="mileage" 
                               value="<?php echo htmlspecialchars($_POST['mileage'] ?? '0'); ?>" 
                               min="0" required>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="fuel_type">Fuel Type*</label>
                        <select id="fuel_type" name="fuel_type" required>
                            <option value="petrol" <?php echo ($_POST['fuel_type'] ?? '') == 'petrol' ? 'selected' : ''; ?>>Petrol</option>
                            <option value="diesel" <?php echo ($_POST['fuel_type'] ?? '') == 'diesel' ? 'selected' : ''; ?>>Diesel</option>
                            <option value="electric" <?php echo ($_POST['fuel_type'] ?? '') == 'electric' ? 'selected' : ''; ?>>Electric</option>
                            <option value="hybrid" <?php echo ($_POST['fuel_type'] ?? '') == 'hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                        </select>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="car_type">Vehicle Type*</label>
                        <select id="car_type" name="car_type" required>
                            <option value="sedan" <?php echo ($_POST['car_type'] ?? '') == 'sedan' ? 'selected' : ''; ?>>Sedan</option>
                            <option value="suv" <?php echo ($_POST['car_type'] ?? '') == 'suv' ? 'selected' : ''; ?>>SUV</option>
                            <option value="coupe" <?php echo ($_POST['car_type'] ?? '') == 'coupe' ? 'selected' : ''; ?>>Coupe</option>
                            <option value="hatchback" <?php echo ($_POST['car_type'] ?? '') == 'hatchback' ? 'selected' : ''; ?>>Hatchback</option>
                            <option value="convertible" <?php echo ($_POST['car_type'] ?? '') == 'convertible' ? 'selected' : ''; ?>>Convertible</option>
                            <option value="minivan" <?php echo ($_POST['car_type'] ?? '') == 'minivan' ? 'selected' : ''; ?>>Minivan</option>
                            <option value="truck" <?php echo ($_POST['car_type'] ?? '') == 'truck' ? 'selected' : ''; ?>>Truck</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="availability_status">Availability Status*</label>
                        <select id="availability_status" name="availability_status" required>
                            <option value="available" <?php echo ($_POST['availability_status'] ?? '') == 'available' ? 'selected' : ''; ?>>Available</option>
                            <option value="rented" <?php echo ($_POST['availability_status'] ?? '') == 'rented' ? 'selected' : ''; ?>>Rented</option>
                            <option value="maintenance" <?php echo ($_POST['availability_status'] ?? '') == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="location">Location*</label>
                        <input type="text" id="location" name="location" 
                               value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" 
                          placeholder="Enter vehicle features, condition, etc."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Vehicle Image</label>
                <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                <img id="imagePreview" class="image-preview" alt="Vehicle preview">
            </div>
            
            <button type="submit" class="btn btn-block">
                <i class="bi bi-save"></i> Add Vehicle
            </button>
        </form>
    </div>

    <script>
        // Image preview function
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const file = input.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            
            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
<?php
$db->close();
?>