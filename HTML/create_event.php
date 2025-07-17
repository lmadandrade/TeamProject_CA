<?php
// Starting the session to check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
  // If not logged in, send to login page
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
  <!-- Linking the CSS file -->
  <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>

  <div class="create-event-container">
    <!-- Title of the form -->
    <h2>Create Event</h2>

    <!-- Form to send event data to save_event.php -->
    <form action="save_event.php" method="POST">
      
      <!-- Text field for event name -->
      <input type="text" name="title" placeholder="Event Title" required />

      <!-- Description of the event, not required -->
      <textarea name="description" placeholder="Description" rows="4"></textarea>

      <!-- Inputs for the event's date and optional end date -->
      <div class="inline-fields">
        <input type="date" name="event_date" required />
        <input type="date" name="event_end_date" placeholder="Optional End Date" />
      </div>

      <!-- Set time of the event -->
      <input type="time" name="event_time" required />

      <!-- Type in location name -->
      <input type="text" name="location" placeholder="Location" required />

      <!-- Optional fields: participants and social link -->
      <input type="text" name="participants" placeholder="Invite Participants" />
      <input type="text" name="social_link" placeholder="Social Media Link" />

      <!-- Dropdown and color selector -->
      <div class="inline-fields">
        <select name="reminder_timing" required>
          <option value="24hr">24 hours before</option>
          <option value="1hr">1 hour before</option>
          <option value="custom">Custom</option>
        </select>

        <!-- Color picker for the event's visual tag -->
        <div class="color-picker-wrapper">
          <div class="color-picker-field">
            <label for="colorPicker">Choose a Color</label>
            <input type="color" id="colorPicker" name="color" value="#007bff" />
          </div>
        </div>
      </div>

      <!-- Save and Cancel buttons at the bottom -->
      <div class="inline-fields form-actions">
        <input 
          type="submit" 
          value="Save Event" 
          style="background-color: #007bff; color: white; border: none; padding: 0.6rem 1rem; border-radius: 6px; font-weight: bold; cursor: pointer;" 
        />
        
        <!-- Cancel goes back to dashboard -->
        <a href="dashboard.php" style="text-decoration: none;">
          <button 
            type="button" 
            style="padding: 0.6rem 1rem; border: none; border-radius: 6px; background-color: #ddd; cursor: pointer;">
            Cancel
          </button>
        </a>
      </div>
    </form>
  </div>

</body>
</html>
