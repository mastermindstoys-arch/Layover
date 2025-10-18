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
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $preferred_time = trim($_POST['preferred_time'] ?? '');
    $preferred_language = trim($_POST['preferred_language'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Basic validation
    if (empty($name) || empty($email) || empty($phone) || empty($preferred_time) || empty($preferred_language) || empty($message)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
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

    // Insert contact submission
    $stmt = $db->prepare("INSERT INTO contact_submissions (name, email, phone, preferred_time, preferred_language, message, submitted_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");

    $result = $stmt->execute([
        $name,
        $email,
        $phone,
        $preferred_time,
        $preferred_language,
        $message
    ]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your message! We\'ll get back to you soon.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save your message. Please try again.']);
    }

} catch (Exception $e) {
    error_log('Contact form submission error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
}
?>
