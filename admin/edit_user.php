<?php
// Include the database connection
include('db.php');
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_auth.php"); // Redirect to login if not logged in
    exit();
}

// Fetch user data for editing
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch the user's current details from the database
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
    } else {
        echo "User not found!";
        exit();
    }
} else {
    echo "User ID not provided!";
    exit();
}

// Handle the form submission to update the user details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Update the user details in the database
    $update_sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('sssi', $name, $email, $phone, $user_id);

    if ($update_stmt->execute()) {
        $message = "User updated successfully.";
    } else {
        $message = "Error updating user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        /* General Body Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 900px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 2em;
        }

        /* Message Styles */
        .message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .message.error {
            background-color: #f2dede;
            color: #a94442;
        }

        /* Form Styles */
        form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        label {
            font-size: 1.1em;
            font-weight: bold;
            color: #2c3e50;
        }

        input[type="text"],
        input[type="email"],
        input[type="phone"],
        input[type="submit"],
        button {
            padding: 12px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="phone"]:focus {
            border-color: #2980b9;
            box-shadow: 0 0 8px rgba(41, 128, 185, 0.2);
        }

        button {
            background-color: #2980b9;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #3498db;
        }

        /* Button Styles for Edit and Delete */
        a {
            text-decoration: none;
            color: #2980b9;
            font-weight: bold;
            margin-right: 10px;
        }

        a:hover {
            color: #3498db;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f4f7f6;
            color: #2c3e50;
        }

        table td {
            background-color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Responsive Design for Small Screens */
        @media screen and (max-width: 768px) {
            .container {
                width: 90%;
            }

            form {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit User</h2>

        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

            <button type="submit">Update User</button>
        </form>
    </div>
</body>
</html>
