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
                <section class="section ">
                    <div class="container">
                        <div class="row">

                            <div class="card shadow-sm rounded-3 border p-3">
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-3">TRANSFER PRODUCTS</h5>
                                    <div class="mb-3">
                                        <input type="text" id="searchInput" class="form-control"
                                            placeholder="Search items...">
                                    </div>
                                </div>
                                
                                    <div class="table-responsive ">
                                        <table class="table table-striped text-center " id="table_id">
                                            <thead  class="table-light ">
                                                <tr>
                                                    <th scope="col">S.No</th>
                                                    <th scope="col">ITEMS NAME</th>
                                                    <th scope="col">ITEMS CODE</th>

                                                    <th scope="col">TOTAL ITEMS QUANTITY</th>
                                                    <th scope="col">SHARED QUANTITY</th>
                                                    <!-- <th scope="col"> ITEMS PRICE</th> -->

                                                    <th scope="col">FROM STORE </th>
                                                    <th scope="col">TO STORE </th>
                                                    <th scope="col">TRANSFER DATE </th>
                                                    <th scope="col">TRANSFER STATUS</th>

                                                </tr>
                                            </thead>
                                            <tbody class="transfer_details" id="transfer_details">


                                            </tbody>
                                        </table>
                                    </div>

                                    <div id="pagination" class="mt-3"></div>

                                
                            </div>
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
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        $(document).ready(function() {
            let limit = 10;

            function fetchTransferDetails(page = 1) {
                $.ajax({
                    url: "api/fetch_transfer_details.php", // your API for transfer table
                    type: "GET",
                    data: {
                        page: page,
                        limit: limit
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "Fetch transfer details success") {
                            let rows = "";
                            $.each(response.data, function(index, item) {
                                rows += `
                            <tr>
                                <td>${item.sno}</td>
                                <td>${item.item_name}</td>
                                
                                <td>${item.item_code}</td>
                            

                                <td>${item.available_quantity}</td>
                                <td>${item.shared_quantity}</td>

                                <td>${item.from_store}</td>
                                <td>${item.to_store}</td>
                                 <td>${item.created_at}</td>
                                <td>${item.transfer_status}</td>

                            </tr>
                        `;
                            });
                            $("#transfer_details").html(rows);

                            // build pagination
                            let paginationHTML = "";
                            if (response.total_pages > 1) {
                                paginationHTML += `<button class="btn btn-sm btn-light page-btn" data-page="${response.current_page - 1}" ${response.current_page === 1 ? 'disabled' : ''}>Prev</button> `;
                                for (let i = 1; i <= response.total_pages; i++) {
                                    paginationHTML += `<button class="btn btn-sm ${i === response.current_page ? 'btn-primary' : 'btn-light'} page-btn" data-page="${i}">${i}</button> `;
                                }
                                paginationHTML += `<button class="btn btn-sm btn-light page-btn" data-page="${response.current_page + 1}" ${response.current_page === response.total_pages ? 'disabled' : ''}>Next</button>`;
                            }
                            $("#pagination").html(paginationHTML);

                        } else {
                            $("#transfer_details").html(`
                        <tr>
                            <td colspan="9" class="text-center text-danger">No records found</td>
                        </tr>
                    `);
                            $("#pagination").html("");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("Error:", error);
                        $("#transfer_details").html(`
                    <tr>
                        <td colspan="9" class="text-center text-danger">Something went wrong!</td>
                    </tr>
                `);
                        $("#pagination").html("");
                    }
                });
            }

            // Initial fetch
            fetchTransferDetails(1);

            // handle pagination button click
            $(document).on("click", ".page-btn", function() {
                let page = $(this).data("page");
                fetchTransferDetails(page);
            });

            // DELETE ALL TRANSFER DATA
            $(document).on('click', '#deleteAllBtn', function() {
                if (!confirm("Are you sure you want to delete ALL transfer records? This action cannot be undone.")) {
                    return;
                }

                $.ajax({
                    url: 'api/transfer_delete.php', // your delete API
                    type: 'POST', // safer than DELETE for cross-browser
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            location.reload(); // refresh entire page
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        alert("An error occurred: " + error);
                    }
                });
            });

        });
    </script>






</body>

</html>