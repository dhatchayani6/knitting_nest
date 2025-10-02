<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include __DIR__ . '/../../includes/config.php'; // Database connection

// Fetch items where quantity is less than stock level
$sql = "SELECT id, item_name, item_quantity, stock_level, store_name 
        FROM items
        WHERE CAST(item_quantity AS UNSIGNED) < CAST(stock_level AS UNSIGNED)
        ORDER BY id ASC";

$result = $conn->query($sql);

$lowStockItems = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lowStockItems[] = [
            "id" => $row['id'],
            "item_name" => $row['item_name'],
            "item_quantity" => (int) $row['item_quantity'],
            "stock_level" => (int) $row['stock_level'],
            "shop_name" => $row['store_name'],
            "message" => "Item '{$row['item_name']}' is low in stock ({$row['item_quantity']}/{$row['stock_level']})"
        ];
    }

    http_response_code(200);
    echo json_encode($lowStockItems);
} else {
    // No low stock items
    http_response_code(200);
    echo json_encode([]); // Return empty array so JS shows "All items sufficiently stocked"
}

$conn->close();
?>