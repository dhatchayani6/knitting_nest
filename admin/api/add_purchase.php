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
$purchase_item = $_POST['purchase_item'] ?? null;
$date_of_purchase = $_POST['date_of_purchase'] ?? null;
$purchase_code = $_POST['purchase_code'] ?? null;
$distributor_name = $_POST['distributor_name'] ?? null;
$purchase_quantity = $_POST['purchase_quantity'] ?? null;


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
if (!$purchase_item)
    $missing_fields[] = "purchase_item";
if (!$date_of_purchase)
    $missing_fields[] = "date_of_purchase";
if (!$purchase_code)
    $missing_fields[] = "purchase_code";
if (!$distributor_name)
    $missing_fields[] = "distributor_name";
if (!$purchase_quantity)
    $missing_fields[] = "purchase_quantity";
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

// Prepare and bind db column name
$stmt = $conn->prepare("INSERT INTO purchase_order (purchase_name, purchase_code, purchase_date, distributor_name, purchase_quantity,items_image) 
    VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "ssssss",
    $purchase_item,
    $purchase_code,
    $date_of_purchase,
    $distributor_name,
    $purchase_quantity,
    $items_image //postman la intha name kudukanum

);


// Execute
if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Purchase Item inserted successfully",
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