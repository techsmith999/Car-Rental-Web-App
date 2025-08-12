<?php
session_start();
include 'db.php'; // Database connection
/*
// Handle registration
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

    $query = $conn->prepare("INSERT INTO admins (name, username, email, password) VALUES (?, ?, ?, ?)");
    $query->bind_param("ssss", $name, $username, $email, $password);

    if ($query->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    exit();
}
*/
// Handle login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            // Set session variables after successful login
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name']; // Store the admin name for later use

            echo "success";
        } else {
            echo "invalid";
        }
    } else {
        echo "invalid";
    }
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Authentication | Car Rental</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: linear-gradient(to right, #141e30, #243b55);
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            padding: 30px;
            border-radius: 15px;
            animation: fadeIn 1s;
        }
        .toggle-btn {
            cursor: pointer;
            color: #007bff;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow bg-light">
                    <!-- Success Alert -->
                    <div class="alert alert-success d-none" id="success-alert">
                        Registration successful! Kindly log in.
                    </div>

                    <h2 class="text-center" id="form-title">Admin Login</h2>
                    
                    <!-- Registration Form -->
                    <form id="registerForm" class="d-none">
                        <input type="text" id="reg-name" class="form-control my-2" placeholder="Full Name" required>
                        <input type="text" id="reg-username" class="form-control my-2" placeholder="Username" required>
                        <input type="email" id="reg-email" class="form-control my-2" placeholder="Email" required>
                        <input type="password" id="reg-password" class="form-control my-2" placeholder="Password" required>
                        <button type="submit" class="btn btn-success w-100">Register</button>
                        <p class="text-danger mt-2" id="register-error"></p>
                        <p class="mt-3 text-center"><span class="toggle-btn">Already have an account? Login</span></p>
                    </form>

                    <!-- Login Form -->
                    <form id="loginForm">
                        <input type="text" id="login-username" class="form-control my-2" placeholder="Username" required>
                        <input type="password" id="login-password" class="form-control my-2" placeholder="Password" required>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                        <p class="text-danger mt-2" id="login-error"></p>
                        <p class="mt-3 text-center"><span class="toggle-btn">Don't have an account? Register</span></p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Toggle between login and register
            $(".toggle-btn").click(function() {
                $("#registerForm, #loginForm").toggleClass("d-none");
                $("#form-title").text($("#registerForm").hasClass("d-none") ? "Admin Login" : "Admin Registration");
            });

            // Register Admin
            $("#registerForm").submit(function(event) {
                event.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "admin_auth.php",
                    data: {
                        register: true,
                        name: $("#reg-name").val(),
                        username: $("#reg-username").val(),
                        email: $("#reg-email").val(),
                        password: $("#reg-password").val()
                    },
                    success: function(response) {
                        if (response === "success") {
                            // Show success alert
                            $("#success-alert").removeClass("d-none").addClass("show");

                            // Clear registration form fields
                            $("#reg-name").val("");
                            $("#reg-username").val("");
                            $("#reg-email").val("");
                            $("#reg-password").val("");

                            // Switch to login form
                            $(".toggle-btn").click();

                            // Hide the success alert after 3 seconds
                            setTimeout(function() {
                                $("#success-alert").removeClass("show").addClass("d-none");
                            }, 3000);
                        } else {
                            $("#register-error").text("Error registering admin.");
                        }
                    }
                });
            });

            // Login Admin
            $("#loginForm").submit(function(event) {
                event.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "admin_auth.php",
                    data: {
                        login: true,
                        username: $("#login-username").val(),
                        password: $("#login-password").val()
                    },
                    success: function(response) {
                        if (response === "success") {
                            window.location.href = "index.php";
                        } else {
                            $("#login-error").text("Invalid username or password.");
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>