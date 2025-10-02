<?php
include __DIR__ . '/../../includes/config.php';

// Set JSON header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Check if user is logged in
if (!isset($_SESSION['bio_id'])) {
    echo json_encode([
        "success" => false,
        "count" => 0,
        "notifications" => [],
        "message" => "Please log in to view notifications"
    ]);
    exit;
}

$bioid = $_SESSION['bio_id'];


// Get the shop_id for this user
$shopQuery = $conn->prepare("SELECT stores_id FROM shopkeeper WHERE shopkeeper_bioid = ?");
$shopQuery->bind_param("i", $bioid);
$shopQuery->execute();
$shopResult = $shopQuery->get_result();

if ($shopResult->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "count" => 0,
        "notifications" => [],
        "message" => "Shop not found for this user"
    ]);
    exit;
}

$shopRow = $shopResult->fetch_assoc();
$shop_id = $shopRow['shop_id'];

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