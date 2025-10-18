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
            // Get all customers or single customer
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT * FROM customers WHERE customer_id = ?");
                $stmt->execute([$_GET['id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $result]);
            } else {
                $stmt = $db->query("SELECT * FROM customers ORDER BY created_at DESC");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $results]);
            }
            break;

        case 'POST':
            // Create new customer
            $input = json_decode(file_get_contents('php://input'), true);

            $stmt = $db->prepare("INSERT INTO customers (
                full_name, email, phone, company_name, designation,
                address, city, state, country, zip_code, gst_number, pan_number,
                customer_type, customer_status, notes, total_revenue
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $input['full_name'],
                $input['email'],
                $input['phone'] ?? null,
                $input['company_name'] ?? null,
                $input['designation'] ?? null,
                $input['address'] ?? null,
                $input['city'] ?? null,
                $input['state'] ?? null,
                $input['country'] ?? null,
                $input['zip_code'] ?? null,
                $input['gst_number'] ?? null,
                $input['pan_number'] ?? null,
                $input['customer_type'] ?? 'individual',
                $input['customer_status'] ?? 'active',
                $input['notes'] ?? null,
                $input['total_revenue'] ?? 0
            ]);

            $newId = $db->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Customer created successfully', 'customer_id' => $newId]);
            break;

        case 'PUT':
            // Update customer
            $input = json_decode(file_get_contents('php://input'), true);
            $customerId = $input['customer_id'] ?? null;

            if (!$customerId) {
                throw new Exception('Customer ID is required');
            }

            $stmt = $db->prepare("UPDATE customers SET
                full_name = ?, email = ?, phone = ?, company_name = ?, designation = ?,
                address = ?, city = ?, state = ?, country = ?, zip_code = ?,
                gst_number = ?, pan_number = ?, customer_type = ?, customer_status = ?, notes = ?, total_revenue = ?
                WHERE customer_id = ?");

            $stmt->execute([
                $input['full_name'],
                $input['email'],
                $input['phone'] ?? null,
                $input['company_name'] ?? null,
                $input['designation'] ?? null,
                $input['address'] ?? null,
                $input['city'] ?? null,
                $input['state'] ?? null,
                $input['country'] ?? null,
                $input['zip_code'] ?? null,
                $input['gst_number'] ?? null,
                $input['pan_number'] ?? null,
                $input['customer_type'] ?? 'individual',
                $input['customer_status'] ?? 'active',
                $input['notes'] ?? null,
                $input['total_revenue'] ?? 0,
                $customerId
            ]);

            echo json_encode(['success' => true, 'message' => 'Customer updated successfully']);
            break;

        case 'DELETE':
            // Delete customer
            $customerId = $_GET['id'] ?? null;

            if (!$customerId) {
                throw new Exception('Customer ID is required');
            }

            $stmt = $db->prepare("DELETE FROM customers WHERE customer_id = ?");
            $stmt->execute([$customerId]);

            echo json_encode(['success' => true, 'message' => 'Customer deleted successfully']);
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