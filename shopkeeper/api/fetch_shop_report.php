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

// Initialize response
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
    // ✅ Fetch Summary
    $summarySql = "
        SELECT 
            COUNT(s.id) AS total_sales,
            SUM(s.item_price) AS total_revenue
        FROM sales s
        WHERE s.store_id = ?
    ";
    $stmt = $conn->prepare($summarySql);
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $summaryRes = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // ✅ Fetch total stock correctly from items table
    $stockSql = "SELECT SUM(CAST(remaining_quantity AS UNSIGNED)) AS total_stock FROM sales WHERE store_id = ?";
    $stmt = $conn->prepare($stockSql);
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $stockRes = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $response['summary'] = [
        'total_sales' => (int) ($summaryRes['total_sales'] ?? 0),
        'total_revenue' => (float) ($summaryRes['total_revenue'] ?? 0),
        'total_stock' => (int) ($stockRes['total_stock'] ?? 0) // ✅ correct total stock
    ];

    // ✅ Fetch Top Products
    $topSql = "
    SELECT 
        i.item_name,
        i.sub_category,
        i.item_quantity AS available_quantity,
        SUM(s.item_price) AS total_revenue
    FROM sales s
    LEFT JOIN items i ON s.item_id = i.id
    WHERE s.store_id = ?
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