<?php
/**
 * Get Started Form Handler
 * Handles submissions from the Get Started modal
 * Created: October 16, 2025
 */

require_once 'config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Method not allowed', null, 405);
}

// Get and sanitize form data
$full_name = sanitizeInput($_POST['full_name'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$service_interest = sanitizeInput($_POST['service_interest'] ?? '');
$preferred_time = sanitizeInput($_POST['preferred_time'] ?? '');
$preferred_language = sanitizeInput($_POST['preferred_language'] ?? '');

// Validate required fields
$errors = [];

if (empty($full_name)) {
    $errors[] = 'Full name is required';
}

if (empty($phone)) {
    $errors[] = 'Phone number is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!validateEmail($email)) {
    $errors[] = 'Please enter a valid email address';
}

if (empty($service_interest)) {
    $errors[] = 'Service interest is required';
}

if (!empty($errors)) {
    sendJsonResponse(false, 'Validation failed', ['errors' => $errors], 400);
}

try {
    $db = getDB();

    // Check if email already exists (optional - you can remove this if you want to allow multiple submissions)
    $stmt = $db->prepare("SELECT id FROM get_started WHERE email = ? AND submitted_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        sendJsonResponse(false, 'You have already submitted a request in the last 24 hours. We will contact you soon!', null, 429);
    }

    // Insert the form data
    $stmt = $db->prepare("
        INSERT INTO get_started (
            full_name, phone, email, service_interest,
            preferred_time, preferred_language, ip_address, user_agent
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $result = $stmt->execute([
        $full_name,
        $phone,
        $email,
        $service_interest,
        $preferred_time ?: null,
        $preferred_language ?: null,
        getClientIP(),
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);

    if ($result) {
        $submission_id = $db->lastInsertId();

        // Log the activity
        logActivity('get_started_form_submitted', "New get started form submission from {$full_name} ({$email})", null);

        // You can add email notification here if needed
        // sendNotificationEmail($email, $full_name, $service_interest);

        sendJsonResponse(true, 'Thank you for your interest! We will contact you within 24 hours.', [
            'submission_id' => $submission_id,
            'submitted_at' => date('Y-m-d H:i:s')
        ]);
    } else {
        sendJsonResponse(false, 'Failed to save your request. Please try again.', null, 500);
    }

} catch (PDOException $e) {
    error_log("Database error in get_started handler: " . $e->getMessage());
    sendJsonResponse(false, 'A database error occurred. Please try again later.', null, 500);
} catch (Exception $e) {
    error_log("General error in get_started handler: " . $e->getMessage());
    sendJsonResponse(false, 'An unexpected error occurred. Please try again later.', null, 500);
}
?>