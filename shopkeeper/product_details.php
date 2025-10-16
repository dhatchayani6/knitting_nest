<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" href="css/app.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        tr th {
            font-size: 15px;
            font-weight: 600;
        }

        .filter-btns .btn {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <div class="app" id="app">
            <?php include('includes/header.php') ?>
            <?php include('includes/sidebar.php') ?>

            <article class="content dashboard-page bg-white">
                <section class="section card shadow border p-3 mt-4">
                    <div class="container">

                        <div class="row mb-3 filter-btns p-3">
                            <div class="col">
                                <button class="btn btn-primary stock-filter-btn" data-filter="all">All</button>
                                <button class="btn btn-success stock-filter-btn" data-filter="in-stock">In
                                    Stock</button>
                                <button class="btn btn-warning stock-filter-btn" data-filter="low-stock">Low
                                    Stock</button>
                                <button class="btn btn-danger stock-filter-btn" data-filter="out-of-stock">Out of
                                    Stock</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table  text-center">
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
            </article>
        </div>
    </div>

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