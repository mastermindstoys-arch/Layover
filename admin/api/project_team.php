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
            // Get all team members or single team member
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT * FROM project_team WHERE team_id = ?");
                $stmt->execute([$_GET['id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $result]);
            } else {
                $stmt = $db->query("SELECT * FROM project_team ORDER BY assigned_date DESC");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $results]);
            }
            break;

        case 'POST':
            // Create new team member assignment
            $input = json_decode(file_get_contents('php://input'), true);

            $stmt = $db->prepare("INSERT INTO project_team (
                project_id, developer_id, role_in_project, assigned_date,
                hours_allocated, status
            ) VALUES (?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $input['project_id'],
                $input['developer_id'],
                $input['role_in_project'] ?? 'developer',
                $input['assigned_date'] ?? date('Y-m-d'),
                $input['hours_allocated'] ?? null,
                $input['status'] ?? 'active'
            ]);

            echo json_encode(['success' => true, 'message' => 'Team member assigned successfully', 'id' => $db->lastInsertId()]);
            break;

        case 'PUT':
            // Update team member
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;

            if (!$id) {
                throw new Exception('Team member ID is required');
            }

            $stmt = $db->prepare("UPDATE project_team SET
                project_id = ?, developer_id = ?, role_in_project = ?, assigned_date = ?,
                hours_allocated = ?, hours_worked = ?, status = ?
                WHERE id = ?");

            $stmt->execute([
                $input['project_id'],
                $input['developer_id'],
                $input['role_in_project'] ?? 'developer',
                $input['assigned_date'] ?? null,
                $input['hours_allocated'] ?? null,
                $input['hours_worked'] ?? 0,
                $input['status'] ?? 'active',
                $id
            ]);

            echo json_encode(['success' => true, 'message' => 'Team member updated successfully']);
            break;

        case 'DELETE':
            // Delete team member
            $id = $_GET['id'] ?? null;

            if (!$id) {
                throw new Exception('Team member ID is required');
            }

            $stmt = $db->prepare("DELETE FROM project_team WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode(['success' => true, 'message' => 'Team member removed successfully']);
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