<!-- Offcanvas Sidebar (Mobile) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Dashboard Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0">
        <div class="sidebar border-0 position-static">

            <!-- Brand -->
            <a href="shopkeeper_index.php" class="navbar-brand d-flex align-items-center gap-2 mb-4 px-3">
                <div class="logo">
                    <img src="../images/logo1.png" alt="logo" width="30" height="30">
                </div>
                <span class="fw-bold text-uppercase text-dark">KNITTING NEST</span>
            </a>
            <a href="shopkeeper_index.php" class="nav-link d-flex align-items-center gap-2"> <i
                    class="bi bi-house-door-fill"></i> Dashboard </a>
            <!-- Sales -->
            <a href="sales.php" class="nav-link d-flex align-items-center gap-2 px-3 mb-1">
                <i class="bi bi-currency-dollar"></i> Sales
            </a>

            <!-- View Products -->
            <a href="view_products.php" class="nav-link d-flex align-items-center gap-2 px-3 mb-1">
                <i class="bi bi-box-seam"></i> View Products
            </a>

            <!-- Received Products -->
            <a href="received_products.php" class="nav-link d-flex align-items-center gap-2 px-3 mb-1">
                <i class="bi bi-bag-check-fill"></i> Received Products
            </a>

            <!-- Sales Report -->
            <a href="sales_report.php" class="nav-link d-flex align-items-center gap-2 px-3 mb-1">
                <i class="bi bi-graph-up"></i> Sales Report
            </a>

            <!-- Profile Section -->
            <div class="profile-section mt-4 px-3 d-flex align-items-center gap-2">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Profile" class="rounded-circle"
                    width="40" height="40" />
                <span>Jane Smith</span>
            </div>

        </div>
    </div>
</div>

<!-- Desktop Sidebar -->
<aside class="sidebar d-none d-lg-block">

    <!-- Brand -->
    <a href="shopkeeper_index.php" class="navbar-brand d-flex align-items-center gap-2 mb-4">
        <div class="logo">
            <img src="../images/logo1.png" alt="logo" width="30" height="30">
        </div>
        <span class="fw-bold text-uppercase text-dark">KNITTING NEST</span>
    </a>
    <a href="shopkeeper_index.php" class="nav-link d-flex align-items-center gap-2"> <i
            class="bi bi-house-door-fill"></i>
        Dashboard </a>
    <!-- Sales -->
    <a href="sales.php" class="nav-link d-flex align-items-center gap-2 mb-1">
        <i class="bi bi-currency-dollar"></i> Sales
    </a>

    <!-- View Products -->
    <a href="view_products.php" class="nav-link d-flex align-items-center gap-2 mb-1">
        <i class="bi bi-box-seam"></i> View Products
    </a>

    <!-- Received Products -->
    <a href="received_products.php" class="nav-link d-flex align-items-center gap-2 mb-1">
        <i class="bi bi-bag-check-fill"></i> Received Products
    </a>

    <!-- Sales Report -->
    <a href="sales_report.php" class="nav-link d-flex align-items-center gap-2 mb-1">
        <i class="bi bi-graph-up"></i> Sales Report
    </a>

    <div class="profile-section mt-4 text-center">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Profile" class="rounded-circle mb-2"
            width="60" height="60" />

        <?php
        // Fetch from session
        $bio_id = $_SESSION['bio_id'] ?? 'Unknown';
        $user_type = $_SESSION['usertype'] ?? 'Guest';
        ?>

        <div class="fw-semibold text-dark">
            <?php echo htmlspecialchars(ucfirst($user_type)) . ' (' . htmlspecialchars($bio_id) . ')'; ?>
        </div>
    </div>

</aside>