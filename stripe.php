<?php
/**
 * Stripe Payment Handler
 * Creates a Stripe Checkout session for donations
 * Environment variables required: STRIPE_SECRET_KEY
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
        if (strpos(trim($line), '#') === 0) continue; // Skip comments
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        putenv("$key=$value");
    }
}

session_start();

// Get Stripe secret key
$stripeSecretKey = getenv('STRIPE_SECRET_KEY');
if (empty($stripeSecretKey)) {
    error_log('Stripe Error: Missing API secret key');
    die(json_encode(['error' => 'Payment gateway not configured. Please contact support.']));
}

// Set Stripe API key
\Stripe\Stripe::setApiKey($stripeSecretKey);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token validation
    $csrfToken = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';
    if (empty($csrfToken)) {
        error_log('Stripe Error: Missing CSRF token');
        die(json_encode(['error' => 'Invalid request. Please try again.']));
    }

    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $donorEmail = isset($_POST['donor_email']) ? filter_var($_POST['donor_email'], FILTER_SANITIZE_EMAIL) : '';
    $donorName = isset($_POST['donor_name']) ? trim(htmlspecialchars($_POST['donor_name'], ENT_QUOTES, 'UTF-8')) : 'Donor';
    $donationCause = isset($_POST['cause']) ? trim(htmlspecialchars($_POST['cause'], ENT_QUOTES, 'UTF-8')) : 'General Donation';

    // Validate inputs
    if ($amount < 1) {
        error_log('Stripe Error: Invalid amount: ' . $amount);
        die(json_encode(['error' => 'Minimum donation is $1 USD']));
    }

    if (!filter_var($donorEmail, FILTER_VALIDATE_EMAIL)) {
        error_log('Stripe Error: Invalid email: ' . $donorEmail);
        die(json_encode(['error' => 'Invalid email address']));
    }

    if (empty($donorName) || strlen($donorName) < 2) {
        error_log('Stripe Error: Invalid donor name');
        die(json_encode(['error' => 'Please enter your full name']));
    }

    // Generate unique transaction reference
    $reference = 'DONATION_' . time() . '_' . bin2hex(random_bytes(4));

    // Store transaction info in session for webhook verification
    $_SESSION['stripe_transaction'] = array(
        'reference' => $reference,
        'amount' => $amount,
        'donor_email' => $donorEmail,
        'donor_name' => $donorName,
        'cause' => $donationCause,
        'timestamp' => time(),
    );

    // Determine success/cancel URLs
    $successUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
        ? "https://" . $_SERVER['HTTP_HOST'] . "/donate.html?status=success&amount=" . urlencode($amount) . "&ref=" . urlencode($reference)
        : "http://" . $_SERVER['HTTP_HOST'] . "/donate.html?status=success&amount=" . urlencode($amount) . "&ref=" . urlencode($reference);

    $cancelUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
        ? "https://" . $_SERVER['HTTP_HOST'] . "/donate.html?status=cancelled"
        : "http://" . $_SERVER['HTTP_HOST'] . "/donate.html?status=cancelled";

    try {
        // Create Stripe Checkout Session
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Donation to Child of Hope',
                        'description' => htmlspecialchars_decode($donationCause),
                    ],
                    'unit_amount' => intval($amount * 100), // Amount in cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'customer_email' => $donorEmail,
            'metadata' => [
                'reference' => $reference,
                'donor_name' => $donorName,
                'donor_email' => $donorEmail,
                'cause' => $donationCause,
                'timestamp' => time(),
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'reference' => $reference,
                    'donor_name' => $donorName,
                    'amount_usd' => $amount,
                ]
            ]
        ]);

        // Log transaction attempt
        error_log("Stripe: Initiating donation of USD $amount from $donorEmail (Ref: $reference, Session: {$checkout_session->id})");

        // Return checkout URL for redirect
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'checkout_url' => $checkout_session->url,
            'session_id' => $checkout_session->id,
            'reference' => $reference
        ]);
        exit;

    } catch (\Stripe\Exception\ApiErrorException $e) {
        error_log('Stripe API Error: ' . $e->getMessage());
        die(json_encode(['error' => 'Payment processing error: ' . $e->getMessage()]));
    } catch (Exception $e) {
        error_log('Stripe Error: ' . $e->getMessage());
        die(json_encode(['error' => 'An error occurred. Please try again.']));
    }
} else {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed']));
}
?>
