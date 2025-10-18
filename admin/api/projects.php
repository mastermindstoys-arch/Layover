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
            // Get all projects or single project
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("
                    SELECT p.*,
                           c.full_name as customer_name, c.company_name as customer_company
                    FROM project p
                    LEFT JOIN customers c ON p.customer_id = c.customer_id
                    WHERE p.id = ?
                ");
                $stmt->execute([$_GET['id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                // Handle multiple developers
                if ($result && $result['developer_id']) {
                    $developerIds = json_decode($result['developer_id'], true);
                    if (is_array($developerIds) && !empty($developerIds)) {
                        $placeholders = str_repeat('?,', count($developerIds) - 1) . '?';
                        $stmt = $db->prepare("SELECT developer_id, full_name FROM developer WHERE developer_id IN ($placeholders)");
                        $stmt->execute($developerIds);
                        $developers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $result['developer_names'] = array_column($developers, 'full_name');
                        $result['developers'] = $developers;
                    } else {
                        $result['developer_names'] = [];
                        $result['developers'] = [];
                    }
                } else {
                    $result['developer_names'] = [];
                    $result['developers'] = [];
                }

                echo json_encode(['success' => true, 'data' => $result]);
            } else {
                $stmt = $db->query("
                    SELECT p.*,
                           c.full_name as customer_name, c.company_name as customer_company
                    FROM project p
                    LEFT JOIN customers c ON p.customer_id = c.customer_id
                    ORDER BY p.created_at DESC
                ");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Handle multiple developers for each project
                foreach ($results as &$result) {
                    if ($result['developer_id']) {
                        $developerIds = json_decode($result['developer_id'], true);
                        if (is_array($developerIds) && !empty($developerIds)) {
                            $placeholders = str_repeat('?,', count($developerIds) - 1) . '?';
                            $stmt = $db->prepare("SELECT developer_id, full_name FROM developer WHERE developer_id IN ($placeholders)");
                            $stmt->execute($developerIds);
                            $developers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $result['developer_names'] = array_column($developers, 'full_name');
                            $result['developers'] = $developers;
                        } else {
                            $result['developer_names'] = [];
                            $result['developers'] = [];
                        }
                    } else {
                        $result['developer_names'] = [];
                        $result['developers'] = [];
                    }
                }

                echo json_encode(['success' => true, 'data' => $results]);
            }
            break;

        case 'POST':
            // Create new project
            $input = json_decode(file_get_contents('php://input'), true);

            // Handle developer_id as array
            $developerIdJson = null;
            if (isset($input['developer_id']) && is_array($input['developer_id'])) {
                $developerIdJson = json_encode(array_map('intval', $input['developer_id']));
            } elseif (isset($input['developer_id']) && !empty($input['developer_id'])) {
                $developerIdJson = json_encode([intval($input['developer_id'])]);
            }

            $stmt = $db->prepare("INSERT INTO project (
                project_name, customer_id, developer_id, project_category, status, priority,
                budget, currency, progress_percentage, description, technologies, start_date, end_date,
                advance_payment, settlement, subscription, subscription_amount
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $input['project_name'],
                $input['customer_id'] ?? null,
                $developerIdJson,
                $input['project_category'] ?? null,
                $input['status'] ?? 'planning',
                $input['priority'] ?? 'medium',
                $input['budget'] ?? null,
                $input['currency'] ?? 'INR',
                $input['progress_percentage'] ?? 0,
                $input['description'] ?? null,
                $input['technologies'] ?? null,
                $input['start_date'] ?? null,
                $input['end_date'] ?? null,
                $input['advance_payment'] ?? 0,
                $input['settlement'] ?? 0,
                $input['subscription'] ?? 0,
                $input['subscription_amount'] ?? 0
            ]);

            $newId = $db->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Project created successfully', 'project_id' => $newId]);
            break;        case 'PUT':
            // Update project
            $input = json_decode(file_get_contents('php://input'), true);
            $projectId = $input['id'] ?? null;

            if (!$projectId) {
                throw new Exception('Project ID is required');
            }

            // Handle developer_id as array
            $developerIdJson = null;
            if (isset($input['developer_id']) && is_array($input['developer_id'])) {
                $developerIdJson = json_encode(array_map('intval', $input['developer_id']));
            } elseif (isset($input['developer_id']) && !empty($input['developer_id'])) {
                $developerIdJson = json_encode([intval($input['developer_id'])]);
            }

            $stmt = $db->prepare("UPDATE project SET
                project_name = ?, customer_id = ?, developer_id = ?, project_category = ?,
                status = ?, priority = ?, budget = ?, currency = ?, progress_percentage = ?,
                description = ?, technologies = ?, start_date = ?, end_date = ?,
                advance_payment = ?, settlement = ?, subscription = ?, subscription_amount = ?
                WHERE id = ?");

            $stmt->execute([
                $input['project_name'],
                $input['customer_id'] ?? null,
                $developerIdJson,
                $input['project_category'] ?? null,
                $input['status'] ?? 'planning',
                $input['priority'] ?? 'medium',
                $input['budget'] ?? null,
                $input['currency'] ?? 'INR',
                $input['progress_percentage'] ?? 0,
                $input['description'] ?? null,
                $input['technologies'] ?? null,
                $input['start_date'] ?? null,
                $input['end_date'] ?? null,
                $input['advance_payment'] ?? 0,
                $input['settlement'] ?? 0,
                $input['subscription'] ?? 0,
                $input['subscription_amount'] ?? 0,
                $projectId
            ]);

            echo json_encode(['success' => true, 'message' => 'Project updated successfully']);
            break;

        case 'DELETE':
            // Delete project
            $projectId = $_GET['id'] ?? null;

            if (!$projectId) {
                throw new Exception('Project ID is required');
            }

            $stmt = $db->prepare("DELETE FROM project WHERE id = ?");
            $stmt->execute([$projectId]);

            echo json_encode(['success' => true, 'message' => 'Project deleted successfully']);
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