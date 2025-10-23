<!-- Header -->
<header class="header-bar d-flex align-items-center justify-content-between px-3 py-2 bg-light shadow-sm">
    <!-- Sidebar Toggle (mobile) -->
    <button class="btn  d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
        <i class="bi bi-list"></i>
    </button>

    <!-- Title -->
    <h5 class="mb-0 text-dark flex-grow-1 text-center">SUPER ADMIN</h5>

    <!-- Notifications & Profile -->
    <div class="d-flex align-items-center gap-3">
        <!-- Notification Icon -->
        <!-- <button class="btn btn-link text-dark p-0 fs-5" title="Notifications">
            <i class="bi bi-bell"></i>
        </button> -->

        <!-- Logout Button -->
        <form action="../api/logout.php" method="POST" class="m-0">
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>

</header>