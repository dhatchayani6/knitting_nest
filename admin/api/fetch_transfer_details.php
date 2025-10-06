<?php
// Allow CORS & JSON response
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include your database connection
include __DIR__ . '/../../includes/config.php';

// Pagination parameters
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Count total records
$totalQuery = $conn->query("SELECT COUNT(*) as total FROM item_transfers");
$totalRow = $totalQuery->fetch_assoc();
$total_records = $totalRow['total'];
$total_pages = ceil($total_records / $limit);

// Fetch records with shop names
$sql = "SELECT 
            t.id,
            t.item_id,
            i.item_name,
            i.item_code,
            t.from_store_id,
            t.to_store_id,
            t.available_quantity,
            t.shared_quantity,
            t.transfer_status,
              DATE(t.created_at) AS created_at,
            f.stores_name AS from_store,
            to_shop.stores_name AS to_store
        FROM item_transfers t
        LEFT JOIN items i ON t.item_id = i.id
        LEFT JOIN shops f ON t.from_store_id = f.id
        LEFT JOIN shops to_shop ON t.to_store_id = to_shop.id
        ORDER BY t.id DESC
        LIMIT $offset, $limit";

$result = $conn->query($sql);

$data = [];
$sno = $offset + 1;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['sno'] = $sno++; // Serial number
        $data[] = $row;
    }
    echo json_encode([
        'status' => 'Fetch transfer details success',
        'data' => $data,
        'current_page' => $page,
        'total_pages' => $total_pages
    ]);
} else {
    echo json_encode([
        'status' => 'No records found',
        'data' => [],
        'current_page' => $page,
        'total_pages' => 0
    ]);
}
