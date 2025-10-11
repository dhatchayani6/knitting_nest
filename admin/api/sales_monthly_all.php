<?php
header('Content-Type: application/json');
include('../../includes/config.php');

// Calculate total revenue per month for last 6 months across all shops
$sql = "
    SELECT
        DATE_FORMAT(created_at, '%Y-%m') AS ym,
        DATE_FORMAT(created_at, '%b %Y') AS label,
        SUM(
            CAST(REPLACE(REPLACE(COALESCE(item_price, '0'), '$', ''), ',', '') AS DECIMAL(14,2))
            * CAST(COALESCE(total_items, '0') AS UNSIGNED)
        ) AS revenue
    FROM sales
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY ym
    ORDER BY ym ASC
";

$result = $conn->query($sql);

$labels = [];
$data = [];
$map = [];

if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $map[$r['ym']] = [
            'label' => $r['label'],
            'revenue' => (float) $r['revenue']
        ];
    }
}

// Build last 6 months even if there’s no data for some
for ($i = 5; $i >= 0; $i--) {
    $dt = new DateTime();
    $dt->modify("-$i month");
    $ym = $dt->format('Y-m');
    $labels[] = $dt->format('M Y');
    $data[] = isset($map[$ym]) ? round($map[$ym]['revenue'], 2) : 0;
}

echo json_encode(['labels' => $labels, 'data' => $data]);
