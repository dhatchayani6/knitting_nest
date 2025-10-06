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
$result2 = $conn->query("SELECT id, item_name,store_name FROM items");
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

                     <div class="card shadow-sm rounded-3 border p-4">
                        <span class="fw-bold">ITEMS TRANSFER </span>
                        <form action="" id="transfer_product" method="post" class="p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ITEM NAME</label>
                                        <select class="form-select" name="item_name" id="item_id" required>
                                            <option value="">Select Item</option>
                                            <?php foreach ($items as $item): ?>
                                                <option value="<?= htmlspecialchars($item['id']) ?>">
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
                                        <!-- Hidden input to store store ID -->
<input type="hidden" name="from_store_id" id="from_store_id">

<!-- Display input for user to see store name -->
<input type="text" id="from_store_name" class="form-control" readonly>

                                        <!-- <input type="text" name="from_store_id" id="from_store_id" class="form-control" -->
                                            <!-- readonly> -->
                                    </div>

                                     <div class="mb-3">
                                            <label class="form-label">ITEM IMAGE</label>
                                            <input class="form-control" type="file" name="items_image" required>
                                        </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">AVAILABLE QUANTITY</label>
                                        <input type="number" name="available_quantity" id="available_quantity"
                                            class="form-control" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">QUANTITY</label>
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

                                     <div class="mb-3">
                                        <label class="form-label">SUB_CATEGORY</label>
                                        <input type="text" name="sub_category" id="sub_category"
                                            class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary w-35">Transfer Product</button>
                                </div>
                            </div>
                        </form>
                     </div>
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

   
  $('#item_id').change(function () {
    var id = $(this).val();
    if (id) {
        $.ajax({
            url: 'api/get_item_details.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    var first = res.data[0];

                    $('#item_code').val(first.item_code);
                    $('#available_quantity').val(first.available_quantity);
                     $('#sub_category').val(first.sub_category);

                    // ✅ Fill display and hidden inputs
                    $('#from_store_name').val(first.store_name); // visible to user
                    $('#from_store_id').val(first.store_id);     // sent to DB

                    if (res.data.length > 1) {
                        // multiple items logic (optional)
                        $('#multiple_items_container').show();
                        var select = $('#specific_item_select');
                        select.empty();
                        res.data.forEach(item => {
                            select.append(`
                                <option value="${item.id}" 
                                        data-code="${item.item_code}" 
                                        data-qty="${item.available_quantity}" 
                                         data-sub_category="${item.sub_category}" 
                                        data-store-id="${item.store_id}" 
                                        data-store-name="${item.store_name}">
                                    Code: ${item.item_code}, Qty: ${item.available_quantity}, Store: ${item.store_name}, sub_category: ${item.sub_category}
                                </option>`);
                        });
                    } else {
                        $('#multiple_items_container').hide();
                    }
                } else {
                    $('#item_code, #available_quantity, #sub_category, #from_store_name, #from_store_id').val('');
                    alert(res.message);
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Error fetching item details.');
            }
        });
    } else {
        $('#item_code, #available_quantity, #sub_category, #from_store_name, #from_store_id').val('');
        $('#multiple_items_container').hide();
    }
});

// When multiple items selected
$(document).on('change', '#specific_item_select', function () {
    var selected = $(this).find(':selected');
    $('#item_code').val(selected.data('code'));
    $('#available_quantity').val(selected.data('qty'));
    $('#from_store_name').val(selected.data('store-name')); // display
    $('#from_store_id').val(selected.data('store-id'));     // actual value
});

    // Form submission remains the same
    $('#transfer_product').submit(function (e) {
        e.preventDefault();

        var available = parseInt($('#available_quantity').val());
        var shared = parseInt($('#shared_quantity').val());

        if (shared > available) {
            alert('Cannot transfer more than available quantity!');
            return;
        }
        
        
    var form = $(this)[0]; // get the form DOM element
    var formData = new FormData(form); // Create FormData object from form

        $.ajax({
            url: 'api/transfer_item.php',
            type: 'POST',
            data: formData,
            processData: false,  // Important! Prevent jQuery from converting data into a query string
            contentType: false,  // Important! Let the browser set the correct Content-Type including multipart/form-data boundary
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    alert('Item transferred successfully!');
                    $('#transfer_product')[0].reset();
                    $('#item_code, #available_quantity ,#from_store_id').val('');
                    $('#multiple_items_container').hide();
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