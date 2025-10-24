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

            <section class="section card shadow border p-3 mt-4">
                <div class="container">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-3">TRANSFER DETAILS</h6>
                        <div class="mb-3">
                            <input type="text" id="search_input" class="form-control" placeholder="Search items...">
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive ">
                            <table class="table table-bordered text-center " id="table_id">
                                <thead class="table-light ">
                                    <tr>
                                        <th>S.No</th>
                                        <th>ITEMS NAME</th>
                                        <th>ITEMS CODE</th>

                                        <th>AVAILABLE QUANTITY</th>
                                        <th>TRANSFERED QUANTITY</th>
                                        <!-- <th > ITEMS PRICE</th> -->

                                        <th>FROM STORE </th>
                                        <th>TO STORE </th>
                                        <th>TRANSFER DATE </th>
                                        <th>TRANSFER STATUS</th>

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


    <script>
        $(document).ready(function () {
            let limit = 10;

            function fetchTransferDetails(page = 1, search = '') {
                $.ajax({
                    url: "api/fetch_transfer_details.php", // your API for transfer table
                    type: "GET",
                    data: {
                        page: page,
                        limit: limit,
                        search: search  // ✅ Add search input
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "Fetch transfer details success") {
                            let rows = "";
                            $.each(response.data, function (index, item) {
                                rows += `
                            <tr>
                                <td>${item.sno}</td>
                                <td>${item.item_name}</td>
                                
                                <td>${item.item_code}</td>
                            

                                <td>${item.available_quantity}</td>
                                <td>${item.shared_quantity}</td>

                                <td>${item.from_store.stores_name} - ${item.from_store.stores_location}</td>
<td>${item.to_store.stores_name} - ${item.to_store.stores_location}</td>


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
                    error: function (xhr, status, error) {
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

            // Search input keyup
            $('#search_input').on('keyup', function () {
                let search = $(this).val();
                fetchTransferDetails(1, search);
            })

            // Pagination click
            $(document).on("click", ".page-btn", function () {
                let page = $(this).data("page");
                let search = $('#search_input').val();
                fetchTransferDetails(page, search);
            });




        });

        $('#searchInput').on('keyup', function () {
            const searchValue = $(this).val().trim();
            fetchTransferDetails(1, searchValue);
        });

    </script>
</body>

</html>