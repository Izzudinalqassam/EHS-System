<!-- components/navbar.php -->
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark shadow-sm">
    <a class="navbar-brand" href="index.php">
        <img src="image/logo_bp.png" alt="PT Bekasi Power" style="height: 40px; width: auto; margin-right: 10px;">
        <span class="d-none d-lg-inline">SISTEM EHS</span>
    </a>
    
    <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#">
        <i class="fas fa-bars"></i>
    </button>
    
    <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item">
            <?php if (isset($_SESSION['role'])):
                $role_class = 'badge-secondary';
                if ($_SESSION['role'] === 'admin') {
                    $role_class = 'badge-success';
                } elseif ($_SESSION['role'] === 'security') {
                    $role_class = 'badge-warning';
                }
            ?>
                <span class="badge <?php echo $role_class; ?> p-2"><strong><?php echo strtoupper($_SESSION['role']); ?></strong></span>
            <?php endif; ?>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-white-50 small"><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?></span>
                <i class="fas fa-user-circle fa-2x"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a class="dropdown-item" href="manage_users.php">
                        <i class="fas fa-users-cog fa-sm fa-fw mr-2 text-gray-400"></i>
                        Manage Users
                    </a>
                    <div class="dropdown-divider"></div>
                <?php endif; ?>
                <a class="dropdown-item" href="logout.php">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>