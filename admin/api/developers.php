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
            // Get all developers or single developer
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT * FROM developer WHERE developer_id = ?");
                $stmt->execute([$_GET['id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $result]);
            } else {
                $stmt = $db->query("SELECT * FROM developer ORDER BY created_at DESC");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $results]);
            }
            break;

        case 'POST':
            // Create new developer
            $input = json_decode(file_get_contents('php://input'), true);

            $stmt = $db->prepare("INSERT INTO developer (
                full_name, email, phone, skills, experience_years,
                specialization, hourly_rate, status, join_date, notes,
                salary_accumulated, salary_paid
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $input['full_name'],
                $input['email'],
                $input['phone'] ?? null,
                $input['skills'] ?? null,
                $input['experience_years'] ?? 0,
                $input['specialization'] ?? null,
                $input['hourly_rate'] ?? null,
                $input['status'] ?? 'active',
                $input['join_date'] ?? date('Y-m-d'),
                $input['notes'] ?? null,
                $input['salary_accumulated'] ?? 0,
                $input['salary_paid'] ?? 0
            ]);

            $newId = $db->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Developer created successfully', 'developer_id' => $newId]);
            break;

        case 'PUT':
            // Update developer
            $input = json_decode(file_get_contents('php://input'), true);
            $developerId = $input['developer_id'] ?? null;

            if (!$developerId) {
                throw new Exception('Developer ID is required');
            }

            $stmt = $db->prepare("UPDATE developer SET
                full_name = ?, email = ?, phone = ?, skills = ?, experience_years = ?,
                specialization = ?, hourly_rate = ?, status = ?, join_date = ?, notes = ?,
                salary_accumulated = ?, salary_paid = ?
                WHERE developer_id = ?");

            $stmt->execute([
                $input['full_name'],
                $input['email'],
                $input['phone'] ?? null,
                $input['skills'] ?? null,
                $input['experience_years'] ?? 0,
                $input['specialization'] ?? null,
                $input['hourly_rate'] ?? null,
                $input['status'] ?? 'active',
                $input['join_date'] ?? null,
                $input['notes'] ?? null,
                $input['salary_accumulated'] ?? 0,
                $input['salary_paid'] ?? 0,
                $developerId
            ]);

            echo json_encode(['success' => true, 'message' => 'Developer updated successfully']);
            break;

        case 'DELETE':
            // Delete developer
            $developerId = $_GET['id'] ?? null;

            if (!$developerId) {
                throw new Exception('Developer ID is required');
            }

            $stmt = $db->prepare("DELETE FROM developer WHERE developer_id = ?");
            $stmt->execute([$developerId]);

            echo json_encode(['success' => true, 'message' => 'Developer deleted successfully']);
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