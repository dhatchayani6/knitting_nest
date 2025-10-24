<?php
// Allow CORS & JSON response
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include your database connection
include __DIR__ . '/../../config/config.php';

// Pagination parameters
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build WHERE clause for search
$whereClause = '';
if (!empty($search)) {
    $searchEscaped = mysqli_real_escape_string($conn, $search);
    $whereClause = "WHERE 
        i.item_name LIKE '%$searchEscaped%' OR 
        i.item_code LIKE '%$searchEscaped%' OR 
        f.stores_name LIKE '%$searchEscaped%' OR 
        to_shop.stores_name LIKE '%$searchEscaped%' OR 
        t.created_at LIKE '%$searchEscaped%' OR 
        t.transfer_status LIKE '%$searchEscaped%'";
}

// Count total records
$totalQuery = $conn->query("SELECT COUNT(*) as total FROM item_transfers t
                            LEFT JOIN items i ON t.item_id = i.id
                            LEFT JOIN shops f ON t.from_store_id = f.id
                            LEFT JOIN shops to_shop ON t.to_store_id = to_shop.id
                            $whereClause");
$totalRow = $totalQuery->fetch_assoc();
$total_records = $totalRow['total'];
$total_pages = ceil($total_records / $limit);

// Fetch transfer records with store info
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
            DATE_FORMAT(t.created_at, '%d-%m-%Y') AS created_at,
            f.stores_name AS from_store_name,
            f.stores_location AS from_store_location,
            to_shop.stores_name AS to_store_name,
            to_shop.stores_location AS to_store_location
        FROM item_transfers t
        LEFT JOIN items i ON t.item_id = i.id
        LEFT JOIN shops f ON t.from_store_id = f.id
        LEFT JOIN shops to_shop ON t.to_store_id = to_shop.id
        $whereClause
        ORDER BY t.id DESC
        LIMIT $offset, $limit";

$result = $conn->query($sql);

$data = [];
$sno = $offset + 1;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['sno'] = $sno++;
        // Build store objects
        $row['from_store'] = [
            'stores_name' => $row['from_store_name'],
            'stores_location' => $row['from_store_location']
        ];
        $row['to_store'] = [
            'stores_name' => $row['to_store_name'],
            'stores_location' => $row['to_store_location']
        ];

        // Remove raw columns
        unset($row['from_store_name'], $row['from_store_location'], $row['to_store_name'], $row['to_store_location']);

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
