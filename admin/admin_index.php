<?php
include('../includes/config.php');

$salesAgg = [];

// Aggregated sales per item & store
$salesAggQuery = "
    SELECT 
        s.item_code,
        s.store_id,
        SUM(CAST(s.total_items AS SIGNED)) AS units_sold,
        SUM(CAST(s.item_price AS SIGNED)) AS revenue,
        SUM(CAST(s.remaining_quantity AS SIGNED)) AS remaining_qty
    FROM sales s
    GROUP BY s.item_code, s.store_id
";

$stmt = $conn->prepare($salesAggQuery);
$stmt->execute();
$salesAggRes = $stmt->get_result();

while ($row = $salesAggRes->fetch_assoc()) {
    // Cast numeric fields properly
    $row['units_sold'] = (int) $row['units_sold'];
    $row['revenue'] = (int) $row['revenue'];
    $row['remaining_qty'] = (int) $row['remaining_qty'];

    $salesAgg[] = $row;
}


// $salesAgg now contains aggregated sales per item & store


// Build an in-memory map of sales_agg for quick joins (optional, but keeps later queries simpler)
$salesAgg = [];
while ($row = $salesAggRes->fetch_assoc()) {
    $key = $row['item_code'] . '::' . $row['store_id'];
    $salesAgg[$key] = [
        'units_sold' => (int) $row['units_sold'],
        'revenue' => (float) $row['revenue'],
        'remaining_qty' => (int) $row['remaining_qty'],
    ];
}
$stmt->close();

// 2) Build a single aggregated inventory view (item + store) using SQL (we'll compute counts from DB directly)
$inventoryCountsQuery = "
    WITH sales_agg AS (
        SELECT 
            s.item_code,
            s.store_id,
            SUM(CAST(s.total_items AS SIGNED))        AS units_sold,
            SUM(CAST(s.total_items AS SIGNED) * CAST(s.item_price AS DECIMAL(12,2))) AS revenue,
            SUM(CAST(s.remaining_quantity AS SIGNED)) AS remaining_qty
        FROM sales s
        GROUP BY s.item_code, s.store_id
    )
    SELECT 
        COUNT(*) AS totalProducts, -- total items across stores
        SUM(CASE WHEN i.item_quantity = 0 THEN 1 ELSE 0 END) AS outOfStock,
        SUM(CASE 
                WHEN i.item_quantity > 0 
                     AND i.item_quantity < i.stock_level   -- below 20% of ideal stock
                THEN 1 
                ELSE 0 
            END) AS lowStock
    FROM items i
    LEFT JOIN shops sh ON i.store_id = sh.id
    LEFT JOIN sales_agg sa ON i.item_code = sa.item_code AND i.store_id = sa.store_id
";

$stmt = $conn->prepare($inventoryCountsQuery);
$stmt->execute();
$counts = $stmt->get_result()->fetch_assoc();
$stmt->close();

// convert to ints and compute inStock
$totalProducts = (int) ($counts['totalProducts'] ?? 0);
$outOfStock = (int) ($counts['outOfStock'] ?? 0);
$lowStock = (int) ($counts['lowStock'] ?? 0);
$inStock = max(0, $totalProducts - $lowStock - $outOfStock);

// 3) Low stock items details (limit 10) — based on aggregated remaining_qty per item-store
$lowStockItems = [];

$lowStockQuery = "
    SELECT 
        i.item_name,
        i.item_code,
        (CAST(i.item_quantity AS SIGNED) - COALESCE(SUM(CAST(s.total_items AS SIGNED)),0)) AS remaining_quantity,
        COALESCE(i.stock_level, 0) AS stock_level,
        COALESCE(i.items_image, '') AS items_image,
        COALESCE(i.sub_category, '') AS sub_category,
        COALESCE(i.vendor_name, '') AS vendor_name,
        COALESCE(sh.stores_name, '') AS store_name
    FROM items i
    LEFT JOIN shops sh ON i.store_id = sh.id
    LEFT JOIN sales s ON i.item_code = s.item_code AND i.store_id = s.store_id
    GROUP BY i.item_name, i.item_code, i.item_quantity, i.stock_level, i.items_image, i.sub_category, i.vendor_name, sh.stores_name
    HAVING remaining_quantity > 0 AND remaining_quantity < stock_level
    ORDER BY remaining_quantity ASC
    LIMIT 10
