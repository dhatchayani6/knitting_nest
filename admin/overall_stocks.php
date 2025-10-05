<?php

include('../includes/config.php'); // adjust path if needed

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
    <style>
        tr th {
            font-size: 15px;
            font-weight: 600;
        }
    </style>

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


                <div class="d-flex justify-content-between align-items-center mb-3">
                    <!-- <button id="deleteAllBtn" class="btn btn-danger btn-sm">
                        <i class="fa fa-trash"></i> Delete All
                    </button> -->
                </div>
                <!-- table start -->
                <section class="section card shadow border p-3 mt-4">
                    <div class="container">
                        <h5 class="mb-3">OVERALL STOCK DETAILS</h5>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>STORE NAME</th>
                                            <th> NAME</th>
                                            <th> CODE</th>
                                            <th>AVAILABLE QUANTITY</th>
                                            <th>UNIT PRICE</th>
                                            <th>STOCK </th>
                                            <th>IMAGE</th>
                                        </tr>
                                    </thead>
                                    <tbody id="stock_details">
                                    </tbody>
                                </table>
                            </div>

                            <div id="stock_pagination" class="mt-3"></div>
                        </div>
                    </div>
                </section>

                <!-- table end -->
            </article>




        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="js/vendor.js"></script>
    <script src="js/app.js"></script>


    <script>
        $(document).ready(function () {
            let limit = 10;

            function fetchStockDetails(page = 1) {
                $.ajax({
                    url: "api/fetch_items.php",
                    type: "GET",
                    data: { page: page, limit: limit },
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            let rows = "";
                            $.each(response.data, function (index, item) {
                                rows += `
                            <tr>
                                <td>${item.sno}</td>
                                <td>${item.store_name}</td>
                                <td>${item.item_name}</td>
                                <td>${item.item_code}</td>
                                <td>${item.item_quantity}</td>
                                <td>${item.item_price}</td>
                                <td>${item.stock_level}</td>
                                <td><img src="../${item.items_image}" alt="${item.item_name}" width="50"></td>
                            </tr>
                        `;
                            });
                            $("#stock_details").html(rows);

                            // Pagination buttons
                            let paginationHTML = "";
                            if (response.total_pages > 1) {
                                paginationHTML += `<button class="btn btn-sm btn-light stock-page-btn" data-page="${response.current_page - 1}" ${response.current_page === 1 ? 'disabled' : ''}>Prev</button> `;
                                for (let i = 1; i <= response.total_pages; i++) {
                                    paginationHTML += `<button class="btn btn-sm ${i === response.current_page ? 'btn-primary' : 'btn-light'} stock-page-btn" data-page="${i}">${i}</button> `;
                                }
                                paginationHTML += `<button class="btn btn-sm btn-light stock-page-btn" data-page="${response.current_page + 1}" ${response.current_page === response.total_pages ? 'disabled' : ''}>Next</button>`;
                            }
                            $("#stock_pagination").html(paginationHTML);
                        } else {
                            $("#stock_details").html(`<tr><td colspan="9" class="text-center text-danger">No records found</td></tr>`);
                            $("#stock_pagination").html("");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log("Error:", error);
                        $("#stock_details").html(`<tr><td colspan="9" class="text-center text-danger">Something went wrong!</td></tr>`);
                        $("#stock_pagination").html("");
                    }
                });
            }

            // Initial fetch
            fetchStockDetails(1);

            // Handle pagination
            $(document).on("click", ".stock-page-btn", function () {
                let page = $(this).data("page");
                fetchStockDetails(page);
            });
        });
    </script>







</body>

</html>