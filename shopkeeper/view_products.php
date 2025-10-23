<?php
session_start(); // 🔥 Must be first
include('../config/config.php');

// Check if user is logged in
if (!isset($_SESSION['bio_id'])) {
    // Redirect to index page if not logged in
    header("Location: ../index.php");
    exit;
}

$bioid = $_SESSION['bio_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin</title>
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="../stylesheet/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .form-control:disabled,
        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        }

        .container {
            padding: 30px;
        }
    </style>
</head>

<body>
    <?php include('includes/sidebar.php') ?>

    <main class="content">
        <?php include('includes/header.php'); ?>

        <div class="scroll-section">

            <section class="border rounded">
                <div class="container mt-4">

                    <?php
                    // Get the shop_id for this shopkeeper
                    $stmt = $conn->prepare("SELECT shop_id FROM shopkeeper WHERE shopkeeper_bioid = ?");
                    $stmt->bind_param("i", $bioid);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $shop_id = $row['shop_id'];

                        // Get the shop name
                        $stmt2 = $conn->prepare("SELECT stores_name FROM shops WHERE id = ?");
                        $stmt2->bind_param("i", $shop_id);
                        $stmt2->execute();
                        $shop_result = $stmt2->get_result();

                        $shop_name = "Unknown Shop";
                        if ($shop_result->num_rows > 0) {
                            $shop_row = $shop_result->fetch_assoc();
                            $shop_name = $shop_row['stores_name'];
                        }

                        echo "<h6 class='mb-3'>Shop Name: <strong>" . htmlspecialchars($shop_name) . "</strong></h6>";

                        // Fetch products for this shop
                        $stmt3 = $conn->prepare("SELECT * FROM items WHERE store_id = ?");
                        $stmt3->bind_param("i", $shop_id);
                        $stmt3->execute();
                        $products = $stmt3->get_result();

                        if ($products->num_rows > 0) {
                            echo '<div class="table-responsive">';
                            echo '<table class="table table-bordered  text-center">';
                            echo '<thead>';
                            echo '<tr>
                        <th>S.No</th>
                        <th>Item Name</th>
                        <th>Price (Rs.)</th>
                        <th>Quantity</th>
                                                <th>Item Image</th>

                      </tr>';
                            echo '</thead>';
                            echo '<tbody>';
                            $count = 1;
                            while ($product = $products->fetch_assoc()) {
                                echo '<tr>
                            <td>' . $count++ . '</td>
                            
                            <td>' . htmlspecialchars($product['item_name']) . '</td>
                            <td>' . htmlspecialchars($product['item_price']) . '</td>
                            <td>' . htmlspecialchars($product['item_quantity']) . '</td>
                            <td><img src="../' . htmlspecialchars($product['items_image']) . '" 
                                     alt="' . htmlspecialchars($product['item_name']) . '" 
                                     style="height:40px; width:40px; object-fit:cover;"></td>
                          </tr>';
                            }
                            echo '</tbody>';
                            echo '</table>';
                            echo '</div>';
                        } else {
                            echo '<p class="text-center">No products available for your shop.</p>';
                        }

                    } else {
                        echo '<p class="text-center">No shop assigned to your account.</p>';
                    }
                    ?>

                </div>
            </section>

        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>

</html>