<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session and check login
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Connect to DB
$conn = new mysqli("localhost", "root", "", "event_planner");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from form
$user_id = $_SESSION['user_id'];
$title = $_POST['title'];
$date = $_POST['date'];
$time = $_POST['time'];
$location = $_POST['location'];
$participants = $_POST['participants'];
$social_link = $_POST['social_link'];
$reminder_timing = $_POST['reminder_timing'];
$color = $_POST['color'];
$description = $_POST['description'];

// Validate
if (empty($title) || empty($date) || empty($time) || empty($color) || empty($reminder_timing)) {
    die("Please fill all required fields.");
}

// Save to DB
$stmt = $conn->prepare("INSERT INTO events (user_id, title, date, time, location, participants, social_link, reminder_timing, color, description)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("isssssssss", $user_id, $title, $date, $time, $location, $participants, $social_link, $reminder_timing, $color, $description);

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
