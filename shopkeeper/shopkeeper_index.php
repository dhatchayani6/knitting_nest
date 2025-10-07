<?php
session_start();
include('../includes/config.php'); // adjust path if needed
// Check if bio_id exists in the session
if (!isset($_SESSION['bio_id'])) {
    echo '<p class="text-center">Please log in to view your products.</p>';
    exit;
}

$bioid = $_SESSION['bio_id'];


// 1️⃣ Get shop name for logged-in shopkeeper
$stmt = $conn->prepare("SELECT shop_name,shop_id FROM shopkeeper WHERE shopkeeper_bioid = ?");
$stmt->bind_param("s", $bioid);
$stmt->execute();
$result = $stmt->get_result();
$shop = $result->fetch_assoc();
$stmt->close();


$itemCount = 0;
$transferCount = 0;
$shopName = "";

if ($shop) {
    $shopName = $shop['shop_name'];
    $shopid = $shop['shop_id'];

    // 2️⃣ Count items belonging to shop
    $stmt = $conn->prepare("SELECT COUNT(*) AS item_count FROM items WHERE store_name = ?");
    $stmt->bind_param("s", $shopName);
    $stmt->execute();
    $result = $stmt->get_result();
    $countRow = $result->fetch_assoc();
    $itemCount = $countRow['item_count'] ?? 0;
    $stmt->close();

    // 3️⃣ Count transferred items
    $stmt = $conn->prepare("SELECT COUNT(*) AS transfer_count FROM item_transfers WHERE to_store_id = ?");
    $stmt->bind_param("i", $shopid);
    $stmt->execute();
    $result = $stmt->get_result();
    $countRow = $result->fetch_assoc();
    $transferCount = $countRow['transfer_count'] ?? 0;
    $stmt->close();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" id="theme-style" href="css/app.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
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
            margin-bottom: 0.3rem;
        }

        .metric-card h4 {
            font-weight: 600;
        }

        .stats-cards .card {
            background-color: #f8f9fa;
            /* soft light background */
        }

        .low-notification-scroll {
            height: 100%;
            max-height: 510px;
            overflow-y: scroll;
            padding: 20px;
            background: #fff;
        }

        .btn-outline-secondary {
            color: #6c757d !important;
        }

        .status-badge {
            display: inline-block;
            padding: 0.35em 0.75em;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 12px;
            text-align: center;
        }

        /* In Stock - soft green */
        .status-in-stock {
            background-color: #d1e7dd;
            /* light green */
            color: #0f5132;
            /* dark green text */
            font-size: 12px;
            font-weight: 400;
        }

        /* Low Stock - soft yellow */
        .status-low-stock {
            background-color: #fff3cd;
            /* light yellow */
            color: #664d03;
            /* dark yellow text */
            font-size: 12px;
            font-weight: 400;
        }

        /* Out of Stock - soft red */
        .status-out-stock {
            background-color: #f8d7da;
            /* light red/pink */
            color: #842029;
            /* dark red text */
            font-size: 12px;
            font-weight: 400;
        }

        .tab-product-performance {
            padding: 10px;
            background: #ffff;
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
                    <div class="container">
                        <?php echo "<h4 class='mb-2'> <strong>" . htmlspecialchars($shopName) . "</strong></h4>" ?>
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-sm-3 col-lg-3">
                                <div
                                    class="card p-3 metric-card d-flex justify-content-between flex-row-reverse align-items-center bg-light shadow-sm rounded">
                                    <i class="bi bi-box-seam fs-2 text-primary me-2"></i>
                                    <div>
                                        <h6>Total Products</h6>
                                        <h4>1,245</h4>
                                        <span class="text-success">+12% last month</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-sm-3 col-lg-3">
                                <div
                                    class="card p-3 metric-card d-flex justify-content-between flex-row-reverse align-items-center bg-light shadow-sm rounded">
                                    <i class="bi bi-stack fs-2 text-success me-2"></i>
                                    <div>
                                        <h6>Products in Stock</h6>
                                        <h4>890</h4>
                                        <span class="text-success">+5% last week</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-sm-3 col-lg-3">
                                <div
                                    class="card p-3 metric-card d-flex justify-content-between flex-row-reverse align-items-center bg-light shadow-sm rounded">
                                    <i class="bi bi-exclamation-triangle fs-2 text-warning me-2"></i>
                                    <div>
                                        <h6>Low Stock Items</h6>
                                        <h4>35</h4>
                                        <span class="text-danger">-30% from target</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-sm-3 col-lg-3">
                                <div
                                    class="card p-3 metric-card d-flex justify-content-between flex-row-reverse align-items-center bg-light shadow-sm rounded">
                                    <i class="bi bi-x-circle fs-2 text-danger me-2"></i>
                                    <div>
                                        <h6>Out of Stock Items</h6>
                                        <h4>15</h4>
                                        <span class="text-danger">+2 last day</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row  justify-content-center">

                            <div class="col-sm-6 col-md-4 mb-3">


                                <div class=" card shadow p-3 mb-5 bg-light rounded">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"> ITEMS LIST</h5>
                                        <h6 class="card-subtitle mb-2 text-body-secondary"></h6>
                                        <p class="card-text">COUNT: <?php echo $itemCount; ?><?php ?></p>

                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 mb-3">


                                <div class=" card shadow p-3 mb-5 bg-light rounded">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">TRANSFER ITEMS</h5>
                                        <h6 class="card-subtitle mb-2 text-body-secondary"></h6>
                                        <p class="card-text">COUNT:<?php echo $shopid ?></p>

                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 mb-3">


                                <div class=" card shadow p-3 mb-5 bg-light rounded">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">EXAM SCHEDULE LIST </h5>
                                        <h6 class="card-subtitle mb-2 text-body-secondary">EXAM DETAILS</h6>
                                        <p class="card-text">COUNT:</p>

                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 mb-3">


                                <div class=" card shadow p-3 mb-5 bg-light rounded">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">STUDENTS MARKS LIST </h5>
                                        <h6 class="card-subtitle mb-2 text-body-secondary">MARKS DETAILS</h6>
                                        <p class="card-text">COUNT:</p>

                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 mb-3">


                                <div class=" card shadow p-3 mb-5 bg-light rounded">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">EXTERNAL DETAILS </h5>
                                        <h6 class="card-subtitle mb-2 text-body-secondary">EXTERNAL DETAILS</h6>
                                        <p class="card-text">COUNT:</p>

                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </section>
                <!-- <?php include('includes/center-content.php') ?> -->

                <section>
                    <h6><i class="bi bi-bell fs-6 text-primary me-2"></i>Low Stock Notifications</h6>

                    <div class="row">
                        <!-- Left Column: Product Cards -->
                        <div class="col-lg-6 mb-4 low-notification-scroll">
                            <div class="d-flex flex-column gap-3">
                                <!-- Repeat Product Card -->
                                <div class="card mb-0 p-3 shadow-sm product-card">
                                    <div class="d-flex align-items-center">
                                        <img src="logo1.png" alt="Smartwatch" class="rounded me-3" width="40"
                                            height="40">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Smartwatch Series 5</h6>
                                            <p class="mb-1 text-muted" style="font-size:0.9rem;">SKU: SW-S-005</p>
                                            <p class="mb-1" style="font-size:0.9rem;">Stock: <span
                                                    class="text-danger">12</span> (Min: 20)</p>
                                        </div>
                                        <div class="mt-2 d-flex flex-column gap-2">
                                            <button class="btn btn-outline-secondary btn-sm">View
                                                Details</button>
                                            <button class="btn btn-primary btn-sm">Replenish Stock</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Duplicate for more products -->
                                <div class="card mb-0 p-3 shadow-sm product-card">
                                    <div class="d-flex align-items-center">
                                        <img src="logo1.png" alt="Smartwatch" class="rounded me-3" width="40"
                                            height="40">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Smartwatch Series 5</h6>
                                            <p class="mb-1 text-muted" style="font-size:0.9rem;">SKU: SW-S-005</p>
                                            <p class="mb-1" style="font-size:0.9rem;">Stock: <span
                                                    class="text-danger">12</span> (Min: 20)</p>
                                        </div>
                                        <div class="mt-2 d-flex flex-column gap-2">
                                            <button class="btn btn-outline-secondary btn-sm">View
                                                Details</button>
                                            <button class="btn btn-primary btn-sm">Replenish Stock</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Add more cards as needed -->
                                <div class="card mb-0 p-3 shadow-sm product-card">
                                    <div class="d-flex align-items-center">
                                        <img src="logo1.png" alt="Smartwatch" class="rounded me-3" width="40"
                                            height="40">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Smartwatch Series 5</h6>
                                            <p class="mb-1 text-muted" style="font-size:0.9rem;">SKU: SW-S-005</p>
                                            <p class="mb-1" style="font-size:0.9rem;">Stock: <span
                                                    class="text-danger">12</span> (Min: 20)</p>
                                        </div>
                                        <div class="mt-2 d-flex flex-column gap-2">
                                            <button class="btn btn-outline-secondary btn-sm">View
                                                Details</button>
                                            <button class="btn btn-primary btn-sm">Replenish Stock</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-0 p-3 shadow-sm product-card">
                                    <div class="d-flex align-items-center">
                                        <img src="logo1.png" alt="Smartwatch" class="rounded me-3" width="40"
                                            height="40">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Smartwatch Series 5</h6>
                                            <p class="mb-1 text-muted" style="font-size:0.9rem;">SKU: SW-S-005</p>
                                            <p class="mb-1" style="font-size:0.9rem;">Stock: <span
                                                    class="text-danger">12</span> (Min: 20)</p>
                                        </div>
                                        <div class="mt-2 d-flex flex-column gap-2">
                                            <button class="btn btn-outline-secondary btn-sm">View
                                                Details</button>
                                            <button class="btn btn-primary btn-sm">Replenish Stock</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-0 p-3 shadow-sm product-card">
                                    <div class="d-flex align-items-center">
                                        <img src="logo1.png" alt="Smartwatch" class="rounded me-3" width="40"
                                            height="40">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Smartwatch Series 5</h6>
                                            <p class="mb-1 text-muted" style="font-size:0.9rem;">SKU: SW-S-005</p>
                                            <p class="mb-1" style="font-size:0.9rem;">Stock: <span
                                                    class="text-danger">12</span> (Min: 20)</p>
                                        </div>
                                        <div class="mt-2 d-flex flex-column gap-2">
                                            <button class="btn btn-outline-secondary btn-sm">View
                                                Details</button>
                                            <button class="btn btn-primary btn-sm">Replenish Stock</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Sales Chart -->
                        <div class="col-lg-6 mb-4">
                            <div class="card p-3 shadow-sm h-100">
                                <h6>Sales & Profit Trend</h6>
                                <small>Monthly overview of sales performance.</small>
                                <canvas id="salesChart" width="400" height="280"></canvas>
                                <div class="chart-legend">
                                    <span class="legend-sales"><span class="legend-color"></span> Sales</span>
                                    <span class="legend-profit"><span class="legend-color"></span> Profit</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- table for product performance -->
                <section class="tab-product-performance shadow">
                    <h5>Top Product Performance</h5>
                    <small>Overview of best-selling items.</small>
                    <div class="table-responsive">
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
                                <tr>
                                    <td>Wireless Mouse Pro</td>
                                    <td>Accessories</td>
                                    <td>120</td>
                                    <td>$2,400</td>
                                    <td><span class="status-badge status-in-stock">In Stock</span></td>
                                </tr>
                                <tr>
                                    <td>Mechanical Keyboard</td>
                                    <td>Peripherals</td>
                                    <td>85</td>
                                    <td>$4,250</td>
                                    <td><span class="status-badge status-in-stock">In Stock</span></td>
                                </tr>
                                <tr>
                                    <td>Webcam HD 1080p</td>
                                    <td>Video</td>
                                    <td>40</td>
                                    <td>$1,200</td>
                                    <td><span class="status-badge status-low-stock">Low Stock</span></td>
                                </tr>
                                <tr>
                                    <td>Monitor Stand Ergo</td>
                                    <td>Office</td>
                                    <td>60</td>
                                    <td>$900</td>
                                    <td><span class="status-badge status-in-stock">In Stock</span></td>
                                </tr>
                                <tr>
                                    <td>External SSD 1TB</td>
                                    <td>Storage</td>
                                    <td>25</td>
                                    <td>$1,500</td>
                                    <td><span class="status-badge status-out-stock">Out of Stock</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </article>
            <!-- center content ended -->




            <div class="modal fade" id="modal-media">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Media Library</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                <span class="sr-only">Close</span>
                            </button>
                        </div>
                        <div class="modal-body modal-tab-container">
                            <ul class="nav nav-tabs modal-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link" href="#gallery" data-toggle="tab" role="tab">Gallery</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" href="#upload" data-toggle="tab" role="tab">Upload</a>
                                </li>
                            </ul>
                            <div class="tab-content modal-tab-content">
                                <div class="tab-pane fade" id="gallery" role="tabpanel">
                                    <div class="images-container">
                                        <div class="row"> </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade active in" id="upload" role="tabpanel">
                                    <div class="upload-container">
                                        <div id="dropzone">
                                            <form action="https://modularcode.io/" method="POST"
                                                enctype="multipart/form-data" class="dropzone needsclick dz-clickable"
                                                id="demo-upload">
                                                <div class="dz-message-block">
                                                    <div class="dz-message needsclick"> Drop files here or click to
                                                        upload. </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Insert Selected</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
            <div class="modal fade" id="confirm-modal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">
                                <i class="fa fa-warning"></i> Alert
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure want to do this?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Yes</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
        </div>
    </div>
    <!-- Reference block for JS -->
    <div class="ref" id="ref">
        <div class="color-primary"></div>
        <div class="chart">
            <div class="color-primary"></div>
            <div class="color-secondary"></div>
        </div>
    </div>
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '../../www.google-analytics.com/analytics.js', 'ga');
        ga('create', 'UA-80463319-4', 'auto');
        ga('send', 'pageview');
    </script>
    <script src="js/vendor.js"></script>
    <script src="js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [
                    {
                        label: 'Sales',
                        data: [1800, 2700, 1800, 2900, 3200, 3600],
                        borderColor: '#1766a4',
                        backgroundColor: 'transparent',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: false,
                    },
                    {
                        label: 'Profit',
                        data: [900, 1200, 850, 1600, 1800, 2000],
                        borderColor: '#444',
                        backgroundColor: 'transparent',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: false,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value >= 1000 ? value / 1000 + 'k' : value;
                            },
                            font: {
                                size: 12,
                            },
                        },
                        grid: {
                            drawBorder: false,
                        },
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12,
                            },
                        },
                        grid: {
                            drawBorder: false,
                            drawOnChartArea: false,
                        },
                    },
                },
            },
        });
    </script>
</body>

</html>