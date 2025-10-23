<!-- Offcanvas Sidebar (For Mobile) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Dashboard Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0">
        <div class="sidebar border-0 position-static">

            <!-- Brand -->
            <a href="superadmin_index.php" class="navbar-brand d-flex align-items-center gap-2 mb-4 px-3">
                <div class="logo">
                    <img src="../images/logo1.png" alt="logo" width="30" height="30">
                </div>
                <span class="fw-bold text-uppercase text-dark">KNITTING NEST</span>
            </a>

            <!-- Dashboard Link -->
            <a href="admin_index.php" class="nav-link d-flex align-items-center gap-2 px-3 mb-1">
                <i class="bi bi-house-door-fill"></i> Dashboard
            </a>

            <!-- Items Collapsible -->
            <li class="list-unstyled">
                <a href="#itemMenuMobile" class="nav-link d-flex align-items-center gap-2 px-3"
                    data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="itemMenuMobile">

                    <!-- Main icon for menu -->
                    <i class="bi bi-box-seam fs-5 text-primary"></i>

                    <!-- Menu text -->
                    <span>Items</span>

                    <!-- Chevron for collapse toggle -->
                    <i class="bi bi-chevron-down ms-auto fs-6"></i>
                </a>

                <ul class="collapse sidebar-nav ps-4 mt-1 list-unstyled" id="itemMenuMobile">
                    <li>
                        <a href="add_items.php" class="d-flex align-items-center gap-2 px-3">
                            <i class="bi bi-plus-square-fill"></i> Add Items
                        </a>
                    </li>
                    <li>
                        <a href="manage_items.php" class="d-flex align-items-center gap-2 px-3">
                            <i class="bi bi-list-ul"></i> Store Details
                        </a>
                    </li>
                    <li>
                        <a href="stock_details.php" class="d-flex align-items-center gap-2 px-3">
                            <i class="bi bi-box-seam"></i>  Stock Details
                        </a>
                    </li>
                    <li>
                        <a href="purchase_order.php" class="d-flex align-items-center gap-2 px-3">
                            <i class="bi bi-bag-fill"></i> Purchase Order
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Transfer Details Collapsible -->
            <li class="list-unstyled">
                <a href="#transferMenuMobile" class="nav-link d-flex align-items-center gap-2 px-3"
                    data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="transferMenuMobile">
                    <i class="bi bi-pencil-square"></i> Transfer Details
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul class="collapse sidebar-nav ps-4 mt-1 list-unstyled" id="transferMenuMobile">
                    <li>
                        <a href="transfer_product.php" class="d-flex align-items-center gap-2 px-3">
                            <i class="bi bi-arrow-left-right"></i> Items Transfer
                        </a>
                    </li>
                    <li>
                        <a href="transfer_details.php" class="d-flex align-items-center gap-2 px-3">
                            <i class="bi bi-list-ul"></i> List of Transfer Details
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Sales Report -->
            <a href="sales_report.php" class="nav-link d-flex align-items-center gap-2 px-3 mt-1">
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
    <a href="superadmin_index.php" class="navbar-brand d-flex align-items-center gap-2 mb-4">
        <div class="logo">
            <img src="../images/logo1.png" alt="logo" width="30" height="30">
        </div>
        <span class="fw-bold text-uppercase text-dark">KNITTING NEST</span>
    </a>

    <a href="admin_index.php" class="nav-link d-flex align-items-center gap-2">
        <i class="bi bi-house-door-fill"></i> Dashboard
    </a>

    <li>
        <a href="#itemDesktop" class="nav-link d-flex align-items-center gap-2" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="itemDesktop">
            <i class="bi bi-box-seam"></i>Items
            <i class="bi bi-chevron-down ms-auto"></i>

        </a>
        <ul class="collapse sidebar-nav ps-4 mt-1" id="itemDesktop">
            <li>
                <a href="add_items.php" class="d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left-right"></i>Add Items
                </a>
            </li>
            <li>
                <a href="manage_items.php" class="d-flex align-items-center gap-2">
                    <i class="bi bi-list-ul"></i>Store Details

                </a>
            </li>
            <li>
                <a href="stock_details.php" class="d-flex align-items-center gap-2">
                    <i class="bi bi-box-seam"></i> Stock Details
                </a>
            </li>
            <li>
                <a href="purchase_order.php" class="d-flex align-items-center gap-2">
                    <i class="bi bi-bag-fill"></i> Purchase Order
                </a>
            </li>

        </ul>
    </li>




    <!-- Transfer Details Collapsible -->
    <li>
        <a href="#transferMenuDesktop" class="nav-link d-flex align-items-center gap-2" data-bs-toggle="collapse"
            role="button" aria-expanded="false" aria-controls="transferMenuDesktop">
            <i class="bi bi-pencil-square"></i> Transfer Details
            <i class="bi bi-chevron-down ms-auto"></i>

        </a>
        <ul class="collapse sidebar-nav ps-4 mt-1" id="transferMenuDesktop">
            <li>
                <a href="transfer_product.php" class="d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left-right"></i> Items Transfer
                </a>
            </li>
            <li>
                <a href="view_transfer_details.php" class="d-flex align-items-center gap-2">
                    <i class="bi bi-list-ul"></i> View Transfer Details
                </a>
            </li>

        </ul>
    </li>

    <a href="sales_report.php" class="nav-link d-flex align-items-center gap-2">
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