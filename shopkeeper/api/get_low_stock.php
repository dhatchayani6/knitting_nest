<?php
session_start();
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
$shopQuery = $conn->prepare("SELECT shop_id FROM shopkeeper WHERE shopkeeper_bioid = ?");
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

// Query low stock items for this shop only (use store_id)
$sql = "SELECT id, item_name, item_quantity, stock_level, store_name 
        FROM items 
        WHERE CAST(item_quantity AS UNSIGNED) < CAST(stock_level AS UNSIGNED)
        AND store_id = ?
        ORDER BY id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = [
            "id" => $row['id'],
            "item_name" => $row['item_name'],
            "item_quantity" => (int) $row['item_quantity'],
            "stock_level" => (int) $row['stock_level'],
            "shop_name" => $row['store_name'],
            "message" => "Item '{$row['item_name']}' is low in stock ({$row['item_quantity']}/{$row['stock_level']})"
        ];
    }

    echo json_encode([
        "success" => true,
        "count" => count($notifications),
        "notifications" => $notifications,
        "message" => "There are low stock items"
    ]);
} else {
    echo json_encode([
        "success" => true,
        "count" => 0,
        "notifications" => [],
        "message" => "No notifications found"
    ]);
}

$stmt->close();
$conn->close();
?>
