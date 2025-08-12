<?php
// Connect to DB
$conn = new mysqli("localhost", "root", "", "car_rental");

// Check if car_id is provided
if (!isset($_GET['car_id'])) {
    die("No car selected.");
}

$amount = (int) round($car['price_per_day']);
$sql = "SELECT * FROM cars WHERE id = $car_id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("Car not found.");
}

$car = $result->fetch_assoc(); // ✅ THIS was missing

// Correctly convert price_per_day to integer amount
$amount = (int) floatval($car['price_per_day']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pay with M-PESA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .pay-container {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
<div class="pay-container">
    <h2>Pay with M-PESA</h2>
    <form action="api/stk_push.php" method="POST">
        <input type="hidden" name="car_id" value="<?= $car_id ?>">
        <input type="hidden" name="amount" value="<?= (int) $amount ?>">


        <label for="phone">Phone Number</label>
        <input type="text" name="phone" placeholder="07XXXXXXXX" required>

        <label for="amount">Amount (KES)</label>
        <input type="number" value="<?= $amount ?>" readonly>

        <button type="submit">Pay Now</button>
    </form>
</div>
</body>
</html>
