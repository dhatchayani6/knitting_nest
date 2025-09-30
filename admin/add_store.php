
<?php

include('../includes/config.php'); // adjust path if needed

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
?><!doctype html>
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

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
                        <form action="" id="externallogin" method="post" class="p-3">
                            <div class="row">
                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label for="formFile" class="form-label">STORE NAME</label>
                                        <input class="form-control" type="text" name="storename" placeholder="Enter the storename" required>
                                    </div>

                                   



                                </div>
                                <div class="col-md-6">
                                     <div class="mb-3">
                                        <label for="formFileMultiple" class="form-label">STORE LOCATION</label>
                                        <input class="form-control" type="text" name="store_location" placeholder="Enter the store location" required>
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
                                    <button type="submit" name="admin_login" class="btn btn-primary w-35">Add Store</button>
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
                            <table class="table text-center"  >
                                <thead>
                                    <tr>
                                        <th scope="col">s.no</th>
                                        <th scope="col">store name</th>
                                        <th scope="col">store location</th>
                                        <!-- <th scope="col">usertype</th> -->
                                         <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody  id="add_store">
                                    
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </article>
            <!-- table end -->



        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="js/vendor.js"></script>
    <script src="js/app.js"></script>

  <script>
$(document).ready(function () {

    // =====================
    // ADD FORM SUBMIT
    // =====================
    $('#externallogin').on('submit', function (e) {
        e.preventDefault();

        const externalname = $('input[name="externalname"]').val();
        const password = $('input[name="password"]').val();
        const usertype = $('select[name="usertype"]').val();

        const formData = {
            externalname: externalname,
            password: password,
            usertype: usertype
        };

        $.ajax({
            url: 'api/adminlogin.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function (response) {
                if (response.status === true) {
                    alert(response.message);
                    $('#externallogin')[0].reset();
                    fetchLoginDetails(); // refresh table after adding
                } else {
                    if (Array.isArray(response.message)) {
                        alert("Error:\n" + response.message.join("\n"));
                    } else {
                        alert("Error: " + response.message);
                    }
                }
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                alert("Something went wrong. Please try again.");
            }
        });
    });

    // =====================
    // FETCH TABLE DATA
    // =====================
    function fetchLoginDetails() {
        $.ajax({
            url: 'api/fetchlogin.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status === true && Array.isArray(response.data)) {
                    let tableRows = '';
                    response.data.forEach(function (user, index) {
                        tableRows += `
                            <tr>
                                <th scope="row">${user.sno}</th>
                                <td>${user.email}</td>
                                <td>${user.password}</td>
                                <td>${user.usertype}</td>  
                                <td>
                                    <button class="btn btn-danger btn-sm delete-btn" data-id="${user.id}">
                                        <i class="fa fa-trash"></i> 
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#loginTableBody').html(tableRows);
                } else {
                    $('#loginTableBody').html(`
                        <tr><td colspan="5" class="text-center">No data found</td></tr>
                    `);
                }
            },
            error: function (xhr, status, error) {
                console.error("Fetch Error:", error);
                $('#loginTableBody').html(`
                    <tr><td colspan="5" class="text-danger text-center">Something went wrong</td></tr>
                `);
            }
        });
    }

    // =====================
    // DELETE BUTTON CLICK
    // =====================
    $(document).on('click', '.delete-btn', function (e) {
         e.preventDefault();
        let id = $(this).data('id');

        if (!confirm("Are you sure you want to delete this record?")) {
            return;
        }

        let formData = new FormData();
        formData.append('id', id);

        $.ajax({
            url: 'api/deletelogin.php',
            type: 'POST',
             dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === true) {
                    alert(response.message);
                    // fetchLoginDetails(); // refresh after delete
                     location.reload();
            
                } else {
                    alert("Error: " + response.message);
                }
            },
           
        });
    });

    // =====================
    // INITIAL LOAD
    // =====================
    fetchLoginDetails();

});
</script>


</body>

</html>