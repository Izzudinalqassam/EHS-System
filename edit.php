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
    $nopol = $_POST['nopol']; 
    // Simpan ke tabel karyawan
    $update = mysqli_query($konek, "UPDATE karyawan SET nokartu='$nokartu', nik='$nik', nama='$nama', departmen='$departmen', no_wa='$no_wa', nopol='$nopol' WHERE id='$id'");

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
<style>
        body {
            background-image: url('image/inibackground.webp');
            
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
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
    <?php include 'components\sidenav.php'; ?>
        <div id="layoutSidenav_content">
            <div class="container-fluid">
            <form method="POST" class="p-4 rounded shadow-sm" style="background: rgba(0,0,0,0.3); max-width: 600px; margin: auto; color: #fff; backdrop-filter: blur(5px); ">
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
                        <select name="departmen" id="departmen" class="form-control" required>
                            <option value="" disabled selected>Pilih Departemen</option>
                            <option value="Accounting">Accounting</option>
                            <option value="After Sales">After Sales</option>
                            <option value="BAP">BAP</option>
                            <option value="BOD">BOD</option>
                            <option value="Commercial">Commercial</option>
                            <option value="CSR">CSR</option>
                            <option value="Distribution">Distribution</option>
                            <option value="Distribusi">Distribusi</option>
                            <option value="EHS">EHS</option>
                            <option value="Elektrikal">Elektrikal</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Finance">Finance</option>
                            <option value="Fin & Adm">Fin & Adm</option>
                            <option value="HR & GA">HR & GA</option>
                            <option value="Instrument">Instrument</option>
                            <option value="IT">IT</option>
                            <option value="Magang">Magang</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Niaga & Perencaan">Niaga & Perencaan</option>
                            <option value="Operation">Operation</option>
                            <option value="Planer">Planer</option>
                            <option value="Procurement">Procurement</option>
                            <option value="Project">Project</option>
                            <option value="Warehouse">Warehouse</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="no_wa"><i class="fas fa-phone"></i> No HP <span class="text-danger"></span></label>
                        <input type="text" name="no_wa" id="no_wa" class="form-control" value="<?= htmlspecialchars($data['no_wa']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nopol"><i class="fas fa-car"></i> Nomor Kendaraan <span class="text-danger"></span></label>
                        <input type="text" name="nopol" id="nopol" class="form-control" value="<?= htmlspecialchars($data['nopol']); ?>" required>
                    </div>

                    <button class="btn btn-success btn-block mt-4" name="btnSimpan" id="btnSimpan">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </form>
            </div>
            <?php include 'components\footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>