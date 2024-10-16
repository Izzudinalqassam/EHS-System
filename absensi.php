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
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    

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
            <main>
                <div class="container-fluid">
                <?php
// Include your database connection
include "koneksi.php";

// Get today's date
$tanggal_hari_ini = date('Y-m-d');

// Query to count the number of employees who have entered today
$sql_orang_masuk = "SELECT COUNT(*) AS total_masuk 
                    FROM absensi a 
                    JOIN karyawan b ON a.nokartu = b.nokartu
                    WHERE a.jam_masuk != '00:00:00' 
                    AND b.departmen != '' 
                    AND b.departmen IS NOT NULL 
                    AND b.departmen NOT IN ('Magang') 
                    AND a.tanggal = '$tanggal_hari_ini'";
$result_orang_masuk = mysqli_query($konek, $sql_orang_masuk);
$data_orang_masuk = mysqli_fetch_assoc($result_orang_masuk);
$total_orang_masuk = $data_orang_masuk['total_masuk'];

// Query to count the number of employees who have left today
$sql_orang_keluar = "SELECT COUNT(*) AS total_keluar 
                    FROM absensi a 
                    JOIN karyawan b ON a.nokartu = b.nokartu
                    WHERE a.jam_pulang != '00:00:00' 
                    AND b.departmen NOT IN ('tamu', 'Magang') 
                    AND a.tanggal = '$tanggal_hari_ini'";
$result_orang_keluar = mysqli_query($konek, $sql_orang_keluar);
$data_orang_keluar = mysqli_fetch_assoc($result_orang_keluar);
$total_orang_keluar = $data_orang_keluar['total_keluar'];

// New logic: Check if jam_masuk is updated after jam_pulang, and ignore if jam_masuk was initially '00:00:00'
$sql_check_updated_masuk = "SELECT COUNT(*) AS updated_masuk 
                            FROM absensi a 
                            JOIN karyawan b ON a.nokartu = b.nokartu
                            WHERE a.jam_masuk > a.jam_pulang 
                            AND a.jam_masuk != '00:00:00'  -- Ignore records where jam_masuk was never set
                            AND a.jam_pulang != '00:00:00'  -- Only consider valid tap-outs
                            AND b.departmen NOT IN ('tamu', 'Magang') 
                            AND a.tanggal = '$tanggal_hari_ini'";
$result_check_updated_masuk = mysqli_query($konek, $sql_check_updated_masuk);
$data_updated_masuk = mysqli_fetch_assoc($result_check_updated_masuk);
$updated_masuk_count = $data_updated_masuk['updated_masuk'];

// Adjust total_orang_keluar based on updated jam_masuk, only if jam_masuk is valid and was updated
$total_orang_keluar -= $updated_masuk_count;

// Calculate the total number of people currently inside
$total_keseluruhan = $total_orang_masuk - $total_orang_keluar;
?>

<h2 class="mt-4">Dashboard Sistem EHS (Karyawan ) Tanggal <?= $tanggal_hari_ini ?></h2>

<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">Karyawan Tap-In: </div>
            <h1 style="text-align: center; font-size: 2rem;"><?= $total_orang_masuk ?></h1>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="detail_orangmasuk.php">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-danger text-white mb-4">
            <div class="card-body">Karyawan Tap-Out: </div>
            <h1 style="text-align: center; font-size: 2rem;"><?= $total_orang_keluar ?></h1>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="detail_orangkeluar.php">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">Karyawan Didalam: </div>
            <h1 style="text-align: center; font-size: 2rem;"><?= $total_keseluruhan ?></h1>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="#">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
</div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Data Absensi Karyawan Hari Ini
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Departmen</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Status</th>
                                         </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Query data absensi dari tabel karyawan dan absensi, hanya untuk karyawan (bukan tamu atau magang) hari ini
                                        $sql = "SELECT a.tanggal, b.NIK, b.nama, b.departmen, a.jam_masuk, a.jam_pulang 
            FROM absensi a 
            JOIN karyawan b ON a.nokartu = b.nokartu
            WHERE b.departmen NOT IN ('tamu', 'Magang') AND b.departmen != '' AND a.tanggal = '$tanggal_hari_ini'";
                                        $result = mysqli_query($konek, $sql);
                                        $no = 1;

                                        // Looping data hasil query
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $no . "</td>";
                                            echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['NIK']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['departmen']) . "</td>";
                                            echo "<td style='color: green; font-weight: bold;'>{$row['jam_masuk']}</td>";
                                            echo "<td style='color: red; font-weight: bold;'>{$row['jam_pulang']}</td>";

                                            // Cek status absensi
                                            if ($row['jam_masuk'] > $row['jam_pulang']) {
                                                // Jika jam masuk lebih baru dari jam keluar, status jadi IN
                                                echo "<td style='color: green'><b>IN</b></td>";
                                            } elseif ($row['jam_pulang'] != '00:00:00') {
                                                // Jika jam keluar terisi, status jadi OUT
                                                echo "<td style='color: red'><b>OUT</b></td>";
                                            } elseif ($row['jam_masuk'] != '00:00:00') {
                                                // Jika hanya jam masuk terisi, status IN
                                                echo "<td style='color: green'><b>IN</b></td>";
                                            } else {
                                                // Kondisi lain jika tidak ada jam masuk dan jam keluar
                                                echo "<td></td>";
                                            }

                                            echo "</tr>";
                                            $no++;
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
                   
                </div>
            </footer>
        </div>
    </div>
   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
</body>

</html>