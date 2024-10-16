<?php
error_reporting(0);
include "koneksi.php"; // Koneksi ke database
date_default_timezone_set('Asia/Jakarta');

$status_message = ''; // Variabel untuk status pesan

//jika tombol simpan diklik
if (isset($_POST['btnSimpan'])) {
    //baca isi inputan form
    $nama_tamu = $_POST['nama_tamu'];
    $nama_perusahaan = $_POST['nama_perusahaan'];
    $keperluan = $_POST['keperluan'];
    $ingin_bertemu = $_POST['ingin_bertemu'];
    $jam_masuk_tamu = date("H:i:s"); // Jam masuk otomatis
    $jam_keluar_tamu = ""; // Jam keluar otomatis (akan diisi saat keluar)
    $tanggal_tamu = date("Y-m-d"); // Tanggal otomatis
    $nopol = $_POST['nopol'];

    // ambil no_wa dari karyawan berdasarkan nama yang dipilih
    $query = "SELECT no_wa FROM karyawan WHERE nama = '$ingin_bertemu'";
    $result = mysqli_query($konek, $query);
    $row = mysqli_fetch_assoc($result);
    $no_wa = $row['no_wa'];

    //simpan ke tabel tamu
    $simpan = mysqli_query($konek, "INSERT INTO karyawan( nama_tamu, nama_perusahaan, keperluan, ingin_bertemu, jam_masuk_tamu, jam_keluar_tamu, tanggal_tamu, nopol) VALUES( '$nama_tamu', '$nama_perusahaan', '$keperluan', '$ingin_bertemu', '$jam_masuk_tamu', '$jam_keluar_tamu', '$tanggal_tamu', '$nopol')");

    //jika berhasil tersimpan, kirim WA dan tampilkan pesan Tersimpan
    if ($simpan) {
        // Kirim WhatsApp menggunakan Fonnte API
        $curl = curl_init();
        $message = "Ada tamu bernama $nama_tamu dari $nama_perusahaan yang ingin bertemu dengan Anda untuk keperluan $keperluan.";

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $no_wa, // Menggunakan nomor WA dari database
                'message' => $message // Menggunakan pesan yang berisi nama dan keperluan
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Y4guD@NHqj9Has9n7R_g'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $status_message = 'Tersimpan';
    } else {
        $status_message = 'Gagal Tersimpan';
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        body {
            background-image: url('image/inibackground.webp');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
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
                

                <!-- form input -->
                <form method="POST" class="p-4 rounded shadow-sm" style="background: rgba(0,0,0,0.3); max-width: 600px; margin: auto; color: #fff; backdrop-filter: blur(5px); ">
                    <h4 class="mb-4">Tambah Data Tamu</h4>

                    <div class="form-group">
                        <label for="nama_tamu"><i class="fas fa-user"></i> Nama Tamu <span class="text-danger">*</span></label>
                        <input required type="text" name="nama_tamu" id="nama_tamu" placeholder="Nama Tamu" class="form-control" style="width: 100%;">
                    </div>

                    <div class="form-group">
                        <label for="nama_perusahaan"><i class="fas fa-building"></i> Nama Perusahaan</label>
                        <input type="text" name="nama_perusahaan" id="nama_perusahaan" placeholder="Nama Perusahaan" class="form-control" style="width: 100%;">
                    </div>

                    <div class="form-group">
                        <label for="keperluan"><i class="fas fa-file-alt"></i> Keperluan</label>
                        <input type="text" name="keperluan" id="keperluan" placeholder="Keperluan" class="form-control" style="width: 100%;">
                    </div>

                    <div class="form-group">
                        <label for="ingin_bertemu"><i class="fas fa-users"></i> Ingin Bertemu <span class="text-danger">*</span></label>
                        <br>
                        <select name="ingin_bertemu" id="ingin_bertemu" style="width: 100%;">
                            <option value="" disabled selected>Pilih Karyawan</option>
                            <?php
                            $query = "SELECT * FROM karyawan WHERE departmen IS NOT NULL AND departmen != ''";
                            $result = mysqli_query($konek, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['nama'] . "'>" . $row['nama'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nopol"><i class="fas fa-car"></i> Nomor Kendaraan</label>
                        <input type="text" name="nopol" id="nopol" placeholder="Nomor Kendaraan" class="form-control" style="width: 100%;">
                    </div>

                    <button type="submit" name="btnSimpan" class="btn btn-success btn-block mt-4">
                        <i class="fas fa-save"></i> Simpan
                    </button>

                    <p style="font-style: italic; text-align: center;" class="mt-3">
                        Note: Setelah Klik Simpan, Pastikan Tunggu Hingga Berhasil atau Tersimpan Dan Pastikan Internet Bagus.
                    </p>
                </form>

            </div>

            <script>
                $(document).ready(function() {
                    $('#ingin_bertemu').select2();
                });

                // Menampilkan SweetAlert setelah halaman dimuat
                <?php if ($status_message): ?>
                    Swal.fire({
                        icon: '<?php echo $status_message === 'Tersimpan ' ? 'error' : 'success'; ?>',
                        title: '<?php echo $status_message; ?>',
                        showConfirmButton: true,
                    }).then(function() {
                        window.location.href = "datatamu.php";
                    });
                <?php endif; ?>
            </script>

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
</body>

</html>