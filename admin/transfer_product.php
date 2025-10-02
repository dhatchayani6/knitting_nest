<?php
session_start();
include('../includes/config.php'); // adjust path if needed

// If user is not logged in, redirect to login page
if (!isset($_SESSION['bio_id'])) {
    header("Location: ../index.php");
    exit();
}

// fetch all shops names
$sql = "SELECT id, stores_name FROM shops";
$result = $conn->query($sql);

$shops = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $shops[] = $row;
    }
}

// Fetch items
$items = [];
$result2 = $conn->query("SELECT id, item_name FROM items");
if ($result2 && $result2->num_rows > 0) {
    while ($row2 = $result2->fetch_assoc()) {
        $items[] = $row2;
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
                <section class="section">
                    <div class="container">
                        <span class="fw-bold">TRANSFER PRODUCTS</span>
                        <form action="" id="transfer_product" method="post" class="p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ITEM NAME</label>
                                        <select class="form-select" name="item_name" id="item_id" required>
                                            <option value="">Select Item</option>
                                            <?php foreach ($items as $item): ?>
                                                <option value="<?= htmlspecialchars($item['item_name']) ?>">
                                                    <?= htmlspecialchars($item['item_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ITEM CODE</label>
                                        <input type="text" name="item_code" id="item_code" class="form-control"
                                            readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">FROM STORE</label>
                                        <select class="form-select" name="from_store_id" required>
                                            <option value="">Select Store</option>
                                            <?php foreach ($shops as $shop): ?>
                                                <option value="<?= $shop['id'] ?>">
                                                    <?= htmlspecialchars($shop['stores_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">AVAILABLE QUANTITY</label>
                                        <input type="number" name="available_quantity" id="available_quantity"
                                            class="form-control" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">SHARED QUANTITY</label>
                                        <input type="number" name="shared_quantity" id="shared_quantity"
                                            class="form-control" placeholder="Enter quantity" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">TO STORE</label>
                                        <select class="form-select" name="to_store_id" required>
                                            <option value="">Select Store</option>
                                            <?php foreach ($shops as $shop): ?>
                                                <option value="<?= $shop['id'] ?>">
                                                    <?= htmlspecialchars($shop['stores_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary w-35">Transfer Product</button>
                                </div>
                            </div>
                        </form>
                    </div>
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

    <script>
        $(document).ready(function () {
            // Fetch item details when selected
            $('#item_id').change(function () {
                var item_name = $(this).val(); // now this is the name
                if (item_name) {
                    $.ajax({
                        url: 'api/get_item_details.php',
                        type: 'POST',
                        data: { id: item_name }, // keep your PHP expecting 'id' or change key to 'item_name'
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                $('#item_code').val(res.data.item_code);
                                $('#available_quantity').val(res.data.available_quantity);
                            } else {
                                $('#item_code').val('');
                                $('#available_quantity').val('');
                                alert(res.message);
                            }
                        }
                    });
                } else {
                    $('#item_code').val('');
                    $('#available_quantity').val('');
                }
            });


            // Handle form submission
            $('#transfer_product').submit(function (e) {
                e.preventDefault();

                var available = parseInt($('#available_quantity').val());
                var shared = parseInt($('#shared_quantity').val());

                if (shared > available) {
                    alert('Cannot transfer more than available quantity!');
                    return;
                }

                $.ajax({
                    url: 'api/transfer_item.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            alert('Item transferred successfully!');
                            $('#transfer_product')[0].reset();
                            $('#item_code, #available_quantity').val('');
                        } else {
                            alert('Error: ' + res.message);
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>


</body>

</html>