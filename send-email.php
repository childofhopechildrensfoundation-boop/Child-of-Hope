<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration from environment variables
        $smtpUser = getenv('SMTP_USER');
        $smtpPass = getenv('SMTP_PASS');
        $smtpFrom = getenv('SMTP_FROM') ?: $smtpUser;
        $smtpTo   = getenv('SMTP_TO') ?: $smtpUser;

        if (!$smtpUser || !$smtpPass || !$smtpTo) {
            throw new Exception('SMTP configuration is incomplete.');
        }

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender
        $mail->setFrom($smtpFrom, 'Website Contact Form');

        // Receiver
        $mail->addAddress($smtpTo);

        // Reply To
        $mail->addReplyTo($email, $name);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = "
            <h3>New Contact Form Message</h3>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Subject:</strong> {$subject}</p>
            <p><strong>Message:</strong><br>{$message}</p>
        ";

        $mail->send();

        echo '<div class="alert alert-success">Message sent successfully!</div>';

    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Error sending message. Please try again later.</div>';
    }
}
