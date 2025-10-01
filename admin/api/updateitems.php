<?php
include('../../includes/config.php'); 

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

// Validate inputs
$id            = intval($_POST['id']);
$store_name    = $_POST['store_name'] ?? '';
$item_name     = $_POST['item_name'] ?? '';
$item_code     = $_POST['item_code'] ?? '';
$stock_level   = $_POST['stock_level'] ?? 0;
$item_quantity = $_POST['item_quantity'] ?? 0;
$item_price    = $_POST['item_price'] ?? 0;

if ($id <= 0 || empty($store_name) || empty($item_name)) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$stmt = $conn->prepare("UPDATE items 
    SET store_name=?, item_name=?, item_code=?, stock_level=?, item_quantity=?, item_price=? 
    WHERE id=?");
$stmt->bind_param("sssiiii", $store_name, $item_name, $item_code, $stock_level, $item_quantity, $item_price, $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Item updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Update failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
