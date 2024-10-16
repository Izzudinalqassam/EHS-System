<?php
error_reporting(0);
include "koneksi.php"; // Koneksi ke database

// Ambil NIK dari URL
$nik = $_GET['nik'];

// Ambil tanggal dari input filter, jika ada
$filter_tanggal = isset($_POST['filter-date']) ? $_POST['filter-date'] : '';

// Query untuk mengambil data karyawan
$query = "SELECT * FROM karyawan WHERE NIK = '$nik'";
$result = mysqli_query($konek, $query);
$karyawan = mysqli_fetch_assoc($result);

// Query untuk mengambil histori riwayat, dengan filter tanggal jika ada
$histori_query = "SELECT * FROM riwayat WHERE nokartu = '{$karyawan['nokartu']}'";
if ($filter_tanggal) {
    $histori_query .= " AND tanggal = '$filter_tanggal'";
}
$histori_query .= " ORDER BY tanggal, jam_masuk";
$histori_result = mysqli_query($konek, $histori_query);

// Variabel total masuk dan keluar
$masuktanggal = 0;
$keluartanggal = 0;

// Perhitungan total masuk dan keluar berdasarkan histori absensi
while ($row = mysqli_fetch_assoc($histori_result)) {
    if (!empty($row['jam_masuk']) && $row['jam_masuk'] != '00:00:00') {
        $masuktanggal++;
    }
    if (!empty($row['jam_pulang']) && $row['jam_pulang'] != '00:00:00') {
        $keluartanggal++;
    }
}

// Mengulang query untuk menampilkan data histori setelah kalkulasi
$histori_result = mysqli_query($konek, $histori_query);
$prev_row = null; // Menyimpan baris sebelumnya untuk menghitung selisih waktu
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Detail Profil - SB Admin</title>
    <style>
    /* CSS untuk mengubah warna latar belakang baris tabel saat cursor melewati */
    table tbody tr:hover {
        background-color: #f2f2f2; /* Ubah warna sesuai kebutuhan */
    }
