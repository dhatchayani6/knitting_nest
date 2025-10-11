<?php
header('Content-Type: application/json');

session_start();
include('../../includes/config.php');

$bio_id = $_SESSION['bio_id'] ?? 0;
$shop_id = 1;

// Get shop assigned to this user
if ($bio_id) {
    $stmt = $conn->prepare("SELECT shop_id FROM shopkeeper WHERE shopkeeper_bioid = ?");
    $stmt->bind_param("i", $bio_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $shop_id = $row['shop_id'];
    }
}

$data = [];

if ($shop_id) {
    $stmt = $conn->prepare("
        SELECT id, item_name, item_code, created_at,total_items
        FROM sales
        WHERE store_id = ?
        ORDER BY created_at ASC
        LIMIT 10
    ");
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
