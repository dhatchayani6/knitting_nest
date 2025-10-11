<?php
include('../../includes/config.php');

header('Content-Type: application/json');

$query = "
    SELECT 
        id,
        item_code,
        item_name,
        total_items,
        item_price,
        item_quantity,
        remaining_quantity,
        DATE_FORMAT(created_at, '%d-%m-%Y') AS created_date
    FROM sales
    ORDER BY created_at DESC
";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $sales = [];
    while ($row = $result->fetch_assoc()) {
        $sales[] = [
            'id' => $row['id'],
            'item_code' => $row['item_code'],
            'item_name' => $row['item_name'],
            'total_items' => $row['total_items'],
            'item_price' => $row['item_price'],
            'item_quantity' => $row['item_quantity'],
            'remaining_quantity' => $row['remaining_quantity'],
            'created_at' => $row['created_date']  // formatted as DD-MM-YYYY
        ];
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Sales fetched successfully',
        'data' => $sales
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No sales found',
        'data' => []
    ], JSON_PRETTY_PRINT);
}
?>