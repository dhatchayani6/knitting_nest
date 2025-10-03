<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include __DIR__ . '/../../includes/config.php';

if (!isset($_SESSION['bio_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in to view your transfers']);
    exit;
}

$bioid = $_SESSION['bio_id'];

// Get shop_id for this bio_id
$shopQuery = $conn->prepare("SELECT shop_id FROM shopkeeper WHERE shopkeeper_bioid = ?");
$shopQuery->bind_param("i", $bioid);
$shopQuery->execute();
$shopResult = $shopQuery->get_result();
if ($shopResult->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Shop not found for this user']);
    exit;
}
$shopRow = $shopResult->fetch_assoc();
$shop_id = $shopRow['shop_id'];

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build base WHERE clause
$where = "(t.from_store_id = ? OR t.to_store_id = ?)";
$params = [$shop_id, $shop_id];
$types = "ii";

// Add search filter if provided
if (!empty($search)) {
    $where .= " AND (i.item_name LIKE ? OR i.id LIKE ? OR t.item_code LIKE ? OR f.stores_name LIKE ? OR to_shop.stores_name LIKE ?)";
    $searchParam = "%$search%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
    $types .= "sssss";
}

// Count total records
$totalSql = "SELECT COUNT(*) AS total
             FROM item_transfers t
             LEFT JOIN shops f ON t.from_store_id = f.id
             LEFT JOIN shops to_shop ON t.to_store_id = to_shop.id
             LEFT JOIN items i ON t.item_id = i.id
             WHERE $where";
$totalStmt = $conn->prepare($totalSql);
$totalStmt->bind_param($types, ...$params);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$total_records = $totalRow['total'];
$total_pages = ceil($total_records / $limit);

// Fetch paginated records
$sql = "SELECT 
            t.id,
            t.item_id,
            i.item_name,        -- ✅ item name from items table
            i.item_code,        -- ✅ take code from items too (to avoid mismatch)
            t.from_store_id,
            t.to_store_id,
            t.available_quantity,
            t.shared_quantity,
            t.transfer_status,
            t.created_at,
            f.stores_name AS from_store,
            to_shop.stores_name AS to_store
        FROM item_transfers t
        LEFT JOIN items i ON t.item_id = i.id
        LEFT JOIN shops f ON t.from_store_id = f.id
        LEFT JOIN shops to_shop ON t.to_store_id = to_shop.id
        WHERE $where
        ORDER BY t.id DESC
        LIMIT ?, ?";

$params[] = $offset;
$params[] = $limit;
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$sno = $offset + 1;
while ($row = $result->fetch_assoc()) {
    $row['sno'] = $sno++;
    $data[] = $row;
}

echo json_encode([
    'status' => !empty($data) ? 'Fetch transfer details success' : 'No records found',
    'data' => $data,
    'current_page' => $page,
    'total_pages' => $total_pages
]);
