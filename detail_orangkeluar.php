<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'koneksi.php'; // Menggunakan file koneksi.php';

// Mendapatkan tanggal hari ini
$tanggal_hari_ini = date('Y-m-d');

// Query untuk mendapatkan data orang yang keluar (tap-out) hari ini
$sql = "SELECT a.tanggal, a.nokartu, b.NIK, b.nama, b.departmen, a.jam_masuk, a.jam_pulang 
        FROM absensi a 
        JOIN karyawan b ON a.nokartu = b.nokartu
        WHERE b.departmen NOT IN ('tamu') 
        AND b.departmen != '' 
        AND a.tanggal = '$tanggal_hari_ini'
        AND a.jam_pulang != '00:00:00'
        AND a.status = 'OUT'
        ORDER BY a.jam_pulang DESC";

// Alternative query that also includes records where tap-out happened today (based on last_update)
$sql_alternative = "SELECT a.tanggal, a.nokartu, b.NIK, b.nama, b.departmen, a.jam_masuk, a.jam_pulang 
        FROM absensi a 
        JOIN karyawan b ON a.nokartu = b.nokartu
        WHERE b.departmen NOT IN ('tamu') 
        AND b.departmen != '' 
        AND (
            (a.tanggal = '$tanggal_hari_ini' AND a.jam_pulang != '00:00:00' AND a.status = 'OUT')
            OR 
            (DATE(a.last_update) = '$tanggal_hari_ini' AND a.jam_pulang != '00:00:00' AND a.status = 'OUT')
        )
        ORDER BY a.last_update DESC";

// Use the alternative query for better accuracy
$sql = $sql_alternative;

$result = mysqli_query($konek, $sql);

// Menghitung total orang yang keluar
$total_orang_keluar = mysqli_num_rows($result);
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
    <title>Detail Tap-Out - PT. Bekasi Power</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <style>
        .page-header {
            position: relative;
            margin: 20px auto 30px;
            padding: 20px 25px;
            border-radius: 12px;
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            color: white;
            overflow: hidden;
        }

        .page-header h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .page-header .date-badge {
            display: inline-block;
            margin-left: 12px;
            padding: 5px 15px;
            background-color: rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            font-size: 1.1rem;
            font-weight: 500;
            backdrop-filter: blur(5px);
            position: relative;
            z-index: 2;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 60%;
            height: 180%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(35deg);
            z-index: 1;
        }

        .page-header .icon {
            margin-right: 10px;
            font-size: 1.8rem;
        }

        .main-content-animate {
            animation: fadeIn 0.7s;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <?php include 'components/navbar.php'; ?>

    <div id="layoutSidenav">
        <?php include 'components/sidenav.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid main-content-animate">
                    <div class="page-header">
                        <h2>
                            <i class="fas fa-sign-out-alt icon"></i>
                            Detail Tap-Out Karyawan
                            <span class="date-badge" id="tanggalhariini"><?= date('d F Y', strtotime($tanggal_hari_ini)) ?></span>
                        </h2>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Data Karyawan Tap-Out Hari Ini
                            <div class="float-right">
                                <span class="badge badge-danger p-2">Total: <?= $total_orang_keluar ?> Orang</span>
                            </div>
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
                                            <th>Departemen</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Durasi</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        // Reset pointer hasil query
                                        mysqli_data_seek($result, 0);
                                        
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            // Hitung durasi (jam masuk sampai jam keluar)
                                            $jam_masuk = strtotime($row['jam_masuk']);
                                            $jam_keluar = strtotime($row['jam_pulang']);
                                            $durasi_detik = $jam_keluar - $jam_masuk;
                                            
                                            // Format durasi dalam jam dan menit
                                            $durasi_jam = floor($durasi_detik / 3600);
                                            $durasi_menit = floor(($durasi_detik % 3600) / 60);
                                            $durasi_format = sprintf("%d jam %d menit", $durasi_jam, $durasi_menit);
                                            
                                            echo "<tr>";
                                            echo "<td>" . $no . "</td>";
                                            echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['NIK']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['departmen']) . "</td>";
                                            echo "<td style='color: green; font-weight: bold;'>" . $row['jam_masuk'] . "</td>";
                                            echo "<td style='color: red; font-weight: bold;'>" . $row['jam_pulang'] . "</td>";
                                            echo "<td>" . $durasi_format . "</td>";
                                            echo "<td style='color: red'><b>OUT</b></td>";
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
            <?php include 'components/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            $('#dataTable').DataTable({
                "order": [[6, "desc"]] // Sort by jam keluar column (column index 6) in descending order
            });
        });
    </script>
</body>
</html>