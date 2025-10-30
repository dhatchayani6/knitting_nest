<?php
session_start();
include('../../config/config.php');

header('Content-Type: application/json');

// Get logged-in user's shop
$bio_id = $_SESSION['bio_id'] ?? 0;
$shop_id = 0;

// Fetch shop assigned to this shopkeeper
if ($bio_id) {
    $stmt = $conn->prepare("SELECT shop_id FROM shopkeeper WHERE shopkeeper_bioid = ?");
    $stmt->bind_param("i", $bio_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $shop_id = (int) $row['shop_id'];
    }
    $stmt->close();
}

$response = [
    'status' => 'success',
    'summary' => [
        'total_sales' => 0,
        'total_revenue' => 0,
        'total_stock' => 0
    ],
    'topProducts' => []
];

if ($shop_id) {
    // 📅 Period filter
    $period = $_GET['period'] ?? 'monthly';
    $dateCondition = "";

    if ($period === 'daily') {
        $dateCondition = "DATE(s.created_at) = CURDATE()";
    } elseif ($period === 'weekly') {
        $dateCondition = "YEARWEEK(s.created_at, 1) = YEARWEEK(CURDATE(), 1)";
    } else {
        $dateCondition = "MONTH(s.created_at) = MONTH(CURDATE()) AND YEAR(s.created_at) = YEAR(CURDATE())";
    }

    // ✅ Summary Query
    $summarySql = "
        SELECT 
            COUNT(s.id) AS total_sales,
            COALESCE(SUM(s.item_price), 0) AS total_revenue
        FROM sales s
        WHERE s.store_id = ? AND $dateCondition
    ";
    $stmt = $conn->prepare($summarySql);
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $summaryRes = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // ✅ Total stock
    $stockSql = "SELECT COALESCE(SUM(CAST(remaining_quantity AS UNSIGNED)), 0) AS total_stock 
                 FROM sales 
                 WHERE store_id = ?";
    $stmt = $conn->prepare($stockSql);
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $stockRes = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $response['summary'] = [
        'total_sales' => (int) ($summaryRes['total_sales'] ?? 0),
        'total_revenue' => (float) ($summaryRes['total_revenue'] ?? 0),
        'total_stock' => (int) ($stockRes['total_stock'] ?? 0)
    ];

    // ✅ Top Products
    $topSql = "
        SELECT 
            i.item_name,
            i.sub_category,
            i.item_quantity AS available_quantity,
            SUM(s.item_price) AS total_revenue
        FROM sales s
        LEFT JOIN items i ON s.item_id = i.id
        WHERE s.store_id = ? AND $dateCondition
        GROUP BY i.id, i.item_name, i.sub_category, i.item_quantity
        ORDER BY total_revenue DESC
        LIMIT 20
    ";

    $stmt = $conn->prepare($topSql);
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response['topProducts'][] = [
            'item_name' => $row['item_name'],
            'sub_category' => $row['sub_category'],
            'available_quantity' => (int) $row['available_quantity'],
            'total_revenue' => (float) $row['total_revenue']
        ];
    }
    $stmt->close();
}

echo json_encode($response);
$conn->close();
?>