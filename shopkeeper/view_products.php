<?php
session_start(); // 🔥 Must be first
include('../includes/config.php');

// Check if bio_id exists in the session
if (!isset($_SESSION['bio_id'])) {
    echo '<p class="text-center">Please log in to view your products.</p>';
    exit;
}

$bioid = $_SESSION['bio_id'];
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Shop Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" id="theme-style" href="css/app.css">
</head>

<body>
    <div class="main-wrapper">
        <div class="app" id="app">

            <!-- Header -->
            <?php include('includes/header.php'); ?>

            <!-- Sidebar -->
            <?php include('includes/sidebar.php'); ?>

            <!-- Center Content -->
            <article class="content dashboard-page bg-white">
                <section>
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

                            echo "<h4 class='mb-3'>Shop Name: <strong>" . htmlspecialchars($shop_name) . "</strong></h4>";

                            // Fetch products for this shop
                            $stmt3 = $conn->prepare("SELECT * FROM items WHERE store_id = ?");
                            $stmt3->bind_param("i", $shop_id);
                            $stmt3->execute();
                            $products = $stmt3->get_result();

                            if ($products->num_rows > 0) {
                                echo '<div class="row justify-content-center">';
                                while ($product = $products->fetch_assoc()) {
                                    echo '
                                    <div class="col-sm-6 col-md-4 mb-3">
                                        <div class="card shadow-sm h-100">
                                            <img src="../' . htmlspecialchars($product['items_image']) . '" 
                                                class="card-img-top" 
                                                alt="' . htmlspecialchars($product['item_name']) . '" 
                                                style="height:200px; object-fit:cover;">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">' . htmlspecialchars($product['item_name']) . '</h5>
                                                <p class="card-text mb-1">Price: ₹' . htmlspecialchars($product['item_price']) . '</p>
                                                <p class="card-text">Quantity: ' . htmlspecialchars($product['item_quantity']) . '</p>
                                            </div>
                                        </div>
                                    </div>';


                                }
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
            </article>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/vendor.js"></script>
    <script src="js/app.js"></script>
</body>

</html>