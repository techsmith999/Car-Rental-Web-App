<?php
// Include the database connection
include('db.php');
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_auth.php"); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['admin_id'];
// Delete user logic
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the user from the database
    $delete_sql = "DELETE FROM users WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param('i', $delete_id);

    if ($delete_stmt->execute()) {
        $message = "User deleted successfully.";
    } else {
        $message = "Error deleting user.";
    }
}

// Fetch all users from the database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="users.css">
</head>
<body>
    <div class="container">
        <h2>Manage Users</h2>

        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a> |
                            <a href="manage_users.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
