<?php

// Allow CORS & set JSON response
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include __DIR__ . '/../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Only POST method allowed"]);
    exit();
}

// Get POST data
$store_name = $_POST['store_name'] ?? null;
$store_id = $_POST['store_id'] ?? null;
$item_name = $_POST['item_name'] ?? null;
$item_code = $_POST['item_code'] ?? null;
$item_quantity = $_POST['item_quantity'] ?? null;
$item_price = $_POST['item_price'] ?? null;
$stock_level = $_POST['stock_level'] ?? null;
$created_at = date("Y-m-d H:i:s");

// Handle file upload
$items_image = null; // default if no file
if (isset($_FILES['items_image']) && $_FILES['items_image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['items_image']['tmp_name'];
    $fileName = $_FILES['items_image']['name'];
    $fileSize = $_FILES['items_image']['size'];
    $fileType = $_FILES['items_image']['type'];

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExt, $allowedExts)) {
        $newFileName = uniqid('item_', true) . '.' . $fileExt; // unique file name
        $uploadPath = __DIR__ . '/../../uploads/' . $newFileName;

        if (!is_dir(__DIR__ . '/uploads')) {
            mkdir(__DIR__ . '/uploads', 0755, true); // create folder if not exists
        }

        if (move_uploaded_file($fileTmpPath, $uploadPath)) {
            $items_image = 'uploads/' . $newFileName; // store relative path in DB
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Image upload failed"]);
            exit();
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid image format"]);
        exit();
    }
}

// Validate required fields
$missing_fields = [];
if (!$store_name)
    $missing_fields[] = "store_name";
if (!$store_id)
    $missing_fields[] = "store_id";
if (!$item_name)
    $missing_fields[] = "item_name";
if (!$item_code)
    $missing_fields[] = "item_code";
if (!$item_quantity)
    $missing_fields[] = "item_quantity";
if (!$item_price)
    $missing_fields[] = "item_price";
if (!$stock_level)
    $missing_fields[] = "stock_level";
if (!$items_image)
    $missing_fields[] = "items_image";

if (!empty($missing_fields)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields",
        "missing_fields" => $missing_fields
    ]);
    exit();
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO items 
    (store_name, store_id, item_name, item_code, item_quantity, item_price, stock_level, items_image, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "sisssddss",
    $store_name,
    $store_id,
    $item_name,
    $item_code,
    $item_quantity,
    $item_price,
    $stock_level,
    $items_image,
    $created_at
);

// Execute
if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Item inserted successfully",
        "id" => $stmt->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Insertion failed: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>