<?php
session_start();
$conn = new mysqli("localhost", "root", "", "car_rental");

// Validate POST input
if (!isset($_POST['phone'], $_POST['amount'], $_POST['car_id'], $_SESSION['user_id'])) {
    die("Missing required fields.");
}

$phone = $_POST['phone'];
$amount = $_POST['amount'];
$car_id = $_POST['car_id'];
$user_id = $_SESSION['user_id'];

// Format phone number
$phone = preg_replace('/^0/', '254', $phone);

// Insert booking with status pending
$stmt = $conn->prepare("INSERT INTO bookings (user_id, car_id, booking_date, return_date, payment_status) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 DAY), 'Pending')");
$stmt->bind_param("ii", $user_id, $car_id);
$stmt->execute();
$booking_id = $stmt->insert_id;
$stmt->close();

// Save booking ID to session to track
$_SESSION['booking_id'] = $booking_id;

// === Trigger STK Push ===
$consumerKey = 'EAR8WZS2z8vGqxwlAClxB4SFsJskSATzhtI9D0M2PKDnLrsF';
$consumerSecret = 'FJAMTrYC9sw1N6G0LRp3UPsEh9VR86joJGGdkG9p1xalI7JSKGIAQU2DuTeSZKXk';
$credentials = base64_encode("$consumerKey:$consumerSecret");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$token_response = curl_exec($ch);
curl_close($ch);

$access_token = json_decode($token_response)->access_token;
$shortCode = '174379';
$passkey = 'YOUR_PASSKEY';
$timestamp = date('YmdHis');
$password = base64_encode($shortCode . $passkey . $timestamp);

$callbackURL = 'https://365b01632d85.ngrok-free.app/car/car-booking-project/api/callback.php';

$stk_data = [
    'BusinessShortCode' => $shortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $shortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => $callbackURL,
    'AccountReference' => 'Booking#' . $booking_id,
    'TransactionDesc' => 'Car booking payment'
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token
]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($stk_data));
$response = curl_exec($curl);
curl_close($curl);

header('Content-Type: application/json');
echo $response;
