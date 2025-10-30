<?php
session_start();
include('../config/config.php');
if (!isset($_SESSION['bio_id'])) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Shopkeeper Dashboard</title>
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../stylesheet/style.css">

</head>

<body>

    <?php include('includes/sidebar.php') ?>
    <main class="content">
        <?php include('includes/header.php'); ?>

        <div class="container-fluid py-3">
            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="p-3 d-flex justify-content-between align-items-center border rounded shadow-sm">
                        <div>
                            <h6>Total Sales</h6>
                            <div id="totalSales" class="fw-bold fs-5">0</div>
                        </div>
                        <i class="bi bi-cart-fill fs-2 text-primary"></i>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 d-flex justify-content-between align-items-center border rounded shadow-sm">
                        <div>
                            <h6>Total Revenue</h6>
                            <div id="totalRevenue" class="fw-bold fs-5">Rs.0</div>
                        </div>
                        <i class="bi bi-cash-stack fs-2 text-success"></i>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 d-flex justify-content-between align-items-center border rounded shadow-sm">
                        <div>
                            <h6>Total Stock</h6>
                            <div id="totalStock" class="fw-bold fs-5">0</div>
                        </div>
                        <i class="bi bi-box-seam fs-2 text-warning"></i>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="d-flex justify-content-end mb-3">
                <select id="salesFilter" class="form-select w-auto">
                    <option value="daily">Today</option>
                    <option value="weekly">This Week</option>
                    <option value="monthly" selected>This Month</option>
                </select>
            </div>

            <!-- Top Products Table -->
            <section class="report-table">
                <h5 class="mb-3">Top Selling Products</h5>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
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
                                <td colspan="5" class="text-center text-muted">Loading data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <script>
        function fetchShopData(period = 'monthly') {
            $.ajax({
                url: 'api/fetch_shop_report.php',
                type: 'GET',
                data: { period: period },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        $('#totalSales').text(response.summary.total_sales);
                        $('#totalRevenue').text('Rs.' + response.summary.total_revenue.toFixed(2));
                        $('#totalStock').text(response.summary.total_stock);

                        let rows = '';
                        if (response.topProducts.length > 0) {
                            response.topProducts.forEach(item => {
                                rows += `
                                    <tr>
                                        <td>${item.item_name}</td>
                                        <td>${item.sub_category || '—'}</td>
                                        <td>Rs.${item.total_revenue.toFixed(2)}</td>
                                        <td>${item.available_quantity}</td>
                                    </tr>`;
                            });
                        } else {
                            rows = `<tr><td colspan="5" class="text-center text-muted">No products found.</td></tr>`;
                        }
                        $('#topProductsTable').html(rows);
                    } else {
                        alert('Error fetching data');
                    }
                },
                error: function () {
                    alert('AJAX Error');
                }
            });
        }

        $(document).ready(function () {
            fetchShopData('monthly'); // default
            $('#salesFilter').on('change', function () {
                fetchShopData($(this).val());
            });
        });
    </script>

</body>

</html>