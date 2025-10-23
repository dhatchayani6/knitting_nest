<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SuperAdmin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="../stylesheet/style.css">
</head>

<body>

    <?php include('includes/sidebar.php') ?>

    <!-- Main Content -->
    <main class="content">
        <?php include('includes/header.php') ?>


        <div class="scroll-section">
            <!-- Summary Cards -->
            <div class="summary-cards">
                <div class="summary-card">
                    <h6>Total Sales</h6>
                    <div class="value">8,500</div>
                </div>
                <div class="summary-card">
                    <h6>Total Revenue</h6>
                    <div class="value">$185,000</div>
                </div>
                <div class="summary-card">
                    <h6>Avg. Order Value</h6>
                    <div class="value">$75.00</div>
                </div>
                <div class="summary-card">
                    <h6>Conversion Rate</h6>
                    <div class="value">3.2%</div>
                </div>
            </div>

            <!-- Charts -->
            <section class="analytics">
                <div class="chart-card">
                    <h5>Sales Trends</h5>
                    <canvas id="salesLineChart" width="400" height="280"></canvas>
                </div>
                <div class="chart-card">
                    <h5>Category Breakdown</h5>
                    <canvas id="categoryDonutChart" width="400" height="280"></canvas>
                </div>
            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Charts (unchanged)
        const salesCtx = document.getElementById('salesLineChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                datasets: [{
                    label: 'Sales',
                    data: [35000, 45000, 40000, 47000, 46000, 49000, 54000, 60000],
                    borderColor: '#0d9488',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.3
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });

        const categoryCtx = document.getElementById('categoryDonutChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Electronics', 'Apparel', 'Home Goods', 'Books', 'Beauty'],
                datasets: [{
                    data: [25, 20, 20, 15, 20],
                    backgroundColor: ['#007b8a', '#009c66', '#44b2af', '#caeaf6', '#1f2f37'],
                    borderWidth: 0
                }]
            },
            options: { cutout: '70%', plugins: { legend: { display: false } } }
        });
    </script>
</body>

</html>