";

$stmt = $conn->prepare($lowStockQuery);
$stmt->execute();
$res = $stmt->get_result();

while ($r = $res->fetch_assoc()) {
    $r['remaining_quantity'] = (int) $r['remaining_quantity'];
    $r['stock_level'] = (int) $r['stock_level'];
    $lowStockItems[] = $r;
}


// 4) Top products — sold items (per item-store), top by units_sold
$topProducts = [];



$topProducts = [];

$topProductsQuery = "
    SELECT 
        i.item_name,
        i.item_code,
        COALESCE(i.sub_category,'') AS category,
        COALESCE(SUM(CAST(s.total_items AS SIGNED)), 0) AS units_sold,   -- total_items summed as units_sold
        COALESCE(SUM(CAST(s.item_price AS DECIMAL(12,2))), 0) AS revenue, -- total revenue
        COALESCE(TRIM(sh.stores_name), '') AS store_name,
        CASE
            WHEN (i.item_quantity - COALESCE(SUM(s.total_items),0)) <= 0 THEN 'Out of Stock'
            WHEN i.stock_level IS NOT NULL AND (i.item_quantity - COALESCE(SUM(s.total_items),0)) < i.stock_level THEN 'Low Stock'
            ELSE 'In Stock'
        END AS status

    FROM items i
    LEFT JOIN shops sh ON i.store_id = sh.id
    LEFT JOIN sales s ON i.item_code = s.item_code AND i.store_id = s.store_id
    GROUP BY i.item_name, i.item_code, i.sub_category, i.item_quantity, i.stock_level, sh.stores_name
    HAVING units_sold > 0
    ORDER BY units_sold DESC, revenue DESC
    LIMIT 10
";

$stmt = $conn->prepare($topProductsQuery);
$stmt->execute();
$res = $stmt->get_result();

$topProducts = [];
while ($r = $res->fetch_assoc()) {
    $r['units_sold'] = (int) $r['units_sold'];
    $r['revenue'] = is_numeric($r['revenue']) ? (float) $r['revenue'] : 0;
    $topProducts[] = $r;
}

$stmt->close();
?>

<!doctype html>
<html class="no-js" lang="en">

