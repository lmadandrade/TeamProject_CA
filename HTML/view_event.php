<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

require_once "db.php";

if (!isset($_GET['id'])) {
  echo "No event ID specified.";
  exit;
}

$eventId = (int) $_GET['id'];
$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $eventId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
  echo "Event not found.";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Event Details</title>
  <link rel="stylesheet" href="../CSS/style.css" />
  <style>
    .event-details-container {
      background-color: white;
      border-radius: 25px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
      padding: 2.5rem 3rem;
      max-width: 600px;
      margin: 3rem auto;
      text-align: left;
    }

    .event-details-container h2 {
      text-align: center;
      margin-bottom: 2rem;
    }

    .event-details-container p {
      margin: 0.6rem 0;
      font-size: 16px;
    }

    .event-details-container label {
      font-weight: bold;
      margin-right: 0.4rem;
    }

    .event-description-box {
      background: #f4f4f4;
      border-radius: 8px;
      padding: 0.8rem;
      margin-top: 0.4rem;
      font-size: 14px;
    }

    .action-buttons {
      margin-top: 2rem;
      display: flex;
      gap: 1rem;
      justify-content: center;
      flex-wrap: wrap;
    }

    .action-buttons form,
    .action-buttons a {
      display: inline-block;
    }

    .btn-edit {
      background-color: #1a73e8;
      color: white;
      padding: 0.6rem 1.2rem;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      text-decoration: none;
      cursor: pointer;
    }

    .btn-delete {
      background-color: #e53935;
      color: white;
      padding: 0.6rem 1.2rem;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
    }

    .btn-back {
      background-color: transparent;
      color: #1a73e8;
      border: 2px solid #1a73e8;
      padding: 0.6rem 1.2rem;
      border-radius: 8px;
      font-weight: bold;
      text-decoration: none;
      margin-top: 1rem;
      display: block;
      text-align: center;
      width: fit-content;
      margin-left: auto;
      margin-right: auto;
    }
  </style>
</head>
<body>
  <div class="event-details-container">
    <h2>Event Details</h2>

    <p><label>Event Title:</label> <?php echo htmlspecialchars($event['title']); ?></p>
    <p><label>Date:</label> <?php echo htmlspecialchars($event['event_date']); ?></p>
    <?php if (!empty($event['event_end_date'])): ?>
      <p><label>End Date:</label> <?php echo htmlspecialchars($event['event_end_date']); ?></p>
    <?php endif; ?>
    <p><label>Time:</label> <?php echo htmlspecialchars($event['event_time']); ?></p>
    <p><label>Location:</label> <?php echo htmlspecialchars($event['location']); ?></p>
    <?php if (!empty($event['social_link'])): ?>
      <p><label>Social Media Link:</label>
        <a href="<?php echo htmlspecialchars($event['social_link']); ?>" target="_blank">
          <?php echo htmlspecialchars($event['social_link']); ?>
        </a>
      </p>
    <?php endif; ?>
    <p><label>Description:</label></p>
    <div class="event-description-box">
      <?php echo nl2br(htmlspecialchars($event['description'])); ?>
    </div>

    <div class="action-buttons">
      <a href="create_event.php?edit_id=<?php echo $event['id']; ?>" class="btn-edit">Edit Event</a>

      <form action="delete_event.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');">
        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
        <button type="submit" class="btn-delete">Delete Event</button>
      </form>
    </div>

    <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
  </div>
</body>
</html>
