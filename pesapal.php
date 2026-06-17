<?php
/**
 * Pesapal Payment Handler
 * Initiates donation transactions via Pesapal API
 * Environment variables required: PESAPAL_CONSUMER_KEY, PESAPAL_CONSUMER_SECRET
 */

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

// Get environment variables (set in .env or server config)
$consumerKey = getenv('PESAPAL_CONSUMER_KEY');
$consumerSecret = getenv('PESAPAL_CONSUMER_SECRET');
$isSandbox = getenv('PESAPAL_SANDBOX') !== false ? getenv('PESAPAL_SANDBOX') === 'true' : true;

// Validate configuration
if (empty($consumerKey) || empty($consumerSecret)) {
    error_log('Pesapal Error: Missing API credentials in environment variables');
    die(json_encode(['error' => 'Payment gateway not configured. Please contact support.']));
}

// Pesapal API endpoints
$submitUrl = $isSandbox
    ? 'https://sandbox.pesapal.com/api/urlbuilder/api/PostPesapalDirectOrderV4'
    : 'https://www.pesapal.com/api/urlbuilder/api/PostPesapalDirectOrderV4';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simple CSRF token validation (client-generated token is passed through)
    // In production, implement server-side token generation for stronger security
    $csrfToken = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';
    if (empty($csrfToken)) {
        error_log('Pesapal Error: Missing CSRF token');
        die(json_encode(['error' => 'Invalid request. Please try again.']));
    }

    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $donorEmail = isset($_POST['donor_email']) ? filter_var($_POST['donor_email'], FILTER_SANITIZE_EMAIL) : '';
    $donorName = isset($_POST['donor_name']) ? trim(htmlspecialchars($_POST['donor_name'], ENT_QUOTES, 'UTF-8')) : 'Donor';
    $donationCause = isset($_POST['cause']) ? trim(htmlspecialchars($_POST['cause'], ENT_QUOTES, 'UTF-8')) : 'General Donation';

    // Validate inputs
    if ($amount < 1) {
        error_log('Pesapal Error: Invalid amount: ' . $amount);
        die(json_encode(['error' => 'Minimum donation is $1 USD']));
    }

    if (!filter_var($donorEmail, FILTER_VALIDATE_EMAIL)) {
        error_log('Pesapal Error: Invalid email: ' . $donorEmail);
        die(json_encode(['error' => 'Invalid email address']));
    }

    if (empty($donorName) || strlen($donorName) < 2) {
        error_log('Pesapal Error: Invalid donor name');
        die(json_encode(['error' => 'Please enter your full name']));
    }

    // Generate unique transaction reference
    $reference = 'DONATION_' . time() . '_' . bin2hex(random_bytes(4));

    // Callback URL (adjust to your domain)
    $callbackUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
        ? "https://" . $_SERVER['HTTP_HOST'] . "/pesapal_callback.php"
        : "http://" . $_SERVER['HTTP_HOST'] . "/pesapal_callback.php";

    // OAuth signature generation
    $timestamp = time();
    $nonce = bin2hex(random_bytes(16));
    
    // Build request parameters
    $params = array(
        'oauth_consumer_key' => $consumerKey,
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_signature_version' => '1.0',
        'oauth_timestamp' => $timestamp,
        'oauth_nonce' => $nonce,
        'oauth_version' => '1.0',
        'amount' => $amount,
        'currency' => 'USD',
        'description' => $donationCause,
        'reference' => $reference,
        'first_name' => $donorName,
        'last_name' => 'Donor',
        'email' => $donorEmail,
        'phonenumber' => '',
        'callback_url' => $callbackUrl,
    );

    // Sort parameters for signature
    ksort($params);

    // Build base string
    $baseString = 'POST&' . urlencode($submitUrl) . '&';
    $paramString = '';
    foreach ($params as $key => $value) {
        $paramString .= urlencode($key) . '=' . urlencode($value) . '&';
    }
    $paramString = rtrim($paramString, '&');
    $baseString .= urlencode($paramString);

    // Generate OAuth signature
    $signingKey = urlencode($consumerSecret) . '&';
    $signature = base64_encode(hash_hmac('sha1', $baseString, $signingKey, true));
    $params['oauth_signature'] = $signature;

    // Store transaction info in session for callback verification
    $_SESSION['pesapal_transaction'] = array(
        'reference' => $reference,
        'amount' => $amount,
        'donor_email' => $donorEmail,
        'donor_name' => $donorName,
        'cause' => $donationCause,
        'timestamp' => $timestamp,
    );

    // Build authorization header
    $authHeader = 'OAuth ';
    foreach ($params as $key => $value) {
        if (strpos($key, 'oauth_') === 0) {
            $authHeader .= $key . '="' . urlencode($value) . '", ';
        }
    }
    $authHeader = rtrim($authHeader, ', ');

    // Log transaction attempt
    error_log("Pesapal: Initiating donation of USD $amount from $donorEmail (Ref: $reference)");

    // Prepare cURL request
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $submitUrl,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Authorization: ' . $authHeader,
            'Content-Type: application/x-www-form-urlencoded',
        ),
        CURLOPT_POSTFIELDS => http_build_query($params),
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT => 30,
    ));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    
    // No longer use curl_close() in PHP 8.0+ (automatically closed)
    unset($ch);

    if ($curlError) {
        error_log("Pesapal cURL Error: $curlError");
        die(json_encode(['error' => 'Network error. Please try again later.']));
    }

    if ($httpCode === 200) {
        error_log("Pesapal: Redirecting to payment page for Ref: $reference");
        // Redirect to Pesapal payment page
        header('Location: ' . $response);
        exit;
    } else {
        // Log error and return JSON
        error_log("Pesapal API Error: HTTP $httpCode - Response: " . substr($response, 0, 500));
        die(json_encode(['error' => 'Payment initiation failed. Please try again.']));
    }
} else {
    die(json_encode(['error' => 'Invalid request method']));
}
?>
