<?php

include('../includes/config.php'); // adjust path if needed

// If user is not logged in, redirect to login page
// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../index.php");
//     exit();
// }
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
                        <span class="fw-bold">ADD STORE</span>
                        <form action="" id="addstore" method="post" class="p-3">
                            <div class="row">
                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label for="formFile" class="form-label">STORE NAME</label>
                                        <input class="form-control" type="text" name="stores_name"
                                            placeholder="Enter the storename" required>
                                    </div>





                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="formFileMultiple" class="form-label">STORE LOCATION</label>
                                        <input class="form-control" type="text" name="stores_location"
                                            placeholder="Enter the store location" required>
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
                                    <button type="submit" name="addstorelogin" class="btn btn-primary w-35">Add
                                        Store</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </section>

                <!-- center content ended -->

                <!-- table start -->


                <section class="section">
                    <div class="container">
                        <div class="row">
                            <table class="table text-center">
                                <thead>
                                    <tr>
                                        <th scope="col">s.no</th>
                                        <th scope="col">store name</th>
                                        <th scope="col">store location</th>
                                        <!-- <th scope="col">usertype</th> -->
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="fetch_stores">


                                </tbody>
                            </table>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">

    </script>
    <script>
        $(document).ready(function () {

            // 1️⃣ ADD STORE (Insert)
            $('#addstore').on('submit', function (e) {
                e.preventDefault();

                const stores_name = $('input[name="stores_name"]').val();
                const stores_location = $('input[name="stores_location"]').val();
                const usertype = "Admin"; // or get from a dropdown if needed

                $.ajax({
                    url: 'api/stores.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        stores_name: stores_name,
                        stores_location: stores_location,
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
                            $('#addstore')[0].reset();
                            fetch_stores(); // Refresh table after adding
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
                            text: "Something went wrong while adding store."
                        });
                    }
                });
            });

            // 2️⃣ FETCH STORES (GET)
            function fetch_stores() {
                $.ajax({
                    url: 'api/fetchstore.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        let rows = "";
                        if (response.status === "success") {
                            response.data.forEach(function (store, index) {
                                rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${store.stores_name}</td>
                                <td>${store.stores_location}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="${store.id}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                            });
                        } else {
                            rows = `<tr><td colspan="4">${response.message}</td></tr>`;
                        }
                        $('#fetch_stores').html(rows);
                    },
                    error: function (xhr) {
                        console.log("Fetch error:", xhr.responseText);
                        $('#fetch_stores').html(`<tr><td colspan="4">No stores available</td></tr>`);
                    }
                });
            }
            fetch_stores(); // Call fetch on page load

            // 3️⃣ DELETE STORE
            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'api/deletestore.php',
                            type: 'POST',
                            data: { id: id },
                            dataType: 'json',
                            success: function (response) {
                                if (response.status === "success") {
                                    fetch_stores(); // Refresh table after deletion
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                    });
                                }
                            },
                            error: function (xhr) {
                                console.log("Delete error:", xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: "Something went wrong while deleting store."
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>




</body>

</html>