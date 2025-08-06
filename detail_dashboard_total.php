<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
include "koneksi.php";

date_default_timezone_set('Asia/Jakarta');
$tanggal_hari_ini = date('Y-m-d');

// Count for employees inside (not interns or guests)
$karyawan_query = mysqli_query($konek, "
    SELECT COUNT(*) as total 
    FROM absensi a 
    JOIN karyawan b ON a.nokartu = b.nokartu 
    WHERE b.departmen NOT IN ('magang', 'tamu') 
    AND b.departmen != '' 
    AND a.tanggal = '$tanggal_hari_ini' 
    AND (a.status = 'IN' OR (a.jam_masuk != '00:00:00' AND a.jam_pulang = '00:00:00'))
");
$karyawan_didalam = mysqli_fetch_assoc($karyawan_query)['total'];

// Count for interns inside
$magang_query = mysqli_query($konek, "
    SELECT COUNT(*) as total 
    FROM absensi a 
    JOIN karyawan b ON a.nokartu = b.nokartu 
    WHERE b.departmen = 'magang' 
    AND a.tanggal = '$tanggal_hari_ini' 
    AND (a.status = 'IN' OR (a.jam_masuk != '00:00:00' AND a.jam_pulang = '00:00:00'))
");
$magang_didalam = mysqli_fetch_assoc($magang_query)['total'];

// Count for guests inside
$tamu_query = mysqli_query($konek, "
    SELECT SUM(jumlah_tamu) as total 
    FROM tamu 
    WHERE jumlah_tamu != '' 
    AND jam_keluar_tamu = '00:00:00'
");
$tamu_didalam = mysqli_fetch_assoc($tamu_query)['total'] ?? 0;

// Total people inside
$total_didalam = $karyawan_didalam + $magang_didalam + $tamu_didalam;
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
    <title>Total Orang Didalam - Sistem EHS</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
        crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"
        crossorigin="anonymous"></script>
    <style>
        .dashboard-header {
            position: relative;
            margin: 20px auto 30px;
            padding: 20px 25px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            color: white;
            overflow: hidden;
        }

        .dashboard-header h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .dashboard-header .date-badge {
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

        .dashboard-header::before {
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

        .dashboard-header .icon {
            margin-right: 12px;
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
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .counts-section {
            margin-bottom: 20px;
        }

        .count-item {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .count-item h5 {
            margin: 0;
            font-size: 1.1rem;
        }

        .count-item .count {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .karyawan-bg {
            background-color: #22c55e;
        }

        .magang-bg {
            background-color: #ef4444;
        }

        .tamu-bg {
            background-color: #eab308;
        }

        .total-bg {
            background-color: #3b82f6;
        }

        .type-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            color: white;
        }
        
        .karyawan-badge {
            background-color: #22c55e;
        }
        
        .magang-badge {
            background-color: #ef4444;
        }
        
        .tamu-badge {
            background-color: #eab308;
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
                    <div class="dashboard-header">
                        <h2>
                            <i class="fas fa-users icon"></i>
                            Total Orang Didalam PT Bekasi Power
                            <span class="date-badge"><?php echo date('d F Y'); ?></span>
                        </h2>
                    </div>

                    <!-- Counts Section -->
                    <div class="row counts-section">
                        <div class="col-md-3">
                            <div class="count-item karyawan-bg">
                                <h5>Karyawan Didalam</h5>
                                <div class="count"><?php echo $karyawan_didalam; ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="count-item magang-bg">
                                <h5>Magang Didalam</h5>
                                <div class="count"><?php echo $magang_didalam; ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="count-item tamu-bg">
                                <h5>Tamu Didalam</h5>
                                <div class="count"><?php echo $tamu_didalam; ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="count-item total-bg">
                                <h5>Total Didalam</h5>
                                <div class="count"><?php echo $total_didalam; ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Data Semua Orang yang Masih Didalam
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Nama</th>
                                            <th>Kategori</th>
                                            <th>Departemen/Perusahaan</th>
                                            <th>Jam Masuk</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Combine all people inside in a single table
                                        $no = 1;

                                        // 1. First add employees (regular employees, not interns)
                                        $karyawan_sql = "
                                            SELECT 
                                                a.tanggal, 
                                                b.nama, 
                                                'Karyawan' as kategori,
                                                b.departmen, 
                                                a.jam_masuk, 
                                                'IN' as status
                                            FROM absensi a
                                            JOIN karyawan b ON a.nokartu = b.nokartu
                                            WHERE b.departmen NOT IN ('magang', 'tamu') 
                                                AND b.departmen != '' 
                                                AND a.tanggal = '$tanggal_hari_ini' 
                                                AND (a.status = 'IN' OR (a.jam_masuk != '00:00:00' AND a.jam_pulang = '00:00:00'))
                                            ORDER BY a.jam_masuk DESC
                                        ";
                                        $karyawan_result = mysqli_query($konek, $karyawan_sql);
                                        while ($row = mysqli_fetch_assoc($karyawan_result)) {
                                            echo "<tr>";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . $row['tanggal'] . "</td>";
                                            echo "<td>" . $row['nama'] . "</td>";
                                            echo "<td><span class='type-badge karyawan-badge'>" . $row['kategori'] . "</span></td>";
                                            echo "<td>" . $row['departmen'] . "</td>";
                                            echo "<td style='color: green; font-weight: bold;'>" . $row['jam_masuk'] . "</td>";
                                            echo "<td><span class='badge badge-success'>IN</span></td>";
                                            echo "</tr>";
                                        }

                                        // 2. Add interns
                                        $magang_sql = "
                                            SELECT 
                                                a.tanggal, 
                                                b.nama, 
                                                'Magang' as kategori,
                                                b.departmen, 
                                                a.jam_masuk, 
                                                'IN' as status
                                            FROM absensi a
                                            JOIN karyawan b ON a.nokartu = b.nokartu
                                            WHERE b.departmen = 'magang' 
                                                AND a.tanggal = '$tanggal_hari_ini' 
                                                AND (a.status = 'IN' OR (a.jam_masuk != '00:00:00' AND a.jam_pulang = '00:00:00'))
                                            ORDER BY a.jam_masuk DESC
                                        ";
                                        $magang_result = mysqli_query($konek, $magang_sql);
                                        while ($row = mysqli_fetch_assoc($magang_result)) {
                                            echo "<tr>";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . $row['tanggal'] . "</td>";
                                            echo "<td>" . $row['nama'] . "</td>";
                                            echo "<td><span class='type-badge magang-badge'>" . $row['kategori'] . "</span></td>";
                                            echo "<td>" . $row['departmen'] . "</td>";
                                            echo "<td style='color: green; font-weight: bold;'>" . $row['jam_masuk'] . "</td>";
                                            echo "<td><span class='badge badge-success'>IN</span></td>";
                                            echo "</tr>";
                                        }

                                        // 3. Add guests
                                        $tamu_sql = "
                                            SELECT 
                                                tanggal_tamu as tanggal, 
                                                nama_tamu as nama, 
                                                'Tamu' as kategori,
                                                nama_perusahaan as departmen, 
                                                jam_masuk_tamu as jam_masuk, 
                                                DATEDIFF('$tanggal_hari_ini', tanggal_tamu) as days_inside
                                            FROM tamu 
                                            WHERE jam_keluar_tamu = '00:00:00'
                                            ORDER BY tanggal_tamu DESC, jam_masuk_tamu DESC
                                        ";
                                        $tamu_result = mysqli_query($konek, $tamu_sql);
                                        while ($row = mysqli_fetch_assoc($tamu_result)) {
                                            $days_inside = $row['days_inside'];
                                            $status_class = ($days_inside > 0) ? 'warning' : 'success';
                                            $status_text = ($days_inside > 0) ? 'IN (' . $days_inside . ' hari)' : 'IN';

                                            echo "<tr>";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . $row['tanggal'] . "</td>";
                                            echo "<td>" . $row['nama'] . "</td>";
                                            echo "<td><span class='type-badge tamu-badge'>" . $row['kategori'] . "</span></td>";
                                            echo "<td>" . $row['departmen'] . "</td>";
                                            echo "<td style='color: green; font-weight: bold;'>" . $row['jam_masuk'] . "</td>";
                                            echo "<td><span class='badge badge-" . $status_class . "'>" . $status_text . "</span></td>";
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
            <?php include 'components/footer.php'; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" 
            crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "language": {
                    "search": "Pencarian:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                },
                "order": [[5, "desc"]]
            });
        });
    </script>
</body>
</html>