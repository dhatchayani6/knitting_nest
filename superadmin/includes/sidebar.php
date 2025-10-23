<!-- Offcanvas Sidebar (For Mobile) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Dashboard Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0">
        <div class="sidebar border-0 position-static">
            <!-- <a href="superadmin_index.php" class="nav-link d-flex align-items-center gap-2">
                <i class="bi bi-house-door-fill"></i> Dashboard
            </a> -->
            <a href="add_stores.php" class="nav-link d-flex align-items-center gap-2">
                <i class="bi bi-building"></i> Add Stores
            </a>
            <a href="add_shopkeeper.php" class="nav-link d-flex align-items-center gap-2">
                <i class="bi bi-person-plus-fill"></i> Add Shopkeeper
            </a>
            <a href="add_vendor.php" class="nav-link d-flex align-items-center gap-2">
                <i class="bi bi-people-fill"></i> Add Vendor
            </a>
        </div>
    </div>
</div>

<!-- Desktop Sidebar -->
<aside class="sidebar d-none d-lg-block">
    <a href="#" class="navbar-brand d-flex align-items-center gap-2 mb-4">
        <div class="logo">
            <img src="../images/logo1.png" alt="logo" width="30" height="30">
        </div>
        <span class="fw-bold text-uppercase text-dark">KNITTING NEST</span>
    </a>
    <!-- <a href="superadmin_index.php" class="nav-link d-flex align-items-center gap-2">
        <i class="bi bi-house-door-fill"></i> Dashboard
    </a> -->
    <a href="add_stores.php" class="nav-link d-flex align-items-center gap-2">
        <i class="bi bi-building"></i> Add Stores
    </a>
    <a href="add_shopkeeper.php" class="nav-link d-flex align-items-center gap-2">
        <i class="bi bi-person-plus-fill"></i> Add Shopkeeper
    </a>
    <a href="add_vendor.php" class="nav-link d-flex align-items-center gap-2">
        <i class="bi bi-people-fill"></i> Add Vendor
    </a>

    <div class="profile-section mt-4 text-center">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Profile" class="rounded-circle mb-2"
            width="60" height="60" />

        <?php
        session_start();

        // Fetch from session
        $bio_id = $_SESSION['bio_id'] ?? 'Unknown';
        $user_type = $_SESSION['usertype'] ?? 'Guest';
        ?>

        <div class="fw-semibold text-dark">
            <?php echo htmlspecialchars(ucfirst($user_type)) . ' (' . htmlspecialchars($bio_id) . ')'; ?>
        </div>
    </div>


</aside>