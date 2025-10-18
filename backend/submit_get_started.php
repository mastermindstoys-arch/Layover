<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get form data
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $service_interest = trim($_POST['service_interest'] ?? '');
    $preferred_time = trim($_POST['preferred_time'] ?? '');
    $preferred_language = trim($_POST['preferred_language'] ?? '');
    $add_ons = trim($_POST['add_ons'] ?? '');

    // Basic validation
    if (empty($full_name) || empty($email) || empty($phone) || empty($service_interest)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        exit;
    }

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    // Phone validation (10 digits)
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid phone number']);
        exit;
    }

    // Connect to database
    require_once 'config.php';
    $db = getDB();

    // Prepare notes field (combine add_ons if provided)
    $notes = '';
    if (!empty($add_ons) && $add_ons !== 'None') {
        $notes = 'Add-ons: ' . $add_ons;
    }

    // Insert get started submission
    $stmt = $db->prepare("INSERT INTO get_started (full_name, email, phone, service_interest, preferred_time, preferred_language, notes, submitted_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");

    $result = $stmt->execute([
        $full_name,
        $email,
        $phone,
        $service_interest,
        $preferred_time ?: null,
        $preferred_language ?: 'English',
        $notes ?: null
    ]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your interest! We\'ll get back to you within 24 hours.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save your request. Please try again.']);
    }

} catch (Exception $e) {
    error_log('Get Started form submission error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
}
?>
