<?php
// Database configuration - MySQL
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', 'masterminds');
define('MYSQL_DB', 'layover_solutions');

function getDB() {
    static $db = null;
    if ($db === null) {
        try {
            // MySQL connection
            $dsn = "mysql:host=" . MYSQL_HOST . ";dbname=" . MYSQL_DB . ";charset=utf8mb4";
            $db = new PDO($dsn, MYSQL_USER, MYSQL_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db->exec("SET NAMES utf8mb4");

            // Initialize database tables if they don't exist
            initializeDatabase($db);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            $db = null;
        }
    }
    return $db;
}

function initializeDatabase($db) {
    // Since migration is completed, just ensure missing columns are added
    // Tables should already exist from the migration

    // Create users table if it doesn't exist
    try {
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                full_name VARCHAR(255),
                role ENUM('admin', 'developer', 'client') DEFAULT 'client',
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
    } catch (Exception $e) {
        error_log('Error creating users table: ' . $e->getMessage());
    }

    // Create invoices table if it doesn't exist
    try {
        $db->exec("
            CREATE TABLE IF NOT EXISTS invoices (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT,
                customer_id INT,
                invoice_number VARCHAR(50),
                issue_date DATE,
                due_date DATE,
                status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
                subtotal DECIMAL(10,2) DEFAULT 0,
                tax_rate DECIMAL(5,2) DEFAULT 0,
                total_amount DECIMAL(10,2) DEFAULT 0,
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES project(id),
                FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
            )
        ");
    } catch (Exception $e) {
        error_log('Error creating invoices table: ' . $e->getMessage());
    }

    // Add missing columns to project table if they don't exist (ignore errors if columns already exist)
    try { $db->exec("ALTER TABLE project ADD COLUMN project_category VARCHAR(255)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE project ADD COLUMN description TEXT"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE project ADD COLUMN technologies TEXT"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE project ADD COLUMN start_date DATE"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE project ADD COLUMN end_date DATE"); } catch (Exception $e) {}

        // Add missing columns to contact_submissions table if they don't exist (ignore errors if columns already exist)
    try { $db->exec("ALTER TABLE contact_submissions ADD COLUMN phone VARCHAR(50)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE contact_submissions ADD COLUMN preferred_time VARCHAR(50)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE contact_submissions ADD COLUMN preferred_language VARCHAR(50)"); } catch (Exception $e) {}

    // Add missing columns to contact_submissions table if they don't exist (ignore errors if columns already exist)
    try { $db->exec("ALTER TABLE contact_submissions ADD COLUMN phone VARCHAR(50)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE contact_submissions ADD COLUMN preferred_time VARCHAR(50)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE contact_submissions ADD COLUMN preferred_language VARCHAR(50)"); } catch (Exception $e) {}

    // Add missing columns to customers table if they don't exist
    try { $db->exec("ALTER TABLE customers ADD COLUMN full_name VARCHAR(255)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE customers ADD COLUMN company_name VARCHAR(255)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE customers ADD COLUMN designation VARCHAR(255)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE customers ADD COLUMN address TEXT"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE customers ADD COLUMN city VARCHAR(255)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE customers ADD COLUMN state VARCHAR(255)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE customers ADD COLUMN country VARCHAR(255)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE customers ADD COLUMN zip_code VARCHAR(20)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE customers ADD COLUMN gst_number VARCHAR(50)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE customers ADD COLUMN pan_number VARCHAR(50)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE customers ADD COLUMN customer_type ENUM('individual', 'company') DEFAULT 'individual'"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE customers ADD COLUMN notes TEXT"); } catch (Exception $e) {}

    // Update existing customer records to populate full_name from customer_name
    try {
        $db->exec("UPDATE customers SET full_name = customer_name WHERE full_name IS NULL AND customer_name IS NOT NULL");
        $db->exec("UPDATE customers SET company_name = company WHERE company_name IS NULL AND company IS NOT NULL");
    } catch (Exception $e) {}

    // Add missing columns to project table if they don't exist (ignore errors if columns already exist)
    try { $db->exec("ALTER TABLE project ADD COLUMN project_category VARCHAR(255)"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE project ADD COLUMN description TEXT"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE project ADD COLUMN technologies TEXT"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE project ADD COLUMN start_date DATE"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE project ADD COLUMN end_date DATE"); } catch (Exception $e) {}

    // Insert sample data if tables are empty
    $count = $db->query("SELECT COUNT(*) as count FROM get_started")->fetch()['count'];
    if ($count == 0) {
        $sampleData = "
        INSERT INTO get_started (full_name, email, phone, service_interest, status, submitted_at) VALUES
        ('John Doe', 'john@example.com', '+1-555-0123', 'Basic Plan', 'new', NOW()),
        ('Jane Smith', 'jane@example.com', '+1-555-0124', 'Premium Plan', 'contacted', DATE_SUB(NOW(), INTERVAL 1 DAY)),
        ('Bob Johnson', 'bob@example.com', '+1-555-0125', 'Custom Solution', 'qualified', DATE_SUB(NOW(), INTERVAL 2 DAY));

        INSERT INTO contact_submissions (name, email, subject, message) VALUES
        ('Alice Brown', 'alice@example.com', 'Question about services', 'I have a question about your premium plan.'),
        ('Charlie Wilson', 'charlie@example.com', 'Partnership inquiry', 'I''d like to discuss a partnership opportunity.');

        INSERT INTO customers (customer_name, full_name, email, phone, company, company_name, total_revenue) VALUES
        ('Tech Corp', 'John Smith', 'contact@techcorp.com', '+1-555-0100', 'Tech Corp Inc.', 'Tech Corp Inc.', 50000.00),
        ('StartupXYZ', 'Jane Doe', 'hello@startupxyz.com', '+1-555-0101', 'StartupXYZ LLC', 'StartupXYZ LLC', 25000.00);

        INSERT INTO developer (developer_name, email, phone, skills) VALUES
        ('Mike Developer', 'mike@dev.com', '+1-555-0200', 'PHP, JavaScript, React'),
        ('Sarah Coder', 'sarah@dev.com', '+1-555-0201', 'Python, Django, Vue.js');

        INSERT INTO project (project_name, customer_id, developer_id, status, priority, budget, progress_percentage) VALUES
        ('E-commerce Website', 1, 2, 'in_progress', 'high', 15000.00, 75),
        ('Mobile App', 2, 3, 'testing', 'medium', 20000.00, 90);

        INSERT INTO project_activities (project_id, activity_type, activity_description, created_by, is_customer_visible) VALUES
        (1, 'milestone', 'Completed payment integration', 1, 1),
        (2, 'update', 'Fixed login bug', 1, 0);

        INSERT INTO project_team (project_id, developer_id) VALUES
        (1, 1),
        (2, 2);

        INSERT INTO users (username, email, password, full_name, role) VALUES
        ('admin', 'admin@layover.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin')
        ON DUPLICATE KEY UPDATE email = VALUES(email), full_name = VALUES(full_name);

        INSERT INTO admin (username, password_hash) VALUES
        ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password
        ";

        $db->exec($sampleData);
    }
}
?>
