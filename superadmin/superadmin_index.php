<?php
include('../config/config.php');

// 1️⃣ Overall Sales
$salesQuery = $conn->query("SELECT COUNT(CAST(item_quantity AS UNSIGNED)) AS total_sales FROM sales");
$salesData = $salesQuery->fetch_assoc();
$totalSales = $salesData['total_sales'] ?? 0;

// 2️⃣ Total Stock
$stockQuery = $conn->query("SELECT COUNT(CAST(purchase_quantity AS UNSIGNED)) AS total_stock FROM purchase_order");
$stockData = $stockQuery->fetch_assoc();
$totalStock = $stockData['total_stock'] ?? 0;

// 3️⃣ Stock Bought in last 30 days
$boughtQuery = $conn->query("SELECT COUNT(CAST(purchase_quantity AS UNSIGNED)) AS total_bought 
    FROM purchase_order 
    WHERE purchase_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$boughtData = $boughtQuery->fetch_assoc();
$totalBought = $boughtData['total_bought'] ?? 0;

// 4️⃣ Latest purchase orders for table
$purchaseQuery = $conn->query("SELECT * FROM purchase_order ORDER BY purchase_date DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SuperAdmin Dashboard</title>
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../stylesheet/style.css">

    <style>
        .summary-cards .summary-card {
            flex: 1;
            position: relative;
            overflow: hidden;
            color: #fff;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            padding: 20px;
            border-radius: 10px;
        }

        .summary-cards .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .summary-cards .summary-card i {
            font-size: 3rem;
            opacity: 0.25;
            position: absolute;
            right: 20px;
            bottom: 20px;
        }
    </style>
</head>

<body>

    <?php include('includes/sidebar.php'); ?>
    <main class="content">
        <?php include('includes/header.php'); ?>

        <div class="scroll-section p-3">

            <!-- Summary Cards -->
            <div class="summary-cards d-flex gap-3 mb-4 flex-wrap">
                <!-- Overall Sales -->
                <div class="summary-card" style="background: linear-gradient(135deg, #0d9488, #14b8a6);"
                    data-bs-toggle="modal" data-bs-target="#salesModal" onclick="loadSalesData('sales')">
                    <h6 class="text-light">Overall Sales</h6>
                    <div class="value fs-3" id="salesCount"><?= number_format($totalSales) ?></div>
                    <i class="bi bi-cart-fill"></i>
                </div>

                <!-- Total Stock -->
                <div class="summary-card" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);"
                    data-bs-toggle="modal" data-bs-target="#salesModal" onclick="loadSalesData('stock')">
                    <h6 class="text-light">Total Stock</h6>
                    <div class="value fs-3" id="stockCount"><?= number_format($totalStock) ?></div>
                    <i class="bi bi-box-seam"></i>
                </div>

                <!-- Stock Bought -->
                <div class="summary-card" style="background: linear-gradient(135deg, #facc15, #fcd34d); color:#000;"
                    data-bs-toggle="modal" data-bs-target="#salesModal" onclick="loadSalesData('bought')">
                    <h6 class="text-dark">Stock Bought (Last 30 Days)</h6>
                    <div class="value fs-3" id="boughtCount"><?= number_format($totalBought) ?></div>
                    <i class="bi bi-truck"></i>
                </div>

            </div>

            <!-- Purchase Orders Table -->
            <div class="card p-3 shadow-sm m-3">
                <h5 class="mb-3">Latest Purchase Orders</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>S no</th>
                                <th>Purchase Name</th>
                                <th>Purchase Code</th>
                                <th>Purchase Date</th>
                                <th>Distributor</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($purchaseQuery->num_rows > 0) {
                                $i = 1;
                                while ($row = $purchaseQuery->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($row['purchase_name']) ?></td>
                                        <td><?= htmlspecialchars($row['purchase_code']) ?></td>
                                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($row['purchase_date']))) ?></td>
                                        <td><?= htmlspecialchars($row['distributor_name']) ?></td>
                                        <td><?= htmlspecialchars($row['purchase_quantity']) ?></td>
                                        <td>Rs.<?= htmlspecialchars($row['purchase_price']) ?></td>
                                    </tr>
                                <?php endwhile;
                            } else { ?>
                                <tr>
                                    <td colspan="7" class="text-center text-danger fw-bold">No Data Found</td>
                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Sales Details Modal -->
    <div class="modal fade" id="salesModal" tabindex="-1" aria-labelledby="salesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="salesModalLabel">Sales & Stock Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="salesModalContent" class="text-center p-3">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function animateCounter(id, end) {
            let current = 0;
            const increment = Math.ceil(end / 100);
            const obj = document.getElementById(id);
            const timer = setInterval(() => {
                current += increment;
                if (current >= end) {
                    obj.innerText = end.toLocaleString();
                    clearInterval(timer);
                } else {
                    obj.innerText = current.toLocaleString();
                }
            }, 15);
        }

        animateCounter("salesCount", <?= $totalSales ?>);
        animateCounter("stockCount", <?= $totalStock ?>);
        animateCounter("boughtCount", <?= $totalBought ?>);

        // Load data dynamically in modal
        function loadSalesData(type) {
            const container = document.getElementById('salesModalContent');
            container.innerHTML = `
        <div class="text-center p-3">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading data...</p>
        </div>`;

            fetch('fetch_sales_data.php?type=' + type)
                .then(response => response.text())
                .then(html => container.innerHTML = html)
                .catch(() => container.innerHTML = `<p class='text-danger text-center'>Error loading data.</p>`);
        }

    </script>
</body>

</html>