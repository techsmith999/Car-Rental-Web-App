<?php
session_start();
include("db.php"); // Ensure this points to your correct db.php

// Fetch all available cars
$query = "SELECT * FROM cars WHERE availability_status = 'available'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Cars</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            padding: 20px;
            background-image: url(images/carback1.jpg);
            background-size: cover;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .car-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .car-box {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            width: 300px;
            overflow: hidden;
            transition: transform 0.2s;
            background :none;
            backdrop-filter: blur(50px);
        }

        .car-box:hover {
            transform: scale(1.02);
        }

        .car-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .car-details {
            padding: 15px;
            
        }

        .car-details h3 {
            margin: 0 0 10px;
        }

        .car-details p {
            margin: 5px 0;
        }

        .car-details form {
            margin-top: 15px;
            text-align: center;
        }

        .car-details button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .car-details button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<h2>Available Cars</h2>

<div class="car-container">
<?php while ($car = mysqli_fetch_assoc($result)) { ?>
    <div class="car-box">
        <img class="car-image" src="images/<?= htmlspecialchars($car['image']) ?>" alt="<?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?>">
        <div class="car-details">
            <h3><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?></h3>
            <p><strong>Price per day:</strong> <?= number_format($car['price_per_day'], 2) ?> KES</p>
            <p><strong>Year:</strong> <?= htmlspecialchars($car['year']) ?></p>
            <p><strong>Color:</strong> <?= htmlspecialchars($car['color']) ?></p>

            <form action="book_car.php" method="GET">
    <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
    <input type="hidden" name="amount" value="<?= $car['price_per_day'] ?>">

    <label>Pickup Date:</label><br>
    <input type="date" name="pickup_date" required><br>

    <label>Return Date:</label><br>
    <input type="date" name="return_date" required><br>

    <label>Phone Number:</label><br>
    <input type="text" name="phone" placeholder="07XXXXXXXX" required><br><br>

    <button type="submit">Book this car</button>
</form>

        </div>
    </div>
<?php } ?>
</div>

</body>
</html>
