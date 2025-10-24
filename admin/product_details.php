<?php
session_start();
include('../config/config.php'); // adjust path if needed
// Check if user is logged in
if (!isset($_SESSION['bio_id'])) {
    // Redirect to index page if not logged in
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin</title>
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="../stylesheet/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>
    <?php include('includes/sidebar.php') ?>

    <main class="content">
        <?php include('includes/header.php'); ?>

        <div class="scroll-section">

            <section class="section card shadow border p-3 mt-4">
                <div class="container">
                    <div class="row mb-3 g-2">
                        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                            <button class="btn btn-primary w-100 stock-filter-btn" data-filter="all">All</button>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                            <button class="btn btn-success w-100 stock-filter-btn" data-filter="in-stock">In
                                Stock</button>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                            <button class="btn btn-warning w-100 stock-filter-btn" data-filter="low-stock">Low
                                Stock</button>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                            <button class="btn btn-danger w-100 stock-filter-btn" data-filter="out-of-stock">Out of
                                Stock</button>
                        </div>
                    </div>


                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>STORE NAME</th>
                                    <th>NAME</th>
                                    <th>CODE</th>
                                    <th>AVAILABLE QUANTITY</th>
                                    <th>UNIT PRICE</th>
                                    <th>STOCK LEVEL</th>
                                    <th>IMAGE</th>
                                </tr>
                            </thead>
                            <tbody id="stock_details"></tbody>
                        </table>
                    </div>

                    <div id="stock_pagination" class="mt-3"></div>
                </div>
            </section>








        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/vendor.js"></script>
    <script src="js/app.js"></script>
    <script>
        $(document).ready(function () {
            let limit = 10;
            let currentFilter = 'all'; // default filter

            function fetchStockDetails(page = 1, filter = 'all') {
                $.ajax({
                    url: "api/product_details.php",
                    type: "GET",
                    data: { page: page, limit: limit, filter: filter },
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            let rows = "";
                            $.each(response.data, function (index, item) {
                                rows += `
                            <tr>
                                <td>${item.sno}</td>
<td>${item.store.stores_name} - ${item.store.stores_location}</td>
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

                            // Pagination
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
                            // Use backend message here
                            $("#stock_details").html(`<tr><td colspan="8" class="text-center text-danger">${response.message}</td></tr>`);
                            $("#stock_pagination").html("");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log("Error:", error);
                        $("#stock_details").html(`<tr><td colspan="8" class="text-center text-danger">Something went wrong!</td></tr>`);
                        $("#stock_pagination").html("");
                    }
                });
            }

            // Initial fetch
            fetchStockDetails(1, currentFilter);

            // Handle pagination
            $(document).on("click", ".stock-page-btn", function () {
                let page = $(this).data("page");
                fetchStockDetails(page, currentFilter);
            });

            // Handle filter buttons
            $(document).on("click", ".stock-filter-btn", function () {
                currentFilter = $(this).data("filter"); // update filter
                fetchStockDetails(1, currentFilter); // reset to page 1
            });
        });

    </script>
</body>

</html>