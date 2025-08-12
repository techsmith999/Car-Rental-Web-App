<?php
session_start();
// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_auth.php");
    exit();
}
// Database connection
$conn = new mysqli("localhost", "root", "", "car_rental");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM mpesa_transactions ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>MPESA Transactions</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>MPESA Transactions</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Phone</th>
            <th>Amount</th>
            <th>Mpesa Code</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
       <?php while ($row = $result->fetch_assoc()) : ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['phone_number'] ?></td>
        <td><?= $row['amount'] ?></td>
        <td><?= $row['mpesa_receipt_number'] ?></td>
        <td><?= $row['transaction_date'] ?></td>
        <td><?= $row['result_desc'] ?></td>
    </tr>
<?php endwhile; ?>

    </table>
</body>
</html>

<?php $conn->close(); ?>
