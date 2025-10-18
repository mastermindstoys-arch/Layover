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

if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    switch ($method) {
        case 'GET':
            // Get all users or single user
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT id, username, email, full_name, role, status, created_at FROM users WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    echo json_encode(['success' => true, 'data' => $result]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'User not found']);
                }
            } else {
                $stmt = $db->query("SELECT id, username, email, full_name, role, status, created_at FROM users ORDER BY created_at DESC");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $results]);
            }
            break;

        case 'POST':
            // Create new user
            $input = json_decode(file_get_contents('php://input'), true);

            // Validate required fields
            if (empty($input['username']) || empty($input['email']) || empty($input['password'])) {
                throw new Exception('Username, email, and password are required');
            }

            // Check if username or email already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$input['username'], $input['email']]);
            if ($stmt->fetch()) {
                throw new Exception('Username or email already exists');
            }

            $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name, role, status) VALUES (?, ?, ?, ?, ?, ?)");
            $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);

            $stmt->execute([
                $input['username'],
                $input['email'],
                $hashedPassword,
                $input['full_name'] ?? null,
                $input['role'] ?? 'client',
                $input['status'] ?? 'active'
            ]);

            $newId = $db->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'User created successfully', 'user_id' => $newId]);
            break;

        case 'PUT':
            // Update user
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $input['id'] ?? null;

            if (!$userId) {
                throw new Exception('User ID is required');
            }

            // Check if username or email conflicts with other users
            $stmt = $db->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->execute([$input['username'], $input['email'], $userId]);
            if ($stmt->fetch()) {
                throw new Exception('Username or email already exists');
            }

            $updateFields = [];
            $params = [];

            if (isset($input['username'])) {
                $updateFields[] = "username = ?";
                $params[] = $input['username'];
            }
            if (isset($input['email'])) {
                $updateFields[] = "email = ?";
                $params[] = $input['email'];
            }
            if (isset($input['password']) && !empty($input['password'])) {
                $updateFields[] = "password = ?";
                $params[] = password_hash($input['password'], PASSWORD_DEFAULT);
            }
            if (isset($input['full_name'])) {
                $updateFields[] = "full_name = ?";
                $params[] = $input['full_name'];
            }
            if (isset($input['role'])) {
                $updateFields[] = "role = ?";
                $params[] = $input['role'];
            }
            if (isset($input['status'])) {
                $updateFields[] = "status = ?";
                $params[] = $input['status'];
            }

            $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
            $params[] = $userId;

            $stmt = $db->prepare("UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?");
            $stmt->execute($params);

            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            break;

        case 'DELETE':
            // Delete user
            $userId = $_GET['id'] ?? null;

            if (!$userId) {
                throw new Exception('User ID is required');
            }

            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);

            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
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