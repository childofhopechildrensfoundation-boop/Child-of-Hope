<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set JSON header
header('Content-Type: application/json');

// Nonprofit email address
$nonprofit_email = 'childofhopechildrensfoundation@gmail.com';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get form data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Validation
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
    
    // If there are errors, return them
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'errors' => $errors,
            'message' => implode(', ', $errors)
        ]);
        exit;
    }
    
    // Sanitize input
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    
    // Prepare email content
    $email_subject = "New Contact Form Submission: " . $subject;
    
    $email_body = "
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
                    <div class='field-value'>" . $name . "</div>
                </div>
                
                <div class='field'>
                    <div class='field-label'>Email:</div>
                    <div class='field-value'><a href='mailto:" . $email . "'>" . $email . "</a></div>
                </div>
                
                <div class='field'>
                    <div class='field-label'>Subject:</div>
                    <div class='field-value'>" . $subject . "</div>
                </div>
                
                <div class='field'>
                    <div class='field-label'>Message:</div>
                    <div class='field-value'>" . nl2br($message) . "</div>
                </div>
                
                <div class='footer'>
                    <p>This message was sent from your website contact form.</p>
                    <p>Sender's IP: " . $_SERVER['REMOTE_ADDR'] . "</p>
                    <p>Submitted at: " . date('Y-m-d H:i:s') . "</p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: Child of Hope Contact Form" . "\r\n";
    
    // Send email to nonprofit
    $mail_sent = mail($nonprofit_email, $email_subject, $email_body, $headers);
    
    // Send confirmation email to sender
    $confirmation_subject = "We Received Your Message - Child of Hope";
    $confirmation_body = "
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
                <p>Hello <span class='highlight'>" . $name . "</span>,</p>
                
                <p>We have received your message and appreciate you taking the time to contact us. Our team will review your inquiry and respond to you as soon as possible.</p>
                
                <h3>Your Message Details:</h3>
                <p><strong>Subject:</strong> " . $subject . "</p>
                
                <p>If you have any urgent matters, please feel free to call or WhatsApp us at <strong>+256 759 687 471</strong>.</p>
                
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
    </html>
    ";
    
    $confirmation_headers = "MIME-Version: 1.0" . "\r\n";
    $confirmation_headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $confirmation_headers .= "From: " . $nonprofit_email . "\r\n";
    $confirmation_headers .= "X-Mailer: Child of Hope Contact Form" . "\r\n";
    
    mail($email, $confirmation_subject, $confirmation_body, $confirmation_headers);
    
    // Response
    if ($mail_sent) {
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
