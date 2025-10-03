<?php
include __DIR__ . '/../../includes/config.php';

// Set JSON header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');



// Query low stock items (item_quantity < stock_level)
$sql = "SELECT COUNT(*) AS low_stock_count FROM items WHERE CAST(item_quantity AS UNSIGNED) < CAST(stock_level AS UNSIGNED)";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "count" => (int) $row['low_stock_count'],
        "message" => (int) $row['low_stock_count'] > 0 ? "There are low stock items" : "All items sufficiently stocked"
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "count" => 0,
        "message" => "Failed to fetch low stock count"
    ]);
}

$conn->close();
?>