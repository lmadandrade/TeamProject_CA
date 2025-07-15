<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
  $eventId = (int) $_POST['event_id'];
  $userId = $_SESSION['user_id'];

  // Double check user owns the event
  $stmt = $conn->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
  $stmt->bind_param("ii", $eventId, $userId);
  $stmt->execute();
  $stmt->close();
}

$conn->close();

// Redirect back to dashboard
header("Location: dashboard.php");
exit;
?>
