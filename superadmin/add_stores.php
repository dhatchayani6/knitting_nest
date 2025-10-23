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
            <!-- Add Stores Form -->
            <section class="p-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-light text-dark">
                        <h6 class="mb-0">Add New Store</h6>
                    </div>
                    <div class="card-body">
                        <form id="addStoreForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="storeName" class="form-label">Store Name</label>
                                    <input type="text" class="form-control" id="storeName" name="stores_name"
                                        placeholder="Enter store name" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="storeLocation" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="storeLocation" name="stores_location"
                                        placeholder="Enter store location" required>
                                </div>
                            </div>

                            <div class="mt-3 d-flex justify-content-center">
                                <button type="submit" class="btn btn-secondary">Add Store</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Added Stores List -->
            <section class="mt-5 p-3">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table  table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>S No</th>
                                        <th>Store Name</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="storesTableBody">
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

            // Fetch existing stores from backend on page load
            function loadStores() {
                $.ajax({
                    url: 'api/get_stores.php', // Your API to get all stores
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            const tbody = $('#storesTableBody');
                            tbody.empty();
                            response.data.forEach((store, index) => {
                                const row = `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${store.stores_name}</td>
                                        <td>${store.stores_location}</td>
                                        <td>
                                            <button class="btn btn-success btn-sm">Active</button>
                                        </td>
                                    </tr>
                                `;
                                tbody.append(row);
                            });
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
                            title: 'Oops...',
                            text: 'Failed to fetch stores!'
                        });
                    }
                });
            }

            // Initial load
            loadStores();

            // Add store via AJAX
            $('#addStoreForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: 'api/add_stores.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            loadStores(); // Reload table after adding
                            $('#addStoreForm')[0].reset();
                            Swal.fire({
                                icon: 'success',
                                title: 'Added!',
                                text: 'Store added successfully',
                                timer: 2000,
                                showConfirmButton: false
                            });
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
                            title: 'Oops...',
                            text: 'Something went wrong!'
                        });
                    }
                });
            });

        });
    </script>

</body>

</html>