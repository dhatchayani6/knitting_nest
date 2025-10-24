<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

include '../../config/config.php'; // Include database connection

// Pagination parameters
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) && $_GET['limit'] > 0 ? (int) $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build WHERE clause for search
$whereClause = '';
if (!empty($search)) {
    $searchEscaped = mysqli_real_escape_string($conn, $search);
    $whereClause = "WHERE 
        s.stores_name LIKE '%$searchEscaped%' OR 
        i.item_name LIKE '%$searchEscaped%' OR 
        i.item_code LIKE '%$searchEscaped%' OR 
        p.purchase_date LIKE '%$searchEscaped%' OR 
        i.item_price LIKE '%$searchEscaped%'";
}

// Count total items (with WHERE clause)
$totalSql = "SELECT COUNT(DISTINCT i.id) AS total 
             FROM items i
             LEFT JOIN shops s ON i.store_id = s.id
             LEFT JOIN purchase_order p ON i.item_code = p.purchase_code
             LEFT JOIN sales sa ON i.id = sa.item_id
             $whereClause";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$total_records = $totalRow['total'];
$total_pages = ceil($total_records / $limit);

// Fetch items with shop info
$sql = "SELECT 
            i.id, 
            i.store_id,
            s.stores_name, 
            s.stores_location,
            i.item_name, 
            i.item_code, 
            i.item_quantity, 
            i.item_price, 
            i.stock_level, 
            p.purchase_date,
            COUNT(sa.id) AS sales_count
        FROM items i
        LEFT JOIN shops s ON i.store_id = s.id
        LEFT JOIN purchase_order p ON i.item_code = p.purchase_code
        LEFT JOIN sales sa ON i.id = sa.item_id
        $whereClause
        GROUP BY i.id
        ORDER BY i.id ASC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

$items = [];
if ($result && $result->num_rows > 0) {
    $sno = $offset + 1;

    while ($row = $result->fetch_assoc()) {
        $items[] = [
            "sno" => $sno++,
            "id" => $row['id'],
            "store" => [
                "stores_name" => $row['stores_name'],
                "stores_location" => $row['stores_location']
            ],
            "item_name" => $row['item_name'],
            "item_code" => $row['item_code'],
            "item_quantity" => $row['item_quantity'],
            "item_price" => $row['item_price'],
            "stock_level" => $row['stock_level'],
            "purchase_date" => $row['purchase_date'] ? date("d-m-Y", strtotime($row['purchase_date'])) : null,
            "sales_count" => (int) $row['sales_count'],
        ];
    }

    echo json_encode([
        "status" => "success",
        "message" => "Items fetched successfully",
        "data" => $items,
        "current_page" => $page,
        "total_pages" => $total_pages,
        "total_records" => $total_records
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No records found",
        "data" => [],
        "current_page" => $page,
        "total_pages" => 0,
        "total_records" => 0
    ]);
}

$conn->close();
?>
