<?php
session_start();
include('../includes/config.php'); // adjust path if needed

// ============================
// 1. Summary Cards
// ============================
$summarySql = "
    SELECT 
        COUNT(s.id) AS total_sales,
        SUM(s.item_price) AS total_revenue,
        ROUND(AVG(s.item_price)) AS avg_order
    FROM sales s
";
$summaryResult = $conn->query($summarySql);
$summary = $summaryResult->fetch_assoc();

// ============================
// 2. Top Selling Products
// ============================
$topProducts = [];
$topSql = "
    SELECT 
        s.item_name, 
        s.item_code,
        sh.stores_name AS store_name,
        SUM(CAST(s.total_items AS UNSIGNED)) AS units_sold,
        SUM(CAST(s.item_price AS UNSIGNED)) AS revenue
    FROM sales s
    LEFT JOIN shops sh ON s.store_id = sh.id
    GROUP BY s.item_name, s.item_code, sh.stores_name
    ORDER BY units_sold DESC
";
$topResult = $conn->query($topSql);
while ($row = $topResult->fetch_assoc()) {
    $topProducts[] = $row;
}




// ============================
// 3. Recent Transactions
// ============================
$recentTransfers = [];
$recentSql = "
    SELECT 
        t.id,
        t.item_id,
        t.item_code,
        sh_from.stores_name AS from_store,
        sh_to.stores_name AS to_store,
        t.available_quantity,
        t.shared_quantity,
        t.transfer_status,
        t.imagePath,
        DATE_FORMAT(t.created_at, '%d-%m-%Y') AS transfer_date
    FROM item_transfers t
    LEFT JOIN shops sh_from ON t.from_store_id = sh_from.id
    LEFT JOIN shops sh_to ON t.to_store_id = sh_to.id
    ORDER BY t.created_at ASC
";

$recentResult = $conn->query($recentSql);
while ($row = $recentResult->fetch_assoc()) {
    $recentTransfers[] = $row;
}

// ============================
// 4. Sales Trends per Month
// ============================
$labels = [];
$salesData = [];
$monthSql = "
    SELECT DATE_FORMAT(s.created_at, '%b %Y') AS month,
           SUM(s.item_price) AS total
    FROM sales s
    GROUP BY YEAR(s.created_at), MONTH(s.created_at)
    ORDER BY s.created_at ASC
";
$monthResult = $conn->query($monthSql);
while ($row = $monthResult->fetch_assoc()) {
    $labels[] = $row['month'];
    $salesData[] = (int) $row['total'];
}

// ============================
// 5. Category Breakdown
// ============================
$categories = [];
$categoryData = [];
$catSql = "
    SELECT i.sub_category,
           SUM(s.item_price) AS total
    FROM items i
    LEFT JOIN sales s ON i.id = s.item_id
    GROUP BY i.sub_category
";
$catResult = $conn->query($catSql);
while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row['sub_category'] ?? 'Others';
    $categoryData[] = (int) $row['total'];
}

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
            <!-- start header -->
            <?php include('includes/header.php') ?>
            <!-- end header -->

            <!-- sidebar start -->
            <?php include('includes/sidebar.php') ?>
            <!-- end sidebar -->
            <div class="sidebar-overlay" id="sidebar-overlay"></div>
            <div class="sidebar-mobile-menu-handle" id="sidebar-mobile-menu-handle"></div>
            <div class="mobile-menu-handle"></div>
            <!-- center content start -->
            <article class="content dashboard-page bg-white">

                <section>
                    <!-- Summary Cards -->
                    <div class="summary-cards d-flex flex-wrap gap-3 mb-4">
                        <div class="summary-card p-3">
                            <h6>Total Sales</h6>
                            <div class="value"><?= $summary['total_sales'] ?></div>
                        </div>
                        <div class="summary-card p-3">
                            <h6>Total Revenue</h6>
                            <div class="value">Rs. <?= $summary['total_revenue'] ?></div>
                        </div>
                        <div class="summary-card p-3">
                            <h6>Avg. Order Value</h6>
                            <div class="value">Rs. <?= $summary['avg_order'] ?></div>
                        </div>

                    </div>

                </section>
                <section class="analytics d-flex flex-wrap gap-3 mb-4">
                    <div class="chart-card flex-fill p-3">
                        <h5>Sales Trends Over Time</h5>
                        <p class="text-muted small">Monthly sales performance across various metrics.</p>
                        <canvas id="salesLineChart"></canvas>
                    </div>
                    <!-- <div class="chart-card flex-fill p-3">
                        <h5>Category Breakdown</h5>
                        <p class="text-muted small">Sales distribution across different product categories.</p>
                        <canvas id="categoryDonutChart"></canvas>
                    </div> -->
                </section>

                <section class="report-table flex-grow-1">
                    <h5>Top Selling Products</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Store Name</th>
                                <th>Units Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($topProducts)): ?>
                                <?php foreach ($topProducts as $prod): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($prod['item_name']) ?></td>
                                        <td><?= htmlspecialchars($prod['store_name']) ?></td>
                                        <td><?= (int) $prod['units_sold'] ?></td>
                                        <td>Rs. <?= number_format($prod['revenue']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">No top selling products found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>

                <section class="report-table flex-grow-1">
                    <h5>Recent Transactions</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>From Store</th>
                                <th>To Store</th>
                                <th>Item Code</th>
                                <th>Available Quantity</th>
                                <th>Shared Quantity</th>
                                <!-- <th>Status</th> -->
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentTransfers)): ?>
                                <?php foreach ($recentTransfers as $trx): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($trx['id']) ?></td>
                                        <td><?= htmlspecialchars($trx['from_store']) ?></td>
                                        <td><?= htmlspecialchars($trx['to_store']) ?></td>
                                        <td><?= htmlspecialchars($trx['item_code']) ?></td>
                                        <td><?= htmlspecialchars($trx['available_quantity']) ?></td>
                                        <td><?= htmlspecialchars($trx['shared_quantity']) ?></td>
                                        <td><?= htmlspecialchars($trx['transfer_date']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No recent transactions found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>



                <!-- center content ended -->

                <!-- table start -->



            </article>
            <!-- table end -->



        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="js/vendor.js"></script>
    <script src="js/app.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Fetch dynamic sales data from PHP endpoint
        fetch('api/sales_monthly_all.php')
            .then(response => response.json())
            .then(result => {
                const salesCtx = document.getElementById('salesLineChart').getContext('2d');

                const salesLineChart = new Chart(salesCtx, {
                    type: 'line',
                    data: {
                        labels: result.labels,
                        datasets: [
                            {
                                label: 'Sales',
                                data: result.data,
                                borderColor: '#0d9488',
                                backgroundColor: 'transparent',
                                borderWidth: 3,
                                tension: 0.25,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { drawBorder: false },
                                ticks: { font: { size: 12 } }
                            },
                            x: {
                                grid: { drawBorder: false, drawOnChartArea: false },
                                ticks: { font: { size: 12 } }
                            }
                        }
                    }
                });
            })
            .catch(err => console.error('Error fetching sales data:', err));
    </script>



</body>

</html>