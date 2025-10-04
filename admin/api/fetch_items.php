<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include __DIR__ . '/../../includes/config.php';

// Pagination parameters
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Count total items
$totalQuery = $conn->query("SELECT COUNT(*) as total FROM items");
$totalRow = $totalQuery->fetch_assoc();
$total_records = $totalRow['total'];
$total_pages = ceil($total_records / $limit);

// Fetch items with store names from shops table
$sql = "SELECT 
            i.id,
            i.store_id,
            s.stores_name AS store_name,
            i.item_name,
            i.item_code,
            i.item_quantity,
            i.item_price,
            i.stock_level,
            i.items_image
        FROM items i
        LEFT JOIN shops s ON i.store_id = s.id
        ORDER BY i.id DESC
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$sno = $offset + 1;
while ($row = $result->fetch_assoc()) {
    $row['sno'] = $sno++;
    $data[] = $row;
}

echo json_encode([
    'status' => !empty($data) ? 'success' : 'no_records',
    'data' => $data,
    'current_page' => $page,
    'total_pages' => $total_pages
]);
?>