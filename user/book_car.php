<?php
session_start();
include("db.php");

$user_id = $_SESSION['user_id'];
$car_id = $_GET['car_id'];
$pickup_date = $_GET['pickup_date'];
$return_date = $_GET['return_date'];
$phone = $_GET['phone'];
$amount = $_GET['amount'];

// Format phone to international
$phone = preg_replace('/^0/', '254', $phone);

// Insert pending booking first (without CheckoutRequestID)
$stmt = $conn->prepare("INSERT INTO pending_bookings (user_id, car_id, pickup_date, return_date, amount, phone, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("iissds", $user_id, $car_id, $pickup_date, $return_date, $amount, $phone);
$stmt->execute();
$pending_booking_id = $stmt->insert_id;
$stmt->close();

// M-Pesa Credentials
$consumerKey = 'EAR8WZS2z8vGqxwlAClxB4SFsJskSATzhtI9D0M2PKDnLrsF';
$consumerSecret = 'FJAMTrYC9sw1N6G0LRp3UPsEh9VR86joJGGdkG9p1xalI7JSKGIAQU2DuTeSZKXk';
$shortCode = '174379';
$passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

// Timestamp and password
$timestamp = date("YmdHis");
$password = base64_encode($shortCode . $passkey . $timestamp);

// Get access token
$credentials = base64_encode("$consumerKey:$consumerSecret");
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$token = json_decode($response)->access_token;
curl_close($ch);

// Prepare STK push
$stkData = [
    'BusinessShortCode' => $shortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => (int)$amount,
    'PartyA' => $phone,
    'PartyB' => $shortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => 'https://ff14a3e36f7a.ngrok-free.app//car/car-booking-project/api/callback.php',
    'AccountReference' => $pending_booking_id,
    'TransactionDesc' => 'Car rental payment'
];

$stkHeaders = [
    'Content-Type: application/json',
    "Authorization: Bearer $token"
];

$ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt($ch, CURLOPT_HTTPHEADER, $stkHeaders);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($stkData));
$response = curl_exec($ch);
curl_close($ch);

// Extract CheckoutRequestID from response
$stkResponse = json_decode($response);
if (isset($stkResponse->CheckoutRequestID)) {
    $checkout_id = $stkResponse->CheckoutRequestID;

    // Update the pending booking with the CheckoutRequestID
    $stmt = $conn->prepare("UPDATE pending_bookings SET checkout_request_id = ? WHERE id = ?");
    $stmt->bind_param("si", $checkout_id, $pending_booking_id);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Initiated</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 50px;
            background: #f5f5f5;
            text-align: center;
        }
        .box {
            background-color: pink;
            display: inline-block;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .btn {
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>STK Push Sent</h2>
        <p>Please check your phone to complete the payment via M-Pesa.</p>
        <a href="index.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>