</style>

    <link rel="icon" href="image/bp.png" type="image/x-icon">
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
                    <h2 class="mt-4">Detail Profil</h2>

                    <?php
                    if ($karyawan) {
                    ?>
                        <div class='card mb-4'>
                            <div class='card-header'>Informasi Profil</div>
                            <div class='card-body'>
                                <p><strong>NIK:</strong> <?= htmlspecialchars($karyawan['NIK']) ?></p>
                                <p><strong>Nama:</strong> <?= htmlspecialchars($karyawan['nama']) ?></p>
                                <p><strong>Nokartu:</strong> <?= htmlspecialchars($karyawan['nokartu']) ?></p>
                                <p><strong>Departmen:</strong> <?= htmlspecialchars($karyawan['departmen']) ?></p>
                            </div>
                        </div>

                        <form method='POST' action=''>
                            <div class='form-group'>
                                <label for='filter-date'>Pilih Tanggal:</label>
                                <div class='input-group' style='max-width: 250px;'>
                                    <input type='date' class='form-control' id='filter-date' name='filter-date' value='<?= htmlspecialchars($filter_tanggal) ?>'>
                                    <div class='input-group-append'>
                                        <button type='submit' class='btn btn-primary'>Filter</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form method='GET' action='cv_pdf_detail.php' target='_blank'>
                            <input type='hidden' name='nik' value='<?= htmlspecialchars($karyawan['NIK']) ?>'>
                            <input type='hidden' name='filter-date' value='<?= htmlspecialchars($filter_tanggal) ?>'>
                            <button type='submit' class='btn btn-primary mb-4'>Cetak PDF</button>
                        </form>

                        <div class='row mt-4'>
                            <div class='col-xl-3 col-md-6'>
                                <div class='card bg-success text-white mb-4'>
                                    <div class='card-body'> Total Masuk <?= $filter_tanggal ? "Keseluruhan Tanggal $filter_tanggal" : "Keseluruhan" ?></div>
                                    <h1 style='text-align: center; font-size: 2rem;'><?= $masuktanggal ?></h1>
                                    <div class='card-footer d-flex align-items-center justify-content-between'>
                                        <a class='small text-white stretched-link' href='#'>View Details</a>
                                        <div class='small text-white'><i class='fas fa-angle-right'></i></div>
                                    </div>
                                </div>
                            </div>

                            <div class='col-xl-3 col-md-6'>
                                <div class='card bg-danger text-white mb-4'>
                                    <div class='card-body'> Total Keluar <?= $filter_tanggal ? "Keseluruhan Tanggal $filter_tanggal" : "Keseluruhan" ?></div>
                                    <h1 style='text-align: center; font-size: 2rem;'><?= $keluartanggal ?></h1>
                                    <div class='card-footer d-flex align-items-center justify-content-between'>
                                        <a class='small text-white stretched-link' href='#'>View Details</a>
                                        <div class='small text-white'><i class='fas fa-angle-right'></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class='card mb-4'>
                            <div class='card-header'>Riwayat Absensi</div>
                            <div class='card-body'>
                                <div class="table-responsive">
                                    <table class='table table-bordered' id='dataTable' width='100%' cellspacing='0'>
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Jam Masuk</th>
                                                <th>Jam Pulang</th>
                                                <th>Lama Waktu IN</th>
                                                <th>Lama Waktu Out</th>
                                            </tr>
                                        </thead>
                                        <tbody>
    <?php
    $no = 1;
    $prev_row = null; // Untuk menyimpan baris sebelumnya
    while ($row = mysqli_fetch_assoc($histori_result)): ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= htmlspecialchars($row['tanggal']) ?></td>
            <td><?= htmlspecialchars($row['jam_masuk']) ?></td>
            <td><?= htmlspecialchars($row['jam_pulang']) ?></td>
            <td>
                <?php
                // Perhitungan Lama Waktu IN: dari jam masuk ke jam pulang pada hari yang sama
                if (!empty($row['jam_masuk']) && !empty($row['jam_pulang']) && $row['jam_masuk'] != '00:00:00' && $row['jam_pulang'] != '00:00:00') {
                    // Hitung selisih waktu menggunakan TIMEDIFF dan formatkan ke jam, menit, detik
                    $lama_waktu_query = "SELECT SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF('{$row['jam_pulang']}', '{$row['jam_masuk']}'))) AS lama_waktu_in";
                    $lama_waktu_result = mysqli_query($konek, $lama_waktu_query);
                    $lama_waktu_row = mysqli_fetch_assoc($lama_waktu_result);
                    echo "<span style='color: green; font-weight: bold;'>" . htmlspecialchars($lama_waktu_row['lama_waktu_in']) . "</span>";
                } else {
                    echo "<span style='color: green; font-weight: bold;'>N/A</span>";
                }
                ?>
            </td>
            <td>
                <?php
                // Perhitungan Lama Waktu OUT: dari jam pulang sebelumnya ke jam masuk hari ini
                if ($prev_row && !empty($prev_row['jam_pulang']) && !empty($row['jam_masuk']) && $prev_row['jam_pulang'] != '00:00:00' && $row['jam_masuk'] != '00:00:00') {
                    // Hitung selisih waktu antara jam pulang sebelumnya dan jam masuk saat ini
                    $lama_out_query = "SELECT SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF('{$row['jam_masuk']}', '{$prev_row['jam_pulang']}'))) AS lama_waktu_out";
                    $lama_out_result = mysqli_query($konek, $lama_out_query);
                    $lama_out_row = mysqli_fetch_assoc($lama_out_result);
                    echo "<span style='color: red; font-weight: bold;'>" . htmlspecialchars($lama_out_row['lama_waktu_out']) . "</span>";
                } else {
                    echo "<span style='color: red; font-weight: bold;'>N/A</span>";
                }
                ?>
            </td>
        </tr>
    <?php
        // Simpan baris saat ini sebagai baris sebelumnya untuk perhitungan Lama Waktu Out
        $prev_row = $row;
        $no++;
    endwhile; ?>
</tbody>


                                    </table>
                                </div>
                            </div>
                        </div>

                    <?php
                    } else {
                        echo "<p>Karyawan tidak ditemukan.</p>";
                    }
                    ?>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2024</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "ordering": true, // Enable ordering
                "searching": true // Enable searching
            });
        });
    </script>
</body>

</html>