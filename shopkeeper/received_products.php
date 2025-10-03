<?php
session_start();
include('../includes/config.php'); // adjust path if needed
// Check if bio_id exists in the session
if (!isset($_SESSION['bio_id'])) {
    echo '<p class="text-center">Please log in to view your products.</p>';
    exit;
}

$bioid = $_SESSION['bio_id'];
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



                <!-- table start -->
                <section class="section card border rounded shadow p-3">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-3">TRANSFER PRODUCTS</h5>
                        <!-- Search Box -->
                        <div class="mb-3">
                            <input type="text" id="search_input" class="form-control"
                                placeholder="Search by item name, code, or store...">
                        </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.No</th>
                                            <th scope="col">ITEMS NAME</th>
                                            <th scope="col">ITEMS CODE</th>
                                            <th scope="col">TOTAL ITEMS QUANTITY</th>
                                            <th scope="col">SHARED QUANTITY</th>
                                            <th scope="col">FROM STORE </th>
                                            <th scope="col">TO STORE </th>
                                            <th scope="col">Transfer Status</th>




                                        </tr>
                                    </thead>
                                    <tbody class="transfer_details" id="transfer_details">


                                    </tbody>
                                </table>
                            </div>

                            <div id="pagination" class="mt-3"></div>

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

            function fetchTransferDetails(page = 1, search = '') {
                $.ajax({
                    url: "api/fetch_transfer_details.php",
                    type: "GET",
                    data: { page: page, limit: limit, search: search },
                    dataType: "json",
                    success: function (response) {
                        let rows = "";
                        if (response.status === "Fetch transfer details success") {
                            $.each(response.data, function (index, item) {
                                let statusHTML = '';
                                if (item.transfer_status === 'pending') {
                                    statusHTML = `
                                <button class="btn btn-success btn-sm accept-btn" data-id="${item.id}">Accept</button>
                                <button class="btn btn-danger btn-sm reject-btn" data-id="${item.id}">Reject</button>
                            `;
                                } else if (item.transfer_status === 'accepted') {
                                    statusHTML = `<span class="badge bg-success">Accepted</span>`;
                                } else if (item.transfer_status === 'rejected') {
                                    statusHTML = `<span class="badge bg-danger">Rejected</span>`;
                                }

                                rows += `
                            <tr>
                                <td>${item.sno}</td>
                                <td>${item.item_name}</td>
                                <td>${item.item_code}</td>
                                <td>${item.available_quantity}</td>
                                <td>${item.shared_quantity}</td>
                                <td>${item.from_store}</td>
                                <td>${item.to_store}</td>
                                <td>${statusHTML}</td>
                            </tr>
                        `;
                            });
                            $("#transfer_details").html(rows);

                            // Pagination
                            let paginationHTML = "";
                            if (response.total_pages > 1) {
                                paginationHTML += `<button class="btn btn-sm btn-light page-btn" data-page="${response.current_page - 1}" ${response.current_page === 1 ? 'disabled' : ''}>Prev</button> `;
                                for (let i = 1; i <= response.total_pages; i++) {
                                    paginationHTML += `<button class="btn btn-sm ${i === response.current_page ? 'btn-primary' : 'btn-light'} page-btn" data-page="${i}" data-search="${search}">${i}</button> `;
                                }
                                paginationHTML += `<button class="btn btn-sm btn-light page-btn" data-page="${response.current_page + 1}" ${response.current_page === response.total_pages ? 'disabled' : ''}>Next</button>`;
                                $("#pagination").html(paginationHTML);
                            }
                        } else {
                            $("#transfer_details").html(`<tr><td colspan="8" class="text-center text-danger">No records found</td></tr>`);
                            $("#pagination").html("");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText);
                        $("#transfer_details").html(`<tr><td colspan="8" class="text-center text-danger">Something went wrong!</td></tr>`);
                    }
                });
            }

            // Initial fetch
            fetchTransferDetails(1);

            // Search input keyup
            $('#search_input').on('keyup', function () {
                let search = $(this).val();
                fetchTransferDetails(1, search);
            });

            // Pagination click
            $(document).on("click", ".page-btn", function () {
                let page = $(this).data("page");
                let search = $('#search_input').val();
                fetchTransferDetails(page, search);
            });

            // Accept/Reject buttons
            $(document).on('click', '.accept-btn, .reject-btn', function () {
                let transferId = $(this).data('id');
                let action = $(this).hasClass('accept-btn') ? 'accepted' : 'rejected';
                if (!confirm(`Are you sure you want to ${action.toUpperCase()} this transfer?`)) return;
                $.ajax({
                    url: 'api/update_transfer_status.php',
                    type: 'POST',
                    data: { id: transferId, status: action },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            fetchTransferDetails(1, $('#search_input').val());
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText);
                        alert("Something went wrong: " + error);
                    }
                });
            });
        });

    </script>






</body>

</html>