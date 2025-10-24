<?php
include('../../config/config.php'); // adjust path

// Pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter from query
$filter = $_GET['filter'] ?? 'all';
$where = "";
$message = "No items found";

switch ($filter) {
    case 'in-stock':
        $where = "WHERE COALESCE(CAST(i.item_quantity AS SIGNED),0) >= COALESCE(CAST(i.stock_level AS SIGNED),0)";
        $message = "No in-stock items found";
        break;
    case 'low-stock':
        $where = "WHERE COALESCE(CAST(i.item_quantity AS SIGNED),0) < COALESCE(CAST(i.stock_level AS SIGNED),0)
                  AND COALESCE(CAST(i.item_quantity AS SIGNED),0) > 0";
        $message = "No low-stock items found";
        break;
    case 'out-of-stock':
        $where = "WHERE COALESCE(CAST(i.item_quantity AS SIGNED),0) = 0";
        $message = "No out-of-stock items found";
        break;
    default:
        $where = ""; // all items
        $message = "No items found";
}

// Total items for this filter
$totalQuery = "SELECT COUNT(*) AS total FROM items i LEFT JOIN shops sh ON i.store_id = sh.id $where";
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
    // Fetch paginated items with store location
    $query = "
        SELECT 
            i.id,
            i.item_name,
            i.item_code,
            i.item_quantity,
            i.item_price,
            COALESCE(i.stock_level,0) AS stock_level,
            COALESCE(i.items_image,'default.png') AS items_image,
            COALESCE(sh.stores_name,'N/A') AS store_name,
            COALESCE(sh.stores_location,'N/A') AS stores_location
        FROM items i
        LEFT JOIN shops sh ON i.store_id = sh.id
        $where
        ORDER BY i.item_name ASC
        LIMIT $limit OFFSET $offset
    ";
    $result = $conn->query($query);

    $data = [];
    $sno = $offset + 1;
    while ($row = $result->fetch_assoc()) {
        $row['sno'] = $sno++;
        // merge store info as an object
        $row['store'] = [
            'stores_name' => $row['store_name'],
            'stores_location' => $row['stores_location']
        ];
        unset($row['store_name'], $row['stores_location']); // remove old keys
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
?>