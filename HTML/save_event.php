<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "event_planner");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$title = $_POST['title'];
$event_date = $_POST['event_date'];
$event_time = $_POST['event_time'];
$location = $_POST['location'];
$participants = $_POST['participants'];
$social_link = $_POST['social_link'];
$reminder_timing = $_POST['reminder_timing'];
$color = $_POST['color'];
$description = $_POST['description'];

if (empty($title) || empty($event_date) || empty($event_time) || empty($color) || empty($reminder_timing)) {
    die("Please fill all required fields.");
}

$stmt = $conn->prepare("INSERT INTO events 
(user_id, title, event_date, event_time, location, participants, social_link, reminder_timing, color, description)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("isssssssss", $user_id, $title, $event_date, $event_time, $location, $participants, $social_link, $reminder_timing, $color, $description);

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
