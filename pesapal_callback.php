<?php
/**
 * Pesapal Callback Handler
 * Verifies payment status and logs transaction results
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

// Get environment variables
$consumerKey = getenv('PESAPAL_CONSUMER_KEY');
$consumerSecret = getenv('PESAPAL_CONSUMER_SECRET');
$isSandbox = getenv('PESAPAL_SANDBOX') !== false ? getenv('PESAPAL_SANDBOX') === 'true' : true;

// Validate configuration
if (empty($consumerKey) || empty($consumerSecret)) {
    error_log('Pesapal Callback Error: Missing API credentials');
    header('Location: donate.html?status=error&message=Configuration+error');
    exit;
}

// Pesapal API endpoint
$statusUrl = $isSandbox
    ? 'https://sandbox.pesapal.com/api/urlbuilder/api/GetPaymentStatus'
    : 'https://www.pesapal.com/api/urlbuilder/api/GetPaymentStatus';

// Get callback parameters
$pesapalTrackingId = isset($_GET['pesapal_tracking_id']) ? trim($_GET['pesapal_tracking_id']) : null;
$pesapalMerchantReference = isset($_GET['pesapal_merchant_reference']) ? trim($_GET['pesapal_merchant_reference']) : null;

// Validate callback parameters
if (!$pesapalTrackingId || !$pesapalMerchantReference) {
    error_log('Pesapal Callback Error: Missing or invalid callback parameters');
    header('Location: donate.html?status=error&message=Invalid+callback');
    exit;
}

// Validate reference format (basic security check)
if (!preg_match('/^DONATION_\d+_[a-f0-9]+$/', $pesapalMerchantReference)) {
    error_log('Pesapal Callback Error: Suspicious reference format: ' . $pesapalMerchantReference);
    header('Location: donate.html?status=error&message=Invalid+reference');
    exit;
}

// Verify transaction status with Pesapal
$timestamp = time();
$nonce = bin2hex(random_bytes(16));

$params = array(
    'oauth_consumer_key' => $consumerKey,
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_signature_version' => '1.0',
    'oauth_timestamp' => $timestamp,
    'oauth_nonce' => $nonce,
    'oauth_version' => '1.0',
    'pesapal_merchant_reference' => $pesapalMerchantReference,
    'pesapal_tracking_id' => $pesapalTrackingId,
);

ksort($params);

$baseString = 'GET&' . urlencode($statusUrl) . '&';
$paramString = '';
foreach ($params as $key => $value) {
    $paramString .= urlencode($key) . '=' . urlencode($value) . '&';
}
$paramString = rtrim($paramString, '&');
$baseString .= urlencode($paramString);

$signingKey = urlencode($consumerSecret) . '&';
$signature = base64_encode(hash_hmac('sha1', $baseString, $signingKey, true));
$params['oauth_signature'] = $signature;

// Build authorization header
$authHeader = 'OAuth ';
foreach ($params as $key => $value) {
    if (strpos($key, 'oauth_') === 0) {
        $authHeader .= $key . '="' . urlencode($value) . '", ';
    }
}
$authHeader = rtrim($authHeader, ', ');

// Query Pesapal for status
$ch = curl_init();
$queryString = http_build_query($params);
curl_setopt_array($ch, array(
    CURLOPT_URL => $statusUrl . '?' . $queryString,
    CURLOPT_HTTPHEADER => array(
        'Authorization: ' . $authHeader,
    ),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_TIMEOUT => 30,
));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

// No longer use curl_close() in PHP 8.0+ (automatically closed)
unset($ch);

// Parse response
if ($curlError) {
    error_log("Pesapal Callback cURL Error: $curlError for Ref: $pesapalMerchantReference");
    header('Location: donate.html?status=error&message=Unable+to+verify+payment');
    exit;
}

if ($httpCode !== 200) {
    error_log("Pesapal Callback API Error: HTTP $httpCode for Ref: $pesapalMerchantReference");
    header('Location: donate.html?status=error&message=Verification+failed');
    exit;
}

// Parse XML response
$xml = @simplexml_load_string($response);
if (!$xml) {
    error_log("Pesapal Callback Parse Error: Invalid XML response for Ref: $pesapalMerchantReference");
    header('Location: donate.html?status=error&message=Invalid+response');
    exit;
}

$paymentStatus = isset($xml->status) ? (string)$xml->status : 'error';
$amount = isset($xml->amount) ? (string)$xml->amount : '0';
$reference = isset($xml->reference) ? (string)$xml->reference : '';

// Validate response status
if (!in_array($paymentStatus, ['COMPLETED', 'PENDING', 'FAILED', 'INVALID'])) {
    error_log("Pesapal Callback: Unexpected status '$paymentStatus' for Ref: $pesapalMerchantReference");
}

// Log transaction
$logData = array(
    'timestamp' => date('Y-m-d H:i:s'),
    'tracking_id' => $pesapalTrackingId,
    'reference' => $pesapalMerchantReference,
    'amount' => $amount,
    'status' => $paymentStatus,
    'http_code' => $httpCode,
);

$logFile = __DIR__ . '/pesapal_transactions.log';
if (!is_writable(dirname($logFile))) {
    error_log("Warning: Cannot write to transaction log directory: " . dirname($logFile));
}

file_put_contents($logFile, json_encode($logData) . "\n", FILE_APPEND | LOCK_EX);
error_log("Pesapal Callback: Transaction logged - Ref: $pesapalMerchantReference, Status: $paymentStatus, Amount: $amount");

// Redirect based on status
if ($paymentStatus === 'COMPLETED') {
    // Send success email notification
    $donorEmail = isset($_SESSION['pesapal_transaction']['donor_email']) 
        ? $_SESSION['pesapal_transaction']['donor_email'] 
        : '';
    
    $donorName = isset($_SESSION['pesapal_transaction']['donor_name']) 
        ? $_SESSION['pesapal_transaction']['donor_name'] 
        : 'Valued Donor';

    if ($donorEmail && filter_var($donorEmail, FILTER_VALIDATE_EMAIL)) {
        $to = $donorEmail;
        $subject = 'Donation Receipt - Child of Hope Children\'s Foundation';
        $message = "Dear $donorName,\n\n";
        $message .= "Thank you for your generous donation of USD " . number_format($amount, 2) . " to Child of Hope Children's Foundation.\n\n";
        $message .= "Transaction Details:\n";
        $message .= "Reference: $pesapalMerchantReference\n";
        $message .= "Tracking ID: $pesapalTrackingId\n";
        $message .= "Amount: USD " . number_format($amount, 2) . "\n";
        $message .= "Date: " . date('Y-m-d H:i:s') . "\n\n";
        $message .= "Your support makes a real difference in the lives of children in our community.\n\n";
        $message .= "Best regards,\n";
        $message .= "Child of Hope Children's Foundation Team\n";
        $message .= "childofhopechildrensfoundation@gmail.com\n";
        
        $headers = "From: childofhopechildrensfoundation@gmail.com\r\n";
        $headers .= "Reply-To: childofhopechildrensfoundation@gmail.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        if (@mail($to, $subject, $message, $headers)) {
            error_log("Pesapal: Receipt email sent to $donorEmail for Ref: $pesapalMerchantReference");
        } else {
            error_log("Pesapal Warning: Failed to send receipt email to $donorEmail for Ref: $pesapalMerchantReference");
        }
    }
    
    error_log("Pesapal: Successful donation - Ref: $pesapalMerchantReference, Amount: USD $amount from $donorEmail");
    header('Location: donate.html?status=success&amount=' . urlencode($amount) . '&ref=' . urlencode($pesapalMerchantReference));
} elseif ($paymentStatus === 'PENDING') {
    error_log("Pesapal: Pending donation - Ref: $pesapalMerchantReference");
    header('Location: donate.html?status=pending&ref=' . urlencode($pesapalMerchantReference));
} else {
    error_log("Pesapal: Failed/Cancelled donation - Ref: $pesapalMerchantReference, Status: $paymentStatus");
    header('Location: donate.html?status=failed&ref=' . urlencode($pesapalMerchantReference));
}
exit;
?>
