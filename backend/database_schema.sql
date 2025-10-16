-- Layover Solutions Database Schema
-- Database: Layover
-- Created: October 16, 2025

CREATE DATABASE IF NOT EXISTS Layover;
USE Layover;

-- ===========================================
-- 1. get_started - Store form details from Get Started modal
-- ===========================================
CREATE TABLE IF NOT EXISTS get_started (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(150) NOT NULL,
    service_interest VARCHAR(100) NOT NULL,
    preferred_time VARCHAR(50),
    preferred_language VARCHAR(50),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('new', 'contacted', 'qualified', 'converted', 'lost') DEFAULT 'new',
    notes TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_submitted_at (submitted_at)
);

-- ===========================================
-- 2. customers - Store customer details and their projects and activities
-- ===========================================
CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(15),
    company_name VARCHAR(100),
    designation VARCHAR(100),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    country VARCHAR(50),
    zip_code VARCHAR(20),
    gst_number VARCHAR(20),
    pan_number VARCHAR(20),
    customer_type ENUM('individual', 'business', 'enterprise') DEFAULT 'individual',
    customer_status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    total_projects INT DEFAULT 0,
    total_revenue DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    notes TEXT,
    INDEX idx_email (email),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (customer_status),
    INDEX idx_created_at (created_at)
);

-- ===========================================
-- 3. contact - Table to save contact form details
-- ===========================================
CREATE TABLE IF NOT EXISTS contact (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(15),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    contact_type ENUM('general', 'support', 'sales', 'partnership', 'other') DEFAULT 'general',
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    replied_at TIMESTAMP NULL,
    replied_by INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_submitted_at (submitted_at),
    FOREIGN KEY (replied_by) REFERENCES admin(id) ON DELETE SET NULL
);

-- ===========================================
-- 7. admin - Table to track all the details (created first for foreign keys)
-- ===========================================
CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'manager', 'developer', 'support') DEFAULT 'admin',
    department VARCHAR(50),
    phone VARCHAR(15),
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    login_attempts INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status),
    FOREIGN KEY (created_by) REFERENCES admin(id) ON DELETE SET NULL
);

-- ===========================================
-- 4. developer - Table to store developer details, current project, upcoming project
-- ===========================================
CREATE TABLE IF NOT EXISTS developer (
    id INT PRIMARY KEY AUTO_INCREMENT,
    developer_id VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(15),
    skills TEXT,
    experience_years INT DEFAULT 0,
    specialization VARCHAR(100),
    current_project_id INT,
    hourly_rate DECIMAL(8,2),
    status ENUM('active', 'inactive', 'on_leave', 'terminated') DEFAULT 'active',
    join_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    notes TEXT,
    INDEX idx_email (email),
    INDEX idx_developer_id (developer_id),
    INDEX idx_status (status),
    INDEX idx_current_project (current_project_id),
    FOREIGN KEY (current_project_id) REFERENCES project(id) ON DELETE SET NULL
);

