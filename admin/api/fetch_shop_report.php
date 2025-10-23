<?php
session_start();
include '../../config/config.php';
header('Content-Type: application/json');

$shopId = $_GET['shop_id'] ?? '';
$timePeriod = $_GET['time_period'] ?? 'monthly';

$where = [];
if (!empty($shopId)) {
    $where[] = "i.store_id = " . (int) $shopId;
}

// Time filter
switch ($timePeriod) {
    case 'daily':
        $where[] = "DATE(s.created_at) = CURDATE()";
        break;
    case 'weekly':
        $where[] = "YEARWEEK(s.created_at, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'monthly':
    default:
        $where[] = "MONTH(s.created_at) = MONTH(CURDATE()) AND YEAR(s.created_at) = YEAR(CURDATE())";
        break;
}

$whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Summary
$summarySql = "
    SELECT 
        COUNT(s.id) AS total_sales,
        SUM(s.item_price) AS total_revenue,
        SUM(CAST(i.stock_level AS UNSIGNED)) AS total_stock
    FROM sales s
    LEFT JOIN items i ON s.item_id = i.id
    $whereSql
";
$res = $conn->query($summarySql);
$summaryRow = $res->fetch_assoc();
$summary = [
    'total_sales' => $summaryRow['total_sales'] ?? 0,
    'total_revenue' => $summaryRow['total_revenue'] ?? 0,
    'total_stock' => $summaryRow['total_stock'] ?? 0
];

// Top Products
$topProducts = [];
$topSql = "
    SELECT 
        i.item_name,
        i.sub_category,
        i.stock_level AS available_quantity,
        SUM(s.item_price) AS total_revenue
    FROM sales s
    LEFT JOIN items i ON s.item_id = i.id
    $whereSql
    GROUP BY i.item_name, i.sub_category, i.stock_level
    ORDER BY total_revenue DESC
    LIMIT 20
";
$result = $conn->query($topSql);
while ($row = $result->fetch_assoc()) {
    $topProducts[] = $row;
}

echo json_encode([
    'status' => 'success',
    'summary' => $summary,
    'topProducts' => $topProducts
]);
?>