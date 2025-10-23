<?php
include('../config/config.php');
session_start();
// Check if user is logged in
if (!isset($_SESSION['bio_id'])) {
    // Redirect to index page if not logged in
    header("Location: ../index.php");
    exit;
}
// ✅ Step 1: Get logged-in user's bio_id from session
$bio_id = $_SESSION['bio_id'] ?? 0;

// Initialize store vars
$store_id = 0;
$store_name = "";

// ✅ Step 2: Get the store assigned to this bio_id
if ($bio_id) {
    $stmt = $conn->prepare("
        SELECT s.id, s.stores_name
        FROM shops s
        INNER JOIN shopkeeper sk ON s.id = sk.shop_id
        WHERE sk.shopkeeper_bioid = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $bio_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $store_id = $row['id'];
        $store_name = $row['stores_name'];
    }
    $stmt->close();
}

// ✅ Step 3: Fetch items belonging to this store
$items = [];
if ($store_id) {
    $stmt = $conn->prepare("SELECT * FROM items WHERE store_id = ?");
    $stmt->bind_param("i", $store_id);
    $stmt->execute();
    $result2 = $stmt->get_result();
    while ($row2 = $result2->fetch_assoc()) {
        $items[] = $row2;
    }
    $stmt->close();
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
    </style>
</head>

<body>
    <?php include('includes/sidebar.php') ?>

    <main class="content">
        <?php include('includes/header.php'); ?>

        <div class="scroll-section">

            <section class="section">
                <div class="container">
                    <div class="card shadow-sm rounded-3 border p-4">
                        <span class="fw-bold">ADD SALES</span>

                        <form id="addsales" method="post" class="p-3">
                            <div class="row">
                                <!-- STORE -->
                                <div class="col-md-6 mb-3 d-none">
                                    <label class="form-label">STORE</label>
                                    <input type="hidden" name="store_id"
                                        value="<?php echo htmlspecialchars($store_id); ?>">
                                    <input class="form-control" type="text"
                                        value="<?php echo htmlspecialchars($store_name); ?>" readonly>
                                </div>


                                <!-- ITEM NAME -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ITEM NAME</label>
                                    <select class="form-select" name="item_name" id="item_name" required>
                                        <option value="">Select Item</option>
                                        <?php foreach ($items as $item): ?>
                                            <option value="<?php echo $item['id']; ?>">
                                                <?php echo htmlspecialchars($item['item_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- ITEM CODE -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ITEM CODE</label>
                                    <input class="form-control" type="text" name="item_code" id="item_code" readonly>
                                </div>

                                <!-- availabel quantity -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">AVAILABLE QUANTITY</label>
                                    <input class="form-control" type="text" name="available_quantity"
                                        id="available_quantity" readonly>
                                </div>
                                <!-- TOTAL ITEMS -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">TOTAL ITEMS</label>
                                    <input class="form-control" type="number" name="total_items" id="total_items"
                                        min="1" value="1">
                                </div>

                                <!-- PRICE -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">PRICE</label>
                                    <input class="form-control" type="text" name="item_price" id="item_price" readonly>
                                </div>
                            </div>

                            <div class="col-12 text-center mt-3">
                                <button type="submit" class="btn btn-secondary w-35">
                                    <i class="fa-solid fa-cart-plus"></i> OK
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- 🧾 SALES TABLE -->
            <section class="section mt-3">
                <div class="container">

                    <div class="row  justify-content-end mb-3">
                        <!-- 🗓️ Date Filter -->
                        <div class="col-md-4">

                            <div class="input-group">
                                <span class="input-group-text fw-bold">Filter by Date:</span>
                                <input type="date" id="filter_date" class="form-control">
                                <button class="btn btn-secondary mb-0" id="clear_date"><i
                                        class="fa fa-undo"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Items Sold</th>
                                        <th>Item Price</th>
                                        <th>Available Quantity</th>
                                        <th>Remaining Quantity</th>
                                        <th>Sales Date</th>
                                    </tr>
                                </thead>
                                <tbody id="fetch_sales"></tbody>
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
            let unitPrice = 0; // store unit price of selected item

            // 🔹 Auto-fill item details when item name selected
            $('#item_name').on('change', function () {
                let itemId = $(this).val();

                if (itemId) {
                    $.ajax({
                        url: 'api/fetch_item_details.php',
                        type: 'POST',
                        data: { item_id: itemId },
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'success') {
                                $('#item_code').val(response.data.item_code);
                                $('#total_items').val(1);
                                $('#available_quantity').val(response.data.available_quantity);
                                unitPrice = parseFloat(response.data.item_price || 0);
                                $('#item_price').val(Math.round(unitPrice));
                            } else {
                                $('#item_code, #total_items, #item_price, #available_quantity').val('');
                                unitPrice = 0;
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Item Not Found',
                                    text: response.message || 'Item details not found!'
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to fetch item details.'
                            });
                        }
                    });
                } else {
                    $('#item_code, #total_items, #item_price, #available_quantity').val('');
                    unitPrice = 0;
                }
            });


            // 🔹 Recalculate total price on total_items change
            $('#total_items').on('input', function () {
                let totalItems = parseInt($(this).val()) || 0;
                let totalPrice = unitPrice * totalItems;
                $('#item_price').val(Math.round(totalPrice));
            });

            // 🔹 Add Sales
            $('#addsales').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: 'api/add_sales.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Added!',
                                text: response.message
                            });
                            $('#addsales')[0].reset();
                            unitPrice = 0;
                            fetch_sales();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Unable to add sale.'
                        });
                    }
                });
            });


            let limit = 10; //add limit for page
            // 🔹 Fetch Sales Table
            function fetch_sales(date = '', page = 1) {
                $.ajax({
                    url: 'api/fetchsales.php',
                    type: 'GET',
                    data: {
                        date: date,
                        page: page,
                        limit: limit,
                    },
                    dataType: 'json',
                    success: function (response) {
                        let rows = "";
                        if (response.status === "success" && response.data.length > 0) {
                            response.data.forEach((sale, index) => {
                                rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${sale.item_code}</td>
                            <td>${sale.item_name}</td>
                            <td>${sale.total_items}</td>
                            <td>${sale.item_price}</td>
                            <td>${sale.item_quantity}</td>
                            <td>${sale.remaining_quantity}</td>
                            <td>${sale.created_at}</td>

                        </tr>`;
                            });
                        } else {
                            rows = `<tr><td colspan="9" class="text-danger text-center">No sales found</td></tr>`;
                        }
                        $('#fetch_sales').html(rows);

                        // Pagination
                        let paginationHTML = "";

                        // Always show Prev
                        paginationHTML += `<button class="btn btn-sm btn-secondary me-1" ${page <= 1 ? 'disabled' : ''} data-page="${page - 1}">Prev</button>`;

                        // Page numbers
                        for (let i = 1; i <= response.total_pages; i++) {
                            paginationHTML += `<button class="btn btn-sm ${i === page ? 'btn-primary' : 'btn-outline-primary'} me-1" data-page="${i}">${i}</button>`;
                        }

                        // Always show Next
                        paginationHTML += `<button class="btn btn-sm btn-secondary" ${page >= response.total_pages ? 'disabled' : ''} data-page="${page + 1}">Next</button>`;

                        $('#pagination').html(paginationHTML);

                    },
                    error: function () {
                        $('#fetch_sales').html(`<tr><td colspan="9" class="text-danger text-center">Error fetching sales</td></tr>`);
                        $('#pagination').html('');
                    }
                });
            }

            // 🔹 Handle Pagination Click
            $(document).on('click', '#pagination button', function () {
                const selectedPage = parseInt($(this).data('page'));
                const selectedDate = $('#filter_date').val();
                fetch_sales(selectedDate, selectedPage);
            });
            // 🔹 Date Filter Change
            $('#filter_date').on('change', function () {
                const selectedDate = $(this).val();
                fetch_sales(selectedDate);
            });

            // 🔹 Clear Date Filter (show today's data again)
            $('#clear_date').on('click', function () {
                $('#filter_date').val('');
                fetch_sales(); // reload today's data
            });


            fetch_sales(); // Load on page start



        });
    </script>
</body>

</html>