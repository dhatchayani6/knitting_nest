<?php
include('../../includes/config.php');

header('Content-Type: application/json');

// 🗓️ Get today's date (Y-m-d format to match MySQL DATE)
// 🗓️ Get selected date, default to today
$date = isset($_GET['date']) && $_GET['date'] !== '' ? $_GET['date'] : date('Y-m-d');

// Pagination
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// ✅ Total rows for pagination
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM sales WHERE DATE(created_at) = '$date'");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

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
     WHERE DATE(created_at) = '$date'   -- ✅ Only today's records
    ORDER BY created_at DESC
    LIMIT $limit OFFSET $offset
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
        'message' => ' Today Sales fetched successfully',
        'current_page' => $page,
        'total_pages' => $totalPages,
        'data' => $sales
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No sales found',
        'current_page' => $page,
        'total_pages' => 0,
        'data' => []
    ], JSON_PRETTY_PRINT);
}
?>