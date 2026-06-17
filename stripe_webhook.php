<?php
/**
 * Stripe Webhook Handler
 * Handles payment confirmation webhooks from Stripe
 * Verifies payment completion and sends receipt emails
 */

// Include Stripe PHP library
require_once __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Load .env file manually
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envLines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        putenv("$key=$value");
    }
}

// Get Stripe webhook secret
$stripeWebhookSecret = getenv('STRIPE_WEBHOOK_SECRET');
if (empty($stripeWebhookSecret)) {
    error_log('Stripe Webhook Error: Missing webhook secret');
    http_response_code(500);
    exit;
}

// Get request body
$body = file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

// Verify webhook signature
try {
    $event = \Stripe\Webhook::constructEvent($body, $sig_header, $stripeWebhookSecret);
} catch (\UnexpectedValueException $e) {
    error_log('Stripe Webhook: Invalid payload - ' . $e->getMessage());
    http_response_code(400);
    exit;
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    error_log('Stripe Webhook: Invalid signature - ' . $e->getMessage());
    http_response_code(400);
    exit;
}

// Handle charge.completed event (payment successful)
if ($event->type === 'charge.completed') {
    $charge = $event->data->object;
    
    // Extract metadata
    $reference = $charge->metadata->reference ?? 'UNKNOWN';
    $donorName = $charge->metadata->donor_name ?? 'Donor';
    $donorEmail = $charge->metadata->donor_email ?? '';
    $cause = $charge->metadata->cause ?? 'General Donation';
    $amount = $charge->amount / 100; // Convert cents to dollars
    $status = $charge->paid ? 'COMPLETED' : 'FAILED';
    $chargeId = $charge->id;
    
    // Log transaction
    $transactionLog = __DIR__ . '/stripe_transactions.log';
    $logEntry = json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'charge_id' => $chargeId,
        'reference' => $reference,
        'amount' => $amount,
        'status' => $status,
        'donor_name' => $donorName,
        'donor_email' => $donorEmail,
        'cause' => $cause,
        'http_code' => 200,
    ]) . PHP_EOL;
    
    file_put_contents($transactionLog, $logEntry, FILE_APPEND | LOCK_EX);
    
    // Send receipt email if payment successful
    if ($status === 'COMPLETED' && !empty($donorEmail)) {
        $organizationEmail = 'noreply@childofhope.org';
        $subject = 'Donation Receipt - Child of Hope Children\'s Foundation';
        
        $message = "Dear " . htmlspecialchars($donorName) . ",\n\n";
        $message .= "Thank you for your generous donation of USD " . number_format($amount, 2) . " to Child of Hope Children's Foundation.\n\n";
        $message .= "Donation Details:\n";
        $message .= "Amount: USD " . number_format($amount, 2) . "\n";
        $message .= "Reference: " . htmlspecialchars($reference) . "\n";
        $message .= "Charge ID: " . htmlspecialchars($chargeId) . "\n";
        $message .= "Purpose: " . htmlspecialchars($cause) . "\n";
        $message .= "Date: " . date('Y-m-d H:i:s') . "\n\n";
        $message .= "Your generosity will make a real difference in the lives of our children.\n\n";
        $message .= "With gratitude,\n";
        $message .= "Child of Hope Children's Foundation Team\n";
        
        $headers = "From: " . $organizationEmail . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        mail($donorEmail, $subject, $message, $headers);
    }
    
    error_log("Stripe: Successful donation - Ref: $reference, Amount: USD $amount from $donorEmail, Charge: $chargeId");
}

// Handle charge.failed event
if ($event->type === 'charge.failed') {
    $charge = $event->data->object;
    $reference = $charge->metadata->reference ?? 'UNKNOWN';
    $donorEmail = $charge->metadata->donor_email ?? '';
    $amount = $charge->amount / 100;
    
    // Log failed transaction
    $transactionLog = __DIR__ . '/stripe_transactions.log';
    $logEntry = json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'charge_id' => $charge->id,
        'reference' => $reference,
        'amount' => $amount,
        'status' => 'FAILED',
        'failure_reason' => $charge->failure_message ?? 'Unknown',
        'http_code' => 400,
    ]) . PHP_EOL;
    
    file_put_contents($transactionLog, $logEntry, FILE_APPEND | LOCK_EX);
    
    error_log("Stripe: Payment failed - Ref: $reference, Amount: USD $amount, Reason: {$charge->failure_message}");
}

// Return 200 OK to acknowledge webhook receipt
http_response_code(200);
echo json_encode(['received' => true]);
?>