<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Admin Dashboard </title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <!-- Place favicon.ico in the root directory -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" id="theme-style" href="css/app.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* keep your custom styles (copied / slightly trimmed) */
        .metric-card {
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .metric-card h6 {
            font-weight: 500;
            margin-bottom: .3rem;
        }

        .metric-card h4 {
            font-weight: 600;
        }

        .low-notification-scroll {
            height: 100%;
            max-height: 510px;
            overflow-y: auto;
            padding: 20px;
            background: #fff;
        }

        .status-badge {
            display: inline-block;
            padding: .35em .75em;
            font-size: .85rem;
            font-weight: 500;
            border-radius: 12px;
        }

        .status-in-stock {
            background-color: #d1e7dd;
            color: #0f5132;
            font-size: 12px;
            font-weight: 400;
        }

        .status-low-stock {
            background-color: #fff3cd;
            color: #664d03;
            font-size: 12px;
            font-weight: 400;
        }

        .status-out-stock {
            background-color: #f8d7da;
            color: #842029;
            font-size: 12px;
            font-weight: 400;
        }
    </style>

    <style>
        .summary-cards {
            background-color: #edf7fa;
            display: flex;
            justify-content: space-between;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .summary-card {
            background: white;
            border-radius: 8px;
            padding: 1rem 1.5rem;
            flex: 1 1 16%;
            min-width: 150px;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.05);
        }

        .summary-card h6 {
            font-weight: 600;
            color: #4a5568;
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }

        .summary-card .value {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1a202c;
        }

        section.analytics {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            padding: 1.25rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.05);
            flex: 1 1 40%;
            min-width: 320px;
        }

        .chart-card h5 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .chart-legend {
            display: flex;
            gap: 1.5rem;
            margin-top: 15px;
        }

        .chart-legend div {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            color: #4a5568;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .color-electronics {
            background-color: #007b8a;
        }

        .color-apparel {
            background-color: #009c66;
        }

        .color-homegoods {
            background-color: #44b2af;
        }

        .color-books {
            background-color: #caeaf6;
        }

        .color-beauty {
            background-color: #1f2f37;
        }

        .report-table {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.05);
            flex: 1 1 50%;
            min-width: 320px;
        }

        .report-table h5 {
            font-weight: 600;
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            border-bottom: 2px solid #e2e8f0;
        }

        table thead th {
            text-align: left;
            padding: 0.75rem 1rem;
            font-weight: 600;
            color: #4a5568;
            font-size: 0.9rem;
        }

        table tbody tr:hover {
            background-color: #f1f5f9;
        }

        table tbody td {
            padding: 0.6rem 1rem;
            color: #1a202c;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .badge {
            padding: 0.3em 0.7em;
            font-size: 0.8rem;
            border-radius: 12px;
            font-weight: 600;
            white-space: nowrap;
            display: inline-block;
        }

        .badge-electronics {
            background-color: #caeaf6;
            color: #007b8a;
        }

        .badge-apparel {
            background-color: #c7f3d6;
            color: #009c66;
        }

        .badge-homegoods {
            background-color: #c6e5e2;
            color: #44b2af;
        }
    </style>

</head>

