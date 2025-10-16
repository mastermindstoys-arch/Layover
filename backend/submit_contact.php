<?php
/**
 * Contact Form Handler
 * Handles submissions from the contact form
 * Created: October 16, 2025
 */

require_once 'config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Method not allowed', null, 405);
}

// Get and sanitize form data
$name = sanitizeInput($_POST['name'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$subject = sanitizeInput($_POST['subject'] ?? 'Contact Form Inquiry');
$preferred_time = sanitizeInput($_POST['preferred_time'] ?? '');
$preferred_language = sanitizeInput($_POST['preferred_language'] ?? '');
$message = sanitizeInput($_POST['message'] ?? '');

// Validate required fields
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!validateEmail($email)) {
    $errors[] = 'Please enter a valid email address';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

if (!empty($errors)) {
    sendJsonResponse(false, 'Validation failed', ['errors' => $errors], 400);
}

try {
    $db = getDB();

    // Determine contact type based on message content
    $contact_type = 'general';
    $priority = 'medium';

    if (stripos($message, 'support') !== false || stripos($message, 'help') !== false || stripos($message, 'problem') !== false) {
        $contact_type = 'support';
    } elseif (stripos($message, 'quote') !== false || stripos($message, 'price') !== false || stripos($message, 'cost') !== false) {
        $contact_type = 'sales';
        $priority = 'high';
    } elseif (stripos($message, 'partner') !== false || stripos($message, 'collaboration') !== false) {
        $contact_type = 'partnership';
    }

    // Check for urgent keywords
    if (stripos($message, 'urgent') !== false || stripos($message, 'asap') !== false || stripos($message, 'emergency') !== false) {
        $priority = 'urgent';
    }

    // Insert the contact form data
    $stmt = $db->prepare("
        INSERT INTO contact_submissions (
            name, email, phone, preferred_time, preferred_language, message,
            contact_type, priority, ip_address, user_agent
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $result = $stmt->execute([
        $name,
        $email,
        $phone ?: null,
        $preferred_time ?: null,
        $preferred_language ?: null,
        $message,
        $contact_type,
        $priority,
        getClientIP(),
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);

    if ($result) {
        $contact_id = $db->lastInsertId();

        // Log the activity
        logActivity('contact_form_submitted', "New contact form submission from {$name} ({$email})", null);

        // You can add email notification here if needed
        // sendContactNotificationEmail($email, $name, $subject, $message);

        sendJsonResponse(true, 'Thank you for contacting us! We will respond to your message within 24 hours.', [
            'contact_id' => $contact_id,
            'submitted_at' => date('Y-m-d H:i:s'),
            'priority' => $priority
        ]);
    } else {
        sendJsonResponse(false, 'Failed to send your message. Please try again.', null, 500);
    }

} catch (PDOException $e) {
    error_log("Database error in contact handler: " . $e->getMessage());
    sendJsonResponse(false, 'A database error occurred. Please try again later.', null, 500);
} catch (Exception $e) {
    error_log("General error in contact handler: " . $e->getMessage());
    sendJsonResponse(false, 'An unexpected error occurred. Please try again later.', null, 500);
}
?>