<?php
header('Content-Type: application/json');
session_start();
include('../../includes/config.php');

$bio_id = $_SESSION['bio_id'] ?? 0;
$shop_id = 1; // default for testing

// Get the shop assigned to the logged-in user
if ($bio_id) {
    $stmt = $conn->prepare("SELECT shop_id FROM shopkeeper WHERE shopkeeper_bioid = ?");
    $stmt->bind_param("i", $bio_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $shop_id = $row['shop_id'];
    }
}

// Initialize summary
$summary = [
    'total_products' => 0,
    'total_sales' => 0,
    'total_revenue' => 0
];

if ($shop_id) {
    // Total products
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_products FROM items WHERE store_id = ?");
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $summary['total_products'] = intval($row['total_products']);
    }

    // Total sales and revenue
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) AS total_sales,
            SUM(item_price) AS total_revenue
        FROM sales
        WHERE store_id = ?
    ");
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $summary['total_sales'] = intval($row['total_sales']);
        $summary['total_revenue'] = floatval($row['total_revenue']);
    }
}

echo json_encode($summary);
?>