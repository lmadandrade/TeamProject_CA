<?php
// Start session to check if user is logged in
session_start();

// If the user is not logged in, send them to login page
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

// Include the database connection file
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
  $eventId = (int) $_POST['event_id'];
  $userId = $_SESSION['user_id'];

  // Delete the event only if it belongs to the current user
  $stmt = $conn->prepare(" UPDATE event_participants
    SET status = 'declined'
    WHERE event_id = ? 
    AND user_id = ?
  ");

  $stmt->bind_param("ii", $eventId, $userId);
  $stmt->execute();
  $stmt->close();
}

// Close the database connection
$conn->close();

// After deleting, send the user back to the dashboard
header("Location: dashboard.php");
exit;
?>
