<?php
include 'db.php'; // Include database connection

// Fetch bookings with user and car details
$sql = "SELECT b.id, u.name AS user_name, c.make AS car_make, c.model AS car_model, 
               b.pickup_date, b.return_date, b.pickup_status, b.return_status
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN cars c ON b.car_id = c.id
        ORDER BY b.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management</title>
    <style>
        /* Embedding CSS for better design */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .booking-container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .filter-bar {
            margin-bottom: 20px;
            text-align: right;
        }

        .filter-bar select, .filter-bar input {
            padding: 5px 10px;
            margin: 5px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .edit-btn {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .edit-btn:hover {
            background-color: #45a049;
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            width: 500px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 24px;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
        }

        .modal label {
            display: block;
            margin: 10px 0 5px;
        }

        .modal input, .modal select {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .modal button {
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal button:hover {
            background-color: #45a049;
        }

        .cancel-btn {
            background-color: #f44336;
        }

        .cancel-btn:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>

    <div class="booking-container">
        <h1>Booking Management</h1>

        <div class="filter-bar">
            <select id="status-filter">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="picked_up">Picked Up</option>
                <option value="returned">Returned</option>
            </select>
            <input type="date" id="date-filter">
            <input type="text" id="search-filter" placeholder="Search bookings...">
            <button id="apply-filters">Apply</button>
            <button id="reset-filters">Reset</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User</th>
                    <th>Car</th>
                    <th>Pickup Date</th>
                    <th>Return Date</th>
                    <th>Pickup Status</th>
                    <th>Return Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="bookings-table-body">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['user_name']) ?></td>
                            <td><?= htmlspecialchars($row['car_make'] . ' ' . $row['car_model']) ?></td>
                            <td><?= htmlspecialchars($row['pickup_date']) ?></td>
                            <td><?= htmlspecialchars($row['return_date']) ?></td>
                            <td><?= htmlspecialchars($row['pickup_status']) ?></td>
                            <td><?= htmlspecialchars($row['return_status']) ?></td>
                            <td>
                                <button class="edit-btn" data-id="<?= $row['id'] ?>" data-pickup-date="<?= $row['pickup_date'] ?>" data-return-date="<?= $row['return_date'] ?>" data-pickup-status="<?= $row['pickup_status'] ?>" data-return-status="<?= $row['return_status'] ?>">Edit</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8">No bookings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div id="no-bookings" style="display: none;">No bookings found matching your criteria.</div>

        <div class="pagination">
            <button id="prev-page" disabled>Previous</button>
            <span id="page-info">Page 1 of 1</span>
            <button id="next-page" disabled>Next</button>
        </div>
    </div>

    <!-- Edit Booking Modal -->
    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Update Booking</h2>
                <button class="close-btn">&times;</button>
            </div>
            <form id="edit-booking-form">
                <input type="hidden" id="edit-booking-id">

                <label for="edit-pickup-date">Pickup Date</label>
                <input type="datetime-local" id="edit-pickup-date" required>

                <label for="edit-return-date">Return Date</label>
                <input type="datetime-local" id="edit-return-date" required>

                <label for="edit-pickup-status">Pickup Status</label>
                <select id="edit-pickup-status" required>
                    <option value="pending">Pending</option>
                    <option value="picked_up">Picked Up</option>
                </select>

                <label for="edit-return-status">Return Status</label>
                <select id="edit-return-status" required>
                    <option value="pending">Pending</option>
                    <option value="returned">Returned</option>
                </select>

                <button type="button" id="cancel-edit" class="cancel-btn">Cancel</button>
                <button type="submit" class="update-btn">Save Changes</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Open the Edit Modal with data
            $(document).on('click', '.edit-btn', function() {
                const bookingData = $(this).data();

                $('#edit-booking-id').val(bookingData.id);
                $('#edit-pickup-date').val(bookingData.pickupDate);
                $('#edit-return-date').val(bookingData.returnDate);
                $('#edit-pickup-status').val(bookingData.pickupStatus);
                $('#edit-return-status').val(bookingData.returnStatus);

                $('#edit-modal').fadeIn();
            });

            // Close the modal
            $(document).on('click', '.close-btn, #cancel-edit', function() {
                $('#edit-modal').fadeOut();
            });

            // Handle form submission
            $('#edit-booking-form').submit(function(e) {
                e.preventDefault();

                const bookingId = $('#edit-booking-id').val();
                const pickupDate = $('#edit-pickup-date').val();
                const returnDate = $('#edit-return-date').val();
                const pickupStatus = $('#edit-pickup-status').val();
                const returnStatus = $('#edit-return-status').val();

                $.ajax({
                    url: 'update_bookings.php', // Your update script
                    method: 'POST',
                    data: {
                        id: bookingId,
                        pickup_date: pickupDate,
                        return_date: returnDate,
                        pickup_status: pickupStatus,
                        return_status: returnStatus
                    },
                    success: function(response) {
                        if (response === 'success') {
                            alert('Booking updated successfully!');
                            location.reload(); // Reload to show updated data
                        } else {
                            alert('Failed to update booking.');
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again later.');
                    }
                });
            });
        });
    </script>

</body>
</html>

<?php $conn->close(); // Close database connection ?>
