<?php
// Show all errors so I can debug if something goes wrong
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start the session to get user info
session_start();

// If user isn't logged in, send them back to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// include database connection file
require_once "db.php";

// get the user ID from session
$user_id = $_SESSION['user_id'];

// collect all the input values from the form
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

// if the user didn’t choose an end date, use the start date
if (empty($event_end_date)) {
    $event_end_date = $event_date;
}

// make sure required fields are not empty
if (empty($title) || empty($event_date) || empty($event_time) || empty($color) || empty($reminder_timing)) {
    die("Please fill all required fields.");
}

// write the SQL query to save the event
$stmt = $conn->prepare("INSERT INTO events 
(user_id, title, event_date, event_end_date, event_time, location, participants, social_link, reminder_timing, color, description)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// attach values to the query
$stmt->bind_param("issssssssss", $user_id, $title, $event_date, $event_end_date, $event_time, $location, $participants, $social_link, $reminder_timing, $color, $description);

// run the query and check if it worked
if ($stmt->execute()) {
    // go back to dashboard if success
    header("Location: dashboard.php");
    exit;
} else {
    // if there's an error, show it
    echo "Error saving event: " . $stmt->error;
}

// clean up
$stmt->close();
$conn->close();
?>