<body>
    <div class="main-wrapper">
        <div class="app" id="app">
            <!-- header & sidebar -->
            <?php include('includes/header.php') ?>
            <?php include('includes/sidebar.php') ?>


            <!-- center content start -->
            <article class="content dashboard-page bg-white">
                <section>
                    <div class="container-fluid">

                        <!-- Metric cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-sm-3 col-lg-3">
                                <div
                                    class="card p-3 metric-card d-flex justify-content-between flex-row-reverse align-items-center bg-light shadow-sm rounded">
                                    <i class="bi bi-box-seam fs-2 text-primary me-2"></i>
                                    <div>
                                        <h6>Total Products</h6>
                                        <h4 id="totalProducts"><?php echo number_format($totalProducts); ?></h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-sm-3 col-lg-3">
                                <div
                                    class="card p-3 metric-card d-flex justify-content-between flex-row-reverse align-items-center bg-light shadow-sm rounded">
                                    <i class="bi bi-stack fs-2 text-success me-2"></i>
                                    <div>
                                        <h6>Products in Stock</h6>
                                        <h4 id="inStock"><?php echo number_format($inStock); ?></h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-sm-3 col-lg-3">
                                <div
                                    class="card p-3 metric-card d-flex justify-content-between flex-row-reverse align-items-center bg-light shadow-sm rounded">
                                    <i class="bi bi-exclamation-triangle fs-2 text-warning me-2"></i>
                                    <div>
                                        <h6>Low Stock Items</h6>
                                        <h4 id="lowStock"><?php echo number_format($lowStock); ?></h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-sm-3 col-lg-3">
                                <div
                                    class="card p-3 metric-card d-flex justify-content-between flex-row-reverse align-items-center bg-light shadow-sm rounded">
                                    <i class="bi bi-x-circle fs-2 text-danger me-2"></i>
                                    <div>
                                        <h6>Out of Stock Items</h6>
                                        <h4 id="outOfStock"><?php echo number_format($outOfStock); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="row">
                        <!-- Right: Sales chart -->
                        <div class="col-lg-6 mb-4">
                            <div class="card p-3 shadow-sm h-100">
                                <h6>Sales & Revenue (last 6 months)</h6>
                                <small>Monthly revenue aggregates.</small>
                                <canvas id="salesChart" width="400" height="280"></canvas>
                                <div class="chart-legend mt-2">
                                    <span class="legend-sales"><span class="legend-color"></span> Revenue</span>
                                </div>
                            </div>
                        </div>
                        <!-- Left: Low stock cards -->
                        <div class="col-lg-6 mb-4 low-notification-scroll">
                            <div class="d-flex flex-column gap-3">
                                <?php if (!empty($lowStockItems)): ?>
                                    <?php foreach ($lowStockItems as $it): ?>
                                        <div class="card mb-0 p-3 shadow-sm product-card">
                                            <div class="d-flex align-items-center">
                                                <?php $img = $it['items_image'] ?: 'default.png'; ?>
                                                <img src="../<?php echo htmlspecialchars($img); ?>"
                                                    alt="<?php echo htmlspecialchars($it['item_name']); ?>" class="rounded me-3"
                                                    width="48" height="48">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($it['item_name']); ?></h6>
                                                    <p class="mb-1 text-muted" style="font-size:.9rem;">Code:
                                                        <?php echo htmlspecialchars($it['item_code']); ?>
                                                    </p>
                                                    <p class="mb-1" style="font-size:.9rem;">Stock: <span
                                                            class="text-danger"><?php echo htmlspecialchars($it['remaining_quantity']); ?></span>
                                                        (Min: <?php echo htmlspecialchars($it['stock_level']); ?>)</p>
                                                    <?php if ($it['vendor_name']): ?><small class="text-muted">Vendor:
                                                            <?php echo htmlspecialchars($it['vendor_name']); ?></small><?php endif; ?>
                                                </div>
                                                <div class="mt-2 d-flex flex-column gap-2">
                                                    <a href="item-details.php?code=<?php echo urlencode($it['item_code']); ?>"
                                                        class="btn btn-secondary btn-outline-secondary btn-sm">View Details</a>
                                                    <a href="replenish.php?code=<?php echo urlencode($it['item_code']); ?>"
                                                        class="btn btn-primary btn-sm">Replenish</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <h6><i class="bi bi-bell fs-6 text-primary me-2"></i>Low Stock Notifications</h6>

                                    <p class="text-muted">No low-stock products.</p>
                                <?php endif; ?>
                            </div>
                        </div>


                    </div>
                </section>

                <!-- Top products table -->
                <section class="tab-product-performance shadow p-3">
                    <h5>Top Product Performance</h5>
                    <small>Overview of best-selling items.</small>
                    <div class="table-responsive mt-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Store Name</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Units Sold</th>
                                    <th>Revenue</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topProducts)): ?>
                                    <?php foreach ($topProducts as $p): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($p['store_name']); ?></td>
                                            <td><?php echo htmlspecialchars($p['item_name']); ?></td>
                                            <td><?php echo htmlspecialchars($p['category']); ?></td>
                                            <td><?php echo number_format($p['units_sold']); ?></td>
                                            <td>Rs.<?php echo number_format($p['revenue'], 2); ?></td>
                                            <td>
                                                <?php if ($p['status'] === 'In Stock'): ?>
                                                    <span class="status-badge status-in-stock">In Stock</span>
                                                <?php elseif ($p['status'] === 'Low Stock'): ?>
                                                    <span class="status-badge status-low-stock">Low Stock</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-out-stock">Out of Stock</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No product data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </article>
            <!-- table end -->



        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="js/vendor.js"></script>
    <script src="js/app.js"></script>

    <!-- JS libs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const url = 'api/dashboard_sales_monthly.php'; // no store filter
            fetch(url)
                .then(r => r.json())
                .then(data => {
                    if (!data || !data.labels) return;
                    const ctx = document.getElementById('salesChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Revenue',
                                data: data.data,
                                borderColor: '#1766a4',
                                backgroundColor: 'transparent',
                                borderWidth: 3,
                                tension: 0.3,
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function (value) {
                                            return value >= 1000 ? (value / 1000) + 'k' : value;
                                        }
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(err => console.error('Error loading revenue data', err));
        })();
    </script>


</body>

</html>