<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Current directory: " . __DIR__ . "<br>";

include 'api/access_token.php';

$token = getAccessToken();

echo "Access Token: " . $token;
