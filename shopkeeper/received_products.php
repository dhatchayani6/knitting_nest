<?php
session_start(); // Must be first
include('../config/config.php');

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
    <style>
        .form-control:disabled,
        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        } 
        th{
            font-weight: 400 !important;
        }
    </style>
</head>

<body>
    <?php include('includes/sidebar.php') ?>

    <main class="content">
        <?php include('includes/header.php'); ?>

        <div class="scroll-section">

            <section class="section card border rounded shadow p-3">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-3">RECEIVED PRODUCTS</h6>
                        <!-- Search Box -->
                        <div class="mb-3">
                            <input type="text" id="search_input" class="form-control"
                                placeholder="Search by item name, code, or store...">
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive ">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th >S.No</th>
                                        <th >ITEMS NAME</th>
                                        <th >ITEMS CODE</th>
                                        <th >AVAILABLE QUANTITY</th>
                                        <th >TRANSFERED QUANTITY</th>
                                        <th >FROM STORE </th>
                                        <th >TO STORE </th>
                                        <th >Transfer Date </th>
                                        <th >Transfer Status</th>




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

        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                                <td>${item.created_at}</td>
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