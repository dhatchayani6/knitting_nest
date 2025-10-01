<?php
include('../../includes/config.php'); 

header("Content-Type: application/json");

// Accept only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request, only POST allowed"]);
    exit;
}

// Accept both JSON and form-data
if (empty($_POST)) {
    $input = json_decode(file_get_contents("php://input"), true);
} else {
    $input = $_POST;
}

// Sanitize inputs
$id            = intval($input['id'] ?? 0);
$store_name    = trim($input['store_name'] ?? '');
$item_name     = trim($input['item_name'] ?? '');
$item_code     = trim($input['item_code'] ?? '');
$stock_level   = $input['stock_level'] ?? null;
$item_quantity = $input['item_quantity'] ?? null;
$item_price    = $input['item_price'] ?? null;

// Validation step by step
if ($id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid or missing ID"]);
    exit;
}

if ($store_name === '') {
    echo json_encode(["status" => "error", "message" => "Store name is required"]);
    exit;
}

if ($item_name === '') {
    echo json_encode(["status" => "error", "message" => "Item name is required"]);
    exit;
}

if ($item_code === '') {
    echo json_encode(["status" => "error", "message" => "Item code is required"]);
    exit;
}

if (!is_numeric($stock_level)) {
    echo json_encode(["status" => "error", "message" => "Stock level must be a number"]);
    exit;
}

if (!is_numeric($item_quantity)) {
    echo json_encode(["status" => "error", "message" => "Item quantity must be a number"]);
    exit;
}

if (!is_numeric($item_price)) {
    echo json_encode(["status" => "error", "message" => "Item price must be a number"]);
    exit;
}

// Prepare update query
$stmt = $conn->prepare("UPDATE items 
    SET store_name=?, item_name=?, item_code=?, stock_level=?, item_quantity=?, item_price=? 
    WHERE id=?");

$stmt->bind_param(
    "sssiiii", 
    $store_name, 
    $item_name, 
    $item_code, 
    $stock_level, 
    $item_quantity, 
    $item_price, 
    $id
);
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Item updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Update failed: " . $stmt->error]);
}


$stmt->close();
$conn->close();
