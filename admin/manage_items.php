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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Add Items</title>
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../stylesheet/style.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        th {
            font-weight: 400;
        }
    </style>
</head>

<body>
    <?php include('includes/sidebar.php'); ?>
    <main class="content">
        <?php include('includes/header.php'); ?>

        <div class="scroll-section">

            <div class="card shadow-sm rounded-3 border p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-3">STORE DETAILS</h6>
                    <div class="mb-3 d-flex gap-1 align-items-center">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search items...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="table_id">
                        <thead>
                            <tr class="text-center">
                                <th>S.No</th>
                                <th>STORE NAME</th>
                                <th>ITEMS NAME</th>

                                <th>ITEMS CODE</th>
                                <th>AVAILABALE QUANTITY</th>

                                <th>STOCK</th>
                                <!-- <th>LAST PURCHASE DATE</th> -->
                                <th>UNIT PRICE</th>
                                <!-- <th>SALES COUNT</th> -->
                            </tr>
                        </thead>
                        <tbody class="manageitems text-center" id="manageitems">
                            <!-- Rows populated by AJAX -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (if any) -->
                <div id="pagination" class="mt-3"></div>
            </div>



        </div>
    </main>

    <script>
        $(document).ready(function () {
            let limit = 10;

            // Fetch items and populate DataTable
            function fetchItems(page = 1, search = '') {
                $.ajax({
                    url: 'api/itemsview.php',
                    type: 'GET',
                    data: { page: page, limit: limit, search: search },
                    dataType: 'json',
                    success: function (response) {
                        let rows = "";
                        if (response.status === "success" && response.data.length > 0) {
                            response.data.forEach(function (item) {
                                rows += `
                            <tr>
                                <td>${item.sno}</td>
<td>${item.store.stores_name} - ${item.store.stores_location}</td>
                                <td>${item.item_name}</td>

                                <td>${item.item_code}</td>
                                <td>${item.item_quantity}</td>

                                <td>${item.stock_level}</td>
                                <td>${item.item_price}</td>
                            </tr>`;
                            });
                        } else {
                            rows = `<tr><td colspan="10" class="text-center text-muted">${response.message || 'No records found'}</td></tr>`;
                        }
                        $('#manageitems').html(rows);

                        // Pagination buttons
                        let paginationHTML = "";
                        if (response.total_pages > 1) {
                            paginationHTML += `<button class="btn btn-sm btn-light stock-page-btn" data-page="${response.current_page - 1}" ${response.current_page === 1 ? 'disabled' : ''}>Prev</button> `;
                            for (let i = 1; i <= response.total_pages; i++) {
                                paginationHTML += `<button class="btn btn-sm ${i === response.current_page ? 'btn-primary' : 'btn-light'} stock-page-btn" data-page="${i}">${i}</button> `;
                            }
                            paginationHTML += `<button class="btn btn-sm btn-light stock-page-btn" data-page="${response.current_page + 1}" ${response.current_page === response.total_pages ? 'disabled' : ''}>Next</button>`;
                        }
                        $("#pagination").html(paginationHTML);
                    },
                    error: function (xhr) {
                        let message = "Failed to fetch items";
                        if (xhr.status === 404) {
                            message = "No records found";
                        }
                        $('#manageitems').html(`<tr><td colspan="10" class="text-center text-muted">${message}</td></tr>`);
                        $("#pagination").html("");
                    }
                });
            }

            fetchItems(); // Load on page start

            // Search input keyup
            $('#searchInput').on('keyup', function () {
                let search = $(this).val();
                fetchItems(1, search);
            });

            // Delegate event for dynamically created buttons
            $(document).on('click', '.stock-page-btn', function () {
                const page = $(this).data('page');
                if (page && page > 0) {
                    fetchItems(page);
                }
            });
        });

    </script>
</body>

</html>