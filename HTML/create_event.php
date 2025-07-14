<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Create Event</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>
  <div class="create-event-container">
    <h2>Create Event</h2>

    <form action="save_event.php" method="POST">
      <input type="text" name="title" placeholder="Event Title" required />

      <div class="inline-fields">
        <input type="date" name="date" required />
        <input type="time" name="time" required />
      </div>

      <input type="text" name="location" placeholder="Location" />
      <input type="text" name="participants" placeholder="Invite Participants" />
      <input type="text" name="social_link" placeholder="Social Media Link" />

      <div class="inline-fields">
        <select name="reminder_timing" required>
          <option value="24hr">24 hours before</option>
          <option value="1hr">1 hour before</option>
          <option value="custom">Custom</option>
        </select>

        <select name="color" required>
          <option value="#2196F3">Blue</option>
          <option value="#4CAF50">Green</option>
          <option value="#FFC107">Yellow</option>
          <option value="#F44336">Red</option>
        </select>
      </div>

      <textarea name="description" placeholder="Description" rows="4"></textarea>

      <div class="form-actions">
        <button type="submit">Save Event</button>
        <a href="dashboard.php" class="cancel-link">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
