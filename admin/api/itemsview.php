<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

include __DIR__ . '/../../includes/config.php'; // Include database connection

// Pagination parameters
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) && $_GET['limit'] > 0 ? (int) $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Count total items
$totalQuery = $conn->query("SELECT COUNT(*) as total FROM items");
$totalRow = $totalQuery->fetch_assoc();
$total_records = $totalRow['total'];
$total_pages = ceil($total_records / $limit);

// Fetch items with purchase_date and sales count
$sql = "SELECT 
            i.id, 
            i.store_name, 
            i.item_name, 
            i.item_code, 
            i.item_quantity, 
            i.item_price, 
            i.stock_level, 
            p.purchase_date,
            COUNT(s.id) AS sales_count
        FROM items i
        LEFT JOIN purchase_order p ON i.item_code = p.purchase_code
        LEFT JOIN sales s ON i.id = s.item_id
        GROUP BY i.id
        ORDER BY i.id ASC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

$items = [];
if ($result && $result->num_rows > 0) {
    $sno = $offset + 1; // serial number adjusted by offset

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
            "purchase_date" => $row['purchase_date'] ? date("d-m-Y", strtotime($row['purchase_date'])) : null,
            "sales_count" => (int) $row['sales_count'],
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
?>