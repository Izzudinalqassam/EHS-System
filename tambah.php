<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
include "koneksi.php";

//jika tombol simpan diklik
if (isset($_POST['btnSimpan'])) {
    //baca isi inputan form
    $nokartu = $_POST['nokartu'];
    $nama    = $_POST['nama'];
    $NIK     = $_POST['NIK'];
    $no_wa   = $_POST['no_wa'];
    // Determine which department value to use
    if (isset($_POST['tipeInput']) && $_POST['tipeInput'] === 'ketik') {
        $departmen = $_POST['departmen_manual'];
    } else {
        $departmen = $_POST['departmen'];
    }
    $nopol = $_POST['nopol'];

    //simpan ke tabel karyawan
    $simpan = mysqli_query($konek, "INSERT INTO karyawan(nokartu, nama, NIK, no_wa, departmen, nopol) VALUES('$nokartu', '$nama', '$NIK', '$no_wa', '$departmen', '$nopol')");

    //jika berhasil tersimpan, tampilkan pesan Tersimpan
    if ($simpan) {
        $message = "Tersimpan";
        // Kosongkan tabel temporary pendaftaran
        mysqli_query($konek, "DELETE FROM tmp_pendaftaran");
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
    <link rel="icon" href="image/bp.png" type="image/x-icon">
    <title>Dashboard - SB Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
    <style>
        body {
            background-image: url('image/inibackground.webp');
            
            background-position: center;
            background-repeat: no-repeat;
            
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function() {
            setInterval(function() {
                $("#norfid").load('nokartu_pendaftaran.php');
            }, 1000);
        });


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
        <a class="navbar-brand" href="index.php">
            <img src="image/logo bp.png" alt="Logo Perusahaan" style="height: 40px; width: auto; margin-right: 10px;"> SISTEM EHS
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ml-auto ml-md-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
    </nav>

    <body class="sb-nav-fixed">
    <?php include 'components/navbar.php'; ?>
    
    <div id="layoutSidenav">
    <?php include 'components/sidenav.php'; ?>
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <!-- form input -->
                <form method="POST" class="p-4 rounded shadow-sm" style="background: rgba(0,0,0,0.3); max-width: 600px; margin: auto; color: #fff; backdrop-filter: blur(5px); ">
                    <h4 class="mb-4">Tambah Data Karyawan & Pemagang</h4>

                    <div id="norfid" class="form-group">
                    </div>
                    <div class="form-group">
                        <label for="nik"><i class="fas fa-id-badge"></i> NIK <span class="text-danger">*</span></label>
                        <input type="text" name="NIK" id="NIK" class="form-control" placeholder="Nomor Induk Pegawai" required>
                    </div>

                    <div class="form-group">
                        <label for="nama"><i class="fas fa-user"></i> Nama Karyawan <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama Karyawan" required>
                    </div>

                    <div class="form-group">
                        <label for="no_wa"><i class="fas fa-phone"></i> No HP <span class="text-danger">*</span></label>
                        <input type="text" name="no_wa" id="no_wa" class="form-control" placeholder="No. Telepon" required>
                    </div>

                    <div class="form-group">
    <label for="departmen"><i class="fas fa-briefcase"></i> Department <span class="text-danger">*</span></label>
    
    <!-- Radio buttons untuk memilih jenis input -->
    <div class="mb-2">
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="pilihdepartmen" name="tipeInput" class="custom-control-input" value="pilih" checked>
            <label class="custom-control-label" for="pilihdepartmen">Pilih dari daftar</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="ketikdepartmen" name="tipeInput" class="custom-control-input" value="ketik">
            <label class="custom-control-label" for="ketikdepartmen">Ketik manual</label>
        </div>
    </div>
    
    <!-- Dropdown select -->
    <select name="departmen" id="departmen_select" class="form-control" required>
        <option value="" disabled selected>Pilih departmen</option>
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
    <input type="text" name="departmen_manual" id="departmen_manual" class="form-control" placeholder="Ketik nama departmen" style="display: none;">
    </div>
                    
                    <div class="form-group">
                        <label for="nopol"><i class="fas fa-car"></i> Nopol kendaraan <span class="text-danger">*</span></label>
                        <input type="text" name="nopol" id="nopol" class="form-control" placeholder="No polisi kendaraan" required>
                    </div>



                    <button class="btn btn-success btn-block mt-4" name="btnSimpan" id="btnSimpan">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </form>

            </div>
            <!-- Footer -->
            <?php include 'components/footer.php'; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script>
$(document).ready(function() {
    // Handler untuk radio buttons
    $('input[name="tipeInput"]').change(function() {
        if ($(this).val() === 'pilih') {
            $('#departmen_select').show().prop('required', true);
            $('#departmen_manual').hide().prop('required', false).val('');
        } else {
            $('#departmen_select').hide().prop('required', false).val('');
            $('#departmen_manual').show().prop('required', true);
        }
    });

    // Handler untuk form submission
    $('form').submit(function(e) {
        var selectedType = $('input[name="tipeInput"]:checked').val();
        var departmenValue = selectedType === 'pilih' ? 
            $('#departmen_select').val() : 
            $('#departmen_manual').val();
        
        // Set nilai ke hidden input untuk dikirim ke server
        if (!$('input[name="departmen"]').length) {
            $('<input>').attr({
                type: 'hidden',
                name: 'departmen'
            }).appendTo('form');
        }
        $('input[name="departmen"]').val(departmenValue);
    });
});
</script>
</body>

</html>