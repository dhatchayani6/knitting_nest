<?php
session_start();
include('../includes/config.php'); // adjust path if needed



// fetch all shops names
$sql = "SELECT id, stores_name FROM shops";
$result = $conn->query($sql);

$shops = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $shops[] = $row;
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

                        <!-- Card Wrapper -->
                        <div class="card shadow-sm rounded-3 border p-4">
                            <span class="fw-bold mb-3 d-block">ADD ITEMS</span>

                            <form action="" id="add_items" method="post" class="p-3" enctype="multipart/form-data">
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">STORE NAME</label>
                                            <select class="form-select" name="store_name" required>
                                                <option value="">Select Store</option>
                                                <?php foreach ($shops as $shop): ?>
                                                    <option value="<?php echo $shop['id']; ?>">
                                                        <?php echo htmlspecialchars($shop['stores_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">ITEMS NAME</label>
                                            <input class="form-control" type="text" name="item_name"
                                                placeholder="Enter the Item name" required>
                                        </div>

                                         <div class="mb-3">
                                            <label class="form-label">SUB CATEGORY</label>
                                            <input class="form-control" type="text" name="sub_category"
                                                placeholder="Enter the Sub Category" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">ITEMS CODE</label>
                                            <input class="form-control" type="text" name="item_code"
                                                placeholder="Enter the Item code" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">ITEM IMAGE</label>
                                            <input class="form-control" type="file" name="items_image" required>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">ITEMS QUANTITY</label>
                                            <input class="form-control" type="text" name="item_quantity"
                                                placeholder="Enter the Item quantity" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">ITEMS PRICE</label>
                                            <input class="form-control" type="text" name="item_price"
                                                placeholder="Enter the Item price" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">MINIMUM STOCK LEVEL</label>
                                            <input class="form-control" type="text" name="stock_level"
                                                placeholder="Enter the Minimum stock level" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">VENDOR NAME</label>
                                            <input class="form-control" type="text" name="vendor_name"
                                                placeholder="Enter the vendor name" required>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button type="submit" name="additems" class="btn btn-primary w-50 mt-3">
                                            Add Store
                                        </button>
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

            $(document).ready(function () {
                // 1️⃣ ADD items
                $('#add_items').on('submit', function (e) {
                    e.preventDefault();

                    // Get selected store id and name
                    const store_id = $('select[name="store_name"]').val();
                    const store_name = $('select[name="store_name"] option:selected').text().trim();

                    // Create a FormData object, before i create const with each input but image not value is a files means we use formdata
                    let formData = new FormData(this); // 'this' refers to the form element

                    formData.append('store_id', store_id); // add store_id
                    formData.append('store_name', store_name); // add store_name

                    $.ajax({
                        url: 'api/items.php',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        contentType: false, // important for file upload
                        processData: false, // important for file upload

                        success: function (response) {
                            console.log("Add response:", response);
                            if (response.status === "success") {
                                alert(response.message);
                                $('#add_items')[0].reset();
                                // fetch_shopkeepers(); // you can write a function to refresh table
                            } else {
                                alert("Error: " + (response.message || "Unknown error"));
                            }
                        },
                        error: function (xhr) {
                            console.log("XHR error:", xhr.responseText);
                            alert("Something went wrong while adding Items.");
                        }
                    });
                });
            });




        });
    </script>


</body>

</html>