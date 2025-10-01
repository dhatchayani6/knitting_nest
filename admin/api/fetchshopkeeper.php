<?php
header("Content-Type: application/json"); //we recieve data from json formate
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include __DIR__ .'/../../includes/config.php'; // Include database connection

// Fetch all stores
$sql = "SELECT shopkeeper_id, shop_name, store_location, shopkeeper_name,shopkeeper_bioid,password FROM shopkeeper ORDER BY shopkeeper_id ASC";
$result = $conn->query($sql);


$shopkeeper = [];
if ($result && $result->num_rows > 0) {
    
    $sno = 1;
    while ($row = $result->fetch_assoc()) {
        $shopkeeper[] = [
            "sno" => $sno++,//add serial number
             "shopkeeper_id" => $row['shopkeeper_id'],
            "shop_name" => $row['shop_name'],
            "store_location" => $row['store_location'],
            "shopkeeper_name" => $row['shopkeeper_name'],
            "shopkeeper_bioid" => $row['shopkeeper_bioid'],
            "password" => $row['password'],
        ];
    }

    // ✅ Success response with HTTP 200
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "shopkeeper fetched successfully",
        "data" => $shopkeeper
    ]);
} else {
    // ⚠️ No data found — HTTP 404
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "No records found"
    ]);
}

$conn->close();
?>
