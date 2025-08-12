<?php
// === 1. Validate Input ===
if (!isset($_POST['phone']) || !isset($_POST['amount'])) {
    die("❌ Phone number and amount are required.");
}

$phone = trim($_POST['phone']);
$amount = (int) floatval($_POST['amount']); // force amount to be integer

// === 2. Format Phone Number ===
$phone = preg_replace('/\s+/', '', $phone);
if (preg_match('/^0/', $phone)) {
    $phone = '254' . substr($phone, 1);
} elseif (!preg_match('/^254/', $phone)) {
    die("❌ Invalid phone number format. Use 07XXXXXXXX or 2547XXXXXXXX.");
}

// === 3. Get M-PESA Access Token ===
$consumerKey = 'EAR8WZS2z8vGqxwlAClxB4SFsJskSATzhtI9D0M2PKDnLrsF';
$consumerSecret = 'FJAMTrYC9sw1N6G0LRp3UPsEh9VR86joJGGdkG9p1xalI7JSKGIAQU2DuTeSZKXk';
$credentials = base64_encode("$consumerKey:$consumerSecret");

$token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . $credentials
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$token_response = curl_exec($ch);
curl_close($ch);

$token_data = json_decode($token_response);
if (!isset($token_data->access_token)) {
    die("❌ Failed to get access token. Response:\n$token_response");
}

$access_token = $token_data->access_token;

// === 4. Prepare STK Push Payload ===
$shortCode = '174379'; // M-PESA Paybill for sandbox
$passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
$timestamp = date('YmdHis');
$password = base64_encode($shortCode . $passkey . $timestamp);

$callbackURL = 'https://ff14a3e36f7a.ngrok-free.app//car/car-booking-project/api/callback.php';

$accountReference = 'CarRental';
$transactionDesc = 'Payment for car rental';

$stkPayload = [
    'BusinessShortCode' => $shortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $shortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => $callbackURL,
    'AccountReference' => $accountReference,
    'TransactionDesc' => $transactionDesc
];

// === 5. Send STK Push Request ===
$stk_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

$curl = curl_init($stk_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token
]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($stkPayload));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

// === 6. Debug Output ===
echo "<h3>✅ STK Push Debug Info</h3>";
echo "<strong>Phone:</strong> $phone<br>";
echo "<strong>Amount:</strong> $amount<br><br>";

echo "<strong>Payload:</strong><br><pre>" . json_encode($stkPayload, JSON_PRETTY_PRINT) . "</pre>";
echo "<strong>Response:</strong><br><pre>$response</pre>";

if ($err) {
    echo "<br><strong>Curl Error:</strong> $err";
}
?>
