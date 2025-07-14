<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

require_once "db.php";

// Get current logged-in user's ID
$userId = $_SESSION['user_id'];

// Set current month and year manually (April 2025 for now)
$currentMonth = 4;
$currentYear = 2025;

// Fetch all events for this user in the current month
$sql = "SELECT * FROM events 
        WHERE user_id = $userId 
        AND MONTH(event_date) = $currentMonth 
        AND YEAR(event_date) = $currentYear";

$result = mysqli_query($conn, $sql);

$events = [];
$hasEvents = false;
$eventDays = []; // Array to store event days and their associated colors

if ($result && mysqli_num_rows($result) > 0) {
  $hasEvents = true;
  while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;

    // Extract day number from event date (e.g., 10 from 2025-04-10)
    $day = date('j', strtotime($row['event_date']));
    $eventDays[$day] = $row['color'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../CSS/style.css" />
  </head>
<body>
  <div class="dashboard-wrapper">
    <h2>Dashboard</h2>

    <!-- Calendar Container -->
    <div class="calendar-box">
      <div class="calendar-header">
        <span>April 2025</span>
        <form action="" method="GET">
          <button type="submit" name="month" value="prev">←</button>
        </form>
        <form action="" method="GET">
          <button type="submit" name="month" value="next">→</button>
        </form>
        <form action="create_event.php" method="GET">
          <button type="submit" class="create-event-button">Create Event</button>
        </form>
      </div>

      <!-- Static Calendar Layout for April -->
      <table class="calendar">
        <thead>
          <tr>
            <th>SUN</th><th>MON</th><th>TUE</th><th>WED</th><th>THU</th><th>FRI</th><th>SAT</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Generate static days (1–30) split into 5 weeks
          $day = 1;
          for ($week = 0; $week < 5; $week++) {
            echo "<tr>";
            for ($weekday = 0; $weekday < 7; $weekday++) {
              if ($day > 30) {
                echo "<td></td>"; // Empty cell after the end of the month
              } else {
                $class = "";

                // Apply event color class if this day has an event
                if (isset($eventDays[$day])) {
                  switch ($eventDays[$day]) {
                    case '#007bff': $class = "event-blue"; break;
                    case '#f5f379': $class = "event-yellow"; break;
                    case '#4caf50': $class = "event-green"; break;
                  }
                }

                echo "<td class='$class'>$day</td>";
                $day++;
              }
            }
            echo "</tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

    <!-- Upcoming Events Section -->
    <div class="upcoming-events">
      <h4>Upcoming Events</h4>

      <?php if ($hasEvents): ?>
        <?php foreach ($events as $event): ?>
          <div class="event-item">
            <div>
              <!-- Dot with color -->
              <span class="dot 
                <?php
                  switch ($event['color']) {
                    case '#007bff': echo 'blue'; break;
                    case '#f5f379': echo 'yellow'; break;
                    case '#4caf50': echo 'green'; break;
                  }
                ?>">
              </span>
              <?php echo htmlspecialchars($event['title']); ?>
            </div>
            <div>
              <?php echo date('F j, Y g:iA', strtotime($event['event_date'] . ' ' . $event['event_time'])); ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Message and Create Button if there are no events -->
        <p>You don’t have any events for this month.</p>
        <form action="create_event.php" method="GET">
          <button type="submit" class="create-event-button">Create Event</button>
        </form>
      <?php endif; ?>
    </div>

    <!-- Logout -->
    <form action="logout.php" method="POST">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
  </div>
</body>
</html>
