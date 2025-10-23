<?php

// Allow CORS & set JSON response
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include '../../config/config.php';

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Only POST method allowed"]);
    exit();
}

// Get POST data with defaults
$shopkeeper_bioid = $_POST['shopkeeper_bioid'] ?? null;
$shopkeeper_name = $_POST['shopkeeper_name'] ?? null;
$password = $_POST['password'] ?? null;
$store_location = $_POST['store_location'] ?? null;
$shop_name = $_POST['shop_name'] ?? null;
$shop_id = $_POST['shop_id'] ?? null;
$usertype = $_POST['usertype'] ?? "shopkeeper";

// Validate required fields
$missing_fields = [];
if (!$shopkeeper_bioid)
    $missing_fields[] = "shopkeeper_bioid";
if (!$shopkeeper_name)
    $missing_fields[] = "shopkeeper_name";
if (!$password)
    $missing_fields[] = "password";
if (!$store_location)
    $missing_fields[] = "store_location";
if (!$shop_name)
    $missing_fields[] = "shop_name";
if (!$shop_id)
    $missing_fields[] = "shop_id";

if (!empty($missing_fields)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields",
        "missing_fields" => $missing_fields
    ]);
    exit();
}

// ✅ Insert into shopkeeper table
$stmt = $conn->prepare("INSERT INTO shopkeeper 
    (shopkeeper_bioid, shopkeeper_name, password, store_location, shop_name, shop_id, usertype) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $shopkeeper_bioid, $shopkeeper_name, $password, $store_location, $shop_name, $shop_id, $usertype);

if ($stmt->execute()) {
    $new_id = $stmt->insert_id;

    // ✅ Also insert into login table
    $stmt2 = $conn->prepare("INSERT INTO login (bio_id, password, usertype) VALUES (?, ?, ?)");
    $stmt2->bind_param("sss", $shopkeeper_bioid, $password, $usertype);

    if ($stmt2->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Shopkeeper inserted successfully & login created",
            "id" => $new_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Shopkeeper saved but login insert failed: " . $stmt2->error
        ]);
    }
    $stmt2->close();

} else {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Shopkeeper insert failed: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>