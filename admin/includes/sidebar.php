<?php
include "../includes/config.php";
$sql = "SELECT COUNT(*) as low_stock_count FROM items WHERE item_quantity <= stock_level";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$lowStockCount = $row['low_stock_count'];
?>
<aside class="sidebar">
    <div class="sidebar-container">
        <div class="sidebar-header">
            <div class="brand d-flex align-items-center">
                <div class="logo mb-5">
                    <img src="images/logo1.png" alt="logo" width="30" height="30">
                </div>
                <span>KNITTING NEST</span>
            </div>
        </div>
        <nav class="menu">
            <ul class="sidebar-menu metismenu" id="sidebar-menu">
                <li class="active">
                    <a href="admin_index.php">
                        <i class="fa fa-home"></i> Dashboard </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-table"></i> Items
                        <i class="fa arrow"></i>
                    </a>
                    <ul class="sidebar-nav">
                        <li>
                            <a href="additems.php"> Add items </a>
                        </li>
                        <li>
                            <a href="manage_item.php"> Manage items </a>
                        </li>
                        <li>
                            <a href="overall_stocks.php"></i>Overall Stock Details </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <i class="fa-solid fa-cart-shopping"></i>Stores
                        <i class="fa arrow"></i>
                    </a>
                    <ul class="sidebar-nav">
                        <li>
                            <a href="add_store.php"> Add store </a>
                        </li>
                        <li>
                            <a href="add_shopkeeper.php"> Add Shopkeeper </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="#">
                        <i class="fa fa-pencil-square-o"></i> Transfer Details
                        <i class="fa arrow"></i>
                    </a>
                    <ul class="sidebar-nav">
                        <li>
                            <a href="transfer_product.php"> Transfer products </a>
                        </li>
                        <li>
                            <a href="transfer_details.php"> List of Transfer Details </a>
                        </li>

                    </ul>

                </li>

                <!-- <li>
                    <a href="notification.php">
                        <i class="fa fa-bell" style="font-size: 20px; position: relative;">
                            <span id="lowStockBadge" class="badge bg-danger" style="    position: absolute;
    top: 0px;
    right: -182px;
    font-size: 11px;
    border-radius: 50%; <?= $lowStockCount > 0 ? '' : 'display:none;' ?>">
                                <?= $lowStockCount ?>
                            </span>
                        </i> Notification
                    </a>
                </li> -->


                <li>
                    <a href="overall_sales_report.php">
                        <i class="fa-solid fa-file"></i> Sales Report</a>
                </li>



                <!-- <li>
                    <a href="view_studentsmarks.php">
                       <i class="fa-solid fa-circle-info"></i>Students Marks</a>
                </li>
                 <li>
                    <a href="view_externaldetails.php">
                        <i class="fa-solid fa-file"></i> Examineer_Details</a>
                </li>
                
                <li>
                    <a href="../excel_examineer_details.php">
                        <i class="fa-solid fa-download"></i> Download Examineer_Details </a>
                </li>
                <li>
                    <a href="../download_marks.php">
                        <i class="fa-solid fa-download"></i> Download Students_Marks </a>
                </li> -->

                <!-- <li>
                                    <a href="#">
                                        <i class="fa fa-file-text-o"></i> Pages
                                        <i class="fa arrow"></i>
                                    </a>
                                    <ul class="sidebar-nav">
                                        <li>
                                            <a href="login.html"> Login </a>
                                        </li>
                                        <li>
                                            <a href="signup.html"> Sign Up </a>
                                        </li>
                                        <li>
                                            <a href="reset.html"> Reset </a>
                                        </li>
                                        <li>
                                            <a href="error-404.html"> Error 404 App </a>
                                        </li>
                                        <li>
                                            <a href="error-404-alt.html"> Error 404 Global </a>
                                        </li>
                                        <li>
                                            <a href="error-500.html"> Error 500 App </a>
                                        </li>
                                        <li>
                                            <a href="error-500-alt.html"> Error 500 Global </a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-sitemap"></i> Menu Levels
                                        <i class="fa arrow"></i>
                                    </a>
                                    <ul class="sidebar-nav">
                                        <li>
                                            <a href="#"> Second Level Item
                                                <i class="fa arrow"></i>
                                            </a>
                                            <ul class="sidebar-nav">
                                                <li>
                                                    <a href="#"> Third Level Item </a>
                                                </li>
                                                <li>
                                                    <a href="#"> Third Level Item </a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a href="#"> Second Level Item </a>
                                        </li>
                                        <li>
                                            <a href="#"> Second Level Item
                                                <i class="fa arrow"></i>
                                            </a>
                                            <ul class="sidebar-nav">
                                                <li>
                                                    <a href="#"> Third Level Item </a>
                                                </li>
                                                <li>
                                                    <a href="#"> Third Level Item </a>
                                                </li>
                                                <li>
                                                    <a href="#"> Third Level Item
                                                        <i class="fa arrow"></i>
                                                    </a>
                                                    <ul class="sidebar-nav">
                                                        <li>
                                                            <a href="#"> Fourth Level Item </a>
                                                        </li>
                                                        <li>
                                                            <a href="#"> Fourth Level Item </a>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li> -->
                <!-- <li>
                                    <a href="screenful.html">
                                        <i class="fa fa-bar-chart"></i> Agile Metrics
                                        <span class="label label-screenful">by Screenful</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://github.com/modularcode/modular-admin-html">
                                        <i class="fa fa-github-alt"></i> Theme Docs </a>
                                </li> -->
            </ul>
        </nav>
    </div>
    <!-- <footer class="sidebar-footer">
                        <ul class="sidebar-menu metismenu" id="customize-menu">
                            <li>
                                <ul>
                                    <li class="customize">
                                        <div class="customize-item">
                                            <div class="row customize-header">
                                                <div class="col-4"> </div>
                                                <div class="col-4">
                                                    <label class="title">fixed</label>
                                                </div>
                                                <div class="col-4">
                                                    <label class="title">static</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label class="title">Sidebar:</label>
                                                </div>
                                                <div class="col-4">
                                                    <label>
                                                        <input class="radio" type="radio" name="sidebarPosition" value="sidebar-fixed">
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="col-4">
                                                    <label>
                                                        <input class="radio" type="radio" name="sidebarPosition" value="">
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label class="title">Header:</label>
                                                </div>
                                                <div class="col-4">
                                                    <label>
                                                        <input class="radio" type="radio" name="headerPosition" value="header-fixed">
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="col-4">
                                                    <label>
                                                        <input class="radio" type="radio" name="headerPosition" value="">
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label class="title">Footer:</label>
                                                </div>
                                                <div class="col-4">
                                                    <label>
                                                        <input class="radio" type="radio" name="footerPosition" value="footer-fixed">
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="col-4">
                                                    <label>
                                                        <input class="radio" type="radio" name="footerPosition" value="">
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="customize-item">
                                            <ul class="customize-colors">
                                                <li>
                                                    <span class="color-item color-red" data-theme="red"></span>
                                                </li>
                                                <li>
                                                    <span class="color-item color-orange" data-theme="orange"></span>
                                                </li>
                                                <li>
                                                    <span class="color-item color-green active" data-theme=""></span>
                                                </li>
                                                <li>
                                                    <span class="color-item color-seagreen" data-theme="seagreen"></span>
                                                </li>
                                                <li>
                                                    <span class="color-item color-blue" data-theme="blue"></span>
                                                </li>
                                                <li>
                                                    <span class="color-item color-purple" data-theme="purple"></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                                <a href="#">
                                    <i class="fa fa-cog"></i> Customize </a>
                            </li>
                        </ul>
                    </footer> -->
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