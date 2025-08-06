<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
include "koneksi.php";

date_default_timezone_set('Asia/Jakarta');
$tanggal_hari_ini = date('Y-m-d');
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
    <title>Karyawan Didalam - Sistem EHS</title>
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
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
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
                            Karyawan Didalam PT Bekasi Power
                            <span class="date-badge"><?php echo date('d F Y'); ?></span>
                        </h2>
                    </div>

                    <!-- Data Table Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Data Karyawan yang Masih Didalam
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
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Query karyawan (bukan magang) yang masih di dalam (status IN)
                                        $sql = "SELECT a.tanggal, a.nokartu, b.NIK, b.nama, b.departmen, a.jam_masuk, a.status
                                                FROM absensi a
                                                JOIN karyawan b ON a.nokartu = b.nokartu
                                                WHERE b.departmen != 'magang' 
                                                AND b.departmen != 'tamu'
                                                AND b.departmen != ''
                                                AND a.tanggal = '$tanggal_hari_ini'
                                                AND (a.status = 'IN' OR (a.jam_masuk != '00:00:00' AND a.jam_pulang = '00:00:00'))
                                                ORDER BY a.jam_masuk DESC";
                                        
                                        $result = mysqli_query($konek, $sql);
                                        $no = 1;

                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . $row['tanggal'] . "</td>";
                                            echo "<td>" . $row['NIK'] . "</td>";
                                            echo "<td>" . $row['nama'] . "</td>";
                                            echo "<td>" . $row['departmen'] . "</td>";
                                            echo "<td style='color: green; font-weight: bold;'>" . $row['jam_masuk'] . "</td>";
                                            echo "<td><span class='badge badge-success'>IN</span></td>";
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