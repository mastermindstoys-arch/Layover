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
            // Get all get_started submissions or single submission
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT * FROM get_started WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $result]);
            } else {
                $stmt = $db->query("SELECT * FROM get_started ORDER BY submitted_at DESC");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $results]);
            }
            break;

        case 'POST':
            // Create new get_started submission
            $input = json_decode(file_get_contents('php://input'), true);

            $stmt = $db->prepare("INSERT INTO get_started (full_name, email, phone, service_interest, preferred_time, preferred_language, notes, submitted_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");

            $stmt->execute([
                $input['full_name'],
                $input['email'],
                $input['phone'],
                $input['service_interest'],
                $input['preferred_time'],
                $input['preferred_language'],
                $input['notes']
            ]);

            echo json_encode(['success' => true, 'message' => 'Get started submission created successfully']);
            break;

        case 'PUT':
            // Update get_started submission
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;

            if (!$id) {
                throw new Exception('ID is required');
            }

            $stmt = $db->prepare("UPDATE get_started SET
                full_name = ?, phone = ?, email = ?, service_interest = ?,
                preferred_time = ?, preferred_language = ?, status = ?, notes = ?
                WHERE id = ?");

            $stmt->execute([
                $input['full_name'],
                $input['phone'],
                $input['email'],
                $input['service_interest'],
                $input['preferred_time'],
                $input['preferred_language'],
                $input['status'],
                $input['notes'],
                $id
            ]);

            echo json_encode(['success' => true, 'message' => 'Get started submission updated successfully']);
            break;

        case 'DELETE':
            // Delete get_started submission
            $id = $_GET['id'] ?? null;

            if (!$id) {
                throw new Exception('ID is required');
            }

            $stmt = $db->prepare("DELETE FROM get_started WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode(['success' => true, 'message' => 'Get started submission deleted successfully']);
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