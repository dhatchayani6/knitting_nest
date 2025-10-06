<?php
header("Content-Type: application/json"); // we receive data in JSON format
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");  // changed to GET to match frontend AJAX
header("Access-Control-Allow-Headers: Content-Type");

include __DIR__ . '/../../includes/config.php'; // Include database connection

// Pagination parameters
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) && $_GET['limit'] > 0 ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Count total items
$totalQuery = $conn->query("SELECT COUNT(*) as total FROM items");
$totalRow = $totalQuery->fetch_assoc();
$total_records = $totalRow['total'];
$total_pages = ceil($total_records / $limit);

// Fetch paginated items
$sql = "SELECT id, store_name, item_name, item_code, item_quantity, item_price, stock_level, created_at FROM items ORDER BY id ASC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$items = [];
if ($result && $result->num_rows > 0) {

    $sno = $offset + 1;  // serial number adjusted by offset
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            "sno" => $sno++,
            "id" => $row['id'],
            "store_name" => $row['store_name'],
            "item_name" => $row['item_name'],
            "item_code" => $row['item_code'],
            "item_quantity" => $row['item_quantity'],
            "item_price" => $row['item_price'],
            "stock_level" => $row['stock_level'],
            "created_at" => date("d-m-Y", strtotime($row['created_at'])),
        ];
    }

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "Items fetched successfully",
        "data" => $items,
        "current_page" => $page,
        "total_pages" => $total_pages,
        "total_records" => $total_records
    ]);
} else {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "No records found"
    ]);
}

$conn->close();
