<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "db.php";

// Get form data
$user_id = $_SESSION['user_id'];
$title = $_POST['title'];
$event_date = $_POST['event_date']; // required
$event_end_date = $_POST['event_end_date']; // optional
$event_time = $_POST['event_time'];
$location = $_POST['location'];
$participants = $_POST['participants'];
$social_link = $_POST['social_link'];
$reminder_timing = $_POST['reminder_timing'];
$color = $_POST['color'];
$description = $_POST['description'];

// Handle optional end date
if (empty($event_end_date)) {
    $event_end_date = $event_date;
}

// Validate required fields
if (empty($title) || empty($event_date) || empty($event_time) || empty($color) || empty($reminder_timing)) {
    die("Please fill all required fields.");
}

// Prepare SQL insert
$stmt = $conn->prepare("INSERT INTO events 
(user_id, title, event_date, event_end_date, event_time, location, participants, social_link, reminder_timing, color, description)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("issssssssss", $user_id, $title, $event_date, $event_end_date, $event_time, $location, $participants, $social_link, $reminder_timing, $color, $description);

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit;
} else {
    echo "Error saving event: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
