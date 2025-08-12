<?php
// Start the session
session_start();

// Destroy the session to log the user out
session_unset();   // Unset all session variables
session_destroy(); // Destroy the session

// Redirect the user to the login page or homepage
header("Location: admin_auth.php"); // Change login.php to your actual login page URL
exit();
?>
