<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit;
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

      <table class="calendar">
        <thead>
          <tr>
            <th>SUN</th><th>MON</th><th>TUE</th><th>WED</th><th>THU</th><th>FRI</th><th>SAT</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td></tr>
          <tr><td>8</td><td class="event-blue">9</td><td class="event-blue">10</td><td class="event-blue">11</td><td class="event-blue">12</td><td class="event-blue">13</td><td>14</td></tr>
          <tr><td>15</td><td class="event-yellow">16</td><td>17</td><td>18</td><td>19</td><td>20</td><td>21</td></tr>
          <tr><td>22</td><td class="event-green">23</td><td class="event-green">24</td><td class="event-green">25</td><td class="event-green">26</td><td class="event-green">27</td><td>28</td></tr>
          <tr><td>29</td><td>30</td><td></td><td></td><td></td><td></td><td></td></tr>
        </tbody>
      </table>
    </div>

    <div class="upcoming-events">
      <h4>Upcoming Events</h4>
      <div class="event-item">
        <div><span class="dot blue"></span> Team Meeting</div>
        <div>April 10, 2025 10:00AM</div>
      </div>
      <div class="event-item">
        <div><span class="dot yellow"></span> Workshop</div>
        <div>April 16, 2025 10:00AM</div>
      </div>
      <div class="event-item">
        <div><span class="dot green"></span> Group Assignment</div>
        <div>April 24, 2025 10:00AM</div>
      </div>
    </div>

    <form action="logout.php" method="POST">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
  </div>
</body>
</html>
