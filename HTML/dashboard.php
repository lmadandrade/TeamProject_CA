<?php
// Show all errors during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session and redirect to login if not logged in
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

// Connect to database
require_once "db.php";
$userId = $_SESSION['user_id'];

// Determine current month/year or use today
if (isset($_GET['month']) && isset($_GET['year'])) {
  $currentMonth = (int) $_GET['month'];
  $currentYear = (int) $_GET['year'];
} else {
  $currentMonth = date('n');
  $currentYear = date('Y');
}

// Setup navigation for previous/next months
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

// Fetch events for this user and current month
$sql = "SELECT * FROM events 
        WHERE user_id = $userId 
        AND (
          (MONTH(event_date) = $currentMonth AND YEAR(event_date) = $currentYear) OR
          (MONTH(event_end_date) = $currentMonth AND YEAR(event_end_date) = $currentYear)
        )";

$result = mysqli_query($conn, $sql);

// Prepare variables to track events
$events = [];
$hasEvents = false;
$eventsByDate = [];

// Group events by date
if ($result && mysqli_num_rows($result) > 0) {
  $hasEvents = true;

  while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;

    $start = strtotime($row['event_date']);
    $end = !empty($row['event_end_date']) ? strtotime($row['event_end_date']) : $start;

    while ($start <= $end) {
      if (date('n', $start) == $currentMonth && date('Y', $start) == $currentYear) {
        $dayKey = date('Y-m-d', $start);

        // Determine how to display event in calendar
        if ($row['event_date'] === $row['event_end_date'] || empty($row['event_end_date'])) {
          $eventsByDate[$dayKey][] = ['color' => $row['color'], 'type' => 'single'];
        } elseif ($dayKey === date('Y-m-d', strtotime($row['event_date']))) {
          $eventsByDate[$dayKey][] = ['color' => $row['color'], 'type' => 'start'];
        } elseif ($dayKey === date('Y-m-d', strtotime($row['event_end_date']))) {
          $eventsByDate[$dayKey][] = ['color' => $row['color'], 'type' => 'end'];
        } else {
          $eventsByDate[$dayKey][] = ['color' => $row['color'], 'type' => 'middle'];
        }
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
    
    <!-- Dashboard Header with Centered Title and Gear Icon -->
    <div class="dashboard-header">
      <h2 class="dashboard-title">Dashboard</h2>
      <a href="profile.php" class="gear-link" title="User Settings">&#9881;</a>
    </div>

    <!-- Calendar Box -->
    <div class="calendar-box">
      <div class="calendar-header">
        <span><?php echo date('F Y', strtotime("$currentYear-$currentMonth-01")); ?></span>

        <!-- Navigation buttons -->
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

        <!-- Create new event -->
        <form action="create_event.php" method="GET">
          <button type="submit" class="create-event-button">Create Event</button>
        </form>
      </div>

      <!-- Calendar Table -->
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

            $dateKey = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
            $onclick = isset($eventsByDate[$dateKey]) ? "onclick='showDayEvents(\"$dateKey\")'" : "";

            echo "<td class='calendar-day' data-date='$dateKey' $onclick>";
            echo "<div>$day</div>";

            if (isset($eventsByDate[$dateKey])) {
              foreach ($eventsByDate[$dateKey] as $event) {
                $color = htmlspecialchars($event['color']);
                $type = $event['type'];
                echo "<div class='event-bar bar-$type' style='background-color: $color;'></div>";
              }
            }

            echo "</td>";
            $day++;
          }

          echo "</tr>";
          ?>
        </tbody>
      </table>
    </div>

    <!-- Upcoming Events List -->
    <div class="upcoming-events">
      <h4>Upcoming Events</h4>

      <?php if ($hasEvents): ?>
        <?php foreach ($events as $event): ?>
          <?php $eventDateKey = date('Y-m-d', strtotime($event['event_date'])); ?>
          <a href="view_event.php?id=<?php echo $event['id']; ?>" class="event-main-link">
            <div class="event-item event-row" data-date="<?php echo $eventDateKey; ?>">
              <div class="event-left">
                <span class="dot" style="background-color: <?php echo htmlspecialchars($event['color']); ?>;"></span>
                <strong class="event-title"><?php echo htmlspecialchars($event['title']); ?></strong>
              </div>
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

    <!-- Logout -->
    <form action="logout.php" method="POST">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
  </div>

  <!-- Event Modal -->
  <div id="dayEventModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h3>Events on <span id="modal-date"></span></h3>
      <div id="modal-events-list"></div>
    </div>
  </div>

  <script>
    const allEvents = <?php echo json_encode($events); ?>;

    function showDayEvents(date) {
      const modal = document.getElementById("dayEventModal");
      const modalDate = document.getElementById("modal-date");
      const modalEvents = document.getElementById("modal-events-list");

      const events = allEvents.filter(event => {
        const start = new Date(event.event_date);
        const end = event.event_end_date ? new Date(event.event_end_date) : start;
        const target = new Date(date);
        return target >= start && target <= end;
      });

      if (events.length === 0) return;

      modalDate.innerText = date;
      modalEvents.innerHTML = "";

      events.forEach(event => {
        const item = document.createElement("div");
        item.className = "modal-event";
        item.innerHTML = `
          <span class="dot" style="background-color: ${event.color};"></span>
          <strong>${event.title}</strong><br>
          <small>${event.event_time}</small>
        `;
        modalEvents.appendChild(item);
      });

      modal.style.display = "block";
    }

    function closeModal() {
      document.getElementById("dayEventModal").style.display = "none";
    }

    window.onclick = function(event) {
      const modal = document.getElementById("dayEventModal");
      if (event.target === modal) {
        closeModal();
      }
    }
  </script>
</body>
</html>
