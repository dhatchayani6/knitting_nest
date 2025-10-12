<?php
include('../includes/config.php'); // adjust path if needed

// Prepare default values
$totalProducts = 0;
$inStock = 0;
$lowStock = 0;
$outOfStock = 0;
$lowStockItems = [];
$topProducts = [];

// 1) Total products (count all items)
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM items");
$stmt->execute();
$totalProducts = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

// 2) Out of stock (sales.remaining_quantity = 0)
$stmt = $conn->prepare("SELECT COUNT(*) AS outcount FROM sales WHERE CAST(remaining_quantity AS SIGNED) = 0");
$stmt->execute();
$outOfStock = (int) ($stmt->get_result()->fetch_assoc()['outcount'] ?? 0);
$stmt->close();

// 3) Low stock (remaining_quantity <= items.stock_level AND > 0)
$stmt = $conn->prepare("
    SELECT COUNT(*) AS lowcount
    FROM sales s
    LEFT JOIN items i ON s.item_code = i.item_code
    WHERE CAST(s.remaining_quantity AS SIGNED) > 0
      AND i.stock_level IS NOT NULL
      AND CAST(s.remaining_quantity AS SIGNED) <= CAST(i.stock_level AS SIGNED)
");
$stmt->execute();
$lowStock = (int) ($stmt->get_result()->fetch_assoc()['lowcount'] ?? 0);
$stmt->close();

// 4) In stock = totalProducts - lowStock - outOfStock
$inStock = max(0, $totalProducts - $lowStock - $outOfStock);

// 5) Low stock items details (limit 10)
$stmt = $conn->prepare("
    SELECT s.item_name,
           s.item_code,
           s.remaining_quantity,
           COALESCE(i.stock_level, '') AS stock_level,
           COALESCE(i.items_image, '') AS items_image,
           COALESCE(i.sub_category, '') AS sub_category,
           COALESCE(i.vendor_name, '') AS vendor_name
    FROM sales s
    LEFT JOIN items i ON s.item_code = i.item_code
    WHERE CAST(s.remaining_quantity AS SIGNED) > 0
      AND i.stock_level IS NOT NULL
      AND CAST(s.remaining_quantity AS SIGNED) <= CAST(i.stock_level AS SIGNED)
    ORDER BY CAST(s.remaining_quantity AS SIGNED) ASC
    LIMIT 10
");
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
    $lowStockItems[] = $r;
}
$stmt->close();

// 6) Top products — only sold items
$stmt = $conn->prepare("
    SELECT 
        i.item_name,
        i.item_code,
        COALESCE(i.sub_category,'') AS category,
        COALESCE(CAST(sales_agg.units_sold AS UNSIGNED), 0) AS units_sold,
        COALESCE(CAST(sales_agg.revenue AS UNSIGNED), 0) AS revenue,
        COALESCE(sh.stores_name, '') AS store_name,
        CASE
            WHEN COALESCE(sales_agg.remaining_qty, i.stock_level) = 0 THEN 'Out of Stock'
            WHEN i.stock_level IS NOT NULL AND COALESCE(sales_agg.remaining_qty, i.stock_level) <= i.stock_level THEN 'Low Stock'
            ELSE 'In Stock'
        END AS status
    FROM items i
    LEFT JOIN (
        SELECT item_code, store_id,
               SUM(total_items) AS units_sold,
               SUM(item_price) AS revenue,
               SUM(remaining_quantity) AS remaining_qty
        FROM sales
        GROUP BY item_code, store_id
        HAVING SUM(total_items) > 0
    ) sales_agg 
        ON i.item_code = sales_agg.item_code AND i.store_id = sales_agg.store_id
    LEFT JOIN shops sh ON i.store_id = sh.id
    ORDER BY units_sold DESC
    LIMIT 10
");


$stmt->execute();
$res = $stmt->get_result();
$topProducts = [];
while ($r = $res->fetch_assoc()) {
    $topProducts[] = $r; // units_sold and revenue already integers
}
$stmt->close();





// Optional: Output JSON (for API / debugging)
// echo json_encode([
//     'totalProducts' => $totalProducts,
//     'inStock' => $inStock,
//     'lowStock' => $lowStock,
//     'outOfStock' => $outOfStock,
//     'lowStockItems' => $lowStockItems,
//     'topProducts' => $topProducts
// ]);
?>

<!doctype html>
<html class="no-js" lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSS & icons -->
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" id="theme-style" href="css/app.css">
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
</head>

<body>
    <div class="main-wrapper">
        <div class="app" id="app">
            <!-- header & sidebar -->
            <?php include('includes/header.php') ?>
            <?php include('includes/sidebar.php') ?>

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
                                                <img src="<?php echo htmlspecialchars($img); ?>"
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
                                                        class="btn btn-outline-secondary btn-sm">View Details</a>
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

            <!-- modals (unchanged) -->
            <?php // ... keep your existing modals here ... ?>

        </div>
    </div>

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