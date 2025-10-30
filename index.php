<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Knitting Nest</title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="stylesheet/responsive.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 500px;
            margin: auto;
            border-radius: 15px;
            border: none;
            padding: 2rem 2.5rem;
            height: 460px;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .login-card h3 {
            font-weight: 600;
            color: #343a40;
        }

        .login-card p {
            font-size: 0.95rem;
            color: #6c757d;
        }

        .form-control {
            height: 50px;
            font-size: 1rem;
            border-radius: 10px;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #6c63ff;
            box-shadow: 0 0 8px rgba(108, 99, 255, 0.3);
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            border: 1px solid #ced4da;
            background-color: #f8f9fa;
        }

        .login-btn {
            background: linear-gradient(90deg, #6c63ff, #3f3dff);
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            height: 50px;
            font-size: 1.1rem;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .login-btn:hover {
            background: linear-gradient(90deg, #3f3dff, #6c63ff);
            transform: translateY(-2px);
        }

        .forgot-link {
            font-size: 0.9rem;
            color: #6c63ff;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: #3f3dff;
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 1.5rem;
                max-width: 100%;
            }

            .form-control {
                height: 45px;
                font-size: 0.95rem;
            }

            .login-btn {
                height: 45px;
                font-size: 1rem;
            }
        }

        .vh-100 {
            height: 89vh !important;
        }

        .navbar-brand img {
            height: 56px !important;
            width: 60px !important;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container justify-content-center">
            <a class="navbar-brand d-flex align-items-center">
                <img src="images/logo1.png" alt="Logo" class="d-inline-block align-text-top me-2">
                <h3 class="fw-bold mb-0" style="font-size: 2rem; ">Knitting Nest</h3>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card login-card shadow">
            <h3 class="text-center mb-2">Welcome Back</h3>
            <p class="text-center mb-4">Enter your credentials to access the Knitting Nest.</p>

            <form id="loginForm">
                <!-- Bio ID -->
                <div class="mb-3">
                    <label for="bioid" class="form-label fw-semibold">Shopkeeper Bioid</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fa-solid fa-id-card"></i>
                        </span>
                        <input type="text" class="form-control" id="bioid" name="bioid"
                            placeholder="Enter your Shopkeeper ID or Employee ID" required>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Enter your password" required>
                        <span class="input-group-text bg-white" id="togglePassword" style="cursor: pointer;">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>

                <!-- Forgot Password -->
                <div class="mb-3 text-end">
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <!-- Login Button -->
                <div class="d-flex align-items-center justify-content-center">
                    <button type="submit" class="btn btn-secondary w-50">Login</button>

                </div>
            </form>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Toggle password visibility
            $("#togglePassword").click(function () {
                let passwordInput = $("#password");
                let icon = $(this).find("i");
                if (passwordInput.attr("type") === "password") {
                    passwordInput.attr("type", "text");
                    icon.removeClass("fa-eye").addClass("fa-eye-slash");
                } else {
                    passwordInput.attr("type", "password");
                    icon.removeClass("fa-eye-slash").addClass("fa-eye");
                }
            });
            $("#loginForm").on("submit", function (e) {
                e.preventDefault();

                // Prepare JSON data
                let formData = {
                    bioid: $("#bioid").val(),
                    password: $("#password").val()
                };

                $.ajax({
                    url: "api/login.php",
                    type: "POST",
                    contentType: "application/json", // Send as JSON
                    data: JSON.stringify(formData),   // Convert JS object to JSON string
                    dataType: "json",                 // Expect JSON response
                    success: function (res) {
                        // No need for JSON.parse(res)
                        if (res.status === 200) {
                            alert("Login Success! Welcome " + res.usertype + "\nBio ID: " + res.bio_id);
                            // Redirect based on usertype if needed
                            if (res.usertype === "Admin") {
                                window.location.href = "admin/admin_index.php";
                            } else if (res.usertype === "shopkeeper") {
                                window.location.href = "shopkeeper/shopkeeper_index.php";
                            } else if (res.usertype === "superadmin") {
                                window.location.href = "superadmin/superadmin_index.php";
                            }
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function () {
                        alert("API request failed!");
                    }
                });
            });
        });
    </script>


</body>

</html>