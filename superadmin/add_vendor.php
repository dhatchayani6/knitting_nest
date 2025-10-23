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

            <!-- Vendor Form -->
            <section class="mt-4 p-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-light text-dark">
                        <h5 class="mb-0">Add New Vendor</h5>
                    </div>
                    <div class="card-body">
                        <form id="addVendorForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="vendorName" class="form-label">Vendor Name</label>
                                    <input type="text" class="form-control" id="vendorName" name="vendor_name"
                                        placeholder="Enter vendor name" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="vendorEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="vendorEmail" name="email"
                                        placeholder="Enter email" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="vendorMobile" class="form-label">Mobile Number</label>
                                    <input type="text" class="form-control" id="vendorMobile" name="mobile_number"
                                        placeholder="Enter mobile number" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="vendorPAN" class="form-label">PAN Number</label>
                                    <input type="text" class="form-control" id="vendorPAN" name="pan_number"
                                        placeholder="Enter PAN number">
                                </div>

                                <div class="col-md-12">
                                    <label for="vendorAddress" class="form-label">Address</label>
                                    <textarea class="form-control" id="vendorAddress" name="address"
                                        placeholder="Enter address" required></textarea>
                                </div>
                            </div>

                            <div class="mt-3 d-flex justify-content-center">
                                <button type="submit" class="btn btn-secondary">Add Vendor</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Added Vendors List -->
            <section class="mt-5 p-3">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>S No</th>
                                        <th>Vendor Name</th>
                                        <th>Email</th>
                                        <th>Mobile Number</th>
                                        <th>PAN Number</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="vendorsTableBody">
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

            // Load existing vendors
            function loadVendors() {
                $.ajax({
                    url: 'api/get_vendors.php', // backend API to fetch vendors
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            const tbody = $('#vendorsTableBody');
                            tbody.empty();
                            response.data.forEach((vendor, index) => {
                                const row = `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${vendor.vendor_name}</td>
                                        <td>${vendor.email}</td>
                                        <td>${vendor.mobile_number}</td>
                                        <td>${vendor.pan_number}</td>
                                        <td>${vendor.address}</td>
                                        <td><button class="btn btn-success btn-sm">Active</button></td>
                                    </tr>
                                `;
                                tbody.append(row);
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Oops...', 'Failed to fetch vendors!', 'error');
                    }
                });
            }

            // Initial load
            loadVendors();

            // Add vendor via AJAX
            $('#addVendorForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: 'api/add_vendor.php', // backend API to add vendor
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            loadVendors();
                            $('#addVendorForm')[0].reset();
                            Swal.fire({
                                icon: 'success',
                                title: 'Added!',
                                text: 'Vendor added successfully',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Oops...', 'Something went wrong!', 'error');
                    }
                });
            });

        });
    </script>

</body>

</html>