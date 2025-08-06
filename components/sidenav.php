<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Menu</div>
                <a class="nav-link" href="index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                    Home
                </a>
                <a class="nav-link" href="absensi.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                    Rekapitulasi
                </a>
                
                <!-- Menu yang dapat diakses oleh kedua role -->
                <a class="nav-link" href="datatamu.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                    Data Tamu
               
                <a class="nav-link" href="riwayat.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div>
                    Riwayat Absen
                </a>

                </a>
                <a class="nav-link" href="scan.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-id-card"></i></div>
                    Scan Kartu
                </a>
                <a class="nav-link" href="data_kendaraan.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-car"></i></div>
                    Data Kendaraan
                </a>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <!-- Menu khusus Admin -->
                <a class="nav-link" href="datakaryawan.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                    Data Karyawan
                </a>
              
                
                <a class="nav-link" href="manage_users.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                    Kelola Pengguna
                </a>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'security'): ?>
                <!-- Menu khusus Security -->
               
                <?php endif; ?>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">PT.Bekasi Power </div>
            <?php if (isset($_SESSION['role'])): ?>
                Departemen EHS (<?php echo ucfirst($_SESSION['role']); ?>)
            <?php else: ?>
                Departemen EHS
            <?php endif; ?>
        </div>
    </nav>
</div>