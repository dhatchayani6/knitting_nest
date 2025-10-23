<?php
session_start();

include('../config/config.php');
// Check if user is logged in
if (!isset($_SESSION['bio_id'])) {
    // Redirect to index page if not logged in
    header("Location: ../index.php");
    exit;
}
// Fetch vendors from DB
$vendors = array();
$vendorQuery = "SELECT vendor_name FROM vendors ORDER BY vendor_name ASC";
if ($result = $conn->query($vendorQuery)) {
    while ($row = $result->fetch_assoc()) {
        $vendors[] = $row['vendor_name'];
    }
    $result->free();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Purchase Orders</title>
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../stylesheet/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include('includes/sidebar.php'); ?>
    <main class="content">
        <?php include('includes/header.php'); ?>

        <div class="scroll-section">

            <!-- Purchase Form -->
            <div class="card shadow-sm rounded p-4 mb-4">
                <span class="fw-bold">ADD PURCHASE ORDER</span>
                <form id="addpurchase" method="post" enctype="multipart/form-data" class="p-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ITEM NAME</label>
                                <input class="form-control" type="text" name="purchase_item"
                                    placeholder="Enter Purchase Item Name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">DATE OF PURCHASE</label>
                                <input class="form-control" type="date" name="date_of_purchase" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ITEM IMAGE</label>
                                <input class="form-control" type="file" name="items_image" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">PURCHASE PRICE</label>
                                <input class="form-control" type="number" name="purchase_price"
                                    placeholder="Enter Item Price" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ITEM CODE</label>
                                <input class="form-control" type="text" name="purchase_code"
                                    placeholder="Enter Purchase Code" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Select Vendor</label>
                                <select class="form-select" name="distributor_name" id="vendorDropdown" required>
                                    <option value="" selected disabled>Select Vendor</option>
                                    <?php foreach ($vendors as $vendor): ?>
                                        <option value="<?= htmlspecialchars($vendor) ?>"><?= htmlspecialchars($vendor) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">PURCHASE QUANTITY</label>
                                <input class="form-control" type="number" name="purchase_quantity"
                                    placeholder="Enter Quantity" required>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-secondary btn-sm">Purchase</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Purchase Table -->
            <div class="card shadow-sm rounded p-4 mb-4">
                <h6 class="mb-3">Purchase Orders</h6>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>ITEM NAME</th>
                                <th>ITEM CODE</th>
                                <th>DISTRIBUTOR NAME</th>
                                <th>QUANTITY</th>
                                <th>PRICE</th> <!-- ✅ NEW COLUMN -->

                                <th>DATE OF PURCHASE</th>

                                <th>ITEM IMAGE</th>
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
            // Add purchase via separate page
            $('#addpurchase').on('submit', function (e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: 'api/add_purchase.php', // Separate API file
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

            // Fetch purchases
            function fetchPurchases() {
                $.ajax({
                    url: 'api/fetch_purchase.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        let rows = "";
                        if (response.status === "success" && response.data.length > 0) {
                            response.data.forEach(function (p, index) {
                                rows += `
                            <tr>
                                <td>${p.sno}</td>
                                <td>${p.purchase_name}</td>
                                <td>${p.purchase_code}</td>
                                <td>${p.distributor_name}</td>
                                <td>${p.purchase_quantity}</td>
                                <td>${p.purchase_price}</td>

                                <td>${p.purchase_date}</td>


                                <td><img src="../${p.items_image}" style="width: 40px; height:auto;"></td>
                                
                            </tr>
                        `;
                            });
                        } else {
                            rows = `<tr><td colspan="8" class="text-center text-danger">No purchase orders</td></tr>`;
                        }
                        $('#purchase_order').html(rows);
                    }
                });
            }

            fetchPurchases();


        });
    </script>
</body>

</html>