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
        $mail->Subject = 'Your "' . $eventDetails['title'] . '" event has been created!';
        $mail->isHTML(true);
        $mail->Body = "
            <p><b>Title:</b> {$eventDetails['title']}</p>
            <p><b>Date:</b> {$eventDetails['event_date']}</p>
            <p><b>Time:</b> {$eventDetails['event_time']}</p>
            <p><b>Location:</b> {$eventDetails['location']}</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        echo "Email could not be sent: Mail Error {$mail->ErrorInfo}";
    }

}

// function to check for reminders 
function sendReminderEmails($conn) {

    // sql query to check if reminder needs to be sent based on current time
    $sql = "
        SELECT e.*, u.email
        FROM events e
        JOIN users u ON e.user_id = u.id
        WHERE TIMESTAMPDIFF(MINUTE, NOW(), TIMESTAMP(e.event_date, e.event_time)) <= e.reminder_time * 60
            AND TIMESTAMPDIFF(MINUTE, NOW(), TIMESTAMP(e.event_date, e.event_time)) > 0
    ";

    // run query and store in result 
    $result = $conn->query($sql);

    // check if query returned anything
    if ($result->num_rows > 0) {
        // loop through each event in result
         while ($event = $result->fetch_assoc()) {
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'eventzcollegeproject@gmail.com';
                $mail->Password = 'wbhy opjm vqlt mojr';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('eventzcollegeproject@gmail.com', 'Eventz Reminder');
                $mail->addAddress($event['email']);

                $mail->Subject = 'Reminder: Upcoming Event "' . $event['title'] . '"';
                $mail->isHTML(true);

                // get date only
                $date_only = substr($event['event_date'], 0, 10);

                $mail->Body = "
                    <p><b>Title:</b> {$event['title']}</p>
                    <p><b>Date:</b> {$event['date_only']}</p>
                    <p><b>Time:</b> {$event['event_time']}</p>
                    <p><b>Location:</b> {$event['location']}</p>
                    <p>This is a reminder that you event is due in {$event['reminder_time']} hours</p>
                ";

                $mail->send();
            }catch (Exception $e) {
                echo "Email could not be sent: Mail Error {$mail->ErrorInfo}";
            }
         }

    }

}

