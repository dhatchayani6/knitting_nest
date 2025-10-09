<?php
// Allow CORS for testing and JSON response
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Include database
include __DIR__ . '/../../includes/config.php'; // adjust path

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Only POST method allowed"]);
    exit();
}

// ✅ Get store ID from form-data or x-www-form-urlencoded
if (!isset($_POST['id']) || empty($_POST['id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing record ID"]);
    exit();
}

$id = intval($_POST['id']);
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid purchase_order ID"]);
    exit();
}


// Delete store
$stmt = $conn->prepare("DELETE FROM purchase_order WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "purchase_order deleted successfully"]);
        exit();
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "No purchase_order found with given ID"]);
        exit();
    }
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Deletion failed: " . $stmt->error]);
    exit();
}


$stmt->close();
$conn->close();
