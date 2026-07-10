<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$nonprofit_email = getenv('SMTP_TO') ?: 'childofhopechildrensfoundation@gmail.com';

function buildContactEmailBody(string $name, string $email, string $subject, string $message, string $phone = ''): string
{
    $phoneBlock = empty($phone) ? '' : "<div class='field'><div class='field-label'>Phone:</div><div class='field-value'>" . htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') . "</div></div>";

    return "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #001D23; color: white; padding: 20px; border-radius: 5px 5px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 0 0 5px 5px; }
            .field { margin-bottom: 15px; }
            .field-label { font-weight: bold; color: #001D23; }
            .field-value { padding: 10px; background: white; border-left: 3px solid #FF6F0F; margin-top: 5px; }
            .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Contact Form Submission</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='field-label'>Name:</div>
                    <div class='field-value'>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</div>
                </div>
                <div class='field'>
                    <div class='field-label'>Email:</div>
                    <div class='field-value'><a href='mailto:" . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</a></div>
                </div>
                " . $phoneBlock . "
                <div class='field'>
                    <div class='field-label'>Subject:</div>
                    <div class='field-value'>" . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . "</div>
                </div>
                <div class='field'>
                    <div class='field-label'>Message:</div>
                    <div class='field-value'>" . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "</div>
                </div>
                <div class='footer'>
                    <p>This message was sent from your website contact form.</p>
                    <p>Sender's IP: " . htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</p>
                    <p>Submitted at: " . date('Y-m-d H:i:s') . "</p>
                </div>
            </div>
        </div>
    </body>
    </html>";
}

function buildConfirmationEmailBody(string $name, string $subject): string
{
    return "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #001D23; color: white; padding: 20px; border-radius: 5px 5px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 0 0 5px 5px; }
            .highlight { color: #FF6F0F; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Thank You for Contacting Child of Hope</h2>
            </div>
            <div class='content'>
                <p>Hello <span class='highlight'>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</span>,</p>
                <p>We have received your message and appreciate you taking the time to contact us. Our team will review your inquiry and respond to you as soon as possible.</p>
                <h3>Your Message Details:</h3>
                <p><strong>Subject:</strong> " . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . "</p>
                <p>If you have any urgent matters, please feel free to call or WhatsApp us at <strong>+256 731 751 309</strong>.</p>
                <p>Thank you for supporting Child of Hope Children's Foundation!</p>
                <p>
                    Best regards,<br>
                    <strong>Child of Hope Team</strong><br>
                    Nansana Yesu Amala, Kampala Uganda<br>
                    Email: <a href='mailto:childofhopechildrensfoundation@gmail.com'>childofhopechildrensfoundation@gmail.com</a>
                </p>
            </div>
        </div>
    </body>
    </html>";
}

function sendMailWithMailer(string $to, string $subject, string $body, string $fromAddress = '', string $replyTo = '', string $replyName = ''): bool
{
    $mail = new PHPMailer(true);

    try {
        $smtpHost = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $smtpPort = (int) (getenv('SMTP_PORT') ?: 587);
        $smtpUser = getenv('SMTP_USER');
        $smtpPass = getenv('SMTP_PASS');
        $smtpFrom = getenv('SMTP_FROM') ?: $fromAddress ?: $smtpUser ?: 'noreply@childofhope.org';
        $smtpTo = getenv('SMTP_TO') ?: $to;
        $smtpSecure = getenv('SMTP_SECURE') ?: 'tls';

        if ($smtpUser && $smtpPass) {
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->Port = $smtpPort;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;

            if (strtolower($smtpSecure) === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif (strtolower($smtpSecure) === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
        } else {
            $mail->isMail();
        }

        $mail->CharSet = 'UTF-8';
        $mail->setFrom($smtpFrom, 'Child of Hope Website');
        $mail->addAddress($smtpTo, 'Child of Hope');
        if (!empty($replyTo)) {
            $mail->addReplyTo($replyTo, $replyName ?: $replyTo);
        }
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags(preg_replace('/<br\s*\/?\>/i', "\n", $body));
        $mail->send();

        return true;
    } catch (Exception $e) {
        error_log('PHPMailer error: ' . $e->getMessage());
        return false;
    }
}

function sendMailWithFallback(string $to, string $subject, string $body, string $fromAddress = '', string $replyTo = ''): bool
{
    $fromHeader = !empty($fromAddress) ? $fromAddress : 'noreply@childofhope.org';
    $replyHeader = !empty($replyTo) ? $replyTo : $fromHeader;
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: {$fromHeader}\r\n";
    $headers .= "Reply-To: {$replyHeader}\r\n";
    $headers .= "X-Mailer: Child of Hope Contact Form\r\n";

    return @mail($to, $subject, $body, $headers);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    $errors = [];

    if (empty($name) || strlen($name) < 2) {
        $errors[] = 'Please enter a valid name (at least 2 characters)';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }

    if (empty($subject) || strlen($subject) < 3) {
        $errors[] = 'Please enter a subject (at least 3 characters)';
    }

    if (empty($message) || strlen($message) < 10) {
        $errors[] = 'Please enter a message (at least 10 characters)';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'errors' => $errors,
            'message' => implode(', ', $errors)
        ]);
        exit;
    }

    $email_subject = 'New Contact Form Submission: ' . $subject;
    $email_body = buildContactEmailBody($name, $email, $subject, $message, $phone);
    $confirmation_subject = 'We Received Your Message - Child of Hope';
    $confirmation_body = buildConfirmationEmailBody($name, $subject);

    $organization_sent = sendMailWithMailer($nonprofit_email, $email_subject, $email_body, getenv('SMTP_FROM') ?: $email, $email, $name);
    $confirmation_sent = sendMailWithMailer($email, $confirmation_subject, $confirmation_body, getenv('SMTP_FROM') ?: $nonprofit_email, $nonprofit_email, 'Child of Hope');

    if (!$organization_sent) {
        $organization_sent = sendMailWithFallback($nonprofit_email, $email_subject, $email_body, getenv('SMTP_FROM') ?: $email, $email);
    }

    if (!$confirmation_sent) {
        $confirmation_sent = sendMailWithFallback($email, $confirmation_subject, $confirmation_body, getenv('SMTP_FROM') ?: $nonprofit_email, $nonprofit_email);
    }

    if ($organization_sent && $confirmation_sent) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Thank you! Your message has been sent successfully. We will respond to you soon.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'There was an error sending your message. Please try again later or contact us directly.'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}
?>
