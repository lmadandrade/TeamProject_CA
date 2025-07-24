<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

// DB connection
$host = 'localhost';
$db = 'event_planner';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

// Send email function
function sendReminderEmail($userEmail, $userName, $eventTitle, $eventDateTime) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'eventplanner416@gmail.com';
        $mail->Password = 'yoggwiahsfsywlmx';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('eventplanner416@gmail.com', 'Event Planner Team');
        $mail->addAddress($userEmail, $userName);

        $mail->isHTML(true);
        $mail->Subject = 'Reminder: ' . $eventTitle;
        $mail->Body = "
            <h2>Event Reminder</h2>
            <p>Hi <strong>$userName</strong>,</p>
            <p>This is a reminder for your upcoming event:</p>
            <ul>
                <li><strong>Event:</strong> $eventTitle</li>
                <li><strong>Date & Time:</strong> $eventDateTime</li>
            </ul>
            <p>Best regards,<br>Event Planner Team</p>
        ";
        $mail->send();
        echo "Reminder sent to $userEmail<br>";
    } catch (Exception $e) {
        echo "Error sending to $userEmail: {$mail->ErrorInfo}<br>";
    }
}

// Check if run via auto mode (?auto=1)
if (isset($_GET['auto']) && $_GET['auto'] == 1) {
    $now = new DateTime();

    // Fetch all events and join with users
    $sql = "SELECT e.*, u.email AS user_email, u.username 
            FROM events e
            JOIN users u ON e.user_id = u.id";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $eventDateTime = new DateTime($row['event_date']);
        $hoursToEvent = ($eventDateTime->getTimestamp() - $now->getTimestamp()) / 3600;

        // Round to 1 decimal place
        $hoursToEventRounded = round($hoursToEvent, 1);

        // 24hr reminder
        if ($row['reminder_24hr'] && !$row['reminder_24hr_sent'] && abs($hoursToEventRounded - 24) <= 0.2) {
            sendReminderEmail($row['user_email'], $row['username'], $row['title'], $row['event_date']);
            $conn->query("UPDATE events SET reminder_24hr_sent = 1 WHERE id = {$row['id']}");
        }

        // 1hr reminder
        if ($row['reminder_1hr'] && !$row['reminder_1hr_sent'] && abs($hoursToEventRounded - 1) <= 0.2) {
            sendReminderEmail($row['user_email'], $row['username'], $row['title'], $row['event_date']);
            $conn->query("UPDATE events SET reminder_1hr_sent = 1 WHERE id = {$row['id']}");
        }

        // Custom reminder
        if (!empty($row['reminder_custom_hours']) && !$row['reminder_custom_sent']) {
            $customHours = floatval($row['reminder_custom_hours']);
            if (abs($hoursToEventRounded - $customHours) <= 0.2) {
                sendReminderEmail($row['user_email'], $row['username'], $row['title'], $row['event_date']);
                $conn->query("UPDATE events SET reminder_custom_sent = 1 WHERE id = {$row['id']}");
            }
        }
    }

    exit;
}

// Manual test form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userEmail = $_POST['email'] ?? '';
    $userName = $_POST['name'] ?? 'Guest';
    $eventTitle = $_POST['event'] ?? 'Untitled Event';
    $eventDate = $_POST['date'] ?? 'Unknown Date';

    sendReminderEmail($userEmail, $userName, $eventTitle, $eventDate);
} else {
    echo '
    <form method="POST" action="notification.php">
        <label>User Email: <input type="email" name="email" required></label><br><br>
        <label>User Name: <input type="text" name="name" required></label><br><br>
        <label>Event Title: <input type="text" name="event" required></label><br><br>
        <label>Event Date & Time: <input type="datetime-local" name="date" required></label><br><br>
        <button type="submit">Send Test Reminder</button>
    </form>';
}
?>
