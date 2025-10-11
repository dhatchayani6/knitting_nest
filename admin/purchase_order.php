<?php

include('../includes/config.php'); // adjust path if needed

// If user is not logged in, redirect to login page
// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../index.php");
//     exit();
// }


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

                        <div class="card shadow-sm rounded-3 border p-4">
                            <span class="fw-bold">PURCHASE ORDER </span>
                            <form action="" id="addpurchase" method="post" class="p-3" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6">

                                        <div class="mb-3">
                                            <label for="formFile" class="form-label">ITEM NAME</label>
                                            <input class="form-control" type="text" name="purchase_item"
                                                placeholder="Enter the Pruchase Item Name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="formFileMultiple" class="form-label">DATE OF PURCHASE </label>
                                            <input class="form-control" type="DATE" name="date_of_purchase" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">ITEM IMAGE</label>
                                            <input class="form-control" type="file" name="items_image" required>
                                        </div>



                                    </div>
                                    <div class="col-md-6">

                                        <div class="mb-3">
                                            <label for="formFile" class="form-label">ITEM CODE</label>
                                            <input class="form-control" type="text" name="purchase_code"
                                                placeholder="Enter the Purchase Code" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="formFile" class="form-label">DISTRIBUTOR NAME </label>
                                            <input class="form-control" type="text" name="distributor_name"
                                                placeholder="Enter the Distributor Name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="formFileMultiple" class="form-label">PURCHASE QUANTITY </label>
                                            <input class="form-control" type="text" name="purchase_quantity"
                                                placeholder="Enter the Purchase Quantity" required>
                                        </div>


                                    </div>
                                    <div class="col-12  text-center ">
                                        <button type="submit" name="purchase"
                                            class="btn btn-primary w-35">Purchase</button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </section>

                <!-- center content ended -->

                <!-- table start -->


                <section class="section">
                    <div class="container">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.NO</th>
                                            <th scope="col">ITEM NAME</th>

                                            <th scope="col">ITEM CODE</th>
                                            <th scope="col">DATE OF PURCHASE </th>
                                            <th scope="col">DISTRIBUTOR NAME</th>
                                            <th scope="col">PURCHASE QUANTITY</th>
                                            <th scope="col">ITEM IMAGE</th>
                                            <th scope="col">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="purchase_order">


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </article>
            <!-- table end -->



        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="js/vendor.js"></script>
    <script src="js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {

            $(document).ready(function () {
                // 1️⃣ ADD SHOPKEEPER
                $('#addpurchase').on('submit', function (e) {
                    e.preventDefault();

                    // Create a FormData object, before i create const with each input but image not value is a files means we use formdata
                    let formData = new FormData(this); // 'this' refers to the form element

                    $.ajax({
                        url: 'api/add_purchase.php',
                        type: 'POST',
                        dataType: 'json',
                        data: formData,
                        contentType: false, // important for file upload
                        processData: false, // important for file upload

                        success: function (response) {
                            console.log("Add response:", response);
                            if (response.status === "success") {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Added!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                $('#addpurchase')[0].reset();
                                purchase_order(); // Refresh table after adding
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || "Unknown error"
                                });
                            }
                        },
                        error: function (xhr) {
                            console.log("XHR error:", xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong while adding shopkeeper.'
                            });
                        }
                    });
                });
            });


            // 2️⃣ FETCH STORES (GET)
            function purchase_order() {
                $.ajax({
                    url: 'api/fetch_purchase.php', // Your API path
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        let rows = "";
                        if (response.status === "success" && response.data.length > 0) {
                            // Populate table rows if data exists
                            response.data.forEach(function (purchase, index) {
                                rows += `
                        <tr>
                            <td>${purchase.sno}</td>
                            <td>${purchase.purchase_name}</td>
                            <td>${purchase.purchase_code}</td>
                            <td>${purchase.purchase_date}</td>
                            <td>${purchase.distributor_name}</td>
                            <td>${purchase.purchase_quantity}</td>
                           <td>
    <img src="../${purchase.items_image}" alt="Item Image" style="width: 80px; height: auto;" />
</td>

                            
                            <td>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${purchase.id}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                            });
                        } else if (response.status === "success" && response.data.length === 0) {
                            // No data found
                            rows = `<tr><td colspan="8" class="text-center text-danger">No data found</td></tr>`;
                            Swal.fire({
                                icon: 'info',
                                title: 'No Data',
                                text: 'No purchase found.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            // API returned error
                            rows = `<tr><td colspan="8" class="text-center text-danger">${response.message}</td></tr>`;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }

                        $('#purchase_order').html(rows);
                    },
                    error: function (xhr) {
                        console.log("Fetch error:", xhr.responseText);
                        $('#purchase_order').html(`<tr><td colspan="8" class="text-center text-danger"> NO purchase order</td></tr>`);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to fetch purchase'
                        });
                    }
                });
            }



            purchase_order(); // Call fetch on page load

            // 3️⃣ DELETE STORE
            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This store will be deleted permanently.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'api/delete_purchase.php',
                            type: 'POST',
                            data: { id: id },
                            dataType: 'json',
                            success: function (response) {
                                if (response.status === "success") {
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    );
                                    purchase_order(); // Refresh table after deletion
                                } else {
                                    Swal.fire(
                                        'Error',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function (xhr) {
                                Swal.fire(
                                    'Error',
                                    'Something went wrong while deleting store.',
                                    'error'
                                );
                                console.log("Delete error:", xhr.responseText);
                            }
                        });
                    }
                });
            });

        });
    </script>


</body>

</html>