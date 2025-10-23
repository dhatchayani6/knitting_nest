<?php
include('../config/config.php');
session_start();


$bioid = $_SESSION['bio_id'];
// Check if user is logged in
if (!isset($_SESSION['bio_id'])) {
    // Redirect to index page if not logged in
    header("Location: ../index.php");
    exit;
}
// Get shop_id for this shopkeeper
$shopQuery = $conn->prepare("SELECT shop_id FROM shopkeeper WHERE shopkeeper_bioid = ?");
$shopQuery->bind_param("i", $bioid);
$shopQuery->execute();
$shopResult = $shopQuery->get_result();
if ($shopResult->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Shop not found for this user']);
    exit;
}
$shopRow = $shopResult->fetch_assoc();
$shop_id = $shopRow['shop_id'];

// --- 1. Total Products ---
$totalProducts = 0;
$totalProductsQuery = $conn->prepare("SELECT COUNT(*) AS total FROM items WHERE store_id = ?");
$totalProductsQuery->bind_param("i", $shop_id);
$totalProductsQuery->execute();
$totalProductsResult = $totalProductsQuery->get_result();
$totalProducts = $totalProductsResult->fetch_assoc()['total'] ?? 0;

// --- 2. In Stock ---
$inStock = 0;
$inStockQuery = $conn->prepare("
    SELECT COUNT(*) AS inStock 
    FROM items 
    WHERE store_id = ? 
      AND COALESCE(CAST(item_quantity AS SIGNED), 0) >= COALESCE(CAST(stock_level AS SIGNED), 0)
");
$inStockQuery->bind_param("i", $shop_id);
$inStockQuery->execute();
$inStockResult = $inStockQuery->get_result();
$inStock = $inStockResult->fetch_assoc()['inStock'] ?? 0;

// --- 3. Low Stock ---
$lowStock = 0;
$lowStockQuery = $conn->prepare("
    SELECT COUNT(*) AS lowStock 
    FROM items 
    WHERE store_id = ?
      AND COALESCE(CAST(item_quantity AS SIGNED), 0) < COALESCE(CAST(stock_level AS SIGNED), 0)
      AND COALESCE(CAST(item_quantity AS SIGNED), 0) > 0
");
$lowStockQuery->bind_param("i", $shop_id);
$lowStockQuery->execute();
$lowStockResult = $lowStockQuery->get_result();
$lowStock = $lowStockResult->fetch_assoc()['lowStock'] ?? 0;

// --- 4. Out of Stock ---
$outOfStock = 0;
$outOfStockQuery = $conn->prepare("
    SELECT COUNT(*) AS outOfStock 
    FROM items 
    WHERE store_id = ?
      AND COALESCE(CAST(item_quantity AS SIGNED), 0) = 0
");
$outOfStockQuery->bind_param("i", $shop_id);
$outOfStockQuery->execute();
$outOfStockResult = $outOfStockQuery->get_result();
$outOfStock = $outOfStockResult->fetch_assoc()['outOfStock'] ?? 0;

// --- 5. Low Stock Items Details (Top 10) ---
$lowStockItems = [];
$lowStockItemsQuery = $conn->prepare("
    SELECT item_code, item_name, item_quantity, COALESCE(stock_level, 0) AS stock_level, items_image, vendor_name
    FROM items
    WHERE store_id = ? 
      AND COALESCE(CAST(item_quantity AS SIGNED), 0) < COALESCE(CAST(stock_level AS SIGNED), 0)
    ORDER BY CAST(item_quantity AS SIGNED) ASC
    LIMIT 10
");
$lowStockItemsQuery->bind_param("i", $shop_id);
$lowStockItemsQuery->execute();
$lowStockItemsResult = $lowStockItemsQuery->get_result();
while ($row = $lowStockItemsResult->fetch_assoc()) {
    $lowStockItems[] = $row;
}

// --- 6. Top Products ---
$topProducts = [];
$topProductsQuery = $conn->prepare("
    SELECT sh.stores_name, i.item_name, i.item_code, i.sub_category,
           SUM(CAST(s.total_items AS SIGNED)) AS units_sold,
           SUM(CAST(s.item_price AS DECIMAL(12,2))) AS revenue_count,
           CASE
               WHEN COALESCE(CAST(i.item_quantity AS SIGNED),0)=0 THEN 'Out of Stock'
               WHEN COALESCE(CAST(i.item_quantity AS SIGNED),0) < COALESCE(CAST(i.stock_level AS SIGNED),0) THEN 'Low Stock'
               ELSE 'In Stock'
           END AS status
    FROM items i
    INNER JOIN sales s ON s.item_id = i.id
    INNER JOIN shops sh ON s.store_id = sh.id
    WHERE i.store_id = ?
    GROUP BY sh.stores_name, i.item_code, i.item_name, i.sub_category
    ORDER BY CASE
                 WHEN COALESCE(CAST(i.item_quantity AS SIGNED),0)=0 THEN 1
                 WHEN COALESCE(CAST(i.item_quantity AS SIGNED),0) < COALESCE(CAST(i.stock_level AS SIGNED),0) THEN 2
                 ELSE 3
             END, units_sold DESC
    LIMIT 10
");
$topProductsQuery->bind_param("i", $shop_id);
$topProductsQuery->execute();
$topProductsResult = $topProductsQuery->get_result();
while ($row = $topProductsResult->fetch_assoc()) {
    $topProducts[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin</title>
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="../stylesheet/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Blinking icon */
        .blinking {
            animation: blink 1.2s infinite;
            font-weight: bold;
        }

        @keyframes blink {

            0%,
            50%,
            100% {
                opacity: 1;
            }

            25%,
            75% {
                opacity: 0;
            }
        }

        /* Low Stock Card Hover */
        .low-stock-item:hover {
            background-color: #ffe3e3;
            transition: 0.3s;
            cursor: pointer;
        }

        /* Scrolling Animation */
        @keyframes scrollUp {
            0% {
                transform: translateY(100%);
            }

            100% {
                transform: translateY(-100%);
            }
        }

        #lowStockScroller {
            display: flex;
            flex-direction: column;
            animation: scrollUp 20s linear infinite;
        }

        #lowStockScroller:hover {
            animation-play-state: paused;
        }
    </style>



</head>

<body>

    <?php include('includes/sidebar.php') ?>

    <!-- Main Content -->

    <main class="content">
        <?php include('includes/header.php'); ?>

        <div class="scroll-section">
            <!-- Low Stock Alert Panel -->
            <div id="lowStockPanel" class="position-fixed end-0 top-50 translate-middle-y p-3"
                style="z-index:1050; width:320px; display:none;">
                <div class="card shadow border rounded-3 bg-light">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-exclamation-triangle-fill me-2 blinking"></i> Low Stock Alerts</span>
                        <button class="btn btn-sm btn-light" id="closeAlertPanel">&times;</button>
                    </div>
                    <div class="card-body p-2" style="max-height: 400px; overflow:hidden;" id="lowStockAlertBody">
                        <div id="lowStockScroller"></div>
                    </div>
                </div>
            </div>



            <!-- Metric Cards -->
            <div class="row g-3 mb-4">
                <!-- Total Products -->
                <div class="col-12 col-md-6 col-lg-3">
                    <a href="product_details.php" class="text-decoration-none">
                        <div
                            class="card p-3 metric-card d-flex justify-content-between flex-row-reverse align-items-center bg-light shadow-sm rounded">
                            <i class="bi bi-box-seam fs-2 text-primary me-2"></i>
                            <div>
                                <h6>Total Products</h6>
                                <h4 id="totalProducts"><?= number_format($totalProducts); ?></h4>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Products in Stock -->
                <div class="col-12 col-md-6 col-lg-3">
                    <a href="product_details.php" class="text-decoration-none">
                        <div
                            class="card p-3 metric-card d-flex justify-content-between flex-row-reverse align-items-center bg-light shadow-sm rounded">
                            <i class="bi bi-stack fs-2 text-success me-2"></i>
                            <div>
                                <h6>Products in Stock</h6>
                                <h4 id="inStock"><?= number_format($inStock); ?></h4>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Low Stock Items -->
                <div class="col-12 col-md-6 col-lg-3">
                    <a href="product_details.php" class="text-decoration-none">
                        <div
                            class="card p-3 metric-card d-flex justify-content-between flex-row-reverse align-items-center bg-light shadow-sm rounded">
                            <i class="bi bi-exclamation-triangle fs-2 text-warning me-2"></i>
                            <div>
                                <h6>Low Stock Items</h6>
                                <h4 id="lowStock"><?= number_format($lowStock); ?></h4>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Out of Stock Items -->
                <div class="col-12 col-md-6 col-lg-3">
                    <a href="product_details.php" class="text-decoration-none">
                        <div
                            class="card p-3 metric-card d-flex justify-content-between flex-row-reverse align-items-center bg-light shadow-sm rounded">
                            <i class="bi bi-x-circle fs-2 text-danger me-2"></i>
                            <div>
                                <h6>Out of Stock Items</h6>
                                <h4 id="outOfStock"><?= number_format($outOfStock); ?></h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>


            <!-- Low Stock Notification & Sales Chart -->
            <div class="row g-4 mb-4 flex-column flex-md-row align-items-start">
                <!-- Low Stock Notifications -->
                <div class="col-12 col-md-6">
                    <div class="card p-3 d-flex flex-column gap-3 h-100 shadow-sm">
                        <h6 class="mb-3">
                            <i class="bi bi-bell text-primary me-2"></i>
                            Low Stock Notification
                        </h6>


                        <?php if (!empty($lowStockItems)): ?>
                            <?php foreach ($lowStockItems as $it): ?>
                                <div class="card mb-0 p-3 shadow-sm product-card">
                                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
                                        <?php $img = $it['items_image'] ?: 'default.png'; ?>
                                        <img src="../<?= htmlspecialchars($img) ?>"
                                            alt="<?= htmlspecialchars($it['item_name']) ?>" class="rounded me-sm-3 mb-2 mb-sm-0"
                                            width="48" height="48">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= htmlspecialchars($it['item_name']) ?></h6>
                                            <p class="mb-1 text-muted small">Code:
                                                <?= htmlspecialchars($it['item_code']) ?>
                                            </p>
                                            <p class="mb-1 small">
                                                Stock:
                                                <span class="text-danger"><?= htmlspecialchars($it['item_quantity']) ?></span>
                                                (Min: <?= htmlspecialchars($it['stock_level']) ?>)
                                            </p>
                                            <?php if ($it['vendor_name']): ?>
                                                <small class="text-muted">Vendor:
                                                    <?= htmlspecialchars($it['vendor_name']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="d-flex flex-sm-column gap-2 ms-sm-3 mt-2 mt-sm-0">
                                            <a href="item-details.php?code=<?= urlencode($it['item_code']) ?>"
                                                class="btn btn-secondary btn-sm w-100">View</a>
                                            <a href="replenish.php?code=<?= urlencode($it['item_code']) ?>"
                                                class="btn btn-primary btn-sm w-100">Replenish</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No low-stock products.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sales & Revenue Chart -->
                <div class="col-12 col-md-6 mt-3 mt-md-0">
                    <div class="card p-3 mt-3 shadow-sm h-100">
                        <h6 class="mb-1">
                            <i class="bi bi-graph-up me-2 text-success"></i>
                            Sales & Revenue (Last 6 Months)
                        </h6>
                        <small class="text-muted mb-3">Monthly revenue aggregates.</small>
                        <canvas id="salesChart" width="400" height="280"></canvas>
                    </div>
                </div>
            </div>



            <!-- Top Products Table -->
            <section class="tab-product-performance shadow p-3 mb-4">
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($topProducts)): ?>
                                <?php foreach ($topProducts as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['stores_name']) ?></td>
                                        <td><?= htmlspecialchars($p['item_name']) ?></td>
                                        <td><?= htmlspecialchars($p['sub_category'] ?? 'N/A') ?></td>
                                        <td><?= number_format($p['units_sold']) ?></td>
                                        <td><?= number_format($p['revenue_count']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No product data available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>




        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- JS libs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        (function () {
            const shopId = <?php echo json_encode($shop_id); ?>;
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
                .catch(err => console.error('Error loading revenue data', err));
        })();
    </script>

    <script>
        function fetchLowStock() {
            $.ajax({
                url: 'api/get_low_stock.php',
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    const panel = $('#lowStockPanel');
                    const scroller = $('#lowStockScroller');
                    scroller.empty(); // clear previous items

                    // Access the data array from response
                    const data = response.notifications || [];

                    if (data.length === 0) {
                        panel.hide();
                        return;
                    }

                    panel.show();

                    // Append items
                    data.forEach(item => {
                        const html = `
                <div class="low-stock-item d-flex align-items-center mb-2">
                    <img src="../${item.items_image || 'images/default.png'}" alt="${item.item_name}" class="rounded me-2" width="40" height="40">
                    <div class="item-info small">
                        <strong>${item.item_name}</strong><br>
                        Stock: <span class="text-danger">${item.item_quantity}</span> / Min: ${item.stock_level}<br>
                        Vendor: ${item.vendor_name || 'N/A'}
                    </div>
                </div>`;
                        scroller.append(html);
                    });

                    // Duplicate only once for smooth scroll if there are 2+ items
                    if (data.length > 1) {
                        scroller.children().clone().appendTo(scroller);
                    }
                },
                error: function () {
                    $('#lowStockAlertBody').html('<p class="text-danger small">Error loading data</p>');
                }
            });
        }

        fetchLowStock();
        setInterval(fetchLowStock, 10000);

        $('#closeAlertPanel').click(function () {
            $(this).closest('#lowStockPanel').hide();
        });


    </script>
</body>

</html>