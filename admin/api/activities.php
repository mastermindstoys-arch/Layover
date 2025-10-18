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
            // Get all activities, single activity, or templates
            if (isset($_GET['templates'])) {
                // Get phase templates
                $stmt = $db->query("SELECT * FROM project_phase_templates ORDER BY phase_order, sub_phase_order");
                $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Group by phase
                $grouped = [];
                foreach ($templates as $template) {
                    $phaseName = $template['phase_name'];
                    if (!isset($grouped[$phaseName])) {
                        $grouped[$phaseName] = [
                            'phase_name' => $phaseName,
                            'phase_order' => $template['phase_order'],
                            'sub_phases' => []
                        ];
                    }
                    $grouped[$phaseName]['sub_phases'][] = [
                        'name' => $template['sub_phase_name'],
                        'order' => $template['sub_phase_order'],
                        'description' => $template['description']
                    ];
                }
                
                echo json_encode(['success' => true, 'data' => array_values($grouped)]);
            } elseif (isset($_GET['id'])) {
                $stmt = $db->prepare("
                    SELECT a.*,
                           p.project_name,
                           u.username as user_name
                    FROM project_activities a
                    LEFT JOIN project p ON a.project_id = p.id
                    LEFT JOIN users u ON a.created_by = u.id
                    WHERE a.id = ?
                ");
                $stmt->execute([$_GET['id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    echo json_encode(['success' => true, 'data' => $result]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Activity not found']);
                }
            } else {
                $stmt = $db->query("
                    SELECT a.*,
                           p.project_name,
                           u.username as user_name
                    FROM project_activities a
                    LEFT JOIN project p ON a.project_id = p.id
                    LEFT JOIN users u ON a.created_by = u.id
                    ORDER BY a.created_at DESC
                ");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $results]);
            }
            break;

        case 'POST':
            // Create new activity
            $input = json_decode(file_get_contents('php://input'), true);

            // Validate required fields
            if (empty($input['project_id']) || empty($input['phase']) || empty($input['sub_phase'])) {
                throw new Exception('Project, phase, and sub_phase are required');
            }

            $stmt = $db->prepare("INSERT INTO project_activities (project_id, phase, sub_phase, phase_order, sub_phase_order, created_by, is_customer_visible) VALUES (?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $input['project_id'],
                $input['phase'],
                $input['sub_phase'],
                $input['phase_order'] ?? null,
                $input['sub_phase_order'] ?? null,
                $input['created_by'] ?? null,
                $input['is_customer_visible'] ?? 0
            ]);

            $newId = $db->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Activity created successfully', 'activity_id' => $newId]);
            break;

        case 'PUT':
            // Update activity
            $input = json_decode(file_get_contents('php://input'), true);
            $activityId = $input['id'] ?? null;

            if (!$activityId) {
                throw new Exception('Activity ID is required');
            }

            $stmt = $db->prepare("UPDATE project_activities SET
                project_id = ?, phase = ?, sub_phase = ?, phase_order = ?, sub_phase_order = ?, created_by = ?, is_customer_visible = ?
                WHERE id = ?");

            $stmt->execute([
                $input['project_id'],
                $input['phase'],
                $input['sub_phase'],
                $input['phase_order'] ?? null,
                $input['sub_phase_order'] ?? null,
                $input['created_by'] ?? null,
                $input['is_customer_visible'] ?? 0,
                $activityId
            ]);

            echo json_encode(['success' => true, 'message' => 'Activity updated successfully']);
            break;

        case 'DELETE':
            // Delete activity
            $activityId = $_GET['id'] ?? null;

            if (!$activityId) {
                throw new Exception('Activity ID is required');
            }

            $stmt = $db->prepare("DELETE FROM project_activities WHERE id = ?");
            $stmt->execute([$activityId]);

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