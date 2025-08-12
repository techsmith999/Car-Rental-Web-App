<?php
// Include the database connection file
include('db.php');

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the values from the form submission
    $id = $_POST['id'];
    $pickup_date = $_POST['pickup_date'];
    $return_date = $_POST['return_date'];
    $pickup_status = $_POST['pickup_status'];
    $return_status = $_POST['return_status'];

    // Validate the data (simple validation for now)
    if (empty($id) || empty($pickup_date) || empty($return_date) || empty($pickup_status) || empty($return_status)) {
        echo "All fields are required!";
        exit;
    }

    // Prepare the SQL update query
    $sql = "UPDATE bookings 
            SET pickup_date = ?, return_date = ?, pickup_status = ?, return_status = ? 
            WHERE id = ?";

    // Prepare and bind the statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssi", $pickup_date, $return_date, $pickup_status, $return_status, $id);

        // Execute the query
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Failed to update booking.";
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Error in preparing the statement.";
    }
} else {
    echo "Invalid request.";
}

// Close the database connection
$conn->close();
?>
