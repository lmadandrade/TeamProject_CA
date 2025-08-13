<?php
// Show all errors so I can debug if something goes wrong
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start the session to get user info
session_start();

// If user isn't logged in, send them back to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// include database connection file
require_once "db.php";
// include connection to send mail file
require_once "sendMail.php";

// get the user ID from session
$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

// collect all the input values from the form
$title = $_POST['title'];
$event_date = $_POST['event_date']; // required
$event_end_date = $_POST['event_end_date']; // optional
$event_time = $_POST['event_time'];
$location = $_POST['location'];
$participants = $_POST['participants'];
$social_link = $_POST['social_link'];
$reminder_timing = $_POST['reminder_time'];
$color = $_POST['color'];
$description = $_POST['description'];

$emails = [];

// make sure required fields are not empty
if (empty($title) || empty($event_date) || empty($event_time) || empty($color) || empty($reminder_timing)) {
    echo "<script>alert('Please fill out the required '); window.location.href='create_event.php';</script>";
    exit;
}

// concatenate date and time to get variable with full datetime
$event_date = $event_date . ' ' . $event_time . ':00';

// check that event date is in the future
if (strtotime($event_date) <= time()) {
    echo "<script>alert('Event Must be in the future '); window.location.href='create_event.php';</script>";
    exit;
}

// get event start time in seconds
$eventSTS = strtotime($event_date); 
$secondsUntilEvent = $eventSTS - time(); // find how many seconds between now and event
$reminder_hours = (int)$reminder_timing;
$reminder_seconds = $reminder_hours * 3600;

// check that your reminder time happens before event
if (($reminder_seconds) >= $secondsUntilEvent) {
    echo "<script>alert('Reminder must be before the event');window.location.href='create_event.php'; </script>";
    exit;

}

// check that end date isnt empty and if isnt make sure it comes after event date
if (!empty($event_end_date)) {
    $event_end_date = $event_end_date . ' ' . $event_time . ':00';
    if (strtotime($event_end_date) <= strtotime($event_date)) {
        echo "<script>alert('End date must come after start date '); window.location.href='create_event.php';</script>";
        exit;
    }
} else {
    $event_end_date = null;
}

// convert reminder timing to int
$reminder_timing = (int)$reminder_timing;
// convert reminder timing to minutes
$reminder_timing = $reminder_timing * 60;


// write the SQL query to save the event
$stmt = $conn->prepare("INSERT INTO events
(user_id, title, event_date, event_end_date, location, social_link, description)
VALUES (?, ?, ?, ?, ?, ?, ?)");

// attach values to the query
$stmt->bind_param("issssss", $user_id, $title, $event_date, $event_end_date, $location, $social_link, $description);

// if there is an error
if (!$stmt->execute()) {
    echo "Error saving event: " . $stmt->error;
    $stmt->close();
    $conn->close();
    exit;
}

// get event id for next table insert
$eventId = $stmt->insert_id;
$stmt->close();


// *** code for inserting into event_partipants ***

// insert invent creator in first
$stmtEPInsertOwner = $conn->prepare("INSERT INTO event_participants 
(event_id, user_id, status, reminder_minutes_before, color)
VALUES (?, ?, 'accepted', ?, ?)
");

$stmtEPInsertOwner->bind_param("iiis", $eventId, $user_id, $reminder_timing, $color);
$stmtEPInsertOwner->execute();
$stmtEPInsertOwner->close();

// invite other users if the partipants field isnt empty
if ($participants !== "") {
    // split emails by ' / and space
    $unsortedEmails = preg_split('/[,\s;]+/', $participants);

    // loop through emails
    foreach($unsortedEmails as $email) {
        // set email to lower and remove and white space left
        $email = strtolower(trim($email));
        // if email isnt empty and it doesnt match the users email add to array
        if ($email !== "" && $email !== $user_email) { 
            $emails[] = $email;
        }
    }

    $emails = array_unique($emails); // remove duplicates

    // do if emails isnt empty
    if (!empty($emails)) {
        // registered user insert
        $stmtEPInsertUser = $conn->prepare("INSERT INTO event_participants 
        (event_id, user_id, status, reminder_minutes_before) 
        SELECT ?, id, 'pending', ?
        FROM users
        WHERE email = ?");

        // guest user insert
        $stmtEPInsertGuest = $conn->prepare(" INSERT INTO 
        event_participants (event_id, email, status, reminder_minutes_before)
        VALUES (?, ?, 'pending', ?)
        ");

        foreach ($emails as $email) { 
            // try inserting a user
            $stmtEPInsertUser->bind_param("iis", $eventId, $reminder_timing, $email);
            $stmtEPInsertUser->execute();

            // if there was no matching user, insert guest
            if ($stmtEPInsertUser->affected_rows === 0) {
                $stmtEPInsertGuest->bind_param("isi", $eventId, $email, $reminder_timing);
                $stmtEPInsertGuest->execute();
            }
        }
        $stmtEPInsertUser->close();
        $stmtEPInsertGuest->close();
    }

}

// get event details for email
$eventDetails = [
    'title'      => $title,
    'event_date' => $event_date, 
    'event_time' => $event_time, 
    'location'   => $location,
];

// send email to creator 
sendEventCreatedEmail($user_email, $eventDetails);

// send emails to invities only if there is an emails file
if (!empty($emails)) { 
    sendEventInvitations($emails, $eventDetails);
}

header("Location: dashboard.php");
exit;
?>
