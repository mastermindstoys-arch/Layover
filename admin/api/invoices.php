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
            // Get all invoices or single invoice
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("
                    SELECT i.*,
                           p.project_name,
                           c.full_name as customer_name, c.company_name as customer_company
                    FROM invoices i
                    LEFT JOIN project p ON i.project_id = p.id
                    LEFT JOIN customers c ON i.customer_id = c.customer_id
                    WHERE i.id = ?
                ");
                $stmt->execute([$_GET['id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    echo json_encode(['success' => true, 'data' => $result]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Invoice not found']);
                }
            } else {
                $stmt = $db->query("
                    SELECT i.*,
                           p.project_name,
                           c.full_name as customer_name, c.company_name as customer_company
                    FROM invoices i
                    LEFT JOIN project p ON i.project_id = p.id
                    LEFT JOIN customers c ON i.customer_id = c.customer_id
                    ORDER BY i.created_at DESC
                ");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $results]);
            }
            break;

        case 'POST':
            // Create new invoice
            $input = json_decode(file_get_contents('php://input'), true);

            // Validate required fields
            if (empty($input['project_id']) || empty($input['customer_id']) || empty($input['issue_date']) || empty($input['due_date'])) {
                throw new Exception('Project, customer, issue date, and due date are required');
            }

            $stmt = $db->prepare("INSERT INTO invoices (
                project_id, customer_id, invoice_number, issue_date, due_date, status,
                subtotal, tax_rate, total_amount, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $input['project_id'],
                $input['customer_id'],
                $input['invoice_number'] ?? null,
                $input['issue_date'],
                $input['due_date'],
                $input['status'] ?? 'draft',
                $input['subtotal'] ?? 0,
                $input['tax_rate'] ?? 0,
                $input['total_amount'] ?? 0,
                $input['notes'] ?? null
            ]);

            $newId = $db->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Invoice created successfully', 'invoice_id' => $newId]);
            break;

        case 'PUT':
            // Update invoice
            $input = json_decode(file_get_contents('php://input'), true);
            $invoiceId = $input['id'] ?? null;

            if (!$invoiceId) {
                throw new Exception('Invoice ID is required');
            }

            $stmt = $db->prepare("UPDATE invoices SET
                project_id = ?, customer_id = ?, invoice_number = ?, issue_date = ?,
                due_date = ?, status = ?, subtotal = ?, tax_rate = ?, total_amount = ?,
                notes = ?
                WHERE id = ?");

            $stmt->execute([
                $input['project_id'],
                $input['customer_id'],
                $input['invoice_number'] ?? null,
                $input['issue_date'],
                $input['due_date'],
                $input['status'] ?? 'draft',
                $input['subtotal'] ?? 0,
                $input['tax_rate'] ?? 0,
                $input['total_amount'] ?? 0,
                $input['notes'] ?? null,
                $invoiceId
            ]);

            echo json_encode(['success' => true, 'message' => 'Invoice updated successfully']);
            break;

        case 'DELETE':
            // Delete invoice
            $invoiceId = $_GET['id'] ?? null;

            if (!$invoiceId) {
                throw new Exception('Invoice ID is required');
            }

            $stmt = $db->prepare("DELETE FROM invoices WHERE id = ?");
            $stmt->execute([$invoiceId]);

            echo json_encode(['success' => true, 'message' => 'Invoice deleted successfully']);
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