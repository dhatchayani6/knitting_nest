<?php
include('../../config/config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['bio_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please log in',
        'current_page' => 1,
        'total_pages' => 1,
        'data' => []
    ]);
    exit;
}

$bioid = $_SESSION['bio_id'];

// Get store_id for this user
$shopQuery = $conn->prepare("SELECT shop_id, shop_name FROM shopkeeper WHERE shopkeeper_bioid = ? LIMIT 1");
$shopQuery->bind_param("s", $bioid); // 's' because bioid is varchar
$shopQuery->execute();
$shopResult = $shopQuery->get_result();

if ($shopResult->num_rows === 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Store not found for this user',
        'current_page' => 1,
        'total_pages' => 1,
        'data' => []
    ]);
    exit;
}

$shopRow = $shopResult->fetch_assoc();
$store_id = $shopRow['shop_id'];
$store_name = $shopRow['shop_name'];

// Pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter
$filter = $_GET['filter'] ?? 'all';
$where = ["i.store_id = '$store_id'"];
$message = "No items found";

switch ($filter) {
    case 'in-stock':
        $where[] = "COALESCE(CAST(i.item_quantity AS SIGNED),0) >= COALESCE(CAST(i.stock_level AS SIGNED),0)";
        $message = "No in-stock items found";
        break;
    case 'low-stock':
        $where[] = "COALESCE(CAST(i.item_quantity AS SIGNED),0) < COALESCE(CAST(i.stock_level AS SIGNED),0)
                    AND COALESCE(CAST(i.item_quantity AS SIGNED),0) > 0";
        $message = "No low-stock items found";
        break;
    case 'out-of-stock':
        $where[] = "COALESCE(CAST(i.item_quantity AS SIGNED),0) = 0";
        $message = "No out-of-stock items found";
        break;
}

$whereSql = "WHERE " . implode(" AND ", $where);

// Total items
$totalQuery = "SELECT COUNT(*) AS total FROM items i $whereSql";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'] ?? 0;
$totalPages = ceil($totalItems / $limit);

if ($totalItems == 0) {
    $response = [
        'status' => 'error',
        'message' => $message,
        'current_page' => 1,
        'total_pages' => 1,
        'data' => []
    ];
} else {
    // Fetch items
    $query = "
        SELECT 
            i.id,
            i.item_name,
            i.item_code,
            i.item_quantity,
            i.item_price,
            COALESCE(i.stock_level,0) AS stock_level,
            COALESCE(i.items_image,'default.png') AS items_image,
            '$store_name' AS store_name
        FROM items i
        $whereSql
        ORDER BY i.item_name ASC
        LIMIT $limit OFFSET $offset
    ";

    $result = $conn->query($query);

    $data = [];
    $sno = $offset + 1;
    while ($row = $result->fetch_assoc()) {
        $row['sno'] = $sno++;
        $data[] = $row;
    }

    $response = [
        'status' => 'success',
        'current_page' => $page,
        'total_pages' => $totalPages,
        'data' => $data
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
