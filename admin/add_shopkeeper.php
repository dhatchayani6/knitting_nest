<?php

include('../includes/config.php'); // adjust path if needed

// If user is not logged in, redirect to login page
// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../index.php");
//     exit();
// }


// fetch all shops names
$sql = "SELECT id, stores_name FROM shops";
$result = $conn->query($sql);

$shops = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $shops[] = $row;
    }
}
?>


<!doctype html>
<html class="no-js" lang="en">

<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Admin Dashboard </title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <!-- Place favicon.ico in the root directory -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" id="theme-style" href="css/app.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>


</head>

<body>
    <div class="main-wrapper">
        <div class="app" id="app">
            <!-- start header -->
            <?php include('includes/header.php') ?>
            <!-- end header -->

            <!-- sidebar start -->
            <?php include('includes/sidebar.php') ?>
            <!-- end sidebar -->
            <div class="sidebar-overlay" id="sidebar-overlay"></div>
            <div class="sidebar-mobile-menu-handle" id="sidebar-mobile-menu-handle"></div>
            <div class="mobile-menu-handle"></div>
            <!-- center content start -->
            <article class="content dashboard-page bg-white">
                <section class="section">
                    <div class="container">

                        <div class="card shadow-sm rounded-3 border p-4">
                            <span class="fw-bold">ADD SHOPKEEPER</span>
                            <form action="" id="addshopkeeper" method="post" class="p-3">
                                <div class="row">
                                    <div class="col-md-6">


                                        <div class="mb-3">
                                            <label for="formFileDisabled" class="form-label">STORE NAME</label>
                                            <select class="form-select" name="shop_name" required>
                                                <option value="">Select Store</option>
                                                <?php foreach ($shops as $shop): ?>
                                                    <option value="<?php echo $shop['id']; ?>">
                                                        <?php echo htmlspecialchars($shop['stores_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                        </div>
                                        <div class="mb-3">
                                            <label for="formFile" class="form-label">SHOPKEEPER NAME</label>
                                            <input class="form-control" type="text" name="shopkeeper_name"
                                                placeholder="Enter the shopkeeper name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="formFile" class="form-label">SHOPKEEPER PASSWORD</label>
                                            <input class="form-control" type="text" name="password"
                                                placeholder="Enter the shopkeeper name" required>
                                        </div>



                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="formFileMultiple" class="form-label">STORE LOCATION</label>
                                            <input class="form-control" type="text" name="store_location"
                                                placeholder="Enter the store location" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="formFile" class="form-label">SHOPKEEPER BIO_ID</label>
                                            <input class="form-control" type="text" name="shopkeeper_bioid"
                                                placeholder="Enter the shopkeeper name" required>
                                        </div>



                                    </div>





                                    <!-- <div class="col-md-6">


                                    <div class="mb-3">
                                        <label for="formFileDisabled" class="form-label">Usertype</label>
                                        <select class="form-select" name="usertype" required>
                                            <option>Select menu</option>
                                            <option value="Admin">Admin</option>
                                            <option value="External"></option>

                                        </select>
                                    </div>

                                </div> -->
                                    <div class="col-12  text-center ">
                                        <button type="submit" name="shopkeeper" class="btn btn-primary w-35">Add
                                            Shopkeeper</button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </section>

                <!-- center content ended -->

                <!-- table start -->


                <section class="section">
                    <div class="container">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.NO</th>
                                            <th scope="col">STORE NAME</th>
                                            <th scope="col">STORE LOCATIONS</th>
                                            <th scope="col">SHOPKEEPER NAME</th>
                                            <th scope="col">SHOPKEEPER BIO_ID</th>
                                            <th scope="col">SHOPKEEPER PASSWORD</th>
                                            <th scope="col">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fetch_shopkeeper">


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </article>
            <!-- table end -->



        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="js/vendor.js"></script>
    <script src="js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {

            $(document).ready(function () {
                // 1️⃣ ADD SHOPKEEPER
                $('#addshopkeeper').on('submit', function (e) {
                    e.preventDefault();

                    const shop_id = $('select[name="shop_name"]').val(); // get selected shop id
                    const shop_name = $('select[name="shop_name"] option:selected').text().trim(); // get selected shop name
                    const shopkeeper_name = $('input[name="shopkeeper_name"]').val();
                    const password = $('input[name="password"]').val();
                    const store_location = $('input[name="store_location"]').val();
                    const shopkeeper_bioid = $('input[name="shopkeeper_bioid"]').val();
                    const usertype = "shopkeeper"; // static for now

                    $.ajax({
                        url: 'api/shopkeeper.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            shop_id: shop_id,
                            shop_name: shop_name,
                            shopkeeper_name: shopkeeper_name,
                            password: password,
                            store_location: store_location,
                            shopkeeper_bioid: shopkeeper_bioid,
                            usertype: usertype
                        },
                        success: function (response) {
                            console.log("Add response:", response);
                            if (response.status === "success") {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Added!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                $('#addshopkeeper')[0].reset();
                                fetch_shopkeeper(); // Refresh table after adding
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || "Unknown error"
                                });
                            }
                        },
                        error: function (xhr) {
                            console.log("XHR error:", xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong while adding shopkeeper.'
                            });
                        }
                    });
                });
            });


            // 2️⃣ FETCH STORES (GET)
            function fetch_shopkeeper() {
                $.ajax({
                    url: 'api/fetchshopkeeper.php', // Your API path
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        let rows = "";
                        if (response.status === "success" && response.data.length > 0) {
                            // Populate table rows if data exists
                            response.data.forEach(function (shopkeeper, index) {
                                rows += `
                        <tr>
                            <td>${shopkeeper.sno}</td>
                            <td>${shopkeeper.shop_name}</td>
                            <td>${shopkeeper.store_location}</td>
                            <td>${shopkeeper.shopkeeper_name}</td>
                            <td>${shopkeeper.shopkeeper_bioid}</td>
                            <td>${shopkeeper.password}</td>
                            <td>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${shopkeeper.shopkeeper_id}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                            });
                        } else if (response.status === "success" && response.data.length === 0) {
                            // No data found
                            rows = `<tr><td colspan="7" class="text-center text-danger">No data found</td></tr>`;
                            Swal.fire({
                                icon: 'info',
                                title: 'No Data',
                                text: 'No shopkeepers found.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            // API returned error
                            rows = `<tr><td colspan="7" class="text-center text-danger">${response.message}</td></tr>`;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }

                        $('#fetch_shopkeeper').html(rows);
                    },
                    error: function (xhr) {
                        console.log("Fetch error:", xhr.responseText);
                        $('#fetch_shopkeeper').html(`<tr><td colspan="7" class="text-center text-danger"> NO Shopkeepers</td></tr>`);
                        // Swal.fire({
                        //     icon: 'error',
                        //     title: 'Error',
                        //     text: 'Failed to fetch shopkeepers'
                        // });
                    }
                });
            }



            fetch_shopkeeper(); // Call fetch on page load

            // 3️⃣ DELETE STORE
            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                let id = $(this).data('id');

                if (confirm("Are you sure you want to delete this store?")) {
                    $.ajax({
                        url: 'api/deleteshopkeeper.php',
                        type: 'POST',
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === "success") {
                                alert(response.message);
                                fetch_shopkeeper(); // Refresh table after deletion
                            } else {
                                alert("Error: " + response.message);
                            }
                        },
                        error: function (xhr) {
                            console.log("Delete error:", xhr.responseText);
                            alert("Something went wrong while deleting store.");
                        }
                    });
                }
            });
        });
    </script>


</body>

</html>