<?php
session_start();
include('db.php'); // Include your DB connection

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirect to dashboard if logged in
    exit();
}

$error = '';
$success = '';

// Register logic
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    if (empty($name) || empty($phone) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        // Insert the user into the database
        $stmt = $conn->prepare("INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $phone, $email, $hashed_password);

        if ($stmt->execute()) {
            $success = 'Registration successful! You can now log in.';
        } else {
            $error = 'An error occurred. Please try again.';
        }
    }
}

// Login logic
if (isset($_POST['login'])) {
    $email = $_POST['login_email'];
    $password = $_POST['login_password'];

    if (empty($email) || empty($password)) {
        $error = 'Both fields are required.';
    } else {
        // Check if user exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header('Location: index.php'); // Redirect to dashboard after successful login
                exit();
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'User not found.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Authentication</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="auth-container">
    <h2>User Authentication</h2>
    
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Register Section -->
    <div class="register-section">
        <h3>Register</h3>
        <form action="user_auth.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="#" id="switch-to-login">Login here</a></p>
    </div>

    <!-- Login Section -->
    <div class="login-section" style="display: none;">
        <h3>Login</h3>
        <form action="user_auth.php" method="POST">
            <input type="email" name="login_email" placeholder="Email" required>
            <input type="password" name="login_password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <p>Don't have an account? <a href="#" id="switch-to-register">Register here</a></p>
    </div>

</div>

<script>
    document.getElementById('switch-to-login').addEventListener('click', function() {
        document.querySelector('.register-section').style.display = 'none';
        document.querySelector('.login-section').style.display = 'block';
    });

    document.getElementById('switch-to-register').addEventListener('click', function() {
        document.querySelector('.login-section').style.display = 'none';
        document.querySelector('.register-section').style.display = 'block';
    });
</script>

</body>
</html>
