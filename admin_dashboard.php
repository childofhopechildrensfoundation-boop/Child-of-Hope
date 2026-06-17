<?php
/**
 * Combined Admin Dashboard
 * View donation transactions from both Pesapal and Stripe
 */

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

session_start();

$adminPassword = getenv('ADMIN_PASSWORD') ?: 'changeme123';
$isAuthenticated = false;
$loginError = '';

if (isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true) {
    $isAuthenticated = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($_POST['password'] === $adminPassword) {
        $_SESSION['admin_authenticated'] = true;
        $isAuthenticated = true;
    } else {
        $loginError = 'Invalid password';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_dashboard.php');
    exit;
}

// Parse transactions from both files
$allTransactions = [];
$completedDonations = 0;
$pendingDonations = 0;
$failedDonations = 0;
$totalDonations = 0;

if ($isAuthenticated) {
    // Parse Pesapal transactions
    $pesapalLog = __DIR__ . '/pesapal_transactions.log';
    if (file_exists($pesapalLog)) {
        $pesapalLines = file($pesapalLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($pesapalLines as $line) {
            if (empty($line)) continue;
            $trans = json_decode($line, true);
            if ($trans) {
                $trans['payment_method'] = 'Pesapal';
                $allTransactions[] = $trans;
            }
        }
    }

    // Parse Stripe transactions
    $stripeLog = __DIR__ . '/stripe_transactions.log';
    if (file_exists($stripeLog)) {
        $stripeLines = file($stripeLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($stripeLines as $line) {
            if (empty($line)) continue;
            $trans = json_decode($line, true);
            if ($trans) {
                $trans['payment_method'] = 'Stripe';
                $allTransactions[] = $trans;
            }
        }
    }

    // Sort transactions by timestamp (newest first)
    usort($allTransactions, function($a, $b) {
        $timeA = strtotime($a['timestamp'] ?? 0);
        $timeB = strtotime($b['timestamp'] ?? 0);
        return $timeB - $timeA;
    });

    // Calculate statistics
    foreach ($allTransactions as $trans) {
        $status = $trans['status'] ?? 'UNKNOWN';
        if ($status === 'COMPLETED') {
            $completedDonations++;
            $totalDonations += floatval($trans['amount'] ?? 0);
        } else if ($status === 'PENDING') {
            $pendingDonations++;
        } else if ($status === 'FAILED') {
            $failedDonations++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child of Hope - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-box h2 {
            color: #667eea;
            margin-bottom: 30px;
            text-align: center;
        }
        .dashboard-container {
            padding: 30px 15px;
        }
        .dashboard-header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .dashboard-header h1 {
            color: #667eea;
            margin: 0;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
        }
        .stat-card h5 {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .stat-card p {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
        .stat-card.success {
            border-left-color: #28a745;
        }
        .stat-card.success h5 {
            color: #28a745;
        }
        .stat-card.warning {
            border-left-color: #ffc107;
        }
        .stat-card.warning h5 {
            color: #ffc107;
        }
        .stat-card.danger {
            border-left-color: #dc3545;
        }
        .stat-card.danger h5 {
            color: #dc3545;
        }
        .transactions-table {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }
        .badge-completed {
            background: #28a745;
        }
        .badge-pending {
            background: #ffc107;
            color: #000;
        }
        .badge-failed {
            background: #dc3545;
        }
        .btn-logout {
            background: #dc3545;
            color: white;
            border: none;
        }
        .btn-logout:hover {
            background: #c82333;
            color: white;
        }
    </style>
</head>
<body>

<?php if (!$isAuthenticated): ?>
    <!-- Login Form -->
    <div class="login-container">
        <div class="login-box">
            <h2><i class="fas fa-lock"></i> Admin Login</h2>
            <?php if ($loginError): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($loginError); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary w-100" name="login">Login</button>
            </form>
            <hr>
            <p class="text-center text-muted small mt-3">
                <i class="fas fa-lock-open"></i> Default password: <strong>changeme123</strong>
                <br><small>(Change this immediately after login!)</small>
            </p>
        </div>
    </div>

<?php else: ?>
    <!-- Dashboard -->
    <div class="dashboard-container">
        <div class="container-fluid">
            <div class="dashboard-header">
                <div>
                    <h1><i class="fas fa-chart-line"></i> Child of Hope - Donations Dashboard</h1>
                    <small class="text-muted">Combined Pesapal & Stripe Transactions</small>
                </div>
                <a href="?logout" class="btn btn-logout">Logout</a>
            </div>

            <!-- Statistics -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card success">
                        <h5><i class="fas fa-check-circle"></i> Completed</h5>
                        <p><?php echo $completedDonations; ?></p>
                        <p class="text-muted">USD <?php echo number_format($totalDonations, 2); ?></p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card warning">
                        <h5><i class="fas fa-hourglass"></i> Pending</h5>
                        <p><?php echo $pendingDonations; ?></p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card danger">
                        <h5><i class="fas fa-times-circle"></i> Failed</h5>
                        <p><?php echo $failedDonations; ?></p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <h5><i class="fas fa-list"></i> Total</h5>
                        <p><?php echo count($allTransactions); ?></p>
                        <p class="text-muted">Transactions</p>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="transactions-table mt-5">
                <h3 class="mb-4"><i class="fas fa-list"></i> Recent Transactions</h3>
                
                <?php if (empty($allTransactions)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No transactions yet. When donors make payments, they'll appear here.
                    </div>
                <?php else: ?>
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Donor</th>
                                <th>Email</th>
                                <th>Amount</th>
                                <th>Purpose</th>
                                <th>Payment Method</th>
                                <th>Reference</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allTransactions as $trans): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($trans['timestamp'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($trans['donor_name'] ?? 'Unknown'); ?></td>
                                    <td><?php echo htmlspecialchars($trans['donor_email'] ?? 'N/A'); ?></td>
                                    <td><strong>USD <?php echo number_format($trans['amount'] ?? 0, 2); ?></strong></td>
                                    <td><?php echo htmlspecialchars($trans['cause'] ?? $trans['description'] ?? 'General'); ?></td>
                                    <td>
                                        <?php if ($trans['payment_method'] === 'Stripe'): ?>
                                            <span class="badge bg-info"><i class="fas fa-credit-card"></i> Stripe</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><i class="fas fa-mobile-alt"></i> Pesapal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small><?php echo htmlspecialchars(substr($trans['reference'] ?? $trans['charge_id'] ?? 'N/A', 0, 15)); ?>...</small></td>
                                    <td>
                                        <?php 
                                            $status = $trans['status'] ?? 'UNKNOWN';
                                            if ($status === 'COMPLETED'): ?>
                                                <span class="badge badge-completed"><i class="fas fa-check"></i> Completed</span>
                                            <?php elseif ($status === 'PENDING'): ?>
                                                <span class="badge badge-pending"><i class="fas fa-hourglass"></i> Pending</span>
                                            <?php else: ?>
                                                <span class="badge badge-failed"><i class="fas fa-times"></i> Failed</span>
                                            <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Export Button -->
                    <div class="mt-4">
                        <button type="button" class="btn btn-primary" onclick="exportToCSV()">
                            <i class="fas fa-download"></i> Export to CSV
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function exportToCSV() {
            const table = document.querySelector('.transactions-table table');
            if (!table) {
                alert('No data to export');
                return;
            }

            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            rows.forEach(row => {
                const cols = row.querySelectorAll('td, th');
                const csvRow = [];
                cols.forEach(col => {
                    csvRow.push('"' + col.innerText.replace(/"/g, '""') + '"');
                });
                csv.push(csvRow.join(','));
            });

            const csvContent = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv.join('\n'));
            const link = document.createElement('a');
            link.setAttribute('href', csvContent);
            link.setAttribute('download', 'donations_' + new Date().toISOString().split('T')[0] + '.csv');
            link.click();
        }
    </script>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
