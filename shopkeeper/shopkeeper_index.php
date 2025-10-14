<?php
session_start();
include('../includes/config.php'); // adjust path if needed

// Check login
if (!isset($_SESSION['bio_id'])) {
    echo '<p class="text-center">Please log in to view your products.</p>';
    exit;
}
$bioid = $_SESSION['bio_id'];

// Get shop name and id for logged-in shopkeeper
$stmt = $conn->prepare("SELECT shop_name, shop_id FROM shopkeeper WHERE shopkeeper_bioid = ?");
$stmt->bind_param("s", $bioid);
$stmt->execute();
$result = $stmt->get_result();
$shop = $result->fetch_assoc();
$stmt->close();

$shopName = "";
$shopid = 0;

if ($shop) {
    $shopName = $shop['shop_name'];
    $shopid = (int) $shop['shop_id'];
}

// Prepare default values
$totalProducts = 0;
$inStock = 0;
$lowStock = 0;
$outOfStock = 0;
$lowStockItems = [];
$topProducts = [];

/**
 * NOTES on schema used:
 * - sales table columns used: item_code, total_items, item_price, store_id, remaining_quantity, created_at
 * - items table columns used: item_code, stock_level, items_image, sub_category, vendor_name
 * We join sales <-> items on item_code where needed.
 */

// 1) Total products (count items table rows for this store_id)
if ($shopid) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM items WHERE store_id = ?");
    $stmt->bind_param("i", $shopid);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $totalProducts = (int) ($row['total'] ?? 0);
    $stmt->close();

    // 2) Out of stock (use sales.remaining_quantity = 0)
    $stmt = $conn->prepare("SELECT COUNT(*) AS outcount FROM sales WHERE store_id = ? AND CAST(remaining_quantity AS SIGNED) = 0");
    $stmt->bind_param("i", $shopid);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $outOfStock = (int) ($row['outcount'] ?? 0);
    $stmt->close();

    // 3) Low stock (remaining_quantity <= items.stock_level AND remaining_quantity > 0)
    // join sales -> items on item_code
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS lowcount
        FROM sales s
        LEFT JOIN items i ON s.item_code = i.item_code
        WHERE s.store_id = ?
          AND CAST(s.remaining_quantity AS SIGNED) > 0
          AND (
                i.stock_level IS NOT NULL
                AND CAST(s.remaining_quantity AS SIGNED) <= CAST(i.stock_level AS SIGNED)
              )
    ");
    $stmt->bind_param("i", $shopid);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $lowStock = (int) ($row['lowcount'] ?? 0);
    $stmt->close();

    // 4) In stock = totalProducts - lowStock - outOfStock (safe fallback)
    $inStock = max(0, $totalProducts - $lowStock - $outOfStock);

    // 5) Low stock items details (limit 10)
    $stmt = $conn->prepare("
        SELECT s.item_name AS item_name,
               s.item_code AS item_code,
               s.remaining_quantity AS remaining_quantity,
               COALESCE(i.stock_level, '') AS stock_level,
               COALESCE(i.items_image, '') AS items_image,
               COALESCE(i.sub_category, '') AS sub_category,
               COALESCE(i.vendor_name, '') AS vendor_name
        FROM sales s
        LEFT JOIN items i ON s.item_code = i.item_code
        WHERE s.store_id = ?
          AND CAST(s.remaining_quantity AS SIGNED) > 0
          AND i.stock_level IS NOT NULL
          AND CAST(s.remaining_quantity AS SIGNED) <= CAST(i.stock_level AS SIGNED)
        ORDER BY CAST(s.remaining_quantity AS SIGNED) ASC
        LIMIT 10
    ");
    $stmt->bind_param("i", $shopid);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $lowStockItems[] = $r;
    }
    $stmt->close();

    // 6) Top products by total_items (units sold) — using sales table
    $stmt = $conn->prepare("
     SELECT 
        ss.item_name,
        ss.item_code,
        COALESCE(i.sub_category, '') AS category,
        ss.units_sold,
        ss.revenue,
        CASE
            WHEN SUM(CAST(s.remaining_quantity AS SIGNED)) = 0 THEN 'Out of Stock'
            WHEN i.stock_level IS NOT NULL 
                 AND SUM(CAST(s.remaining_quantity AS SIGNED)) <= CAST(i.stock_level AS SIGNED)
            THEN 'Low Stock'
            ELSE 'In Stock'
        END AS status
    FROM (
        SELECT 
            s.item_code,
            s.item_name,
            s.store_id,
            SUM(CAST(s.total_items AS UNSIGNED)) AS units_sold,
            SUM(
                CAST(REPLACE(REPLACE(s.item_price, '$', ''), ',', '') AS DECIMAL(10,2))
            ) AS revenue
        FROM sales s
        WHERE s.store_id = ?
        GROUP BY s.item_code
    ) AS ss
    LEFT JOIN items i ON ss.item_code = i.item_code
    LEFT JOIN sales s ON s.item_code = ss.item_code AND s.store_id = ss.store_id
    WHERE ss.store_id = ?
    GROUP BY ss.item_code
    ORDER BY ss.units_sold DESC
    LIMIT 10
");

   $stmt->bind_param("ii", $shopid, $shopid);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        // enforce numeric types
        $r['units_sold'] = (int) $r['units_sold'];
        $r['revenue'] = (float) $r['revenue'];
        $topProducts[] = $r;
    }
    $stmt->close();
}
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
                        <h4 class='mb-2'><strong><?php echo htmlspecialchars($shopName); ?></strong></h4>

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
                                                        <?php echo htmlspecialchars($it['item_code']); ?></p>
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
        // fetch monthly revenue data (api/sales_monthly.php)
        (function () {
            const shopId = <?php echo json_encode($shopid); ?>;
            const url = 'api/sales_monthly.php?store_id=' + encodeURIComponent(shopId);

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
                .catch(err => {
                    console.error('Error loading revenue data', err);
                });
        })();
    </script>
</body>

</html>