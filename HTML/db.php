<?php
// Database connection configuration
$host = 'localhost';
$db   = 'event_planner';
$user = 'root';
$pass = ''; // Default for XAMPP is empty
$charset = 'utf8mb4';

// Create a new MySQLi connection
$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}
?>
