<?php
include('../../includes/config.php');

header('Content-Type: application/json');

$query = "SELECT id, item_code, item_name, total_items, item_price, item_quantity, remaining_quantity 
          FROM sales 
          ORDER BY created_at DESC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $sales = [];
    while ($row = $result->fetch_assoc()) {
        $sales[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Sales fetched successfully',
        'data' => $sales
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No sales found',
        'data' => []
    ]);
}
?>