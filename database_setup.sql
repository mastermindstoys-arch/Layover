-- Database setup for Layover Solutions website
-- Run this SQL to create the necessary tables

CREATE DATABASE IF NOT EXISTS layover_solutions;
USE layover_solutions;

-- Get Started submissions table
CREATE TABLE IF NOT EXISTS get_started (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    service_interest VARCHAR(100),
    preferred_time VARCHAR(50),
    preferred_language VARCHAR(50),
    status ENUM('new', 'contacted', 'qualified', 'converted', 'lost') DEFAULT 'new',
    notes TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact submissions table
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255),
    message TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    company VARCHAR(255),
    customer_status ENUM('active', 'inactive') DEFAULT 'active',
    total_revenue DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Developers table
CREATE TABLE IF NOT EXISTS developer (
    developer_id INT AUTO_INCREMENT PRIMARY KEY,
    developer_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    skills TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Projects table
CREATE TABLE IF NOT EXISTS project (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(255) NOT NULL,
    customer_id INT,
    developer_id INT,
    status ENUM('planning', 'in_progress', 'testing', 'completed', 'cancelled') DEFAULT 'planning',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    budget DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'USD',
    progress_percentage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (developer_id) REFERENCES developer(developer_id)
);

-- Insert sample data for testing
INSERT INTO get_started (full_name, email, phone, service_interest, status, submitted_at) VALUES
('John Doe', 'john@example.com', '+1-555-0123', 'Basic Plan', 'new', NOW()),
('Jane Smith', 'jane@example.com', '+1-555-0124', 'Premium Plan', 'contacted', NOW() - INTERVAL 1 DAY),
('Bob Johnson', 'bob@example.com', '+1-555-0125', 'Custom Solution', 'qualified', NOW() - INTERVAL 2 DAY);

INSERT INTO contact_submissions (name, email, subject, message) VALUES
('Alice Brown', 'alice@example.com', 'Question about services', 'I have a question about your premium plan.'),
('Charlie Wilson', 'charlie@example.com', 'Partnership inquiry', 'I''d like to discuss a partnership opportunity.');

INSERT INTO customers (customer_name, email, phone, company, total_revenue) VALUES
('Tech Corp', 'contact@techcorp.com', '+1-555-0100', 'Tech Corp Inc.', 50000.00),
('StartupXYZ', 'hello@startupxyz.com', '+1-555-0101', 'StartupXYZ LLC', 25000.00);

INSERT INTO developer (developer_name, email, phone, skills) VALUES
('Mike Developer', 'mike@dev.com', '+1-555-0200', 'PHP, JavaScript, React'),
('Sarah Coder', 'sarah@dev.com', '+1-555-0201', 'Python, Django, Vue.js');

INSERT INTO project (project_name, customer_id, developer_id, status, priority, budget, progress_percentage) VALUES
('E-commerce Website', 1, 1, 'in_progress', 'high', 15000.00, 75),
('Mobile App', 2, 2, 'testing', 'medium', 20000.00, 90);