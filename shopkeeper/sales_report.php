<?php
session_start();
include('../includes/config.php'); // adjust path if needed



// fetch all shops names
$sql = "SELECT id, stores_name FROM shops";
$result = $conn->query($sql);

$shops = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $shops[] = $row;
    }
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
                    <div class="summary-cards">
                        <div class="summary-card">
                            <h6>Total Sales</h6>
                            <div class="value">8,500</div>
                        </div>
                        <div class="summary-card">
                            <h6>Total Revenue</h6>
                            <div class="value">Rs. 185,000</div>
                        </div>
                        <div class="summary-card">
                            <h6>Avg. Order Value</h6>
                            <div class="value">Rs. 75.00</div>
                        </div>
                        <div class="summary-card">
                            <h6>Conversion Rate</h6>
                            <div class="value">3.2%</div>
                        </div>
                    </div>
                </section>
                <section class="analytics">
                    <div class="chart-card">
                        <h5>Sales Trends Over Time</h5>
                        <p class="text-muted small">Monthly sales performance across various metrics.</p>
                        <canvas id="salesLineChart" width="400" height="280"></canvas>
                    </div>
                    <div class="chart-card">
                        <h5>Product Category Breakdown</h5>
                        <p class="text-muted small">Sales distribution across different product categories.</p>
                        <canvas id="categoryDonutChart" width="400" height="280"></canvas>
                    </div>
                </section>

                <section class="d-flex flex-wrap gap-4">
                    <section class="report-table flex-grow-1">
                        <h5>Top Selling Products</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Units Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Wireless Headphones Pro</td>
                                    <td><span class="badge badge-electronics">Electronics</span></td>
                                    <td>1200</td>
                                    <td>Rs. 72000.00</td>
                                </tr>
                                <tr>
                                    <td>Organic Cotton T-Shirt</td>
                                    <td><span class="badge badge-apparel">Apparel</span></td>
                                    <td>2500</td>
                                    <td>Rs. 50000.00</td>
                                </tr>
                                <tr>
                                    <td>Smart Home Speaker X</td>
                                    <td><span class="badge badge-electronics">Electronics</span></td>
                                    <td>800</td>
                                    <td>Rs. 48000.00</td>
                                </tr>
                                <tr>
                                    <td>Aromatherapy Diffuser</td>
                                    <td><span class="badge badge-homegoods">Home Goods</span></td>
                                    <td>1500</td>
                                    <td>Rs. 30000.00</td>
                                </tr>
                                <tr>
                                    <td>Stainless Steel Water Bottle</td>
                                    <td><span class="badge badge-homegoods">Home Goods</span></td>
                                    <td>2000</td>
                                    <td>Rs. 25000.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </section>
                    <section class="report-table flex-grow-1">
                        <h5>Recent Transactions</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Shop</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>TRX78901</td>
                                    <td>Shop A</td>
                                    <td>Alice Johnson</td>
                                    <td>Rs. 120.50</td>
                                    <td>2024-07-28</td>
                                </tr>
                                <tr>
                                    <td>TRX78902</td>
                                    <td>Shop B</td>
                                    <td>Bob Williams</td>
                                    <td>Rs. 75.00</td>
                                    <td>2024-07-27</td>
                                </tr>
                                <tr>
                                    <td>TRX78903</td>
                                    <td>Shop A</td>
                                    <td>Charlie Brown</td>
                                    <td>Rs. 250.99</td>
                                    <td>2024-07-27</td>
                                </tr>
                                <tr>
                                    <td>TRX78904</td>
                                    <td>Shop C</td>
                                    <td>Diana Miller</td>
                                    <td>Rs. 45.75</td>
                                    <td>2024-07-26</td>
                                </tr>
                                <tr>
                                    <td>TRX78905</td>
                                    <td>Shop B</td>
                                    <td>Eve Davis</td>
                                    <td>Rs. 180.20</td>
                                    <td>2024-07-26</td>
                                </tr>
                            </tbody>
                        </table>
                    </section>
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
        const salesCtx = document.getElementById('salesLineChart').getContext('2d');
        const salesLineChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                datasets: [
                    {
                        label: 'Sales',
                        data: [35000, 45000, 40000, 47000, 46000, 49000, 54000, 60000],
                        borderColor: '#0d9488',
                        backgroundColor: 'transparent',
                        borderWidth: 3,
                        tension: 0.25,
                        fill: false
                    },
                    {
                        label: 'Other Metric',
                        data: [1000, 2000, 1800, 1500, 2300, 2100, 2600, 2800],
                        borderColor: '#2563eb',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.3,
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
                        ticks: {
                            font: { size: 12 }
                        }
                    },
                    x: {
                        grid: { drawBorder: false, drawOnChartArea: false },
                        ticks: { font: { size: 12 } }
                    }
                }
            }
        });

        const categoryCtx = document.getElementById('categoryDonutChart').getContext('2d');
        const categoryDonutChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Electronics', 'Apparel', 'Home Goods', 'Books', 'Beauty'],
                datasets: [{
                    data: [25, 20, 20, 15, 20],
                    backgroundColor: ['#007b8a', '#009c66', '#44b2af', '#caeaf6', '#1f2f37'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '70%',
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>


</body>

</html>