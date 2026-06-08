<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "skill_exchange_db";

// Create Connection
$conn = mysqli_connect($host, $user, $password, $dbname);

// Check Connection
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Start global session for user tracking
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>