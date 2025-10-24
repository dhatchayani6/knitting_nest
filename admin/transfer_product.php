<?php
session_start();
include('../config/config.php'); // adjust path if needed

// If user is not logged in, redirect to login page
if (!isset($_SESSION['bio_id'])) {
    header("Location: ../index.php");
    exit();
}

// fetch all shops names
$sql = "SELECT id, stores_name,stores_location FROM shops";
$result = $conn->query($sql);

$shops = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $shops[] = $row;
    }
}

// Fetch items
$items = [];
$result2 = $conn->query("SELECT id, item_name,store_name FROM items");
if ($result2 && $result2->num_rows > 0) {
    while ($row2 = $result2->fetch_assoc()) {
        $items[] = $row2;
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
        th {
            font-weight: 400;
        }

        .form-control:disabled,
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



            <section class="section">
                <div class="container">

                    <div class="card shadow-sm rounded-3 border p-4">
                        <span class="fw-bold">ITEMS TRANSFER </span>
                        <form action="" id="transfer_product" method="post" class="p-3">
                            <div class="row">
                                <!-- ROW 1: ITEM NAME & ITEM CODE -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ITEM NAME</label>
                                        <select class="form-select" name="item_name" id="item_id" required>
                                            <option value="">Select Item</option>
                                            <?php foreach ($items as $item): ?>
                                                <option value="<?= htmlspecialchars($item['id']) ?>">
                                                    <?= htmlspecialchars($item['item_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ITEM CODE</label>
                                        <input type="text" name="item_code" id="item_code" class="form-control"
                                            readonly>
                                    </div>
                                </div>

                                <!-- ROW 2: SUB CATEGORY & ITEM IMAGE -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">SUB CATEGORY</label>
                                        <input type="text" name="sub_category" id="sub_category" class="form-control"
                                            readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ITEM IMAGE</label>
                                        <input class="form-control" type="file" name="items_image" required>
                                    </div>
                                </div>

                                <!-- ROW 3: FROM STORE & TO STORE -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">FROM STORE</label>
                                        <input type="hidden" name="from_store_id" id="from_store_id">
                                        <input type="text" id="from_store_name" class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">TO STORE</label>
                                        <select class="form-select" name="to_store_id" required>
                                            <option value="">Select Store</option>
                                            <?php foreach ($shops as $shop): ?>
                                                <option value="<?= htmlspecialchars($shop['id']) ?>">
                                                    <?= htmlspecialchars($shop['stores_name'] . ' - ' . $shop['stores_location']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>


                                <!-- ROW 4: AVAILABLE QUANTITY & TRANSFER QUANTITY -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">AVAILABLE QUANTITY</label>
                                        <input type="number" name="available_quantity" id="available_quantity"
                                            class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">TRANSFER QUANTITY</label>
                                        <input type="number" name="shared_quantity" id="shared_quantity"
                                            class="form-control" placeholder="Enter quantity" required>
                                    </div>
                                </div>

                                <!-- ROW 5: SUBMIT BUTTON -->
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-secondary w-35">Transfer Product</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </section>

        </div>
    </main>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>


    <script>
        $(document).ready(function () {

            $('#item_id').change(function () {
                var id = $(this).val();
                if (id) {
                    $.ajax({
                        url: 'api/get_item_details.php',
                        type: 'POST',
                        data: { id: id },
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                var first = res.data[0];

                                $('#item_code').val(first.item_code);
                                $('#available_quantity').val(first.available_quantity);
                                $('#sub_category').val(first.sub_category);

                                $('#from_store_name').val(first.store_name + ' - ' + first.store_location);
                                $('#from_store_id').val(first.store_id);     // hidden

                                if (res.data.length > 1) {
                                    $('#multiple_items_container').show();
                                    var select = $('#specific_item_select');
                                    select.empty();
                                    res.data.forEach(item => {
                                        select.append(`
                                    <option value="${item.id}" 
                                            data-code="${item.item_code}" 
                                            data-qty="${item.available_quantity}" 
                                            data-sub_category="${item.sub_category}" 
                                            data-store-id="${item.store_id}" 
                                            data-store-name="${item.store_name}">
                                        Code: ${item.item_code}, Qty: ${item.available_quantity}, Store: ${item.store_name}, Sub: ${item.sub_category}
                                    </option>`);
                                    });
                                } else {
                                    $('#multiple_items_container').hide();
                                }
                            } else {
                                $('#item_code, #available_quantity, #sub_category, #from_store_name, #from_store_id').val('');
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Oops!',
                                    text: res.message
                                });
                            }
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error fetching item details.'
                            });
                        }
                    });
                } else {
                    $('#item_code, #available_quantity, #sub_category, #from_store_name, #from_store_id').val('');
                    $('#multiple_items_container').hide();
                }
            });

            $(document).on('change', '#specific_item_select', function () {
                var selected = $(this).find(':selected');
                $('#item_code').val(selected.data('code'));
                $('#available_quantity').val(selected.data('qty'));
                $('#from_store_name').val(selected.data('store-name'));
                $('#from_store_id').val(selected.data('store-id'));
            });

            // Form submission with SweetAlert
            $('#transfer_product').submit(function (e) {
                e.preventDefault();

                var available = parseInt($('#available_quantity').val());
                var shared = parseInt($('#shared_quantity').val());

                if (shared > available) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Quantity',
                        text: 'Cannot transfer more than available quantity!'
                    });
                    return;
                }

                var form = $(this)[0];
                var formData = new FormData(form);

                $.ajax({
                    url: 'api/transfer_item.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Item transferred successfully!'
                            }).then(() => {
                                $('#transfer_product')[0].reset();
                                $('#item_code, #available_quantity ,#from_store_id').val('');
                                $('#multiple_items_container').hide();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.message
                            });
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.'
                        });
                    }
                });
            });

        });

    </script>
</body>

</html>