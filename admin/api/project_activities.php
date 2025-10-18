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
            // Get all activities or single activity
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT * FROM project_activities WHERE activity_id = ?");
                $stmt->execute([$_GET['id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $result]);
            } else {
                $stmt = $db->query("SELECT * FROM project_activities ORDER BY created_at DESC");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $results]);
            }
            break;

        case 'POST':
            // Create new activity
            $input = json_decode(file_get_contents('php://input'), true);

            $stmt = $db->prepare("INSERT INTO project_activities (
                project_id, activity_type, activity_description, created_by,
                is_customer_visible, attachments
            ) VALUES (?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $input['project_id'],
                $input['activity_type'],
                $input['activity_description'] ?? null,
                $input['created_by'] ?? $_SESSION['admin_id'] ?? 1,
                $input['is_customer_visible'] ?? 1,
                $input['attachments'] ?? null
            ]);

            echo json_encode(['success' => true, 'message' => 'Activity created successfully', 'id' => $db->lastInsertId()]);
            break;

        case 'PUT':
            // Update activity
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;

            if (!$id) {
                throw new Exception('Activity ID is required');
            }

            $stmt = $db->prepare("UPDATE project_activities SET
                project_id = ?, activity_type = ?, activity_description = ?,
                is_customer_visible = ?, attachments = ?
                WHERE id = ?");

            $stmt->execute([
                $input['project_id'],
                $input['activity_type'],
                $input['activity_description'] ?? null,
                $input['is_customer_visible'] ?? 1,
                $input['attachments'] ?? null,
                $id
            ]);

            echo json_encode(['success' => true, 'message' => 'Activity updated successfully']);
            break;

        case 'DELETE':
            // Delete activity
            $id = $_GET['id'] ?? null;

            if (!$id) {
                throw new Exception('Activity ID is required');
            }

            $stmt = $db->prepare("DELETE FROM project_activities WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode(['success' => true, 'message' => 'Activity deleted successfully']);
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