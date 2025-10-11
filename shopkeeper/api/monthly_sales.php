<?php
header('Content-Type: application/json');
session_start();
include('../../includes/config.php');

$bio_id = $_SESSION['bio_id'] ?? 0;
// $shop_id = 1; // for testing

// Get shop assigned to user
if ($bio_id) {
    $stmt = $conn->prepare("SELECT shop_id FROM shopkeeper WHERE shopkeeper_bioid = ?");
    $stmt->bind_param("i", $bio_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $shop_id = $row['shop_id'];
    }
}

// Prepare array for 12 months
$monthlySales = array_fill(1, 12, 0);

if ($shop_id) {
    $stmt = $conn->prepare("
        SELECT MONTH(created_at) AS month, SUM(item_price) AS total_amount
        FROM sales
        WHERE store_id = ?
        GROUP BY MONTH(created_at)
    ");
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $monthlySales[intval($row['month'])] = floatval($row['total_amount']);
    }
}

// Return sales in order Jan -> Dec
echo json_encode(array_values($monthlySales));
