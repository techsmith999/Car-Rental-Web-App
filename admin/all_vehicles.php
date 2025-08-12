<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_auth.php");
    exit();
}

// Database connection
$db = new mysqli('localhost', 'root', '', 'car_rental');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Pagination variables
$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Search and filter variables
$search = isset($_GET['search']) ? $db->real_escape_string($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $db->real_escape_string($_GET['status']) : '';
$type_filter = isset($_GET['type']) ? $db->real_escape_string($_GET['type']) : '';
$fuel_filter = isset($_GET['fuel']) ? $db->real_escape_string($_GET['fuel']) : '';

// Base query
$query = "SELECT * FROM cars WHERE 1=1";
$count_query = "SELECT COUNT(*) as total FROM cars WHERE 1=1";

// Add search condition
if (!empty($search)) {
    $query .= " AND (make LIKE '%$search%' OR model LIKE '%$search%' OR color LIKE '%$search%')";
    $count_query .= " AND (make LIKE '%$search%' OR model LIKE '%$search%' OR color LIKE '%$search%')";
}

// Add filters
if (!empty($status_filter)) {
    $query .= " AND availability_status = '$status_filter'";
    $count_query .= " AND availability_status = '$status_filter'";
}

if (!empty($type_filter)) {
    $query .= " AND car_type = '$type_filter'";
    $count_query .= " AND car_type = '$type_filter'";
}

if (!empty($fuel_filter)) {
    $query .= " AND fuel_type = '$fuel_filter'";
    $count_query .= " AND fuel_type = '$fuel_filter'";
}

// Add pagination
$query .= " LIMIT $start_from, $results_per_page";

// Execute queries
$result = $db->query($query);
$count_result = $db->query($count_query);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $results_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Vehicles | RentalPro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        /* Include your existing styles here */
        
        .vehicles-container {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        
        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .search-bar {
            display: flex;
            margin-bottom: 20px;
        }
        
        .search-bar input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--border-radius) 0 0 var(--border-radius);
            background: rgba(0, 0, 0, 0.3);
            color: var(--text-light);
        }
        
        .search-bar button {
            padding: 0 20px;
            background: var(--accent-color);
            border: none;
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
            color: white;
            cursor: pointer;
        }
        
        .vehicles-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .vehicles-table th, .vehicles-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .vehicles-table th {
            background: rgba(0, 0, 0, 0.3);
            font-weight: 500;
        }
        
        .vehicles-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .status-available {
            color: var(--success-color);
        }
        
        .status-rented {
            color: var(--warning-color);
        }
        
        .status-maintenance {
            color: var(--danger-color);
        }
        
        .action-btns {
            display: flex;
            gap: 10px;
        }
        
        .action-btn {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
            border: none;
        }
        
        .edit-btn {
            background: var(--accent-color);
            color: white;
        }
        
        .delete-btn {
            background: var(--danger-color);
            color: white;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 5px;
        }
        
        .pagination a, .pagination span {
            padding: 8px 15px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            text-decoration: none;
        }
        
        .pagination a:hover {
            background: var(--accent-color);
        }
        
        .pagination .current {
            background: var(--accent-color);
            font-weight: bold;
        }
        
        .no-vehicles {
            text-align: center;
            padding: 30px;
            color: var(--text-muted);
        }
        
        .vehicle-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <div class="main-content">
        <div class="navbar">
            <h3><i class="bi bi-car-front"></i> All Vehicles</h3>
            <div class="user-profile" id="userProfile">
                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Admin">
                <span><?php echo $_SESSION['admin_name'] ?? 'Admin User'; ?></span>
                <i class="bi bi-chevron-down"></i>
                <ul class="user-dropdown" id="userDropdown">
                    <li><i class="bi bi-person"></i> Profile</li>
                    <li><i class="bi bi-gear"></i> Settings</li>
                    <li><i class="bi bi-box-arrow-right"></i> Logout</li>
                </ul>
            </div>
        </div>
        
        <div class="vehicles-container">
            <div class="search-bar">
                <form method="GET" action="all_vehicles.php" style="display: flex; width: 100%;">
                    <input type="text" name="search" placeholder="Search by make, model or color..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>
            
            <div class="filters">
                <div class="filter-group">
                    <label>Status</label>
                    <select onchange="window.location.href=updateQueryString('status', this.value)" 
                            class="filter-select">
                        <option value="">All Statuses</option>
                        <option value="available" <?php echo $status_filter == 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="rented" <?php echo $status_filter == 'rented' ? 'selected' : ''; ?>>Rented</option>
                        <option value="maintenance" <?php echo $status_filter == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Vehicle Type</label>
                    <select onchange="window.location.href=updateQueryString('type', this.value)" 
                            class="filter-select">
                        <option value="">All Types</option>
                        <option value="sedan" <?php echo $type_filter == 'sedan' ? 'selected' : ''; ?>>Sedan</option>
                        <option value="suv" <?php echo $type_filter == 'suv' ? 'selected' : ''; ?>>SUV</option>
                        <option value="coupe" <?php echo $type_filter == 'coupe' ? 'selected' : ''; ?>>Coupe</option>
                        <option value="hatchback" <?php echo $type_filter == 'hatchback' ? 'selected' : ''; ?>>Hatchback</option>
                        <option value="convertible" <?php echo $type_filter == 'convertible' ? 'selected' : ''; ?>>Convertible</option>
                        <option value="minivan" <?php echo $type_filter == 'minivan' ? 'selected' : ''; ?>>Minivan</option>
                        <option value="truck" <?php echo $type_filter == 'truck' ? 'selected' : ''; ?>>Truck</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Fuel Type</label>
                    <select onchange="window.location.href=updateQueryString('fuel', this.value)" 
                            class="filter-select">
                        <option value="">All Fuel Types</option>
                        <option value="petrol" <?php echo $fuel_filter == 'petrol' ? 'selected' : ''; ?>>Petrol</option>
                        <option value="diesel" <?php echo $fuel_filter == 'diesel' ? 'selected' : ''; ?>>Diesel</option>
                        <option value="electric" <?php echo $fuel_filter == 'electric' ? 'selected' : ''; ?>>Electric</option>
                        <option value="hybrid" <?php echo $fuel_filter == 'hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                    </select>
                </div>
                
                <div class="filter-group" style="align-self: flex-end;">
                    <a href="add_car.php" class="btn">
                        <i class="bi bi-plus-circle"></i> Add New Vehicle
                    </a>
                </div>
            </div>
            
            <?php if ($result->num_rows > 0): ?>
                <table class="vehicles-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Make & Model</th>
                            <th>Year</th>
                            <th>Color</th>
                            <th>Type</th>
                            <th>Fuel</th>
                            <th>Price/Day</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['image']); ?>" 
                                             class="vehicle-image" alt="<?php echo $row['make'] . ' ' . $row['model']; ?>">
                                    <?php else: ?>
                                        <div style="width: 80px; height: 60px; background: rgba(255,255,255,0.1); 
                                                    display: flex; align-items: center; justify-content: center;
                                                    border-radius: 4px;">
                                            <i class="bi bi-car-front" style="font-size: 1.5rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['make']); ?></strong><br>
                                    <?php echo htmlspecialchars($row['model']); ?>
                                </td>
                                <td><?php echo $row['year']; ?></td>
                                <td><?php echo htmlspecialchars($row['color']); ?></td>
                                <td><?php echo ucfirst($row['car_type']); ?></td>
                                <td><?php echo ucfirst($row['fuel_type']); ?></td>
                                <td>$<?php echo number_format($row['price_per_day'], 2); ?></td>
                                <td>
                                    <span class="status-<?php echo $row['availability_status']; ?>">
                                        <?php echo ucfirst($row['availability_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="edit_car.php?id=<?php echo $row['id']; ?>" 
                                           class="action-btn edit-btn">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="delete_car.php?id=<?php echo $row['id']; ?>" 
                                           class="action-btn delete-btn"
                                           onclick="return confirm('Are you sure you want to delete this vehicle?');">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="<?php echo updateQueryString('page', $page - 1); ?>">&laquo; Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="<?php echo updateQueryString('page', $i); ?>" 
                           <?php echo $i == $page ? 'class="current"' : ''; ?>>
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="<?php echo updateQueryString('page', $page + 1); ?>">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-vehicles">
                    <i class="bi bi-car-front" style="font-size: 3rem;"></i>
                    <h3>No vehicles found</h3>
                    <p>Try adjusting your search or filters</p>
                    <a href="add_car.php" class="btn" style="margin-top: 15px;">
                        <i class="bi bi-plus-circle"></i> Add Your First Vehicle
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Function to update query string for filters and pagination
        function updateQueryString(key, value) {
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);
            
            if (value === '') {
                params.delete(key);
            } else {
                params.set(key, value);
            }
            
            // Reset to page 1 when changing filters
            if (key !== 'page') {
                params.set('page', '1');
            }
            
            return url.pathname + '?' + params.toString();
        }
        
        // Initialize sidebar and dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            // User dropdown toggle
            const userProfile = document.getElementById('userProfile');
            const userDropdown = document.getElementById('userDropdown');
            
            userProfile.addEventListener('click', function(e) {
                e.stopPropagation();
                this.classList.toggle('active');
                userDropdown.classList.toggle('show');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                userProfile.classList.remove('active');
                userDropdown.classList.remove('show');
            });
        });
    </script>
</body>
</html>