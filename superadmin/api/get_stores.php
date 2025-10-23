<?php
header('Content-Type: application/json');
include '../../config/config.php';

$result = $conn->query("SELECT id,stores_name, stores_location FROM shops ORDER BY id ASC");
$stores = [];
while ($row = $result->fetch_assoc()) {
    $stores[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $stores
]);
?>