<?php
session_start();
session_destroy(); // Destroy all sessions
header("Location: admin_auth.php"); // Redirect to login page
exit();
?>
