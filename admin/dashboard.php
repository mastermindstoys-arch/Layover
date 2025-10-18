<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../index.html');
    exit;
}
?>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .management-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .management-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            border: none;
        }
        .management-card .card-body {
            padding: 1.5rem;
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
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        .action-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin: 0.125rem;
        }
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .sidebar {
            background: var(--heading-color);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
        }
        .sidebar .nav-link:hover {
            color: white;
            background: var(--accent-color);
        }
        .sidebar .nav-link.active {
            color: white;
            background: var(--accent-color);
        }
        .main-content {
            padding: 2rem;
        }
        .modal-xl {
            max-width: 1200px;
        }

        /* Mobile Responsiveness */
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 280px;
                height: 100vh;
                z-index: 1050;
                transition: left 0.3s ease-in-out;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                padding: 1rem;
                margin-left: 0;
                width: 100%;
            }

            .dashboard-header {
                padding: 1rem 0;
                margin-bottom: 1rem;
            }

            .dashboard-header h1 {
                font-size: 1.5rem;
            }

            .dashboard-header .row {
                text-align: center;
            }

            .dashboard-header .col-md-6.text-end {
                text-align: center !important;
                margin-top: 1rem;
            }

            .stats-card {
                padding: 1rem;
                margin-bottom: 0.5rem;
            }

            .stats-number {
                font-size: 1.5rem;
            }

            /* Make stats cards stack vertically */
            .row.gy-4 .col-md-4 {
                margin-bottom: 1rem;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: stretch !important;
            }

            .d-flex.justify-content-between .btn {
                margin-top: 1rem;
                width: 100%;
            }

            .management-card .card-header {
                padding: 0.75rem 1rem;
            }

            .management-card .card-body {
                padding: 1rem;
            }

            .modal-xl, .modal-lg {
                max-width: 95vw;
                margin: 0.5rem auto;
            }

            .modal-dialog {
                margin: 0.5rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .modal-header, .modal-footer {
                padding: 0.75rem 1rem;
            }

            .btn-group .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            .table-responsive {
                font-size: 0.875rem;
                border-radius: 5px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table th, .table td {
                padding: 0.5rem;
                white-space: nowrap;
            }

            .table th {
                font-size: 0.8rem;
                font-weight: 600;
            }

            .action-btn {
                padding: 0.2rem 0.4rem;
                font-size: 0.75rem;
                margin: 0.1rem;
            }

            /* Stack action buttons vertically on very small screens */
            @media (max-width: 480px) {
                .action-btn {
                    display: block;
                    width: 100%;
                    margin-bottom: 0.25rem;
                }

                .table th:last-child,
                .table td:last-child {
                    min-width: 120px;
                }
            }

            /* Form responsiveness */
            .form-control, .form-select {
                font-size: 16px; /* Prevent zoom on iOS */
            }

            .row > .col-md-6 {
                margin-bottom: 1rem;
            }

            .mb-3 {
                margin-bottom: 1rem !important;
            }
        }

        /* Tablet Responsiveness */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .modal-xl {
                max-width: 90vw;
            }

            .main-content {
                padding: 1.5rem;
            }
        }

        /* Mobile Menu Toggle Button */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1051;
            background: #343a40;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 0.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .mobile-menu-toggle:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 58, 64, 0.5);
        }

        @media (max-width: 767.98px) {
            .mobile-menu-toggle {
                display: block;
            }
        }

        /* Overlay for mobile sidebar */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1049;
        }

        .sidebar-overlay.show {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" onclick="toggleSidebar()" aria-label="Toggle navigation menu">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="d-flex flex-column p-3">
                    <h5 class="text-center mb-4">
                        <i class="bi bi-shield-lock me-2"></i>Admin Panel
                    </h5>
                    <nav class="nav nav-pills flex-column">
                        <a class="nav-link active" href="#dashboard" onclick="showSection('dashboard')">
                            <i class="bi bi-house-door me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="#contact-submissions" onclick="showSection('contact-submissions')">
                            <i class="bi bi-envelope me-2"></i>Contact Submissions
                        </a>
                        <a class="nav-link" href="#get-started" onclick="showSection('get-started')">
                            <i class="bi bi-rocket me-2"></i>Get Started Requests
                        </a>
                        <a class="nav-link" href="#customers" onclick="showSection('customers')">
                            <i class="bi bi-people me-2"></i>Customers
                        </a>
                        <a class="nav-link" href="#developers" onclick="showSection('developers')">
                            <i class="bi bi-code-slash me-2"></i>Developers
                        </a>
                        <a class="nav-link" href="#projects" onclick="showSection('projects')">
                            <i class="bi bi-briefcase me-2"></i>Projects
                        </a>
                        <a class="nav-link" href="#users" onclick="showSection('users')">
                            <i class="bi bi-person-circle me-2"></i>Users
                        </a>
                        <a class="nav-link" href="#project-activities" onclick="showSection('project-activities')">
                            <i class="bi bi-activity me-2"></i>Project Activities
                        </a>
                        <a class="nav-link" href="#invoice" onclick="showSection('invoice')">
                            <i class="bi bi-receipt me-2"></i>Invoice
                        </a>
                        <a class="nav-link" href="#settings" onclick="showSection('settings')">
                            <i class="bi bi-gear me-2"></i>Settings
                        </a>
                    </nav>
                    <div class="mt-auto">
                        <button class="btn btn-logout w-100" onclick="logout()">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-0 main-content">
                <!-- Dashboard Section -->
                <div id="dashboard-section" class="section active">
                    <div class="dashboard-header">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h1 class="mb-0"><i class="bi bi-speedometer2 me-3"></i>Dashboard</h1>
                                    <p class="mb-0 mt-2">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>!</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <i class="bi bi-calendar-event me-2"></i>
                                        <span><?php echo date('l, F j, Y'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="container">
                        <div class="row mb-4">
                            <div class="col-md-4 col-lg-2">
                                <div class="stats-card text-center">
                                    <div class="stats-icon text-primary">
                                        <i class="bi bi-envelope"></i>
                                    </div>
                                    <div class="stats-number text-primary" id="contact-count">0</div>
                                    <div class="text-muted">Contact Submissions</div>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <div class="stats-card text-center">
                                    <div class="stats-icon text-success">
                                        <i class="bi bi-rocket"></i>
                                    </div>
                                    <div class="stats-number text-success" id="get-started-count">0</div>
                                    <div class="text-muted">Get Started Requests</div>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <div class="stats-card text-center">
                                    <div class="stats-icon text-info">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="stats-number text-info" id="customer-count">0</div>
                                    <div class="text-muted">Customers</div>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <div class="stats-card text-center">
                                    <div class="stats-icon text-warning">
                                        <i class="bi bi-briefcase"></i>
                                    </div>
                                    <div class="stats-number text-warning" id="project-count">0</div>
                                    <div class="text-muted">Active Projects</div>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <div class="stats-card text-center">
                                    <div class="stats-icon text-secondary">
                                        <i class="bi bi-code-slash"></i>
                                    </div>
                                    <div class="stats-number text-secondary" id="developer-count">0</div>
                                    <div class="text-muted">Developers</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Submissions Section -->
                <div id="contact-submissions-section" class="section" style="display: none;">
                    <div class="container">
                        <div class="management-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-envelope"></i> Contact Submissions</h5>
                                <button class="btn btn-success btn-sm" onclick="showAddContactModal()">
                                    <i class="bi bi-plus-circle"></i> Add Contact
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="contactSubmissionsTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Message</th>
                                                <th>Status</th>
                                                <th>Priority</th>
                                                <th>Submitted</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="contactSubmissionsBody">
                                            <!-- Data will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Get Started Section -->
                <div id="get-started-section" class="section" style="display: none;">
                    <div class="container">
                        <div class="management-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-rocket"></i> Get Started Requests</h5>
                                <button class="btn btn-success btn-sm" onclick="showAddGetStartedModal()">
                                    <i class="bi bi-plus-circle"></i> Add Request
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="getStartedTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Service</th>
                                                <th>Preferred Time</th>
                                                <th>Status</th>
                                                <th>Submitted</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="getStartedBody">
                                            <!-- Data will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Placeholder sections -->
                <div id="customers-section" class="section" style="display: none;">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2><i class="bi bi-people me-2"></i>Customers Management</h2>
                            <button class="btn btn-primary" onclick="openCustomerModal()">
                                <i class="bi bi-plus-circle me-2"></i>Add Customer
                            </button>
                        </div>

                        <!-- Customers Table -->
                        <div class="management-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-people me-2"></i>All Customers</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="customersTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Company</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Revenue</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="customersTableBody">
                                            <!-- Customers will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="developers-section" class="section" style="display: none;">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2><i class="bi bi-code-slash me-2"></i>Developers Management</h2>
                            <button class="btn btn-primary" onclick="openDeveloperModal()">
                                <i class="bi bi-plus-circle me-2"></i>Add Developer
                            </button>
                        </div>

                        <!-- Developers Table -->
                        <div class="management-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-people me-2"></i>All Developers</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="developersTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Salary Settlement</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="developersTableBody">
                                            <!-- Developers will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="projects-section" class="section" style="display: none;">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2><i class="bi bi-briefcase me-2"></i>Projects Management</h2>
                            <button class="btn btn-primary" onclick="openProjectModal()">
                                <i class="bi bi-plus-circle me-2"></i>Add Project
                            </button>
                        </div>

                        <!-- Projects Table -->
                        <div class="management-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-briefcase me-2"></i>All Projects</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="projectsTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Project Name</th>
                                                <th>Customer</th>
                                                <th>Developer</th>
                                                <th>Progress</th>
                                                <th>Pending Amount</th>
                                                <th>Subscription</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="projectsTableBody">
                                            <!-- Projects will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Section -->
                <div id="users-section" class="section" style="display: none;">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2><i class="bi bi-person-circle me-2"></i>Users Management</h2>
                            <button class="btn btn-primary" onclick="openUserModal()">
                                <i class="bi bi-plus-circle me-2"></i>Add User
                            </button>
                        </div>

                        <!-- Users Table -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>All Users</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Created Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="usersTableBody">
                                            <!-- Users will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Activities Section -->
                <div id="project-activities-section" class="section" style="display: none;">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2><i class="bi bi-activity me-2"></i>Project Activities</h2>
                            <button class="btn btn-primary" onclick="openActivityModal()">
                                <i class="bi bi-plus-circle me-2"></i>Add Activity
                            </button>
                        </div>

                        <!-- Project Activities Table -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-activity me-2"></i>All Project Activities</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Project</th>
                                                <th>Phase</th>
                                                <th>Sub Phase</th>
                                                <th>User</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="activitiesTableBody">
                                            <!-- Activities will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Section -->
                <div id="invoice-section" class="section" style="display: none;">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2><i class="bi bi-receipt me-2"></i>Invoice Management</h2>
                            <button class="btn btn-primary" onclick="openInvoiceModal()">
                                <i class="bi bi-plus-circle me-2"></i>Create Invoice
                            </button>
                        </div>

                        <!-- Invoice Table -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>All Invoices</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Invoice ID</th>
                                                <th>Project</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Issue Date</th>
                                                <th>Due Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="invoiceTableBody">
                                            <!-- Invoices will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="settings-section" class="section" style="display: none;">
                    <div class="container"><h2>Settings</h2><p>Under development.</p></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalTitle">Add Contact Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="contactForm">
                        <input type="hidden" id="contactId" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name *</label>
                                    <input type="text" class="form-control" id="contactName" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="contactEmail" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="contactPhone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="contactStatus">
                                        <option value="New">New</option>
                                        <option value="Inprogress">In Progress</option>
                                        <option value="Success">Success</option>
                                        <option value="Cancel">Cancel</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Priority</label>
                                    <select class="form-select" id="contactPriority">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Contact Type</label>
                                    <input type="text" class="form-control" id="contactType" placeholder="e.g., inquiry, support">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" id="contactMessage" rows="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveContact()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="getStartedModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="getStartedModalTitle">Add Get Started Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="getStartedForm">
                        <input type="hidden" id="getStartedId" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="getStartedName" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="getStartedEmail" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone *</label>
                                    <input type="tel" class="form-control" id="getStartedPhone" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Service Interest *</label>
                                    <select class="form-select" id="getStartedService" required>
                                        <option value="">Select Service</option>
                                        <option value="Basic Plan">Basic Plan</option>
                                        <option value="Moderate Plan">Moderate Plan</option>
                                        <option value="Advance Plan">Advance Plan</option>
                                        <option value="Payment Integration">Payment Integration</option>
                                        <option value="Email Integration">Email Integration</option>
                                        <option value="Chat/AI Integration">Chat/AI Integration</option>
                                        <option value="Basic Maintenance">Social Commerce</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Preferred Time</label>
                                    <select class="form-select" id="getStartedTime">
                                        <option value="">Any time works</option>
                                        <option value="9:00 AM - 12:00 PM">Morning (9 AM - 12 PM)</option>
                                        <option value="12:00 PM - 3:00 PM">Afternoon (12 PM - 3 PM)</option>
                                        <option value="3:00 PM - 6:00 PM">Evening (3 PM - 6 PM)</option>
                                        <option value="6:00 PM - 9:00 PM">Late Evening (6 PM - 9 PM)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Preferred Language</label>
                                    <select class="form-select" id="getStartedLanguage">
                                        <option value="English">English</option>
                                        <option value="Tamil">Tamil</option>
                                        <option value="Telugu">Telugu</option>
                                        <option value="Kannada">Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="getStartedStatus">
                                        <option value="new">New</option>
                                        <option value="contacted">Contacted</option>
                                        <option value="qualified">Qualified</option>
                                        <option value="proposal">Proposal Sent</option>
                                        <option value="negotiation">Negotiation</option>
                                        <option value="won">Won</option>
                                        <option value="lost">Lost</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="getStartedNotes" rows="3" placeholder="Additional notes or requirements"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveGetStarted()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Developer Modal -->
    <div class="modal fade" id="developerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="developerModalTitle">Add Developer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="developerForm">
                        <input type="hidden" id="developerId" name="developer_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="developerName" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="developerEmail" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="developerPhone">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Experience (Years)</label>
                                    <input type="number" class="form-control" id="developerExperience" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Specialization</label>
                                    <input type="text" class="form-control" id="developerSpecialization" placeholder="e.g., Web Development, Mobile Apps">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Hourly Rate (₹)</label>
                                    <input type="number" class="form-control" id="developerHourlyRate" min="0" step="0.01">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Join Date</label>
                                    <input type="date" class="form-control" id="developerJoinDate">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="developerStatus">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="on-leave">On Leave</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Skills</label>
                            <textarea class="form-control" id="developerSkills" rows="3" placeholder="e.g., PHP, JavaScript, React, Node.js"></textarea>
                        </div>

                        <!-- Salary Management Section -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Salary Accumulated (₹)</label>
                                    <input type="number" class="form-control" id="developerSalaryAccumulated" min="0" step="0.01" placeholder="0.00" onchange="calculateSalarySettlement()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Salary Paid (₹)</label>
                                    <input type="number" class="form-control" id="developerSalaryPaid" min="0" step="0.01" placeholder="0.00" onchange="calculateSalarySettlement()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Salary Settlement (₹)</label>
                                    <input type="number" class="form-control" id="developerSalarySettlement" readonly step="0.01" placeholder="Auto-calculated">
                                    <small class="form-text text-muted">Auto-calculated: Accumulated - Paid</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="developerNotes" rows="2" placeholder="Additional notes about the developer"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveDeveloper()">Save Developer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalTitle">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="customerForm">
                        <input type="hidden" id="customerId" name="customer_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="customerName" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="customerEmail" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="customerPhone">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="customerCompany">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Designation</label>
                                    <input type="text" class="form-control" id="customerDesignation">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Customer Type</label>
                                    <select class="form-select" id="customerType">
                                        <option value="individual">Individual</option>
                                        <option value="business">Business</option>
                                        <option value="enterprise">Enterprise</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="customerStatus">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="prospect">Prospect</option>
                                        <option value="former">Former</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">GST Number</label>
                                    <input type="text" class="form-control" id="customerGST">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">PAN Number</label>
                                    <input type="text" class="form-control" id="customerPAN">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Total Revenue (₹)</label>
                                    <input type="number" class="form-control" id="customerTotalRevenue" min="0" step="0.01" placeholder="0.00">
                                    <small class="form-text text-muted">Total revenue generated from this customer</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" id="customerAddress" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" id="customerCity">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">State</label>
                                    <input type="text" class="form-control" id="customerState">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" class="form-control" id="customerCountry" value="India">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ZIP Code</label>
                                    <input type="text" class="form-control" id="customerZipCode">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="customerNotes" rows="2" placeholder="Additional notes about the customer"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveCustomer()">Save Customer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Modal -->
    <div class="modal fade" id="projectModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="projectModalTitle">Add Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="projectForm">
                        <input type="hidden" id="projectId" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Project Name *</label>
                                    <input type="text" class="form-control" id="projectName" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Customer</label>
                                    <select class="form-select" id="projectCustomerId">
                                        <option value="">Select Customer</option>
                                        <!-- Customers will be loaded here -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Developer</label>
                                    <select class="form-select" id="projectDeveloperId" multiple size="3">
                                        <option value="">Select Developers</option>
                                        <!-- Developers will be loaded here -->
                                    </select>
                                    <small class="text-muted">Hold Ctrl/Cmd to select multiple developers</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Project Category</label>
                                    <select class="form-select" id="projectCategory">
                                        <option value="">Select Category</option>
                                        <option value="web-development">Web Development</option>
                                        <option value="mobile-app">Mobile App</option>
                                        <option value="e-commerce">E-commerce</option>
                                        <option value="ai-solution">AI Solution</option>
                                        <option value="automation">Automation</option>
                                        <option value="consulting">Consulting</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="projectStatus">
                                        <option value="planning">Planning</option>
                                        <option value="in-progress">In Progress</option>
                                        <option value="on-hold">On Hold</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Priority</label>
                                    <select class="form-select" id="projectPriority">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Budget (₹)</label>
                                    <input type="number" class="form-control" id="projectBudget" min="0" step="0.01" placeholder="0.00">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Progress (%)</label>
                                    <input type="number" class="form-control" id="projectProgress" min="0" max="100" placeholder="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="projectStartDate">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="projectEndDate">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Technologies</label>
                                    <input type="text" class="form-control" id="projectTechnologies" placeholder="e.g., PHP, React, MySQL">
                                </div>
                            </div>
                        </div>

                        <!-- Financial Information Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="mb-3">Financial Information</h6>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Advance Payment (₹)</label>
                                    <input type="number" class="form-control" id="projectAdvancePayment" min="0" step="0.01" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Settlement (₹)</label>
                                    <input type="number" class="form-control" id="projectSettlement" min="0" step="0.01" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Subscription</label>
                                    <select class="form-select" id="projectSubscription">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Subscription Amount (₹)</label>
                                    <input type="number" class="form-control" id="projectSubscriptionAmount" min="0" step="0.01" placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="projectDescription" rows="3" placeholder="Project description and requirements"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveProject()">Save Project</button>
                </div>
            </div>
        </div>
    </div>

    <!-- User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalTitle">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        <input type="hidden" id="userId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Username *</label>
                                    <input type="text" class="form-control" id="userUsername" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="userEmail" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password *</label>
                                    <input type="password" class="form-control" id="userPassword" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="userFullName">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select class="form-select" id="userRole">
                                        <option value="admin">Admin</option>
                                        <option value="developer">Developer</option>
                                        <option value="client">Client</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="userStatus">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="suspended">Suspended</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveUser()">Save User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Activity Modal -->
    <div class="modal fade" id="activityModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="activityModalTitle">Add Project Activity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="activityForm">
                        <input type="hidden" id="activityId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Project *</label>
                                    <select class="form-select" id="activityProjectId" required>
                                        <option value="">Select Project</option>
                                        <!-- Projects will be loaded here -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phase *</label>
                                    <select class="form-select" id="activityPhase" required onchange="onPhaseChange()">
                                        <option value="">Select Phase</option>
                                        <!-- Phases will be loaded dynamically -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Sub Phase *</label>
                                    <select class="form-select" id="activitySubPhase" required>
                                        <option value="">Select Sub Phase</option>
                                        <!-- Sub-phases will be loaded dynamically based on selected phase -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">User</label>
                                    <select class="form-select" id="activityUserId">
                                        <option value="">Select User</option>
                                        <!-- Users will be loaded here -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="activityCustomerVisible">
                                        <label class="form-check-label" for="activityCustomerVisible">
                                            Visible to Customer
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveActivity()">Save Activity</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Modal -->
    <div class="modal fade" id="invoiceModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoiceModalTitle">Create Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="invoiceForm">
                        <input type="hidden" id="invoiceId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Project *</label>
                                    <select class="form-select" id="invoiceProjectId" required>
                                        <option value="">Select Project</option>
                                        <!-- Projects will be loaded here -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Customer *</label>
                                    <select class="form-select" id="invoiceCustomerId" required>
                                        <option value="">Select Customer</option>
                                        <!-- Customers will be loaded here -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Invoice Number</label>
                                    <input type="text" class="form-control" id="invoiceNumber" placeholder="Auto-generated">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Issue Date *</label>
                                    <input type="date" class="form-control" id="invoiceIssueDate" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Due Date *</label>
                                    <input type="date" class="form-control" id="invoiceDueDate" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="invoiceStatus">
                                        <option value="draft">Draft</option>
                                        <option value="sent">Sent</option>
                                        <option value="paid">Paid</option>
                                        <option value="overdue">Overdue</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Items -->
                        <div class="mb-3">
                            <label class="form-label">Invoice Items</label>
                            <div id="invoiceItems">
                                <div class="invoice-item row mb-2">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" placeholder="Description" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control" placeholder="Qty" min="1" value="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control" placeholder="Rate" min="0" step="0.01" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control" placeholder="Amount" readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeInvoiceItem(this)">×</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addInvoiceItem()">+ Add Item</button>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Subtotal</label>
                                    <input type="number" class="form-control" id="invoiceSubtotal" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tax (%)</label>
                                    <input type="number" class="form-control" id="invoiceTaxRate" min="0" max="100" step="0.01" value="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Total Amount</label>
                                    <input type="number" class="form-control" id="invoiceTotal" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="invoiceNotes" rows="3" placeholder="Payment terms, notes, etc."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveInvoice()">Save Invoice</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navigation
        function showSection(sectionName) {
            document.querySelectorAll('.section').forEach(section => {
                section.style.display = 'none';
                section.classList.remove('active');
            });
            const targetSection = document.getElementById(sectionName + '-section');
            if (targetSection) {
                targetSection.style.display = 'block';
                targetSection.classList.add('active');
            }
            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector(`[href="#${sectionName}"]`).classList.add('active');

            if (sectionName === 'contact-submissions') {
                loadContactSubmissions();
            } else if (sectionName === 'get-started') {
                loadGetStartedRequests();
            } else if (sectionName === 'developers') {
                loadDevelopers();
            } else if (sectionName === 'customers') {
                loadCustomers();
            } else if (sectionName === 'projects') {
                loadProjects();
            } else if (sectionName === 'users') {
                loadUsers();
            } else if (sectionName === 'project-activities') {
                loadActivities();
            } else if (sectionName === 'invoice') {
                loadInvoices();
            }
        }

        // Load dashboard stats
        function loadDashboardStats() {
            fetch('api/contact_submissions.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('contact-count').textContent = data.data.length;
                    }
                })
                .catch(error => console.error('Error loading contact stats:', error));

            fetch('api/get_started.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('get-started-count').textContent = data.data.length;
                    }
                })
                .catch(error => console.error('Error loading get started stats:', error));

            fetch('api/developers.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('developer-count').textContent = data.data.length;
                    }
                })
                .catch(error => console.error('Error loading developer stats:', error));

            fetch('api/projects.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('project-count').textContent = data.data.length;
                    }
                })
                .catch(error => console.error('Error loading project stats:', error));
        }

        // Contact Submissions CRUD
        function loadContactSubmissions() {
            fetch('api/contact_submissions.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('contactSubmissionsBody');
                        tbody.innerHTML = '';

                        data.data.forEach(item => {
                            const row = `
                                <tr>
                                    <td>${item.id}</td>
                                    <td>${item.name}</td>
                                    <td>${item.email}</td>
                                    <td>${item.phone || 'N/A'}</td>
                                    <td>${item.message ? item.message.substring(0, 50) + '...' : 'N/A'}</td>
                                    <td>
                                        <span class="status-badge bg-${
                                            item.status === 'New' ? 'primary' :
                                            item.status === 'Inprogress' ? 'warning' :
                                            item.status === 'Success' ? 'success' : 'secondary'
                                        }">
                                            ${item.status}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge bg-${
                                            item.priority === 'urgent' ? 'danger' :
                                            item.priority === 'high' ? 'warning' :
                                            item.priority === 'medium' ? 'info' : 'secondary'
                                        }">
                                            ${item.priority}
                                        </span>
                                    </td>
                                    <td>${new Date(item.submitted_at).toLocaleDateString()}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm action-btn" onclick="viewContact(${item.id})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm action-btn" onclick="editContact(${item.id})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm action-btn" onclick="deleteContact(${item.id})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });
                    }
                })
                .catch(error => console.error('Error loading contact submissions:', error));
        }

        function showAddContactModal() {
            document.getElementById('contactModalTitle').textContent = 'Add Contact Submission';
            document.getElementById('contactId').value = '';
            document.getElementById('contactForm').reset();
            new bootstrap.Modal(document.getElementById('contactModal')).show();
        }

        function editContact(id) {
            fetch(`api/contact_submissions.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = data.data;
                        document.getElementById('contactModalTitle').textContent = 'Edit Contact Submission';
                        document.getElementById('contactId').value = item.id;
                        document.getElementById('contactName').value = item.name;
                        document.getElementById('contactEmail').value = item.email;
                        document.getElementById('contactPhone').value = item.phone || '';
                        document.getElementById('contactMessage').value = item.message || '';
                        document.getElementById('contactStatus').value = item.status || 'New';
                        document.getElementById('contactPriority').value = item.priority || 'medium';
                        document.getElementById('contactType').value = item.contact_type || '';
                        new bootstrap.Modal(document.getElementById('contactModal')).show();
                    }
                })
                .catch(error => console.error('Error loading contact:', error));
        }

        function saveContact() {
            const formData = {
                id: document.getElementById('contactId').value,
                name: document.getElementById('contactName').value,
                email: document.getElementById('contactEmail').value,
                phone: document.getElementById('contactPhone').value,
                message: document.getElementById('contactMessage').value,
                status: document.getElementById('contactStatus').value,
                priority: document.getElementById('contactPriority').value,
                contact_type: document.getElementById('contactType').value
            };

            const method = formData.id ? 'PUT' : 'POST';
            const url = 'api/contact_submissions.php';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('contactModal')).hide();
                    loadContactSubmissions();
                    loadDashboardStats();
                    alert('Contact submission saved successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to save'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Network error. Please try again.');
            });
        }

        function deleteContact(id) {
            if (confirm('Are you sure you want to delete this contact submission?')) {
                fetch(`api/contact_submissions.php?id=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadContactSubmissions();
                        loadDashboardStats();
                        alert('Contact submission deleted successfully!');
                    } else {
                        alert('Error: ' + (data.message || 'Failed to delete'));
                    }
                })
                .catch(error => console.error('Error deleting contact:', error));
            }
        }

        // Get Started CRUD
        function loadGetStartedRequests() {
            fetch('api/get_started.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('getStartedBody');
                        tbody.innerHTML = '';

                        data.data.forEach(item => {
                            const row = `
                                <tr>
                                    <td>${item.id}</td>
                                    <td>${item.full_name}</td>
                                    <td>${item.email}</td>
                                    <td>${item.phone || 'N/A'}</td>
                                    <td>${item.service_interest}</td>
                                    <td>${item.preferred_time || 'N/A'}</td>
                                    <td>
                                        <span class="status-badge bg-${
                                            item.status === 'new' ? 'primary' :
                                            item.status === 'contacted' ? 'info' :
                                            item.status === 'qualified' ? 'warning' :
                                            item.status === 'proposal' ? 'secondary' :
                                            item.status === 'negotiation' ? 'primary' :
                                            item.status === 'won' ? 'success' :
                                            item.status === 'lost' ? 'danger' :
                                            item.status === 'cancelled' ? 'dark' : 'secondary'
                                        }">
                                            ${item.status}
                                        </span>
                                    </td>
                                    <td>${new Date(item.submitted_at).toLocaleDateString()}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm action-btn" onclick="viewGetStarted(${item.id})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm action-btn" onclick="editGetStarted(${item.id})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm action-btn" onclick="deleteGetStarted(${item.id})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });
                    }
                })
                .catch(error => console.error('Error loading get started requests:', error));
        }

        function showAddGetStartedModal() {
            document.getElementById('getStartedModalTitle').textContent = 'Add Get Started Request';
            document.getElementById('getStartedId').value = '';
            document.getElementById('getStartedForm').reset();
            document.getElementById('getStartedStatus').value = 'new';
            new bootstrap.Modal(document.getElementById('getStartedModal')).show();
        }

        function editGetStarted(id) {
            fetch(`api/get_started.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = data.data;
                        document.getElementById('getStartedModalTitle').textContent = 'Edit Get Started Request';
                        document.getElementById('getStartedId').value = item.id;
                        document.getElementById('getStartedName').value = item.full_name;
                        document.getElementById('getStartedEmail').value = item.email;
                        document.getElementById('getStartedPhone').value = item.phone || '';
                        document.getElementById('getStartedService').value = item.service_interest;
                        document.getElementById('getStartedTime').value = item.preferred_time || '';
                        document.getElementById('getStartedLanguage').value = item.preferred_language || 'English';
                        document.getElementById('getStartedStatus').value = item.status || 'new';
                        document.getElementById('getStartedNotes').value = item.notes || '';
                        new bootstrap.Modal(document.getElementById('getStartedModal')).show();
                    }
                })
                .catch(error => console.error('Error loading get started request:', error));
        }

        function saveGetStarted() {
            const formData = {
                id: document.getElementById('getStartedId').value,
                full_name: document.getElementById('getStartedName').value,
                email: document.getElementById('getStartedEmail').value,
                phone: document.getElementById('getStartedPhone').value,
                service_interest: document.getElementById('getStartedService').value,
                preferred_time: document.getElementById('getStartedTime').value,
                preferred_language: document.getElementById('getStartedLanguage').value,
                status: document.getElementById('getStartedStatus').value,
                notes: document.getElementById('getStartedNotes').value
            };

            const method = formData.id ? 'PUT' : 'POST';
            const url = 'api/get_started.php';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('getStartedModal')).hide();
                    loadGetStartedRequests();
                    loadDashboardStats();
                    alert('Get started request saved successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to save'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Network error. Please try again.');
            });
        }

        function deleteGetStarted(id) {
            if (confirm('Are you sure you want to delete this get started request?')) {
                fetch(`api/get_started.php?id=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadGetStartedRequests();
                        loadDashboardStats();
                        alert('Get started request deleted successfully!');
                    } else {
                        alert('Error: ' + (data.message || 'Failed to delete'));
                    }
                })
                .catch(error => console.error('Error deleting get started request:', error));
            }
        }

        // Developer Management Functions
        function loadDevelopers() {
            fetch('api/developers.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('developersTableBody');
                        tbody.innerHTML = '';

                        if (data.data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No developers found. <a href="#" onclick="openDeveloperModal()">Add your first developer</a></td></tr>';
                            return;
                        }

                        data.data.forEach(developer => {
                            const statusBadge = getStatusBadge(developer.status);

                            const row = `
                                <tr>
                                    <td>${developer.developer_id}</td>
                                    <td>${developer.full_name}</td>
                                    <td>${developer.email || 'N/A'}</td>
                                    <td>${developer.phone || 'N/A'}</td>
                                    <td>₹${parseFloat(developer.salary_settlement || 0).toLocaleString()}</td>
                                    <td>${statusBadge}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary action-btn" onclick="editDeveloper('${developer.developer_id}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger action-btn" onclick="deleteDeveloper('${developer.developer_id}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });
                    }
                })
                .catch(error => console.error('Error loading developers:', error));
        }

        function getStatusBadge(status) {
            const statusMap = {
                'active': '<span class="status-badge bg-success">Active</span>',
                'inactive': '<span class="status-badge bg-secondary">Inactive</span>',
                'on-leave': '<span class="status-badge bg-warning">On Leave</span>'
            };
            return statusMap[status] || '<span class="status-badge bg-secondary">Unknown</span>';
        }

        function openDeveloperModal() {
            document.getElementById('developerModalTitle').textContent = 'Add Developer';
            document.getElementById('developerId').value = '';
            document.getElementById('developerForm').reset();
            document.getElementById('developerStatus').value = 'active';
            document.getElementById('developerJoinDate').value = new Date().toISOString().split('T')[0];
            calculateSalarySettlement(); // Initialize settlement calculation
            new bootstrap.Modal(document.getElementById('developerModal')).show();
        }

        function calculateSalarySettlement() {
            const accumulated = parseFloat(document.getElementById('developerSalaryAccumulated').value) || 0;
            const paid = parseFloat(document.getElementById('developerSalaryPaid').value) || 0;
            const settlement = accumulated - paid;
            document.getElementById('developerSalarySettlement').value = settlement.toFixed(2);
        }

        function editDeveloper(id) {
            fetch(`api/developers.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const developer = data.data;
                        document.getElementById('developerModalTitle').textContent = 'Edit Developer';
                        document.getElementById('developerId').value = developer.developer_id;
                        document.getElementById('developerName').value = developer.full_name;
                        document.getElementById('developerEmail').value = developer.email || '';
                        document.getElementById('developerPhone').value = developer.phone || '';
                        document.getElementById('developerSkills').value = developer.skills || '';
                        document.getElementById('developerExperience').value = developer.experience_years || 0;
                        document.getElementById('developerSpecialization').value = developer.specialization || '';
                        document.getElementById('developerHourlyRate').value = developer.hourly_rate || '';
                        document.getElementById('developerJoinDate').value = developer.join_date || '';
                        document.getElementById('developerStatus').value = developer.status || 'active';
                        document.getElementById('developerNotes').value = developer.notes || '';
                        document.getElementById('developerSalaryAccumulated').value = developer.salary_accumulated || 0;
                        document.getElementById('developerSalaryPaid').value = developer.salary_paid || 0;
                        document.getElementById('developerSalarySettlement').value = developer.salary_settlement || 0;
                        new bootstrap.Modal(document.getElementById('developerModal')).show();
                    }
                })
                .catch(error => console.error('Error loading developer:', error));
        }

        function saveDeveloper() {
            const form = document.getElementById('developerForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const developerData = {
                developer_id: document.getElementById('developerId').value,
                full_name: document.getElementById('developerName').value,
                email: document.getElementById('developerEmail').value,
                phone: document.getElementById('developerPhone').value,
                skills: document.getElementById('developerSkills').value,
                experience_years: parseInt(document.getElementById('developerExperience').value) || 0,
                specialization: document.getElementById('developerSpecialization').value,
                hourly_rate: parseFloat(document.getElementById('developerHourlyRate').value) || null,
                join_date: document.getElementById('developerJoinDate').value,
                status: document.getElementById('developerStatus').value,
                notes: document.getElementById('developerNotes').value,
                salary_accumulated: parseFloat(document.getElementById('developerSalaryAccumulated').value) || 0,
                salary_paid: parseFloat(document.getElementById('developerSalaryPaid').value) || 0
            };

            const method = developerData.developer_id ? 'PUT' : 'POST';
            const url = 'api/developers.php';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(developerData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('developerModal')).hide();
                    loadDevelopers();
                    loadDashboardStats();
                    alert(data.message || 'Developer saved successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to save developer'));
                }
            })
            .catch(error => {
                console.error('Error saving developer:', error);
                alert('Network error. Please try again.');
            });
        }

        function deleteDeveloper(id) {
            if (!confirm('Are you sure you want to delete this developer? This action cannot be undone.')) {
                return;
            }

            fetch(`api/developers.php?id=${id}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadDevelopers();
                    loadDashboardStats();
                    alert('Developer deleted successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete developer'));
                }
            })
            .catch(error => console.error('Error deleting developer:', error));
        }

        // Customers CRUD
        function loadCustomers() {
            fetch('api/customers.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('customersTableBody');
                        tbody.innerHTML = '';

                        data.data.forEach(customer => {
                            const statusBadge = getStatusBadge(customer.customer_status);
                            const typeBadge = getTypeBadge(customer.customer_type);
                            const revenue = customer.total_revenue ? '₹' + parseFloat(customer.total_revenue).toLocaleString() : '₹0';

                            const row = `
                                <tr>
                                    <td>${customer.customer_id}</td>
                                    <td>${customer.full_name}</td>
                                    <td>${customer.email || '-'}</td>
                                    <td>${customer.phone || '-'}</td>
                                    <td>${customer.company_name || '-'}</td>
                                    <td>${typeBadge}</td>
                                    <td>${statusBadge}</td>
                                    <td>${revenue}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary action-btn" onclick="editCustomer(${customer.customer_id})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger action-btn" onclick="deleteCustomer(${customer.customer_id})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });
                    }
                })
                .catch(error => console.error('Error loading customers:', error));
        }

        function openCustomerModal() {
            document.getElementById('customerModalTitle').textContent = 'Add Customer';
            document.getElementById('customerId').value = '';
            document.getElementById('customerForm').reset();
            document.getElementById('customerStatus').value = 'active';
            document.getElementById('customerType').value = 'individual';
            document.getElementById('customerCountry').value = 'India';
            document.getElementById('customerTotalRevenue').value = '';
            new bootstrap.Modal(document.getElementById('customerModal')).show();
        }

        function editCustomer(id) {
            fetch(`api/customers.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const customer = data.data;
                        document.getElementById('customerModalTitle').textContent = 'Edit Customer';
                        document.getElementById('customerId').value = customer.customer_id;
                        document.getElementById('customerName').value = customer.full_name;
                        document.getElementById('customerEmail').value = customer.email || '';
                        document.getElementById('customerPhone').value = customer.phone || '';
                        document.getElementById('customerCompany').value = customer.company_name || '';
                        document.getElementById('customerDesignation').value = customer.designation || '';
                        document.getElementById('customerType').value = customer.customer_type || 'individual';
                        document.getElementById('customerStatus').value = customer.customer_status || 'active';
                        document.getElementById('customerGST').value = customer.gst_number || '';
                        document.getElementById('customerPAN').value = customer.pan_number || '';
                        document.getElementById('customerAddress').value = customer.address || '';
                        document.getElementById('customerCity').value = customer.city || '';
                        document.getElementById('customerState').value = customer.state || '';
                        document.getElementById('customerCountry').value = customer.country || 'India';
                        document.getElementById('customerZipCode').value = customer.zip_code || '';
                        document.getElementById('customerNotes').value = customer.notes || '';
                        document.getElementById('customerTotalRevenue').value = customer.total_revenue || '';
                        new bootstrap.Modal(document.getElementById('customerModal')).show();
                    }
                })
                .catch(error => console.error('Error loading customer:', error));
        }

        function saveCustomer() {
            const formData = {
                customer_id: document.getElementById('customerId').value,
                full_name: document.getElementById('customerName').value,
                email: document.getElementById('customerEmail').value,
                phone: document.getElementById('customerPhone').value,
                company_name: document.getElementById('customerCompany').value,
                designation: document.getElementById('customerDesignation').value,
                customer_type: document.getElementById('customerType').value,
                customer_status: document.getElementById('customerStatus').value,
                gst_number: document.getElementById('customerGST').value,
                pan_number: document.getElementById('customerPAN').value,
                address: document.getElementById('customerAddress').value,
                city: document.getElementById('customerCity').value,
                state: document.getElementById('customerState').value,
                country: document.getElementById('customerCountry').value,
                zip_code: document.getElementById('customerZipCode').value,
                notes: document.getElementById('customerNotes').value,
                total_revenue: document.getElementById('customerTotalRevenue').value || '0'
            };

            const method = formData.customer_id ? 'PUT' : 'POST';
            const url = formData.customer_id ? 'api/customers.php' : 'api/customers.php';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
                    loadCustomers();
                    loadDashboardStats();
                    alert('Customer saved successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to save customer'));
                }
            })
            .catch(error => console.error('Error saving customer:', error));
        }

        function deleteCustomer(id) {
            if (!confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
                return;
            }

            fetch(`api/customers.php?id=${id}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadCustomers();
                    loadDashboardStats();
                    alert('Customer deleted successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete customer'));
                }
            })
            .catch(error => console.error('Error deleting customer:', error));
        }

        function getStatusBadge(status) {
            const badges = {
                'active': '<span class="status-badge bg-success">Active</span>',
                'inactive': '<span class="status-badge bg-secondary">Inactive</span>',
                'prospect': '<span class="status-badge bg-warning">Prospect</span>',
                'former': '<span class="status-badge bg-danger">Former</span>'
            };
            return badges[status] || '<span class="status-badge bg-secondary">' + status + '</span>';
        }

        function getTypeBadge(type) {
            const badges = {
                'individual': '<span class="badge bg-primary">Individual</span>',
                'business': '<span class="badge bg-info">Business</span>',
                'enterprise': '<span class="badge bg-success">Enterprise</span>'
            };
            return badges[type] || '<span class="badge bg-secondary">' + type + '</span>';
        }

        // Projects CRUD Functions
        function loadProjects() {
            fetch('api/projects.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('projectsTableBody');
                        tbody.innerHTML = '';

                        data.data.forEach(project => {
                            const developerNames = project.developer_names && project.developer_names.length > 0
                                ? project.developer_names.join(', ')
                                : 'N/A';

                            const row = `
                                <tr>
                                    <td>${project.id}</td>
                                    <td>${project.project_name}</td>
                                    <td>${project.customer_name || 'N/A'}</td>
                                    <td>${developerNames}</td>
                                    <td>${project.progress_percentage}%</td>
                                    <td>₹${parseFloat(project.pending_amount || 0).toLocaleString()}</td>
                                    <td>${project.subscription == 1 ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editProject(${project.id})" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteProject(${project.id})" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });

                        // Update project count in stats
                        document.getElementById('project-count').textContent = data.data.length;
                    }
                })
                .catch(error => console.error('Error loading projects:', error));
        }

        function openProjectModal() {
            document.getElementById('projectModalTitle').textContent = 'Add Project';
            document.getElementById('projectForm').reset();
            document.getElementById('projectId').value = '';
            loadCustomersForProject();
            loadDevelopersForProject();
            new bootstrap.Modal(document.getElementById('projectModal')).show();
        }

        function editProject(id) {
            fetch(`api/projects.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const project = data.data;
                        document.getElementById('projectModalTitle').textContent = 'Edit Project';
                        document.getElementById('projectId').value = project.id;
                        document.getElementById('projectName').value = project.project_name || '';
                        document.getElementById('projectCustomerId').value = project.customer_id || '';

                        // Handle multiple developers
                        const developerSelect = document.getElementById('projectDeveloperId');
                        if (project.developer_id) {
                            const developerIds = JSON.parse(project.developer_id);
                            Array.from(developerSelect.options).forEach(option => {
                                option.selected = developerIds.includes(parseInt(option.value));
                            });
                        }

                        document.getElementById('projectCategory').value = project.project_category || '';
                        document.getElementById('projectStatus').value = project.status || 'planning';
                        document.getElementById('projectPriority').value = project.priority || 'medium';
                        document.getElementById('projectBudget').value = project.budget || '';
                        document.getElementById('projectProgress').value = project.progress_percentage || 0;
                        document.getElementById('projectStartDate').value = project.start_date || '';
                        document.getElementById('projectEndDate').value = project.end_date || '';
                        document.getElementById('projectTechnologies').value = project.technologies || '';
                        document.getElementById('projectAdvancePayment').value = project.advance_payment || '';
                        document.getElementById('projectSettlement').value = project.settlement || '';
                        document.getElementById('projectSubscription').value = project.subscription || 0;
                        document.getElementById('projectSubscriptionAmount').value = project.subscription_amount || '';
                        document.getElementById('projectDescription').value = project.description || '';

                        loadCustomersForProject();
                        loadDevelopersForProject();
                        new bootstrap.Modal(document.getElementById('projectModal')).show();
                    }
                })
                .catch(error => console.error('Error loading project:', error));
        }

        function saveProject() {
            // Get selected developer IDs
            const developerSelect = document.getElementById('projectDeveloperId');
            const selectedDevelopers = Array.from(developerSelect.selectedOptions).map(option => parseInt(option.value)).filter(id => id > 0);

            const formData = {
                id: document.getElementById('projectId').value,
                project_name: document.getElementById('projectName').value,
                customer_id: document.getElementById('projectCustomerId').value,
                developer_id: selectedDevelopers,
                project_category: document.getElementById('projectCategory').value,
                status: document.getElementById('projectStatus').value,
                priority: document.getElementById('projectPriority').value,
                budget: document.getElementById('projectBudget').value || '0',
                progress_percentage: document.getElementById('projectProgress').value || '0',
                start_date: document.getElementById('projectStartDate').value || null,
                end_date: document.getElementById('projectEndDate').value || null,
                technologies: document.getElementById('projectTechnologies').value,
                advance_payment: document.getElementById('projectAdvancePayment').value || '0',
                settlement: document.getElementById('projectSettlement').value || '0',
                subscription: document.getElementById('projectSubscription').value,
                subscription_amount: document.getElementById('projectSubscriptionAmount').value || '0',
                description: document.getElementById('projectDescription').value
            };

            // Validate required fields
            if (!formData.project_name.trim()) {
                alert('Project name is required');
                return;
            }

            const method = formData.id ? 'PUT' : 'POST';
            const url = formData.id ? `api/projects.php?id=${formData.id}` : 'api/projects.php';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('projectModal')).hide();
                    loadProjects();
                    loadDashboardStats();
                    alert('Project saved successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to save project'));
                }
            })
            .catch(error => console.error('Error saving project:', error));
        }

        function deleteProject(id) {
            if (!confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
                return;
            }

            fetch(`api/projects.php?id=${id}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadProjects();
                    loadDashboardStats();
                    alert('Project deleted successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete project'));
                }
            })
            .catch(error => console.error('Error deleting project:', error));
        }

        function loadCustomersForProject() {
            fetch('api/customers.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('projectCustomerId');
                        const currentValue = select.value;
                        select.innerHTML = '<option value="">Select Customer</option>';

                        data.data.forEach(customer => {
                            const option = document.createElement('option');
                            option.value = customer.customer_id;
                            option.textContent = customer.full_name + (customer.company_name ? ` (${customer.company_name})` : '');
                            select.appendChild(option);
                        });

                        select.value = currentValue;
                    }
                })
                .catch(error => console.error('Error loading customers for project:', error));
        }

        function loadDevelopersForProject() {
            fetch('api/developers.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('projectDeveloperId');
                        const currentValue = select.value;
                        select.innerHTML = '<option value="">Select Developer</option>';

                        data.data.forEach(developer => {
                            const option = document.createElement('option');
                            option.value = developer.developer_id;
                            option.textContent = developer.full_name;
                            select.appendChild(option);
                        });

                        select.value = currentValue;
                    }
                })
                .catch(error => console.error('Error loading developers for project:', error));
        }

        function getProjectStatusBadge(status) {
            const badges = {
                'planning': '<span class="status-badge bg-secondary">Planning</span>',
                'in-progress': '<span class="status-badge bg-primary">In Progress</span>',
                'on-hold': '<span class="status-badge bg-warning">On Hold</span>',
                'completed': '<span class="status-badge bg-success">Completed</span>',
                'cancelled': '<span class="status-badge bg-danger">Cancelled</span>'
            };
            return badges[status] || '<span class="status-badge bg-secondary">' + status + '</span>';
        }

        function getPriorityBadge(priority) {
            const badges = {
                'low': '<span class="badge bg-success">Low</span>',
                'medium': '<span class="badge bg-warning">Medium</span>',
                'high': '<span class="badge bg-danger">High</span>',
                'urgent': '<span class="badge bg-dark">Urgent</span>'
            };
            return badges[priority] || '<span class="badge bg-secondary">' + priority + '</span>';
        }

        // Logout function
        function logout() {
            fetch('logout.php')
                .then(() => {
                    window.location.href = '../index.html';
                })
                .catch(error => {
                    console.error('Logout error:', error);
                    window.location.href = '../index.html';
                });
        }

        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');

            if (sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            } else {
                sidebar.classList.add('show');
                overlay.classList.add('show');
            }
        }

        // Close sidebar when clicking on a nav link (mobile)
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 767.98) {
                    toggleSidebar();
                }
            });
        });

        // Close sidebar on window resize if desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 767.98) {
                const sidebar = document.querySelector('.sidebar');
                const overlay = document.querySelector('.sidebar-overlay');
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardStats();
        });

        // Users Management Functions
        function openUserModal(userId = null) {
            if (userId) {
                // Edit mode
                document.getElementById('userModalTitle').textContent = 'Edit User';
                // Load user data (would need API endpoint)
                fetch(`api/users.php?id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const user = data.data;
                            document.getElementById('userId').value = user.id;
                            document.getElementById('userUsername').value = user.username;
                            document.getElementById('userEmail').value = user.email;
                            document.getElementById('userFullName').value = user.full_name || '';
                            document.getElementById('userRole').value = user.role || 'user';
                            document.getElementById('userStatus').value = user.status || 'active';
                        }
                    });
            } else {
                // Add mode
                document.getElementById('userModalTitle').textContent = 'Add User';
                document.getElementById('userForm').reset();
                document.getElementById('userId').value = '';
            }
            new bootstrap.Modal(document.getElementById('userModal')).show();
        }

        function saveUser() {
            const formData = {
                id: document.getElementById('userId').value,
                username: document.getElementById('userUsername').value,
                email: document.getElementById('userEmail').value,
                password: document.getElementById('userPassword').value,
                full_name: document.getElementById('userFullName').value,
                role: document.getElementById('userRole').value,
                status: document.getElementById('userStatus').value
            };

            // Validate required fields
            if (!formData.username.trim() || !formData.email.trim()) {
                alert('Username and email are required');
                return;
            }

            if (!formData.id && !formData.password.trim()) {
                alert('Password is required for new users');
                return;
            }

            const method = formData.id ? 'PUT' : 'POST';
            const url = formData.id ? `api/users.php?id=${formData.id}` : 'api/users.php';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
                    loadUsers();
                    alert('User saved successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to save user'));
                }
            })
            .catch(error => console.error('Error saving user:', error));
        }

        function deleteUser(id) {
            if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                return;
            }

            fetch(`api/users.php?id=${id}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadUsers();
                    alert('User deleted successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete user'));
                }
            })
            .catch(error => console.error('Error deleting user:', error));
        }

        function loadUsers() {
            fetch('api/users.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('usersTableBody');
                        tbody.innerHTML = '';

                        data.data.forEach(user => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${user.id}</td>
                                <td>${user.username}</td>
                                <td>${user.email}</td>
                                <td><span class="badge bg-primary">${user.role}</span></td>
                                <td><span class="badge bg-${user.status === 'active' ? 'success' : 'secondary'}">${user.status}</span></td>
                                <td>${new Date(user.created_at).toLocaleDateString()}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="openUserModal(${user.id})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                    }
                })
                .catch(error => console.error('Error loading users:', error));
        }

        // Project Activities Management Functions
        function openActivityModal(activityId = null) {
            if (activityId) {
                // Edit mode
                const modalTitle = document.getElementById('activityModalTitle');
                if (modalTitle) modalTitle.textContent = 'Edit Activity';
                // Load activity data (would need API endpoint)
                fetch(`api/activities.php?id=${activityId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const activity = data.data;
                            const activityIdEl = document.getElementById('activityId');
                            const activityProjectIdEl = document.getElementById('activityProjectId');
                            const activityPhaseEl = document.getElementById('activityPhase');
                            const activityUserIdEl = document.getElementById('activityUserId');
                            const activitySubPhaseEl = document.getElementById('activitySubPhase');
                            const activityCustomerVisibleEl = document.getElementById('activityCustomerVisible');
                            
                            if (activityIdEl) activityIdEl.value = activity.id;
                            if (activityProjectIdEl) activityProjectIdEl.value = activity.project_id;
                            if (activityPhaseEl) activityPhaseEl.value = activity.phase;
                            if (activityUserIdEl) activityUserIdEl.value = activity.created_by || '';
                            if (activitySubPhaseEl) activitySubPhaseEl.value = activity.sub_phase;
                            if (activityCustomerVisibleEl) activityCustomerVisibleEl.checked = activity.is_customer_visible == 1;
                        }
                    });
            } else {
                // Add mode
                const modalTitle = document.getElementById('activityModalTitle');
                if (modalTitle) modalTitle.textContent = 'Add Project Activity';
                const activityForm = document.getElementById('activityForm');
                if (activityForm) activityForm.reset();
                const activityIdEl = document.getElementById('activityId');
                if (activityIdEl) activityIdEl.value = '';
            }

            // Load projects, users, and phase templates for dropdowns
            loadProjectsForActivity();
            loadUsersForActivity();
            loadPhaseTemplates();

            const modal = document.getElementById('activityModal');
            if (modal) {
                new bootstrap.Modal(modal).show();
            }
        }

        function saveActivity() {
            const formData = {
                id: document.getElementById('activityId')?.value || '',
                project_id: document.getElementById('activityProjectId')?.value || '',
                phase: document.getElementById('activityPhase')?.value || '',
                created_by: document.getElementById('activityUserId')?.value || '',
                sub_phase: document.getElementById('activitySubPhase')?.value || '',
                is_customer_visible: document.getElementById('activityCustomerVisible')?.checked ? 1 : 0
            };

            // Validate required fields
            if (!formData.project_id || !formData.phase || !formData.sub_phase) {
                alert('Project, phase, and sub phase are required');
                return;
            }

            const method = formData.id ? 'PUT' : 'POST';
            const url = formData.id ? `api/activities.php?id=${formData.id}` : 'api/activities.php';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = document.getElementById('activityModal');
                    if (modal) {
                        bootstrap.Modal.getInstance(modal).hide();
                    }
                    loadActivities();
                    alert('Activity saved successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to save activity'));
                }
            })
            .catch(error => console.error('Error saving activity:', error));
        }

        function deleteActivity(id) {
            if (!confirm('Are you sure you want to delete this activity?')) {
                return;
            }

            fetch(`api/activities.php?id=${id}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadActivities();
                    alert('Activity deleted successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete activity'));
                }
            })
            .catch(error => console.error('Error deleting activity:', error));
        }

        function loadActivities() {
            fetch('api/activities.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('activitiesTableBody');
                        tbody.innerHTML = '';

                        data.data.forEach(activity => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${activity.id}</td>
                                <td>${activity.project_name}</td>
                                <td><span class="badge bg-info">${activity.phase}</span></td>
                                <td>${activity.sub_phase.substring(0, 50)}${activity.sub_phase.length > 50 ? '...' : ''}</td>
                                <td>${activity.user_name || 'N/A'}</td>
                                <td>${new Date(activity.created_at).toLocaleString()}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="openActivityModal(${activity.id})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteActivity(${activity.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                    }
                })
                .catch(error => console.error('Error loading activities:', error));
        }

        function loadPhaseTemplates() {
            fetch('api/activities.php?templates=true')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.phaseTemplates = data.data;
                        populatePhaseDropdown();
                    }
                })
                .catch(error => console.error('Error loading phase templates:', error));
        }

        function populatePhaseDropdown() {
            const phaseSelect = document.getElementById('activityPhase');
            if (!phaseSelect) {
                console.error('activityPhase element not found');
                return;
            }
            phaseSelect.innerHTML = '<option value="">Select Phase</option>';
            
            window.phaseTemplates.forEach(phase => {
                const option = document.createElement('option');
                option.value = phase.phase_name;
                option.setAttribute('data-order', phase.phase_order);
                option.textContent = `${phase.phase_order}. ${phase.phase_name}`;
                phaseSelect.appendChild(option);
            });
        }

        function onPhaseChange() {
            const phaseSelect = document.getElementById('activityPhase');
            const subPhaseSelect = document.getElementById('activitySubPhase');
            if (!phaseSelect || !subPhaseSelect) {
                console.error('Phase or sub-phase select elements not found');
                return;
            }
            const selectedPhase = phaseSelect.value;
            
            subPhaseSelect.innerHTML = '<option value="">Select Sub Phase</option>';
            
            if (selectedPhase) {
                const phaseData = window.phaseTemplates.find(p => p.phase_name === selectedPhase);
                if (phaseData) {
                    phaseData.sub_phases.forEach(subPhase => {
                        const option = document.createElement('option');
                        option.value = subPhase.name;
                        option.setAttribute('data-order', subPhase.order);
                        option.setAttribute('data-description', subPhase.description);
                        option.textContent = `${subPhase.order}. ${subPhase.name}`;
                        subPhaseSelect.appendChild(option);
                    });
                }
            }
        }

        function loadProjectsForActivity() {
            fetch('api/projects.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('activityProjectId');
                        if (!select) {
                            console.error('activityProjectId element not found');
                            return;
                        }
                        const currentValue = select.value;
                        select.innerHTML = '<option value="">Select Project</option>';

                        data.data.forEach(project => {
                            const option = document.createElement('option');
                            option.value = project.id;
                            option.textContent = project.project_name;
                            select.appendChild(option);
                        });

                        select.value = currentValue;
                    }
                })
                .catch(error => console.error('Error loading projects for activity:', error));
        }

        function loadUsersForActivity() {
            fetch('api/users.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('activityUserId');
                        if (!select) {
                            console.error('activityUserId element not found');
                            return;
                        }
                        const currentValue = select.value;
                        select.innerHTML = '<option value="">Select User</option>';

                        data.data.forEach(user => {
                            const option = document.createElement('option');
                            option.value = user.id;
                            option.textContent = user.username;
                            select.appendChild(option);
                        });

                        select.value = currentValue;
                    }
                })
                .catch(error => console.error('Error loading users for activity:', error));
        }

        // Invoice Management Functions
        function openInvoiceModal(invoiceId = null) {
            const modalTitle = document.getElementById('invoiceModalTitle');
            const invoiceForm = document.getElementById('invoiceForm');
            const invoiceModal = document.getElementById('invoiceModal');

            if (!modalTitle || !invoiceForm || !invoiceModal) {
                console.error('Invoice modal elements not found');
                alert('Invoice modal is not properly loaded. Please refresh the page.');
                return;
            }

            if (invoiceId) {
                // Edit mode
                modalTitle.textContent = 'Edit Invoice';
                // Load invoice data (would need API endpoint)
                fetch(`api/invoices.php?id=${invoiceId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const invoice = data.data;
                            const invoiceIdEl = document.getElementById('invoiceId');
                            const invoiceProjectIdEl = document.getElementById('invoiceProjectId');
                            const invoiceCustomerIdEl = document.getElementById('invoiceCustomerId');
                            const invoiceNumberEl = document.getElementById('invoiceNumber');
                            const invoiceIssueDateEl = document.getElementById('invoiceIssueDate');
                            const invoiceDueDateEl = document.getElementById('invoiceDueDate');
                            const invoiceStatusEl = document.getElementById('invoiceStatus');
                            const invoiceNotesEl = document.getElementById('invoiceNotes');

                            if (invoiceIdEl) invoiceIdEl.value = invoice.id;
                            if (invoiceProjectIdEl) invoiceProjectIdEl.value = invoice.project_id;
                            if (invoiceCustomerIdEl) invoiceCustomerIdEl.value = invoice.customer_id;
                            if (invoiceNumberEl) invoiceNumberEl.value = invoice.invoice_number;
                            if (invoiceIssueDateEl) invoiceIssueDateEl.value = invoice.issue_date;
                            if (invoiceDueDateEl) invoiceDueDateEl.value = invoice.due_date;
                            if (invoiceStatusEl) invoiceStatusEl.value = invoice.status;
                            if (invoiceNotesEl) invoiceNotesEl.value = invoice.notes || '';
                            // Load invoice items (would need separate API call)
                        }
                    });
            } else {
                // Add mode
                modalTitle.textContent = 'Create Invoice';
                invoiceForm.reset();
                const invoiceIdEl = document.getElementById('invoiceId');
                const invoiceIssueDateEl = document.getElementById('invoiceIssueDate');
                const invoiceDueDateEl = document.getElementById('invoiceDueDate');

                if (invoiceIdEl) invoiceIdEl.value = '';
                // Set current date as issue date
                const today = new Date().toISOString().split('T')[0];
                if (invoiceIssueDateEl) invoiceIssueDateEl.value = today;
                // Set due date to 30 days from now
                const dueDate = new Date();
                dueDate.setDate(dueDate.getDate() + 30);
                if (invoiceDueDateEl) invoiceDueDateEl.value = dueDate.toISOString().split('T')[0];
                // Generate invoice number
                generateInvoiceNumber();
            }

            // Load projects and customers for dropdowns
            loadProjectsForInvoice();
            loadCustomersForInvoice();

            new bootstrap.Modal(invoiceModal).show();
        }

        function saveInvoice() {
            const invoiceId = document.getElementById('invoiceId');
            const invoiceProjectId = document.getElementById('invoiceProjectId');
            const invoiceCustomerId = document.getElementById('invoiceCustomerId');
            const invoiceNumber = document.getElementById('invoiceNumber');
            const invoiceIssueDate = document.getElementById('invoiceIssueDate');
            const invoiceDueDate = document.getElementById('invoiceDueDate');
            const invoiceStatus = document.getElementById('invoiceStatus');
            const invoiceSubtotal = document.getElementById('invoiceSubtotal');
            const invoiceTaxRate = document.getElementById('invoiceTaxRate');
            const invoiceTotal = document.getElementById('invoiceTotal');
            const invoiceNotes = document.getElementById('invoiceNotes');

            if (!invoiceId || !invoiceProjectId || !invoiceCustomerId || !invoiceNumber ||
                !invoiceIssueDate || !invoiceDueDate || !invoiceStatus || !invoiceSubtotal ||
                !invoiceTaxRate || !invoiceTotal || !invoiceNotes) {
                console.error('Missing invoice form elements');
                alert('Invoice form is not properly loaded. Please refresh the page.');
                return;
            }

            const formData = {
                id: invoiceId.value,
                project_id: invoiceProjectId.value,
                customer_id: invoiceCustomerId.value,
                invoice_number: invoiceNumber.value,
                issue_date: invoiceIssueDate.value,
                due_date: invoiceDueDate.value,
                status: invoiceStatus.value,
                subtotal: invoiceSubtotal.value,
                tax_rate: invoiceTaxRate.value,
                total_amount: invoiceTotal.value,
                notes: invoiceNotes.value,
                items: [] // Would collect invoice items
            };

            // Validate required fields
            if (!formData.project_id || !formData.customer_id || !formData.issue_date || !formData.due_date) {
                alert('Project, customer, issue date, and due date are required');
                return;
            }

            const method = formData.id ? 'PUT' : 'POST';
            const url = formData.id ? `api/invoices.php?id=${formData.id}` : 'api/invoices.php';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('invoiceModal')).hide();
                    loadInvoices();
                    alert('Invoice saved successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to save invoice'));
                }
            })
            .catch(error => console.error('Error saving invoice:', error));
        }

        function deleteInvoice(id) {
            if (!confirm('Are you sure you want to delete this invoice?')) {
                return;
            }

            fetch(`api/invoices.php?id=${id}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadInvoices();
                    alert('Invoice deleted successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete invoice'));
                }
            })
            .catch(error => console.error('Error deleting invoice:', error));
        }

        function loadInvoices() {
            fetch('api/invoices.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('invoiceTableBody');
                        tbody.innerHTML = '';

                        data.data.forEach(invoice => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${invoice.invoice_number}</td>
                                <td>${invoice.project_name}</td>
                                <td>${invoice.customer_name}</td>
                                <td>₹${parseFloat(invoice.total_amount).toLocaleString()}</td>
                                <td><span class="badge bg-${getStatusColor(invoice.status)}">${invoice.status}</span></td>
                                <td>${new Date(invoice.issue_date).toLocaleDateString()}</td>
                                <td>${new Date(invoice.due_date).toLocaleDateString()}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="openInvoiceModal(${invoice.id})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteInvoice(${invoice.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                    }
                })
                .catch(error => console.error('Error loading invoices:', error));
        }

        function loadProjectsForInvoice() {
            fetch('api/projects.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('invoiceProjectId');
                        if (!select) {
                            console.warn('invoiceProjectId element not found');
                            return;
                        }
                        const currentValue = select.value;
                        select.innerHTML = '<option value="">Select Project</option>';

                        data.data.forEach(project => {
                            const option = document.createElement('option');
                            option.value = project.id;
                            option.textContent = project.project_name;
                            select.appendChild(option);
                        });

                        select.value = currentValue;
                    }
                })
                .catch(error => console.error('Error loading projects for invoice:', error));
        }

        function loadCustomersForInvoice() {
            fetch('api/customers.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('invoiceCustomerId');
                        if (!select) {
                            console.warn('invoiceCustomerId element not found');
                            return;
                        }
                        const currentValue = select.value;
                        select.innerHTML = '<option value="">Select Customer</option>';

                        data.data.forEach(customer => {
                            const option = document.createElement('option');
                            option.value = customer.customer_id;
                            option.textContent = customer.full_name + (customer.company_name ? ` (${customer.company_name})` : '');
                            select.appendChild(option);
                        });

                        select.value = currentValue;
                    }
                })
                .catch(error => console.error('Error loading customers for invoice:', error));
        }

        function generateInvoiceNumber() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            const invoiceNumberEl = document.getElementById('invoiceNumber');
            if (invoiceNumberEl) {
                invoiceNumberEl.value = `INV-${year}${month}-${random}`;
            } else {
                console.warn('invoiceNumber element not found');
            }
        }

        function addInvoiceItem() {
            const container = document.getElementById('invoiceItems');
            if (!container) {
                console.warn('invoiceItems container not found');
                return;
            }
            const itemDiv = document.createElement('div');
            itemDiv.className = 'invoice-item row mb-2';
            itemDiv.innerHTML = `
                <div class="col-md-5">
                    <input type="text" class="form-control" placeholder="Description" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" placeholder="Qty" min="1" value="1" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" placeholder="Rate" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" placeholder="Amount" readonly>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeInvoiceItem(this)">×</button>
                </div>
            `;
            container.appendChild(itemDiv);
            updateInvoiceTotals();
        }

        function removeInvoiceItem(button) {
            button.closest('.invoice-item').remove();
            updateInvoiceTotals();
        }

        function updateInvoiceTotals() {
            let subtotal = 0;
            document.querySelectorAll('.invoice-item').forEach(item => {
                const qtyInput = item.querySelector('input[placeholder="Qty"]');
                const rateInput = item.querySelector('input[placeholder="Rate"]');
                const amountInput = item.querySelector('input[placeholder="Amount"]');

                if (!qtyInput || !rateInput || !amountInput) {
                    console.warn('Missing invoice item inputs');
                    return;
                }

                const qty = parseFloat(qtyInput.value) || 0;
                const rate = parseFloat(rateInput.value) || 0;
                const amount = qty * rate;
                amountInput.value = amount.toFixed(2);
                subtotal += amount;
            });

            const subtotalElement = document.getElementById('invoiceSubtotal');
            const taxRateElement = document.getElementById('invoiceTaxRate');
            const totalElement = document.getElementById('invoiceTotal');

            if (!subtotalElement || !taxRateElement || !totalElement) {
                console.warn('Missing invoice total elements');
                return;
            }

            subtotalElement.value = subtotal.toFixed(2);

            const taxRate = parseFloat(taxRateElement.value) || 0;
            const taxAmount = subtotal * (taxRate / 100);
            const total = subtotal + taxAmount;

            totalElement.value = total.toFixed(2);
        }

        function getStatusColor(status) {
            switch (status) {
                case 'paid': return 'success';
                case 'sent': return 'primary';
                case 'overdue': return 'danger';
                case 'cancelled': return 'secondary';
                default: return 'warning';
            }
        }

        // Add event listeners for invoice calculations
        document.addEventListener('input', function(e) {
            if (e.target.closest('#invoiceModal')) {
                updateInvoiceTotals();
            }
        });
    </script>
</body>
</html>
