<?php
header('Content-Type: application/json');
include __DIR__ .'/../../includes/config.php'; // Include database connection

// ✅ Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

// ✅ Check ID
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(["status" => "error", "message" => "Item ID required"]);
    exit();
}

$id = intval($_POST['id']);

$stmt = $conn->prepare("SELECT id, store_name, item_name, item_code, stock_level, item_quanitity, item_price 
                        FROM items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $item = $result->fetch_assoc();
    echo json_encode([
        "status" => "success",
        "data" => $item
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Item not found"
    ]);
}

$stmt->close();
$conn->close();
