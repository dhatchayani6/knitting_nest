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
                       <?php echo "<h4 class='mb-2'> <strong>" . htmlspecialchars($shopName) . "</strong></h4>"?>
                        <div class="row  justify-content-center">
                            
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
                                        <p class="card-text">COUNT:<?php echo $shopid?></p>

                                    </div>
                                </div>
                            </div>

                            <!-- <div class="col-sm-6 col-md-4 mb-3">


                                <div class=" card shadow p-3 mb-5 bg-light rounded">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">EXAM SCHEDULE LIST </h5>
                                        <h6 class="card-subtitle mb-2 text-body-secondary">EXAM DETAILS</h6>
                                        <p class="card-text">COUNT:</p>

                                    </div>
                                </div>
                            </div> -->

                            <!-- <div class="col-sm-6 col-md-4 mb-3">


                                <div class=" card shadow p-3 mb-5 bg-light rounded">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">STUDENTS MARKS LIST </h5>
                                        <h6 class="card-subtitle mb-2 text-body-secondary">MARKS DETAILS</h6>
                                        <p class="card-text">COUNT:</p>

                                    </div>
                                </div>
                            </div> -->

                            <!-- <div class="col-sm-6 col-md-4 mb-3">


                                <div class=" card shadow p-3 mb-5 bg-light rounded">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">EXTERNAL DETAILS </h5>
                                        <h6 class="card-subtitle mb-2 text-body-secondary">EXTERNAL DETAILS</h6>
                                        <p class="card-text">COUNT:</p>

                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </section>
                <!-- <?php include('includes/center-content.php') ?> -->
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
</body>

</html>