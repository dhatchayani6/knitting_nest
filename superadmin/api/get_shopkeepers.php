<?php
// Allow CORS & JSON response
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include '../../config/config.php';

// Fetch all shopkeepers
$query = "SELECT s.shopkeeper_bioid, s.shopkeeper_name, s.shopkeeper_bioid, s.shop_name, s.store_location
          FROM shopkeeper s
          ORDER BY s.shopkeeper_bioid DESC";

$result = $conn->query($query);

if ($result) {
    $shopkeepers = [];
    while ($row = $result->fetch_assoc()) {
        $shopkeepers[] = $row;
    }
    echo json_encode([
        "status" => "success",
        "data" => $shopkeepers
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Failed to fetch shopkeepers: " . $conn->error
    ]);
}

$conn->close();
?>