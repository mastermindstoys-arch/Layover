<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Layover Solutions</title>
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .stats-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }
        .table-responsive {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .btn-logout {
            background: #dc3545;
            border: none;
            color: white;
        }
        .btn-logout:hover {
            background: #c82333;
            color: white;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }

    require_once '../backend/config.php';

    // Get stats
    $stats = [];
    try {
        $db = getDB();
        $stats['get_started'] = $db->query("SELECT COUNT(*) as count FROM get_started")->fetch()['count'];
        $stats['contacts'] = $db->query("SELECT COUNT(*) as count FROM contact_submissions")->fetch()['count'];
        $stats['new_leads'] = $db->query("SELECT COUNT(*) as count FROM get_started WHERE status = 'new'")->fetch()['count'];
        $stats['total_revenue'] = $db->query("SELECT SUM(total_revenue) as total FROM customers")->fetch()['total'] ?? 0;

        // Recent get_started submissions
        $recent_get_started = $db->query("SELECT * FROM get_started ORDER BY submitted_at DESC LIMIT 5")->fetchAll();

        // Recent contacts
        $recent_contacts = $db->query("SELECT * FROM contact_submissions ORDER BY submitted_at DESC LIMIT 5")->fetchAll();

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
    ?>

    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="bi bi-speedometer2"></i> Admin Dashboard</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="logout.php" class="btn btn-logout">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon text-primary">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <div class="stats-number text-primary"><?php echo $stats['get_started']; ?></div>
                    <div>Get Started Forms</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon text-success">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div class="stats-number text-success"><?php echo $stats['contacts']; ?></div>
                    <div>Contact Forms</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon text-warning">
                        <i class="bi bi-star"></i>
                    </div>
                    <div class="stats-number text-warning"><?php echo $stats['new_leads']; ?></div>
                    <div>New Leads</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon text-info">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="stats-number text-info">$<?php echo number_format($stats['total_revenue'], 2); ?></div>
                    <div>Total Revenue</div>
                </div>
            </div>
        </div>

        <!-- Recent Submissions -->
        <div class="row">
            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th colspan="4">Recent Get Started Submissions</th>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Service</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_get_started as $submission): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($submission['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($submission['email']); ?></td>
                                <td><?php echo htmlspecialchars($submission['service_interest']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($submission['submitted_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th colspan="4">Recent Contact Forms</th>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_contacts as $contact): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($contact['name']); ?></td>
                                <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                <td><?php echo htmlspecialchars($contact['phone']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($contact['submitted_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>