<?php
include('../../config/config.php');
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Collect form inputs safely
$store_id = $_POST['store_id'] ?? '';
$store_name = $_POST['store_name'] ?? '';
$item_name = $_POST['item_name'] ?? '';
$sub_category = $_POST['sub_category'] ?? '';
$item_code = $_POST['item_code'] ?? '';
$available_quantity = $_POST['available_quantity'] ?? '';
$stock_level = $_POST['stock_level'] ?? '';
$item_price = $_POST['item_price'] ?? '';
$vendor_name = $_POST['vendor_name'] ?? '';

// Validate required fields
if (empty($store_id) || empty($store_name) || empty($item_name) || empty($item_code)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields']);
    exit;
}

// Handle file upload
$upload_dir = "../../uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$file_path = "";
if (!empty($_FILES['items_image']['name'])) {
    $file_name = time() . "_" . basename($_FILES["items_image"]["name"]);
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES["items_image"]["tmp_name"], $target_file)) {
        $file_path = "uploads/" . $file_name; // relative path to store in DB
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Image upload failed']);
        exit;
    }
}

// ✅ Correct SQL — matches parameter count and types
$sql = "INSERT INTO items (
            store_id, store_name, item_name, sub_category, item_code,
            item_quantity, stock_level, item_price, vendor_name, items_image, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

// ✅ Correct parameter binding (types must match values)
// store_id (i), store_name (s), item_name (s), sub_category (s),
// item_code (s), item_quantity (i), stock_level (i),
// item_price (d), vendor_name (s), items_image (s)
// Correct types:
// store_id (i), store_name (s), item_name (s), sub_category (s),
// item_code (s), item_quantity (i), stock_level (i),
// item_price (d), vendor_name (s), items_image (s)
$stmt->bind_param(
    "issssiiiss",
    $store_id,
    $store_name,
    $item_name,
    $sub_category,
    $item_code,
    $available_quantity,
    $stock_level,
    $item_price,
    $vendor_name,
    $file_path
);


// Execute and check result
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Item added successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
}

// Cleanup
$stmt->close();
$conn->close();
?>