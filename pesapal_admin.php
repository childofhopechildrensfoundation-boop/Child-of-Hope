<?php
/**
 * Pesapal Admin Dashboard
 * View donation transactions and generate reports
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

// Simple password protection (change this to a more secure method)
$adminPassword = getenv('ADMIN_PASSWORD') ?: 'changeme123';
$isAuthenticated = false;

// Check if logged in
if (isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true) {
    $isAuthenticated = true;
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($_POST['password'] === $adminPassword) {
        $_SESSION['admin_authenticated'] = true;
        $isAuthenticated = true;
    } else {
        $loginError = 'Invalid password';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: pesapal_admin.php');
    exit;
}

// Get transactions
$transactions = [];
$transactionFile = __DIR__ . '/pesapal_transactions.log';
if (file_exists($transactionFile) && is_readable($transactionFile)) {
    $lines = file($transactionFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $data = @json_decode($line, true);
        if ($data) {
            $transactions[] = $data;
        }
    }
    // Reverse to show newest first
    $transactions = array_reverse($transactions);
}

// Calculate statistics
$totalDonations = 0;
$completedCount = 0;
$pendingCount = 0;
$failedCount = 0;

foreach ($transactions as $trans) {
    if ($trans['status'] === 'COMPLETED') {
        $totalDonations += floatval($trans['amount']);
        $completedCount++;
    } elseif ($trans['status'] === 'PENDING') {
        $pendingCount++;
    } else {
        $failedCount++;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesapal Admin Dashboard - Child of Hope</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
        .login-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            padding: 40px;
        }
        .dashboard-container {
            padding: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stat-card h5 {
            color: #666;
            font-weight: 600;
        }
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-failed {
            background: #f8d7da;
            color: #721c24;
        }
        .navbar {
            background: rgba(255,255,255,0.95) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            color: #667eea !important;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php if (!$isAuthenticated): ?>
    <!-- Login Page -->
    <div class="login-container">
        <div class="login-card">
            <h2 class="text-center mb-4">
                <i class="fas fa-lock"></i> Admin Dashboard
            </h2>
            <?php if (isset($loginError)): ?>
                <div class="alert alert-danger"><?php echo $loginError; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required autofocus>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>
            <p class="text-center mt-3 text-muted small">
                <em>Change default password in environment variables (ADMIN_PASSWORD)</em>
            </p>
        </div>
    </div>
<?php else: ?>
    <!-- Dashboard Page -->
    <nav class="navbar navbar-light">
        <div class="container-fluid">
            <span class="navbar-brand">
                <i class="fas fa-chart-pie"></i> Pesapal Donations Dashboard
            </span>
            <a href="?logout=1" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div class="dashboard-container container-fluid">
        <!-- Statistics Row -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <h5><i class="fas fa-check-circle text-success"></i> Completed</h5>
                    <div class="stat-value"><?php echo $completedCount; ?></div>
                    <p class="text-muted">UGX <?php echo number_format($totalDonations, 0); ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h5><i class="fas fa-hourglass-half text-warning"></i> Pending</h5>
                    <div class="stat-value"><?php echo $pendingCount; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h5><i class="fas fa-times-circle text-danger"></i> Failed</h5>
                    <div class="stat-value"><?php echo $failedCount; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h5><i class="fas fa-list"></i> Total</h5>
                    <div class="stat-value"><?php echo count($transactions); ?></div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="table-container">
            <h4 class="mb-4">Recent Transactions</h4>
            <?php if (count($transactions) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Reference</th>
                                <th>Tracking ID</th>
                                <th>Amount (UGX)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $trans): ?>
                                <tr>
                                    <td><?php echo isset($trans['timestamp']) ? htmlspecialchars($trans['timestamp']) : 'N/A'; ?></td>
                                    <td><code><?php echo htmlspecialchars($trans['reference']); ?></code></td>
                                    <td><code><?php echo htmlspecialchars($trans['tracking_id']); ?></code></td>
                                    <td><?php echo number_format(floatval($trans['amount']), 0); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($trans['status']); ?>">
                                            <?php echo htmlspecialchars($trans['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">No transactions yet.</p>
            <?php endif; ?>
        </div>

        <!-- Actions -->
        <div class="mt-4">
            <a href="pesapal_admin.php" class="btn btn-primary">
                <i class="fas fa-sync"></i> Refresh
            </a>
            <button onclick="downloadCSV()" class="btn btn-success">
                <i class="fas fa-download"></i> Export CSV
            </button>
        </div>
    </div>

    <script>
        function downloadCSV() {
            const transactions = <?php echo json_encode($transactions); ?>;
            let csv = 'Date,Reference,Tracking ID,Amount,Status\n';
            
            transactions.forEach(trans => {
                csv += `"${trans.timestamp}","${trans.reference}","${trans.tracking_id}","${trans.amount}","${trans.status}"\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'pesapal_transactions_' + new Date().getTime() + '.csv';
            a.click();
        }
    </script>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
