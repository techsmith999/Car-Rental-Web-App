<?php
// Include database connection
include 'db.php';

// Handle Registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmail = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($checkEmail->num_rows > 0) {
        echo "Email already exists!";
        exit();
    }

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $password);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Registration failed!";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Car Rental</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $("#registerForm").submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: "register.php",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response === "success") {
                        $("#registerSuccess").removeClass("d-none").text("Registration successful! Redirecting...");
                        $("#registerError").addClass("d-none");
                        setTimeout(() => window.location.href = "login.php", 2000);
                    } else {
                        $("#registerError").removeClass("d-none").text(response);
                        $("#registerSuccess").addClass("d-none");
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
       }
    </style>
</head>
<body class="text-white">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <h2 class="text-center">Register</h2>
                <div id="registerError" class="alert alert-danger d-none"></div>
                <div id="registerSuccess" class="alert alert-success d-none"></div>
                <form id="registerForm">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
                <p class="text-center mt-3">Already have an account? <a href="login.php" class="text-light">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
