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
  <!-- Load bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- load external CSS file -->
  <link rel="stylesheet" href="../CSS/style.css" />
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

      <!-- Input and color selector -->
      <div class="inline-fields">
        <input type="number" id="reminderHours" class="form-control" name="reminder_time" placeholder="Reminder - Hours" min="1" max="168" required/>

        <!-- Color picker for the event's visual tag -->
        <div class="color-picker-wrapper">
          <div class="color-picker-field">
            <label for="colorPicker">Choose a Color</label>
            <input type="color" id="colorPicker" name="color" value="#007bff" required/>
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

  <footer class="footer">
    <div class="container">
      Eventz © 2025
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
