<?php
error_reporting(0);
include "koneksi.php"; // Koneksi ke database

date_default_timezone_set('Asia/Jakarta');

// Periksa apakah ada permintaan untuk mengeluarkan tamu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $jam_keluar = date('H:i:s'); // Dapatkan waktu saat ini

    // Update jam_keluar_tamu di database
    $updateQuery = "UPDATE karyawan SET jam_keluar_tamu = '$jam_keluar' WHERE id = '$id'";
    mysqli_query($konek, $updateQuery);
    
    // Redirect ke halaman datatamu.php setelah update
    header("Location: datatamu.php");
    exit(); // Pastikan tidak ada kode lain yang dieksekusi setelah ini
}

// Query untuk mengambil data tamu
$tamuQuery = mysqli_query($konek, "SELECT * FROM karyawan WHERE nama_tamu != ''");

// Menghitung jumlah tamu
// Menghitung jumlah tamu (nik kosong)
$totalTamuQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM karyawan WHERE jam_keluar_tamu = '00:00:00' AND nik = ''");
$totalTamu = mysqli_fetch_assoc($totalTamuQuery)['total'];

// Menghitung jumlah tamu yang sudah keluar (nik kosong)
$totalKeluarQuery = mysqli_query($konek, "SELECT COUNT(*) as total_keluar FROM karyawan WHERE jam_keluar_tamu != '00:00:00' AND nik = ''");
$totalKeluar = mysqli_fetch_assoc($totalKeluarQuery)['total_keluar'];

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
    <!-- CSS SB Admin -->
    <style>
    /* CSS untuk mengubah warna latar belakang baris tabel saat cursor melewati */
    table tbody tr:hover {
        background-color: #f2f2f2; /* Ubah warna sesuai kebutuhan */
    }
</style>

    <link href="css/styles.css" rel="stylesheet" />
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            setInterval(function() {
                $("#cekkartu").load('bacakartu.php');
            }, 2000);
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "searching": true, // Aktifkan fitur pencarian
                "ordering": true,  // Aktifkan fitur sortir
                "paging": true,    // Aktifkan pagination
                "order": [[0, "asc"]] // Urutkan berdasarkan kolom pertama secara ascending
            });
        });

        function confirmExit(id) {
            let button = document.getElementById('exitButton_' + id);
            if (button.classList.contains('btn-secondary')) {
                return;
            }
            button.classList.add('btn-secondary');
            button.disabled = true;
            Swal.fire({
                title: 'Apakah Anda yakin bahwa tamu akan keluar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, keluarkan!',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form secara dinamis
                    document.getElementById('exitForm_' + id).submit();
                } else {
                    button.classList.remove('btn-secondary');
                    button.disabled = false;
                }
            }).catch(error => {
                console.error('Error on confirmExit:', error);
            });
        }
    </script></head>

<body class="sb-nav-fixed">
    <!-- Navbar -->
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

    <!-- Layout Sidenav -->
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
                        <a class="nav-link active" href="datatamu.php" style="color: white;">
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

        <!-- Main Content -->
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                <h2 class="mt-4">Dashboard Sistem EHS Data <?php echo isset($_POST['filter-date']) ? ' - Tanggal: ' . $_POST['filter-date'] : ''; ?></h2>


                    <!-- Kartu Jumlah Tamu -->
                    <div class="row">
                        <div class="col-xl-6 col-sm-6 mb-3">
                            <div class="card text-white bg-success o-hidden h-100">
                                <div class="card-body">
                                    <div class="card-body-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="mr-5">Tamu Masih Ada: <?php echo $totalTamu; ?></div>
                                </div>
                                <a class="card-footer text-white clearfix small z-1" href="datatamu.php">
                                    <span class="float-left">Lihat Detail</span>
                                    <span class="float-right">
                                        <i class="fas fa-angle-right"></i>
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-6 col-sm-6 mb-3">
                            <div class="card text-white bg-danger o-hidden h-100">
                                <div class="card-body">
                                    <div class="card-body-icon">
                                        <i class="fas fa-user-slash"></i>
                                    </div>
                                    <div class="mr-5">Tamu Sudah Keluar: <?php echo $totalKeluar; ?></div>
                                </div>
                                <a class="card-footer text-white clearfix small z-1" href="datatamu.php">
                                    <span class="float-left">Lihat Detail</span>
                                    <span class="float-right">
                                        <i class="fas fa-angle-right"></i>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <form method="post" action="">
                        <div class="form-row align-items-center mb-3">
                            <div class="col-auto">
                                <label for="filter-date">Filter Tanggal:</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" class="form-control" id="filter-date" name="filter-date" value="<?php echo isset($_POST['filter-date']) ? $_POST['filter-date'] : date('Y-m-d'); ?>">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Cari</button>
                            </div>
                        </div>
                    </form>

                    <?php
                    if (isset($_POST['filter-date'])) {
                        $filter_date = $_POST['filter-date'];
                        $tamuQuery = mysqli_query($konek, "SELECT * FROM karyawan WHERE nama_tamu != '' AND tanggal_tamu = '$filter_date'");
                    }
                    ?>

                    <a href="tambah_tamu.php" class="btn btn-primary">Tambah Tamu</a>
                    <a href="pdf_tamu.php?filter_date=<?php echo isset($filter_date) ? $filter_date : ''; ?>" target="_blank" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Cetak PDF</a>



                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Data Tamu
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Nama Tamu</th>
                                            <th>Nama Perusahaan</th>
                                            <th>Keperluan</th>
                                            <th>Ingin Bertemu</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Nomor Kendaraan</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($tamuQuery)) {
                                            echo "<tr>";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . $row['tanggal_tamu'] . "</td>";
                                            echo "<td>" . $row['nama_tamu'] . "</td>";
                                            echo "<td>" . $row['nama_perusahaan'] . "</td>";
                                            echo "<td>" . $row['keperluan'] . "</td>";
                                            echo "<td>" . $row['ingin_bertemu'] . "</td>";
                                            echo "<td style='color: green; font-weight: bold;'>{$row['jam_masuk_tamu']}</td>";
                                            echo "<td style='color: red; font-weight: bold;'>{$row['jam_keluar_tamu']}</td>";
                                            echo "<td>" . $row['nopol'] . "</td>";
                                            echo '<td>' . ($row['jam_keluar_tamu'] != '00:00:00' ? '<strong style="color:red;">Keluar</strong>' : '<strong style="color:green;">Masuk</strong>') . '</td>';
                                            echo '<td>
                                                    <form method="post" id="exitForm_' . $row['id'] . '" action="">
                                                        <input type="hidden" name="id" value="' . $row['id'] . '">
                                                        <button type="button" class="btn ' . ($row['jam_keluar_tamu'] != '00:00:00' ? 'btn-secondary disabled' : 'btn-danger') . '" id="exitButton_' . $row['id'] . '" onclick="confirmExit(' . $row['id'] . ')">Keluar</button>
                                                    </form>
                                                  </td>';
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
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
