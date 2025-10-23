<?php
include '../../config/config.php'; // Include database connection

$query = "
    SELECT 
        item_code,
        item_name,
        item_quantity,
        COALESCE(stock_level,0) AS stock_level,
        items_image,
        vendor_name
    FROM items
    WHERE COALESCE(CAST(item_quantity AS SIGNED),0) < COALESCE(CAST(stock_level AS SIGNED),0)
";

$result = $conn->query($query);
$items = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($items);
?>