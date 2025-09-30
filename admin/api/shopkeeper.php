<?php

// Allow CORS & set JSON response
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include __DIR__ . "/../../includes/config.php";

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Only POST method allowed"]);
    exit();
}

// Get POST data with defaults
$shopkeeper_bioid   = isset($_POST['shopkeeper_bioid']) ? $_POST['shopkeeper_bioid'] : null;
$shopkeeper_name    = isset($_POST['shopkeeper_name']) ? $_POST['shopkeeper_name'] : null;
$password           = isset($_POST['password']) ? $_POST['password'] : null;
$store_location     = isset($_POST['store_location']) ? $_POST['store_location'] : null;
$shop_name          = isset($_POST['shop_name']) ? $_POST['shop_name'] : null;
$shop_id            = isset($_POST['shop_id']) ? $_POST['shop_id'] : null;
$usertype           = isset($_POST['usertype']) && !empty($_POST['usertype']) ? $_POST['usertype'] : "Shopkeeper";

// Validate required fields (id is auto-increment so we skip it)
$missing_fields = [];

if (!$shopkeeper_bioid)   $missing_fields[] = "shopkeeper_bioid";
if (!$shopkeeper_name)    $missing_fields[] = "shopkeeper_name";
if (!$password)           $missing_fields[] = "password";
if (!$store_location)     $missing_fields[] = "store_location";
if (!$shop_name)          $missing_fields[] = "shop_name";
if (!$shop_id)            $missing_fields[] = "shop_id";
if (!$usertype)           $missing_fields[] = "usertype";

if (!empty($missing_fields)) {
    http_response_code(400); // Bad Request
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields",
        "missing_fields" => $missing_fields
    ]);
    exit();
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO shopkeeper 
    (shopkeeper_bioid, shopkeeper_name, password, store_location, shop_name, shop_id, usertype) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $shopkeeper_bioid, $shopkeeper_name, $password, $store_location, $shop_name, $shop_id, $usertype);

// Execute
if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Shopkeeper inserted successfully",
        "id" => $stmt->insert_id
    ]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "status" => "error",
        "message" => "Insertion failed: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
