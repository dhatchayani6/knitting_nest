<?php
include "../includes/config.php";
$sql = "SELECT COUNT(*) as low_stock_count FROM items WHERE item_quantity <= stock_level";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$lowStockCount = $row['low_stock_count'];
?>

<aside class="sidebar bg-light">
    <div class="sidebar-container">
        <div class="sidebar-header">
            <div class="brand d-flex align-items-center">
                <div class="logo mb-5">
                    <img src="images/logo1.png" alt="logo" width="30" height="30">
                </div>
                <span class="text-dark">KNITTING NEST</span>
            </div>
        </div>
        <nav class="menu">
            <ul class="sidebar-menu metismenu" id="sidebar-menu">
                <!-- Dashboard -->
                <li class="active">
                    <a href="shopkeeper_index.php">
                        <i class="fa fa-home"></i> Dashboard
                    </a>
                </li>

                <!-- sales (billing) -->
                <li>
                    <a href="sales.php"><i class="fa-solid fa-cart-shopping"></i> Sales</a>
                </li>

                <!-- view products -->
                <li>
                    <a href="view_products.php"><i class="fa-solid fa-box-open"></i> View Products</a>
                </li>

                <!-- shared products -->
                <li>
                    <a href="shared_products.php"><i class="fa-solid fa-share"></i> Shared Products</a>
                </li>


                <!-- received products -->
                <li>
                    <a href="received_products.php"><i class="fa-solid fa-truck"></i> Received Products</a>
                </li>

                <!-- sales report -->
                <li>
                    <a href="sales_report.php"><i class="fa-solid fa-chart-line"></i> Sales Report</a>
                </li>




                <!-- Items
                <li>
                    <a href="#">
                        <i class="fa fa-table"></i> Items
                        <i class="fa arrow"></i>
                    </a>
                    <ul class="sidebar-nav">
                        <li><a href="view_products.php">View Products</a></li>
                    </ul>
                </li> -->

                <!-- Transfer Details
                <li>
                    <a href="#">
                        <i class="fa fa-pencil-square-o"></i> Transfer Details
                        <i class="fa arrow"></i>
                    </a>
                    <ul class="sidebar-nav">
                        <li><a href="received_products.php">Received Products</a></li>
                    </ul>
                </li> -->

                <!-- Notification -->
                <!-- <li>
                    <a href="notifications.php">
                        <i class="fa fa-bell" style="font-size: 20px; position: relative;">
                            <span id="lowStockBadge" class="badge bg-danger"
                                style="position: absolute; top: 0px; right: -182px; font-size: 11px; border-radius: 50%;">
                                <?= isset($lowStockCount) ? $lowStockCount : 0 ?>
                            </span>
                        </i> Notification
                    </a>
                </li> -->

            </ul>
        </nav>
    </div>
</aside>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<!-- JS to update notification badge -->
<script>
    function updateLowStockBadge() {
        $.ajax({
            url: "api/get_low_stock.php",
            type: "GET",
            dataType: "json",
            success: function (data) {
                const badge = $("#lowStockBadge");
                if (data.count > 0) {
                    badge.text(data.count);
                    badge.show();
                } else {
                    badge.hide();
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching notifications:", error);
            }
        });
    }

    // Run immediately + every 30 seconds
    updateLowStockBadge();
    setInterval(updateLowStockBadge, 30000);
</script>