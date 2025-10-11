<?php
// api/sales_monthly.php
header('Content-Type: application/json');
include('../../includes/config.php'); // adjust path relative to this file

$store_id = isset($_GET['store_id']) ? (int) $_GET['store_id'] : 0;
if (!$store_id) {
    echo json_encode(['labels' => [], 'data' => []]);
    exit;
}

// Aggregate revenue per month for last 6 months
// Safely handle item_price stored as varchar by removing $ and commas
$sql = "
    SELECT
      DATE_FORMAT(created_at, '%Y-%m') AS ym,
      DATE_FORMAT(created_at, '%b %Y') AS label,
      SUM(
        CAST(REPLACE(REPLACE(COALESCE(item_price,'0'), '$', ''), ',', '') AS DECIMAL(14,2))
        * CAST(COALESCE(total_items,'0') AS UNSIGNED)
      ) AS revenue
    FROM sales
    WHERE store_id = ?
      AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY ym
    ORDER BY ym ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $store_id);
$stmt->execute();
$res = $stmt->get_result();

$labels = [];
$data = [];
// Collect results into associative by ym to ensure months appear in order
$map = [];
while ($r = $res->fetch_assoc()) {
    $map[$r['ym']] = ['label' => $r['label'], 'revenue' => (float) $r['revenue']];
}
$stmt->close();

// Build last 6 months labels (even if no data for a month, show 0)
$months = [];
for ($i = 5; $i >= 0; $i--) {
    $dt = new DateTime();
    $dt->modify("-$i month");
    $ym = $dt->format('Y-m');
    $label = $dt->format('M Y');
    $months[$ym] = $label;
}

foreach ($months as $ym => $label) {
    $labels[] = $label;
    $data[] = isset($map[$ym]) ? round($map[$ym]['revenue'], 2) : 0;
}

echo json_encode(['labels' => $labels, 'data' => $data]);
