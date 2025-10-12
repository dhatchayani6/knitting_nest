<?php
header('Content-Type: application/json');
include('../../includes/config.php');

// 1. Fetch total revenue per month (all shops, no multiplication needed)
$sql = "
    SELECT
        DATE_FORMAT(created_at, '%Y-%m') AS ym,
        DATE_FORMAT(created_at, '%b %Y') AS label,
        SUM(CAST(item_price AS DECIMAL(10,2))) AS revenue
    FROM sales
    WHERE created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 5 MONTH), '%Y-%m-01')
    GROUP BY ym
    ORDER BY ym ASC
";

$result = $conn->query($sql);

$map = [];
if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $map[$r['ym']] = [
            'label' => $r['label'],
            'revenue' => (float) $r['revenue']
        ];
    }
}

// 2. Build last 6 months including missing months
$labels = [];
$data = [];

for ($i = 5; $i >= 0; $i--) {
    $dt = new DateTime();
    $dt->modify("-$i month");
    $ym = $dt->format('Y-m');
    $labels[] = $dt->format('M Y');
    $data[] = isset($map[$ym]) ? round($map[$ym]['revenue'], 2) : 0;
}

// 3. Return JSON
echo json_encode([
    'labels' => $labels,
    'data' => $data
]);
?>