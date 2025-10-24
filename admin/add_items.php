<?php
session_start();
include('../config/config.php'); // adjust path if needed
// Check if user is logged in
if (!isset($_SESSION['bio_id'])) {
    // Redirect to index page if not logged in
    header("Location: ../index.php");
    exit;
}
// Fetch vendors
$vendors = [];
$vendorQuery = "SELECT vendor_name FROM vendors ORDER BY vendor_name ASC";
$result = $conn->query($vendorQuery);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vendors[] = $row['vendor_name'];
    }
    $result->free();
}

// Fetch shops (stores)
$shops = [];
$sql = "SELECT id, stores_name,stores_location FROM shops";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $shops[] = $row;
    }
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
        .table-responsive {
            height: 100%;
            max-height: 330px !important;
            overflow-y: scroll;
        }

        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        }
    </style>
</head>

<body>
    <?php include('includes/sidebar.php'); ?>
    <main class="content">
        <?php include('includes/header.php'); ?>

        <div class="scroll-section">

            <!-- ADD ITEM FORM -->
            <div class="card shadow-sm rounded p-4 mb-4">
                <h6 class="fw-bold mb-3">ADD ITEM</h6>

                <form id="additem" method="post" enctype="multipart/form-data" class="p-3">
                    <div class="row">
                        <!-- STORE -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">STORE NAME</label>
                            <select class="form-select" name="store_id" id="storeSelect" required>
                                <option value="">Select Store</option>
                                <?php foreach ($shops as $shop): ?>
                                    <option value="<?= htmlspecialchars($shop['id']) ?>"
                                        data-name="<?= htmlspecialchars($shop['stores_name']) ?>"
                                        data-location="<?= htmlspecialchars($shop['stores_location']) ?>">
                                        <?= htmlspecialchars($shop['stores_name'] . ' - ' . $shop['stores_location']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="store_name" id="store_name_hidden">
                            <input type="hidden" name="store_location" id="store_location_hidden">
                        </div>

                        <!-- ITEM NAME -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ITEM NAME</label> <select class="form-select" name="item_name"
                                id="item_id" required>
                                <option value="">Select Item</option>
                                <?php
                                $query = $conn->query("SELECT id, purchase_name, purchase_code, purchase_quantity, distributor_name FROM purchase_order ORDER BY purchase_name ASC");
                                while ($row = $query->fetch_assoc()):
                                    ?>
                                    <option value="<?= htmlspecialchars($row['purchase_name']) ?>"
                                        data-code="<?= htmlspecialchars($row['purchase_code']) ?>"
                                        data-quantity="<?= htmlspecialchars($row['purchase_quantity']) ?>"
                                        data-vendor="<?= htmlspecialchars($row['distributor_name']) ?>">
                                        <!-- vendor here -->
                                        <?= htmlspecialchars($row['purchase_name']) ?>
                                    </option>
                                <?php endwhile; ?>

                            </select>
                        </div>


                        <!-- SUBCATEGORY -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SUB CATEGORY</label>
                            <input class="form-control" type="text" name="sub_category"
                                placeholder="Enter Sub Category">
                        </div>

                        <!-- ITEM CODE -->


                        <div class="col-md-6 mb-3">
                            <label class="form-label">ITEM CODE</label>
                            <input class="form-control" type="text" name="item_code" id="item_code" readonly required>
                        </div>



                        <!-- AVAILABLE QTY -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">AVAILABLE QUANTITY</label>
                            <input class="form-control" type="number" name="available_quantity" id="available_quantity"
                                required readonly>
                        </div>


                        <!-- QUANTITY -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">STOCK</label>
                            <input class="form-control" type="number" name="stock_level" placeholder="Enter Quantity"
                                required>
                        </div>

                        <!-- PRICE -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">PRICE</label>
                            <input class="form-control" type="number" step="0.01" name="item_price"
                                placeholder="Enter Price" required>
                        </div>
                        <!-- VENDOR -->
                        <div class="col-md-6 mb-3 d-none">
                            <label class="form-label">VENDOR NAME</label>
                            <input type="text" class="form-control" name="vendor_name" id="vendor_name" readonly
                                required>
                        </div>

                        <!-- IMAGE -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ITEM IMAGE</label>
                            <input class="form-control" type="file" name="items_image" required>
                        </div>

                        <!-- SUBMIT -->
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-secondary btn-sm">Add Item</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- PURCHASE TABLE -->
            <div class="card shadow-sm rounded p-4">
                <h6 class="mb-3">ITEM LIST</h6>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>STORE NAME</th>
                                <th>ITEM NAME</th>
                                <th>ITEM CODE</th>
                                <th>VENDOR NAME</th>
                                <th>AVAILABLE QUANTITY</th>
                                <th>PURCHASE DATE</th>
                                <th>IMAGE</th>
                            </tr>
                        </thead>
                        <tbody id="purchase_order"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <script>
        $(document).ready(function () {
            $('#item_id').on('change', function () {
                let quantity = $(this).find(':selected').data('quantity') || '';
                $('#available_quantity').val(quantity);

                let itemCode = $(this).find(':selected').data('code') || '';
                $('#item_code').val(itemCode);

                // Auto-fill vendor name
                let vendor = $(this).find(':selected').data('vendor') || '';
                $('#vendor_name').val(vendor);
            });


            // Capture store name dynamically
            $('#storeSelect').on('change', function () {
                const selected = $(this).find(':selected');
                $('#store_name_hidden').val(selected.data('name'));
            });

            // Add Item (AJAX)
            $('#additem').on('submit', function (e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: 'api/add_items.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Added!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }
                });
            });

            // Fetch Items
            function fetchItems() {
                $.ajax({
                    url: 'api/fetch_items.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        let rows = "";
                        if (response.status === "success" && response.data.length > 0) {
                            response.data.forEach(function (item, index) {
                                rows += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${item.store_name}</td>
                                        <td>${item.item_name}</td>
                                        <td>${item.item_code}</td>
                                        <td>${item.vendor_name}</td>
                                        <td>${item.stock_level}</td>
                                        <td>${item.created_at}</td>
                                        <td><img src="../${item.items_image}" style="width:40px; height:auto;"></td>
                                    </tr>`;
                            });
                        } else {
                            rows = `<tr><td colspan="8" class="text-danger text-center">No items found</td></tr>`;
                        }
                        $('#purchase_order').html(rows);
                    }
                });
            }

            fetchItems();
        });
    </script>
</body>

</html>