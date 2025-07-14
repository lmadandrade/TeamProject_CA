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
  <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
  <div class="create-event-container">
    <h2>Create Event</h2>
    <form action="save_event.php" method="post">
      <input type="text" name="title" placeholder="Event Title" required />

      <div class="inline-fields">
        <input type="date" name="date" required />
        <input type="time" name="time" required />
      </div>

      <input type="text" name="location" placeholder="Location" required />
      <input type="text" name="participants" placeholder="Invite Participants" />
      <input type="text" name="social_link" placeholder="Social Media Link" />

      <div class="inline-fields">
        <select name="reminder_timing" required>
          <option value="24hr">24 hours before</option>
          <option value="1hr">1 hour before</option>
          <option value="custom">Custom</option>
        </select>

        <div class="color-picker-wrapper">
          <div class="color-picker-field">
            <label for="colorPicker">Choose a Color</label>
            <input type="color" id="colorPicker" name="color" value="#F44336" />
          </div>
        </div>
      </div>

      <textarea name="description" placeholder="Description" rows="4"></textarea>

      <div class="inline-fields form-actions">
        <input type="submit" value="Save Event" style="background-color: #007bff; color: white; border: none; padding: 0.6rem 1rem; border-radius: 6px; font-weight: bold; cursor: pointer;" />
        <a href="dashboard.php" style="text-decoration: none;">
          <button type="button" style="padding: 0.6rem 1rem; border: none; border-radius: 6px; background-color: #ddd; cursor: pointer;">Cancel</button>
        </a>
      </div>
    </form>
  </div>
</body>
</html>
