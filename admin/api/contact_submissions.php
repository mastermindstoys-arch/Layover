<?php
session_start();
header('Content-Type: application/json');

// Disable error output to prevent HTML in JSON responses
error_reporting(0);
ini_set('display_errors', 0);

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../../backend/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

try {
    switch ($method) {
        case 'GET':
            // Get all contact submissions or single submission
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT * FROM contact_submissions WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $result]);
            } else {
                $stmt = $db->query("SELECT * FROM contact_submissions ORDER BY submitted_at DESC");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $results]);
            }
            break;

        case 'PUT':
            // Update contact submission
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;

            if (!$id) {
                throw new Exception('ID is required');
            }

            $stmt = $db->prepare("UPDATE contact_submissions SET
                name = ?, email = ?, phone = ?, preferred_time = ?,
                preferred_language = ?, message = ?, contact_type = ?,
                priority = ?, status = ?
                WHERE id = ?");

            $stmt->execute([
                $input['name'],
                $input['email'],
                $input['phone'],
                $input['preferred_time'],
                $input['preferred_language'],
                $input['message'],
                $input['contact_type'],
                $input['priority'],
                $input['status'],
                $id
            ]);

            echo json_encode(['success' => true, 'message' => 'Contact submission updated successfully']);
            break;

        case 'DELETE':
            // Delete contact submission
            $id = $_GET['id'] ?? null;

            if (!$id) {
                throw new Exception('ID is required');
            }

            $stmt = $db->prepare("DELETE FROM contact_submissions WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode(['success' => true, 'message' => 'Contact submission deleted successfully']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>