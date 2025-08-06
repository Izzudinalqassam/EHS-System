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
    <title>Tamu Didalam - Sistem EHS</title>
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
            background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);
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
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        
        .status-overdue {
            background-color: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
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
                            <i class="fas fa-user-friends icon"></i>
                            Tamu Didalam PT Bekasi Power
                            <span class="date-badge"><?php echo date('d F Y'); ?></span>
                        </h2>
                    </div>

                    <!-- Data Table Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Data Tamu yang Masih Didalam
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Petugas Penginput</th>
                                            <th>Nama Tamu</th>
                                            <th>Nama Perusahaan</th>
                                            <th>Jumlah Tamu</th>
                                            <th>Keperluan</th>
                                            <th>Ingin Bertemu</th>
                                            <th>Jam Masuk</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Query tamu yang masih di dalam (jam keluar = 00:00:00)
                                        $sql = "SELECT *, DATEDIFF('$tanggal_hari_ini', tanggal_tamu) as days_inside
                                                FROM tamu 
                                                WHERE jam_keluar_tamu = '00:00:00'
                                                ORDER BY tanggal_tamu DESC, jam_masuk_tamu DESC";
                                        
                                        $result = mysqli_query($konek, $sql);
                                        $no = 1;

                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $days_inside = $row['days_inside'];
                                            $status_class = '';
                                            $status_text = '';
                                            
                                            if ($days_inside > 0) {
                                                $status_class = 'status-overdue';
                                                $status_text = 'Masuk (' . $days_inside . ' hari)';
                                            } else {
                                                $status_class = 'status-active';
                                                $status_text = 'Masuk';
                                            }
                                            
                                            echo "<tr " . ($days_inside > 0 ? "class='table-warning'" : "") . ">";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . $row['tanggal_tamu'] . "</td>";
                                            echo "<td>" . $row['petugas'] . "</td>";
                                            echo "<td>" . $row['nama_tamu'] . "</td>";
                                            echo "<td>" . $row['nama_perusahaan'] . "</td>";
                                            echo "<td>" . $row['jumlah_tamu'] . "</td>";
                                            echo "<td>" . $row['keperluan'] . "</td>";
                                            echo "<td>" . $row['ingin_bertemu'] . "</td>";
                                            echo "<td style='color: green; font-weight: bold;'>" . $row['jam_masuk_tamu'] . "</td>";
                                            echo "<td><span class='" . $status_class . "'>" . $status_text . "</span></td>";
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
                "order": [[8, "desc"]]
            });
        });
    </script>
</body>
</html>