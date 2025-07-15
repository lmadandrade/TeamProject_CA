<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

require_once "db.php";

$userId = $_SESSION['user_id'];

if (isset($_GET['month']) && isset($_GET['year'])) {
    $currentMonth = (int) $_GET['month'];
    $currentYear = (int) $_GET['year'];
} else {
    $currentMonth = date('n');
    $currentYear = date('Y');
}

$prevMonth = $currentMonth - 1;
$prevYear = $currentYear;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $currentMonth + 1;
$nextYear = $currentYear;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

// Fetch events
$sql = "SELECT * FROM events 
        WHERE user_id = $userId 
        AND (
            (MONTH(event_date) = $currentMonth AND YEAR(event_date) = $currentYear) OR
            (MONTH(event_end_date) = $currentMonth AND YEAR(event_end_date) = $currentYear)
        )";

$result = mysqli_query($conn, $sql);

$events = [];
$hasEvents = false;
$eventDays = [];

if ($result && mysqli_num_rows($result) > 0) {
  $hasEvents = true;

  while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;

    $start = strtotime($row['event_date']);
    $end = !empty($row['event_end_date']) ? strtotime($row['event_end_date']) : $start;

    while ($start <= $end) {
      if (date('n', $start) == $currentMonth && date('Y', $start) == $currentYear) {
        $day = date('j', $start);
        $eventDays[$day] = $row['color'];
      }
      $start = strtotime("+1 day", $start);
    }
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

    <div class="calendar-box">
      <div class="calendar-header">
        <span><?php echo date('F Y', strtotime("$currentYear-$currentMonth-01")); ?></span>

        <form action="" method="GET">
          <input type="hidden" name="month" value="<?php echo $prevMonth; ?>">
          <input type="hidden" name="year" value="<?php echo $prevYear; ?>">
          <button type="submit">←</button>
        </form>

        <form action="" method="GET">
          <input type="hidden" name="month" value="<?php echo $nextMonth; ?>">
          <input type="hidden" name="year" value="<?php echo $nextYear; ?>">
          <button type="submit">→</button>
        </form>

        <form action="create_event.php" method="GET">
          <button type="submit" class="create-event-button">Create Event</button>
        </form>
      </div>

      <table class="calendar">
        <thead>
          <tr>
            <th>SUN</th><th>MON</th><th>TUE</th><th>WED</th><th>THU</th><th>FRI</th><th>SAT</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $firstDay = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
          $daysInMonth = date('t', $firstDay);
          $startDay = date('w', $firstDay);

          $day = 1;
          echo "<tr>";

          for ($i = 0; $i < $startDay; $i++) {
            echo "<td></td>";
          }

          while ($day <= $daysInMonth) {
            if (($startDay + $day - 1) % 7 == 0 && $day != 1) {
              echo "</tr><tr>";
            }

            $style = "";
            if (isset($eventDays[$day])) {
              $color = htmlspecialchars($eventDays[$day]);
              $style = "style='background-color: $color; color: white; border-radius: 50%; padding: 0.4rem;'";
            }

            echo "<td $style>$day</td>";
            $day++;
          }

          echo "</tr>";
          ?>
        </tbody>
      </table>
    </div>

    <div class="upcoming-events">
      <h4>Upcoming Events</h4>

      <?php if ($hasEvents): ?>
        <?php foreach ($events as $event): ?>
          <a href="view_event.php?id=<?php echo $event['id']; ?>" class="event-main-link">
            <div class="event-item event-row">
              <!-- Left: Dot + Title -->
              <div class="event-left">
                <span class="dot" style="background-color: <?php echo htmlspecialchars($event['color']); ?>;"></span>
                <strong class="event-title"><?php echo htmlspecialchars($event['title']); ?></strong>
              </div>

              <!-- Right: Date/Time -->
              <div class="event-right">
                <span class="event-datetime">
                  <?php
                    echo htmlspecialchars(
                      $event['event_end_date']
                        ? date('F j, Y', strtotime($event['event_date'])) . ' – ' . date('F j, Y', strtotime($event['event_end_date'])) . ' ' . date('g:iA', strtotime($event['event_time']))
                        : date('F j, Y g:iA', strtotime($event['event_date'] . ' ' . $event['event_time']))
                    );
                  ?>
                </span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <p>You don’t have any events for this month.</p>
        <form action="create_event.php" method="GET">
          <button type="submit" class="create-event-button">Create Event</button>
        </form>
      <?php endif; ?>
    </div>

    <form action="logout.php" method="POST">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
  </div>
</body>
</html>
