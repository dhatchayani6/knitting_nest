<?php
header("Content-Type: application/json"); // we receive data in JSON format
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include __DIR__ . '/../../includes/config.php'; // Include database connection

// Fetch all stores
$sql = "SELECT id, store_name, item_name, item_code, item_quantity, item_price, stock_level, created_at FROM items ORDER BY id ASC";
$result = $conn->query($sql);

$items = [];
if ($result && $result->num_rows > 0) {

    $sno = 1;
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            "sno" => $sno++, // add serial number
            "id" => $row['id'],
            "store_name" => $row['store_name'],
            "item_name" => $row['item_name'],
            "item_code" => $row['item_code'],
            "item_quantity" => $row['item_quantity'],
            "item_price" => $row['item_price'],
            "stock_level" => $row['stock_level'],
            "created_at" => date("d-m-Y", strtotime($row['created_at'])), // format date
        ];
    }

    // ✅ Success response with HTTP 200
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "items fetched successfully",
        "data" => $items
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