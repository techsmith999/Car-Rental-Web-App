<?php
$mpesaResponse = file_get_contents('php://input');
file_put_contents("mpesa_callback_log.txt", $mpesaResponse . PHP_EOL, FILE_APPEND);

$data = json_decode($mpesaResponse);

if (isset($data->Body->stkCallback->ResultCode) && $data->Body->stkCallback->ResultCode == 0) {
    $callback = $data->Body->stkCallback;
    $items = $callback->CallbackMetadata->Item;

    $amount = $items[0]->Value;
    $mpesaCode = $items[1]->Value;
    $checkout_request_id = $callback->CheckoutRequestID;
    $transactionDate = $items[3]->Value;
    $phone = $items[4]->Value;

    $conn = new mysqli("localhost", "root", "", "car_rental");
    if ($conn->connect_error) {
        die("DB Error: " . $conn->connect_error);
    }

    // Find pending booking using CheckoutRequestID
    $stmt = $conn->prepare("SELECT * FROM pending_bookings WHERE checkout_request_id = ?");
    $stmt->bind_param("s", $checkout_request_id);
    $stmt->execute();
    $pending_result = $stmt->get_result();

    if ($pending_result->num_rows > 0) {
        $pending = $pending_result->fetch_assoc();

        // Move to bookings
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, car_id, pickup_date, return_date, payment_status) VALUES (?, ?, ?, ?, 'paid')");
        $stmt->bind_param("iiss", $pending['user_id'], $pending['car_id'], $pending['pickup_date'], $pending['return_date']);
        $stmt->execute();
        $booking_id = $stmt->insert_id;
        $stmt->close();

        // Insert payment
        $stmt = $conn->prepare("INSERT INTO payments (booking_id, mpesa_receipt_number, amount, phone, transaction_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isds", $booking_id, $mpesaCode, $amount, $phone);
        $stmt->execute();
        $stmt->close();

        // Mark car as unavailable
        $conn->query("UPDATE cars SET availability_status = 'unavailable' WHERE id = " . $pending['car_id']);

        // Delete from pending_bookings
        $conn->query("DELETE FROM pending_bookings WHERE id = " . $pending['id']);

        http_response_code(200);
        echo json_encode(["ResultCode" => 0, "ResultDesc" => "Booking completed"]);
    } else {
        file_put_contents("mpesa_callback_log.txt", "No pending booking found for checkout ID: $checkout_request_id" . PHP_EOL, FILE_APPEND);
        http_response_code(200);
        echo json_encode(["ResultCode" => 0, "ResultDesc" => "No matching pending booking"]);
    }
} else {
    http_response_code(200);
    echo json_encode(["ResultCode" => 0, "ResultDesc" => "Callback received"]);
}
?>
