<?php

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// send event created email
function sendEventCreatedEmail($recipientEmail, $eventDetails) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'eventzcollegeproject@gmail.com';
        $mail->Password = 'wbhy opjm vqlt mojr';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // recipients
        $mail->setFrom('eventzcollegeproject@gmail.com', 'Eventz App');
        $mail->addAddress($recipientEmail);

        // mail content
        $mail->Subject = 'Your "' . $eventDetails['title'] . '" event for ' . $eventDetails['event_date'] . ' has been created';
        $mail->isHTML(true);
        $mail->Body = "
            <p><b>Title:</b> {$eventDetails['title']}</p>
            <p><b>Date:</b> {$eventDetails['event_date']}</p>
            <p><b>Time:</b> {$eventDetails['event_time']}</p>
            <p><b>Location:</b> {$eventDetails['location']}</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        //echo "Email could not be sent: Mail Error {$mail->ErrorInfo}";
    }

}

// function to send invites
function sendEventInvitations($emails, $eventDetails) { 
    foreach ($emails as $email) { 
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'eventzcollegeproject@gmail.com';
            $mail->Password = 'wbhy opjm vqlt mojr';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // recipients
            $mail->setFrom('eventzcollegeproject@gmail.com', 'Eventz App');
            // sends email to each person in emails array
            $mail->addAddress($email);

            // content
            $mail->Subject = 'You are invited: "' . $eventDetails['title'] . '" ' . ' on ' . $eventDetails['event_date'];
            $mail->isHTML(true);
            $mail->Body = "
                <p>You have been invited to an event. Log in or or make an account to accept and track</p>
                <p><b>Title:</b> {$eventDetails['title']}</p>
                <p><b>Date:</b> {$eventDetails['event_date']}</p>
                <p><b>Time:</b> {$eventDetails['event_time']}</p>
                <p><b>Location:</b> {$eventDetails['location']}</p>
            ";

            // send mail
            $mail->send();

        } catch (Exception $e) {
        }
    }
}

// function to check for reminders 
function sendReminderEmails($conn) {

    // sql query to check if reminder needs to be sent based on current time
    $sql = "SELECT 
        ep.id AS ep_id,
        ep.event_id,
        u.email AS recipient_email,              
        e.title,
        e.location,
        e.event_date,
        ep.reminder_minutes_before AS minutes_before,
        e.event_date - INTERVAL ep.reminder_minutes_before MINUTE AS remind_at
        FROM event_participants ep
        JOIN events e ON e.id = ep.event_id
        JOIN users  u ON u.id = ep.user_id
        WHERE ep.reminder_sent = 0
        AND e.event_date > NOW()
        AND ep.status = 'accepted'
        HAVING remind_at <= NOW()
        ";

    // run query and store in result 
    $result = $conn->query($sql);

    // query validation 
    if (!$result || $result->num_rows === 0) {
        return;
    }
    
    // loop through each result
    while ($event = $result->fetch_assoc()) {
        // get event time and date details on their own
        $date_only = substr($event['event_date'], 0, 10);
        $time_only = substr($event['event_date'], 11, 5);

        $minsUntil   = (int)$event['minutes_before'];
        $hoursUntil = $minsUntil / 60;

        $mail = new PHPMailer(true);

          try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'eventzcollegeproject@gmail.com';
            $mail->Password = 'wbhy opjm vqlt mojr';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            // recipient
            $mail->setFrom('eventzcollegeproject@gmail.com', 'Eventz Reminder');
            $mail->addAddress($event['recipient_email']);

            // email content
            $mail->Subject = 'Reminder: "' . $event['title'] . '" on ' . $date_only . ' at ' . $time_only;
            $mail->isHTML(true);
            $mail->Body = "
                <p><b>Title:</b> {$event['title']}</p>
                <p><b>Date:</b> {$date_only}</p>
                <p><b>Time:</b> {$time_only}</p>
                <p><b>Location:</b> {$event['location']}</p>
                <p>This is a reminder that your event is due in approximately {$hoursUntil} hours. </p>
            ";

            $mail->send();

            // update reminder send in event_patricipants so it wont be resent
            $updateReminderStatus = $conn->prepare("UPDATE event_participants SET reminder_sent = 1 WHERE id = ?");
            $updateReminderStatus->bind_param("i", $event['ep_id']);
            $updateReminderStatus->execute();
            $updateReminderStatus->close();

          } catch (Exception $e) {

          }
    }
}

?>