<?php
error_reporting(0);
include "koneksi.php"; // Koneksi ke database

// Baca ID data yang akan di-edit
$id = $_GET['id'];

// Baca data karyawan berdasarkan ID
$query = mysqli_query($konek, "SELECT * FROM karyawan WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

// Jika tombol simpan diklik
if (isset($_POST['btnSimpan'])) {
    // Baca isi inputan form
    $nokartu = $_POST['nokartu'];
    $nik = $_POST['nik'];
    $nama = $_POST['nama'];
    $departmen = $_POST['departmen'];
    $no_wa = $_POST['no_wa']; // Baca no_hp (no_wa)

    // Simpan ke tabel karyawan
    $update = mysqli_query($konek, "UPDATE karyawan SET nokartu='$nokartu', nik='$nik', nama='$nama', departmen='$departmen', no_wa='$no_wa' WHERE id='$id'");

    if ($update) {
        $message = "Tersimpan";
    } else {
        $message = "Gagal Tersimpan";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Dashboard - SB Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="icon" href="image/bp.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript">
        // Menampilkan popup setelah halaman dimuat
        <?php if (isset($message)): ?>
            $(document).ready(function() {
                Swal.fire({
                    icon: '<?php echo ($message == "Tersimpan") ? "success" : "error"; ?>',
                    title: '<?php echo $message; ?>',
                    text: '<?php echo ($message == "Tersimpan") ? "Data berhasil disimpan!" : "Data gagal disimpan!"; ?>',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'datakaryawan.php'; // Kembali ke data karyawan
                    }
                });
            });
        <?php endif; ?>
    </script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">SISTEM EHS</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
        <!-- Navbar-->
        <ul class="navbar-nav ml-auto ml-md-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="login.html">Logout</a>
                </div>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                            Home
                        </a>
                        <a class="nav-link" href="datakaryawan.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                            Data Karyawan
                        </a>
                        <a class="nav-link" href="datatamu.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                            Data Tamu
                        </a>
                        <a class="nav-link" href="absensi.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            Rekap Karyawan
                        </a>

                        <a class="nav-link" href="absensi_magang.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                            Rekap Magang
                        </a>
                        <a class="nav-link" href="riwayat.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div>
                            Riwayat Absen
                        </a>
                        <a class="nav-link" href="scan.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-id-card"></i></div>
                            Scan Kartu
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    Start Bootstrap
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <form method="POST" class="bg-white p-4 rounded shadow-sm" style="max-width: 600px; margin: auto;">
                    <h4 class="mb-4">Edit Data Karyawan</h4>
                    <div class="form-group">
                        <label for="nokartu"><i class="fas fa-id-card"></i> No. Kartu <span class="text-danger">*</span></label>
                        <input type="text" name="nokartu" id="nokartu" class="form-control" value="<?= htmlspecialchars($data['nokartu']); ?>" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="nik"><i class="fas fa-id-badge"></i> NIK <span class="text-danger">*</span></label>
                        <input type="text" name="nik" id="nik" class="form-control" value="<?= htmlspecialchars($data['NIK']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nama"><i class="fas fa-user"></i> Nama Karyawan <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control" value="<?= htmlspecialchars($data['nama']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="departmen"><i class="fas fa-briefcase"></i> Department <span class="text-danger">*</span></label>
                        <input type="text" name="departmen" id="departmen" class="form-control" list="departmen-list" value="<?= htmlspecialchars($data['departmen']); ?>" required>
                        <datalist id="departmen-list">
                            <option value="Operation">
                            <option value="Distribution">
                            <option value="Warehouse">
                            <option value="After Sales">
                            <option value="Maintenance">
                            <option value="Elektrikal">
                            <option value="Instrument">
                            <option value="Engineering">
                            <option value="CSR">
                            <option value="Planer">
                            <option value="Commercial">
                            <option value="EHS">
                            <option value="Procurement">
                            <option value="Accounting">
                            <option value="HR & GA">
                            <option value="Finance">
                            <option value="Fin & Adm">
                            <option value="IT">
                            <option value="BOD">
                            <option value="Niaga & Perencaan">
                            <option value="Project">
                            <option value="Distribusi">
                            <option value="Magang">
                        </datalist>
                    </div>
                    <div class="form-group">
                        <label for="no_wa"><i class="fas fa-phone"></i> No HP <span class="text-danger">*</span></label>
                        <input type="text" name="no_wa" id="no_wa" class="form-control" value="<?= htmlspecialchars($data['no_wa']); ?>" required>
                    </div>

                    <button class="btn btn-success btn-block mt-4" name="btnSimpan" id="btnSimpan">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </form>
            </div>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright BP EHS &copy; Dwiyan's and Alka 2024</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                    <div id="cekkartu"></div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>