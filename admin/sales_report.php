<?php
session_start();
include('../config/config.php'); // adjust path if needed

// Check if user is logged in
if (!isset($_SESSION['bio_id'])) {
    header("Location: ../index.php");
    exit;
}

// Fetch Shops
$shops = [];
$shopSql = "SELECT id, stores_name, stores_location FROM shops ORDER BY stores_name ASC";
$shopResult = $conn->query($shopSql);
while ($shop = $shopResult->fetch_assoc()) {
    $shops[] = $shop;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../stylesheet/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        @media (min-width:768px) {

            #shopFilter,
            #timeFilter {
                width: 100%;
                max-width: 180px !important;
            }
        }
    </style>
</head>

<body>
    <?php include('includes/sidebar.php') ?>
    <main class="content">
        <?php include('includes/header.php'); ?>

        <div class="scroll-section">

            <!-- Filters -->
            <div class="mb-4 d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2 gap-sm-3">
                <label for="shopFilter" class="form-label fw-bold mb-1 mb-sm-0" style="min-width: 120px;">Select
                    Shop</label>
                <select id="shopFilter" class="form-select flex-grow-1">
                    <option value="">All Shops</option>
                    <?php foreach ($shops as $shop): ?>
                        <option value="<?= $shop['id'] ?>">
                            <?= htmlspecialchars($shop['stores_name'] . ' (' . $shop['stores_location'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="timeFilter" class="form-label fw-bold mb-1 mb-sm-0" style="min-width: 120px;">Time
                    Period</label>
                <select id="timeFilter" class="form-select flex-grow-1">
                    <option value="daily">Today</option>
                    <option value="weekly">This Week</option>
                    <option value="monthly" selected>This Month</option>
                </select>
            </div>

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="p-3 d-flex justify-content-between align-items-center gap-2 border rounded shadow-sm">
                        <div>
                            <h6>Total Sales</h6>
                            <div id="totalSales" class="value">0</div>
                        </div>
                        <i class="bi bi-cart-fill fs-2 text-primary"></i>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 d-flex justify-content-between align-items-center gap-2 border rounded shadow-sm">
                        <div>
                            <h6>Total Revenue</h6>
                            <div id="totalRevenue" class="value">Rs.0</div>
                        </div>
                        <i class="bi bi-cash-stack fs-2 text-success"></i>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 d-flex justify-content-between align-items-center gap-2 border rounded shadow-sm">
                        <div>
                            <h6>Total Stock</h6>
                            <div id="totalStock" class="value">0</div>
                        </div>
                        <i class="bi bi-box-seam fs-2 text-warning"></i>
                    </div>
                </div>
            </div>

            <!-- Top Selling Products Table -->
            <section class="report-table flex-grow-1">
                <h5>Top Selling Products</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item Name</th>
                                <th>Subcategory</th>
                                <th>Total Revenue (Rs.)</th>
                                <th>Available Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="topProductsTable">
                            <tr>
                                <td colspan="4" class="text-center text-muted">Select a shop and time period to view
                                    data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <script>
        function fetchShopData(shopId = '', timePeriod = 'monthly') {
            $.ajax({
                url: 'api/fetch_shop_report.php',
                type: 'GET',
                data: { shop_id: shopId, time_period: timePeriod },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        // Summary Cards
                        $('#totalSales').text(response.summary.total_sales);
                        $('#totalRevenue').text('Rs.' + response.summary.total_revenue);
                        $('#totalStock').text(response.summary.total_stock);

                        // Top Products Table
                        let rows = '';
                        if (response.topProducts.length > 0) {
                            response.topProducts.forEach(item => {
                                rows += `<tr>
                                    <td>${item.item_name}</td>
                                    <td>${item.sub_category || '—'}</td>
                                    <td>Rs.${item.total_revenue}</td>
                                    <td>${item.available_quantity}</td>
                                </tr>`;
                            });
                        } else {
                            rows = `<tr><td colspan="4" class="text-center text-muted">No products found.</td></tr>`;
                        }
                        $('#topProductsTable').html(rows);
                    } else {
                        alert(response.message || 'Error fetching data');
                    }
                },
                error: function () {
                    alert('AJAX error');
                }
            });
        }

        $(document).ready(function () {
            let shopId = $('#shopFilter').val();
            let timePeriod = $('#timeFilter').val();

            // Initial load
            fetchShopData(shopId, timePeriod);

            // Shop filter change
            $('#shopFilter').on('change', function () {
                shopId = $(this).val();
                fetchShopData(shopId, timePeriod);
            });

            // Time filter change
            $('#timeFilter').on('change', function () {
                timePeriod = $(this).val();
                fetchShopData(shopId, timePeriod);
            });
        });
    </script>
</body>

</html>