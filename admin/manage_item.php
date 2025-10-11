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
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />

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
                <!-- table start -->
                <section class="section">
                    <div class="container">

                        <div class="row">

                            <!-- Card Wrapper -->
                            <div class="card shadow-sm rounded-3 border p-3">
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-3">STORE DETAILS</h5>
                                    <div class="mb-3">
                                        <input type="text" id="searchInput" class="form-control"
                                            placeholder="Search items...">
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table_id">
                                        <thead class="table-light">
                                            <tr class="text-center">
                                                <th>S.No</th>
                                                <th>STORE NAME</th>
                                                <th>ITEMS CODE</th>
                                                <th>ITEMS NAME</th>
                                                <th>STOCK</th>
                                                <th>LAST PURCHASE DATE</th>
                                                <th>AVAILABALE QUANTITY</th>
                                                <th>UNIT PRICE</th>
                                                <th>SALES COUNT</th>
                                                <th>ACTIONS</th>
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
                    </div>
                </section>
                <!-- table end -->

                <!-- table end -->
            </article>


            <!-- edit model start -->
            <div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterLabel">Edit Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editItemForm">
                                <input type="hidden" id="editItemId" name="id">

                                <div class="mb-3">
                                    <label for="store_name" class="form-label">Store Name</label>
                                    <input type="text" class="form-control" id="store_name" name="store_name">
                                </div>

                                <div class="mb-3">
                                    <label for="item_code" class="form-label">Item Code</label>
                                    <input type="text" class="form-control" id="item_code" name="item_code" required>
                                </div>

                                <div class="mb-3">
                                    <label for="item_name" class="form-label">Item Name</label>
                                    <input type="text" class="form-control" id="item_name" name="item_name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="stock_level" class="form-label">Stock </label>
                                    <input type="number" class="form-control" id="stock_level" name="stock_level"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="purchase_date" class="form-label">Purchase Date</label>
                                    <input type="date" class="form-control" id="purchase_dates" name="purchase_date"
                                        readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="item_quantity" class="form-label">Available Quantity</label>
                                    <input type="number" class="form-control" id="item_quantity" name="item_quantity"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="item_price" class="form-label">Unit Price</label>
                                    <input type="number" class="form-control" id="item_price" name="item_price"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="sales_count" class="form-label">Sales Count</label>
                                    <input type="number" class="form-control" id="sales_count" name="sales_count"
                                        readonly>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="saveChanges">Save Changes</button>
                        </div>
                    </div>
                </div>

            </div>
            <!-- edit model end -->

        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="js/vendor.js"></script>
    <script src="js/app.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    <script>
        $(document).ready(function () {
            let limit = 10;

            // Fetch items and populate DataTable
            function fetchItems(page = 1,search = '') {
                $.ajax({
                    url: 'api/itemsview.php',
                    type: 'GET',
                    data: { page: page,limit: limit,search: search },  // Send page number to backend
                    dataType: 'json',
                    success: function (response) {
                        let rows = "";
                        if (response.status === "success" && response.data.length > 0) {
                            response.data.forEach(function (item) {
                                rows += `
                        <tr>
                            <td>${item.sno}</td>
                            <td>${item.store_name}</td>
                            <td>${item.item_code}</td>
                            <td>${item.item_name}</td>
                            <td>${item.stock_level}</td>
                            <td>${item.purchase_date}</td>
                            <td>${item.item_quantity}</td>
                            <td>${item.item_price}</td>
                            <td>${item.sales_count}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary edit-item" data-id="${item.id}" data-bs-toggle="modal" data-bs-target="#exampleModalCenter">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-item" data-id="${item.id}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>`;
                            });
                        } else {
                            rows = `<tr><td colspan="10" class="text-center">${response.message || 'No records found'}</td></tr>`;
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
                        console.log("Fetch error:", xhr.responseText);
                        $('#manageitems').html(`<tr><td colspan="10" class="text-center">Failed to fetch items</td></tr>`);
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


            // Edit modal
            $(document).on('click', '.edit-item', function () {
                const id = $(this).data('id');
                $.ajax({
                    url: 'api/get_singleitems.php',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === "success") {
                            const item = response.data;
                            $('#editItemId').val(item.id);
                            $('#store_name').val(item.store_name);
                            $('#item_name').val(item.item_name);
                            $('#item_code').val(item.item_code);
                            $('#stock_level').val(item.stock_level);
                            // $("#purchase_dates").val(item.purchase_date);
                            // Fix purchase date for date input
                            if (item.purchase_date) {
                                const parts = item.purchase_date.split('-');
                                const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                                $("#purchase_dates").val(formattedDate);
                            } else {
                                $("#purchase_dates").val('');
                            }
                            $('#item_quantity').val(item.item_quantity);
                            $('#item_price').val(item.item_price);
                            $('#sales_count').val(item.sales_count);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: "Failed to fetch item details",
                        });
                    }
                });
            });

            // Save Changes - update item
            $('#saveChanges').click(function () {
                $.ajax({
                    url: 'api/updateitemS.php',
                    type: 'POST',
                    data: $('#editItemForm').serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === "success") {
                            $('#exampleModalCenter').modal('hide');
                            fetchItems(); // Reload table
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: 'Item updated successfully',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update item',
                        });
                    }
                });
            });

            // Delete Item
            $(document).on('click', '.delete-item', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'api/deleteitems.php',
                            type: 'POST',
                            data: { id: id },
                            dataType: 'json',
                            success: function (response) {
                                if (response.status === "success") {
                                    fetchItems();
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: 'Item deleted successfully',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                    });
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to delete item',
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>


</body>

</html>