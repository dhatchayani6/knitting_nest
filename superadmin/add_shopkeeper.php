<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Superadmin Dashboard</title>
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="../stylesheet/style.css">
</head>

<body>

    <?php include('includes/sidebar.php') ?>
    <main class="content">
        <?php include('includes/header.php') ?>

        <div class="scroll-section">

            <!-- Add Shopkeeper Form -->
            <section class="mt-4 p-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-light text-dark">
                        <h5 class="mb-0">Add Shopkeeper</h5>
                    </div>
                    <div class="card-body">
                        <form id="addShopkeeperForm">
                            <div class="row g-3">

                                <!-- Store Name Dropdown -->
                                <div class="col-md-6">
                                    <label for="shop_name" class="form-label">Store Name</label>
                                    <select class="form-select" id="shop_name" name="shop_name" required>
                                        <option value="">Select Store</option>
                                    </select>
                                    <input type="hidden" name="shop_id" id="shop_id">
                                </div>

                                <!-- Store Location -->
                                <div class="col-md-6">
                                    <label for="store_location" class="form-label">Store Location</label>
                                    <input type="text" class="form-control" id="store_location" name="store_location"
                                        readonly>
                                </div>

                                <!-- Shopkeeper Name -->
                                <div class="col-md-6">
                                    <label for="shopkeeper_name" class="form-label">Shopkeeper Name</label>
                                    <input type="text" class="form-control" id="shopkeeper_name" name="shopkeeper_name"
                                        placeholder="Enter shopkeeper name" required>
                                </div>

                                <!-- Bio ID -->
                                <div class="col-md-3">
                                    <label for="shopkeeper_bioid" class="form-label">Bio ID</label>
                                    <input type="text" class="form-control" id="shopkeeper_bioid"
                                        name="shopkeeper_bioid" placeholder="Enter Bio ID" required>
                                </div>

                                <!-- Password -->
                                <div class="col-md-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter password" required>
                                </div>

                            </div>

                            <div class="mt-3 d-flex justify-content-center">
                                <button type="submit" class="btn btn-secondary">Add Shopkeeper</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Shopkeepers List -->
            <section class="mt-5 p-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-light text-dark">
                        <h5 class="mb-0">Shopkeepers List</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table  table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>S No</th>
                                        <th>Shopkeeper Name</th>
                                        <th>Bio ID</th>
                                        <th>Store Name</th>
                                        <th>Store Location</th>
                                    </tr>
                                </thead>
                                <tbody id="shopkeepersTableBody">
                                    <!-- Dynamic rows will appear here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {

            // Load stores into dropdown
            function loadStores() {
                $.ajax({
                    url: 'api/get_stores.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            const select = $('#shop_name');
                            select.empty().append('<option value="">Select Store</option>');
                            response.data.forEach(store => {
                                select.append(`<option value="${store.stores_name}" data-id="${store.id}" data-location="${store.stores_location}">${store.stores_name}</option>`);
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Failed to fetch stores!', 'error');
                    }
                });
            }

            // Load shopkeepers list
            function loadShopkeepers() {
                $.ajax({
                    url: 'api/get_shopkeepers.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            const tbody = $('#shopkeepersTableBody');
                            tbody.empty();
                            response.data.forEach((shopkeeper, index) => {
                                tbody.append(`
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${shopkeeper.shopkeeper_name}</td>
                                        <td>${shopkeeper.shopkeeper_bioid}</td>
                                        <td>${shopkeeper.shop_name}</td>
                                        <td>${shopkeeper.store_location}</td>
                                    </tr>
                                `);
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Failed to fetch shopkeepers!', 'error');
                    }
                });
            }

            loadStores();
            loadShopkeepers();

            // Auto-fill store location and shop_id
            $('#shop_name').on('change', function () {
                const selected = $(this).find(':selected');
                const location = selected.data('location') || '';
                const id = selected.data('id') || '';
                $('#store_location').val(location);
                $('#shop_id').val(id);
            });

            // Submit form via AJAX
            $('#addShopkeeperForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: 'api/add_shopkeeper.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#addShopkeeperForm')[0].reset();
                            loadShopkeepers(); // Reload table
                            Swal.fire({
                                icon: 'success',
                                title: 'Added!',
                                text: 'Shopkeeper added successfully',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Something went wrong!', 'error');
                    }
                });
            });

        });
    </script>

</body>

</html>