-- ===========================================
-- 5. project - Table to track new project and ongoing and upcoming project
-- ===========================================
CREATE TABLE IF NOT EXISTS project (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id VARCHAR(20) UNIQUE NOT NULL,
    project_name VARCHAR(200) NOT NULL,
    project_description TEXT,
    customer_id INT,
    project_type VARCHAR(100),
    project_category ENUM('web_development', 'mobile_app', 'ui_ux_design', 'digital_marketing', 'ecommerce', 'consulting', 'maintenance', 'other') DEFAULT 'web_development',
    status ENUM('new', 'planning', 'in_progress', 'testing', 'completed', 'on_hold', 'cancelled') DEFAULT 'new',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    estimated_hours INT,
    actual_hours INT DEFAULT 0,
    budget DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'INR',
    start_date DATE,
    estimated_completion DATE,
    actual_completion DATE,
    project_manager INT,
    lead_developer INT,
    progress_percentage INT DEFAULT 0,
    technologies_used TEXT,
    repository_url VARCHAR(500),
    staging_url VARCHAR(500),
    production_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    notes TEXT,
    INDEX idx_project_id (project_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_start_date (start_date),
    INDEX idx_project_manager (project_manager),
    INDEX idx_lead_developer (lead_developer),
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (project_manager) REFERENCES admin(id) ON DELETE SET NULL,
    FOREIGN KEY (lead_developer) REFERENCES developer(id) ON DELETE SET NULL
);

-- ===========================================
-- 6. customerlogin - Table to check the project status
-- ===========================================
CREATE TABLE IF NOT EXISTS customerlogin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    login_token VARCHAR(255),
    token_expiry TIMESTAMP NULL,
    status ENUM('active', 'inactive', 'suspended', 'pending_verification') DEFAULT 'pending_verification',
    email_verified BOOLEAN DEFAULT FALSE,
    email_verification_token VARCHAR(255),
    email_verification_expiry TIMESTAMP NULL,
    last_login TIMESTAMP NULL,
    login_attempts INT DEFAULT 0,
    lockout_until TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_customer_id (customer_id),
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_email_verified (email_verified),
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- ===========================================
-- Additional Tables for Complete System
-- ===========================================

-- Project Activities/Updates
CREATE TABLE IF NOT EXISTS project_activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    activity_type ENUM('status_update', 'milestone', 'bug_report', 'feature_request', 'meeting', 'delivery', 'payment', 'other') DEFAULT 'status_update',
    activity_description TEXT NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_customer_visible BOOLEAN DEFAULT TRUE,
    attachments TEXT,
    INDEX idx_project_id (project_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (project_id) REFERENCES project(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES admin(id) ON DELETE SET NULL
);

-- Project Team Members
CREATE TABLE IF NOT EXISTS project_team (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    developer_id INT NOT NULL,
    role_in_project VARCHAR(100),
    assigned_date DATE,
    unassigned_date DATE NULL,
    hours_allocated INT,
    hours_worked INT DEFAULT 0,
    status ENUM('active', 'completed', 'removed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_project_id (project_id),
    INDEX idx_developer_id (developer_id),
    INDEX idx_status (status),
    FOREIGN KEY (project_id) REFERENCES project(id) ON DELETE CASCADE,
    FOREIGN KEY (developer_id) REFERENCES developer(id) ON DELETE CASCADE
);

-- Payments/Transactions
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT,
    customer_id INT NOT NULL,
    payment_type ENUM('project_payment', 'milestone_payment', 'maintenance_fee', 'additional_service', 'refund') DEFAULT 'project_payment',
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'INR',
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    payment_gateway VARCHAR(50),
    status ENUM('pending', 'completed', 'failed', 'refunded', 'cancelled') DEFAULT 'pending',
    payment_date TIMESTAMP NULL,
    due_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_project_id (project_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status),
    INDEX idx_payment_date (payment_date),
    FOREIGN KEY (project_id) REFERENCES project(id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- File Attachments/Documents
CREATE TABLE IF NOT EXISTS attachments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reference_type ENUM('project', 'customer', 'contact', 'activity') NOT NULL,
    reference_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT,
    file_type VARCHAR(100),
    uploaded_by INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reference (reference_type, reference_id),
    INDEX idx_uploaded_by (uploaded_by),
    FOREIGN KEY (uploaded_by) REFERENCES admin(id) ON DELETE SET NULL
);

-- System Settings
CREATE TABLE IF NOT EXISTS system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_description VARCHAR(255),
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    is_system BOOLEAN DEFAULT FALSE,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key),
    FOREIGN KEY (updated_by) REFERENCES admin(id) ON DELETE SET NULL
);

-- ===========================================
-- Insert Default Admin User
-- ===========================================
INSERT INTO admin (username, email, password_hash, full_name, role, department) VALUES
('admin', 'admin@layoversolutions.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'super_admin', 'Management');

-- ===========================================
-- Insert Default System Settings
-- ===========================================
INSERT INTO system_settings (setting_key, setting_value, setting_description, setting_type) VALUES
('company_name', 'Layover Solutions', 'Company name displayed throughout the system', 'string'),
('company_email', 'info@layoversolutions.com', 'Primary company email address', 'string'),
('company_phone', '+91-XXXXXXXXXX', 'Primary company phone number', 'string'),
('currency', 'INR', 'Default currency for the system', 'string'),
('timezone', 'Asia/Kolkata', 'Default timezone for the system', 'string'),
('maintenance_mode', 'false', 'Enable/disable maintenance mode', 'boolean'),
('email_notifications', 'true', 'Enable/disable email notifications', 'boolean'),
('max_file_size', '10485760', 'Maximum file upload size in bytes (10MB)', 'number'),
('session_timeout', '3600', 'Session timeout in seconds (1 hour)', 'number');

-- ===========================================
-- Create Indexes for Better Performance
-- ===========================================
CREATE INDEX idx_get_started_email_date ON get_started(email, submitted_at);
CREATE INDEX idx_customers_email_status ON customers(email, customer_status);
CREATE INDEX idx_contact_email_status ON contact(email, status);
CREATE INDEX idx_project_status_priority ON project(status, priority);
CREATE INDEX idx_customerlogin_email_status ON customerlogin(email, status);
CREATE INDEX idx_payments_status_date ON payments(status, payment_date);

-- ===========================================
-- Sample Data for Testing (Optional)
-- ===========================================
/*
-- Uncomment these inserts if you want sample data for testing

INSERT INTO customers (customer_id, full_name, email, phone, company_name, customer_type) VALUES
('CUST001', 'John Doe', 'john.doe@example.com', '+91-9876543210', 'ABC Corp', 'business');

INSERT INTO developer (developer_id, full_name, email, phone, skills, experience_years, specialization) VALUES
('DEV001', 'Jane Smith', 'jane.smith@layoversolutions.com', '+91-9876543211', 'PHP, MySQL, JavaScript, React', 5, 'Full Stack Developer');

INSERT INTO project (project_id, project_name, project_description, customer_id, project_type, status, budget) VALUES
('PROJ001', 'E-commerce Website', 'Complete e-commerce solution for ABC Corp', 1, 'Web Development', 'in_progress', 50000.00);
*/