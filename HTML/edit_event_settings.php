<?php

session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
require_once "db.php";

$userId  = (int)$_SESSION['user_id'];
$eventId = (int)($_GET['event_id']);

// Make sure logged in user participated in this event
$stmt = $conn->prepare(" SELECT 
    e.title,
    e.event_date,
    ep.reminder_minutes_before AS reminder_minutes,
    ep.color
    FROM events e
    JOIN event_participants ep ON ep.event_id = e.id
    WHERE e.id = ? AND ep.user_id = ?
");

$stmt->bind_param("ii", $eventId, $userId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

// check to prevent user who doesnt have access to change event
if (!$row) {
    echo "You don't have access to this event.";
    exit; 
}

$title = $row['title'];
$eventDateDisp = date('F j, Y g:i A', strtotime($row['event_date']));
$myMinutes = (int)$row['reminder_minutes'];
// convert minutes to hours
$myHours = $myMinutes / 60;

$myColor = $row['color'];

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newRiminderHours = (int)$_POST['reminder_hrs'];
  // convert to minuts
  $newRiminderMinutes =  $newRiminderHours * 60;
  $color = $_POST['color'];

  $u = $conn->prepare("
    UPDATE event_participants
    SET reminder_minutes_before = ?, color = ?
    WHERE event_id = ? AND user_id = ?
  ");
  $u->bind_param("isii", $newRiminderMinutes, $color, $eventId, $userId);
  $u->execute();
  $u->close();

  header("Location: view_event.php?id=" . $eventId);
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- css -->
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
    <!-- navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><strong>Eventz</strong></a>
            
            <!-- hamerburger menu toggler for small screens -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent">
            <!-- left navigation -->
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="create_event.php">Create Event</a></li>
                <li class="nav-item"><a class="nav-link" href="invitations.php">Invitations</a></li>
            </ul>
            <!-- right navigation -->
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
            </div>
        </div>
    </nav>

    <div class="update-details-container">
        <h2>Edit My Event</h2>

        <p><b>Event:</b> <?= htmlspecialchars($title) ?></p>
        <p><b>When:</b> <?= htmlspecialchars($eventDateDisp) ?></p>

        <form method="post">
            <div>
                <label><b>Update Reminder Time(Hours)</b></label>
                <input type="number" name="reminder_hrs" class="profile-input" min="1" max="168" value="<?= (int)$myHours ?>">
            </div>
            <div>
                <label for="colorPicker"><b>Update Colour</b></label>
                <input type="color" id="colorPicker" name="color" class="profile-input" value="<?= htmlspecialchars($myColor) ?>">
            </div> 

            <div>
                <div class="form-actions" style="margin-top:12px;display:flex;gap:10px;">
                    <button type="submit" class="save-btn">Save</button>
                    <a href="view_event.php?id=<?= (int)$eventId ?>" class="cancel-btn">Cancel</a>
                </div>
            </div>
        </form>
    </div>
  
  <footer class="footer">
    <div class="container">
      Eventz © 2025
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>