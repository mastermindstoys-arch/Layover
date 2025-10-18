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
            // Get all admins or single admin
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT admin_id, username, email, full_name, role, status, created_at, last_login FROM admin WHERE admin_id = ?");
                $stmt->execute([$_GET['id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $result]);
            } else {
                $stmt = $db->query("SELECT admin_id, username, email, full_name, role, status, created_at, last_login FROM admin ORDER BY created_at DESC");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $results]);
            }
            break;

        case 'PUT':
            // Update admin
            $input = json_decode(file_get_contents('php://input'), true);
            $adminId = $input['admin_id'] ?? null;

            if (!$adminId) {
                throw new Exception('Admin ID is required');
            }

            // Build update query dynamically based on provided fields
            $updateFields = [];
            $params = [];

            if (isset($input['full_name'])) {
                $updateFields[] = "full_name = ?";
                $params[] = $input['full_name'];
            }

            if (isset($input['email'])) {
                $updateFields[] = "email = ?";
                $params[] = $input['email'];
            }

            if (isset($input['role'])) {
                $updateFields[] = "role = ?";
                $params[] = $input['role'];
            }

            if (isset($input['status'])) {
                $updateFields[] = "status = ?";
                $params[] = $input['status'];
            }

            if (isset($input['password']) && !empty($input['password'])) {
                $updateFields[] = "password_hash = ?";
                $params[] = password_hash($input['password'], PASSWORD_DEFAULT);
            }

            if (empty($updateFields)) {
                throw new Exception('No fields to update');
            }

            $params[] = $adminId;
            $stmt = $db->prepare("UPDATE admin SET " . implode(', ', $updateFields) . " WHERE admin_id = ?");
            $stmt->execute($params);

            echo json_encode(['success' => true, 'message' => 'Admin updated successfully']);
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