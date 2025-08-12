<?php
// Include database connection
include 'db.php';

// Start session
session_start();

// Handle Login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // If user exists, verify password
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            echo "success";
        } else {
            echo "Invalid email or password!";
        }
    } else {
        echo "Invalid email or password!";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Car Rental</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $("#loginForm").submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: "login.php",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response === "success") {
                        $("#loginSuccess").removeClass("d-none").text("Login successful! Redirecting...");
                        $("#loginError").addClass("d-none");
                        setTimeout(() => window.location.href = "index.php", 2000);
                    } else {
                        $("#loginError").removeClass("d-none").text(response);
                        $("#loginSuccess").addClass("d-none");
                    }
                }
            });
        });
    });
    </script>
    <style>
       .text-white{
        background-image: url(images/login1.jpg);
        background-size: cover;
       }
       .col-md-5{
        backdrop-filter: blur(10px);
        background-color: none;
       }

      
    </style>    
</head>
<body class="text-white">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <h2 class="text-center">Login</h2>
                <div id="loginError" class="alert alert-danger d-none"></div>
                <div id="loginSuccess" class="alert alert-success d-none"></div>
                <form id="loginForm">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <p class="text-center mt-3">Don't have an account? <a href="register.php" class="text-light">Register</a></p>
            </div>
        </div>
    </div>
</body>
</html>
