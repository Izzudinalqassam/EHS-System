<?php
include "koneksi.php"; // Include your database connection

// Query to count total employees, interns, and guests
$totalKaryawanQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM karyawan");
$totalKaryawan = mysqli_fetch_assoc($totalKaryawanQuery)['total'];

$totalMagangQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM karyawan WHERE departmen = 'Magang'");
$totalMagang = mysqli_fetch_assoc($totalMagangQuery)['total'];

$totalTamuQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM karyawan WHERE nama_tamu != ''");
$totalTamu = mysqli_fetch_assoc($totalTamuQuery)['total'];

// Query to get attendance data for the last week
$absensiMingguanQuery = mysqli_query($konek, "
    SELECT tanggal,
           COUNT(CASE WHEN departmen IN ('Operation', 'Distribution', 'Warehouse', 'After Sales', 'Maintenance', 'Elektrikal', 'Instrument', 'Engineering', 'CSR', 'Planer', 'Commercial', 'EHS', 'Procurement', 'Accounting', 'HR & GA', 'Finance', 'Fin & Adm', 'IT', 'BOD', 'Niaga & Perencaan', 'Project', 'Distribusi') THEN 1 END) as total_karyawan,
           COUNT(CASE WHEN departmen = 'Magang' THEN 1 END) as total_magang,
           $totalTamu as total_tamu
    FROM absensi a
    JOIN karyawan b ON a.nokartu = b.nokartu
    WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY tanggal
    ORDER BY tanggal ASC
");
$absensiData = [];
while ($row = mysqli_fetch_assoc($absensiMingguanQuery)) {
    $absensiData[] = [
        'tanggal' => $row['tanggal'],
        'total_karyawan' => $row['total_karyawan'],
        'total_magang' => $row['total_magang'],
        'total_tamu' => $row['total_tamu']
    ];
}

// Prepare data for the chart
$labels = [];
$totalKaryawan = [];
$totalMagang = [];
$totalTamu = [];

foreach ($absensiData as $item) {
    $labels[] = $item['tanggal'];
    $totalKaryawan[] = $item['total_karyawan'];
    $totalMagang[] = $item['total_magang'];
    $totalTamu[] = $item['total_tamu'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Chart</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="icon" href="image/bp.png" type="image/x-icon">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
        crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            setInterval(function() {
                $("#cekkartu").load('bacakartu.php');
            }, 2000);
        });
    </script>
    <style>
        .mt-4 {
            background-color: #f5f5f5;
            border-radius: 50px;
        }

        #myLineChart {
            width: 100%;
            /* Set to full width */
            height: 400px;
            /* Set desired height */
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
                        <a class="nav-link active" href="index.php">
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
                <div class="container-fluid text-center">
                    <h2 class="mt-4">Traffic Absen Mingguan</h2>
                    <canvas id="myLineChart"></canvas>
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

            <script>
                var ctxLine = document.getElementById('myLineChart').getContext('2d');
                var myLineChart = new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: <?= json_encode($labels) ?>,
                        datasets: [{
                                label: 'Karyawan',
                                data: <?= json_encode($totalKaryawan) ?>,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                pointBorderColor: 'rgba(75, 192, 192, 1)',
                                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                                pointStyle: 'rectRounded',
                                tension: 0.4
                            },
                            {
                                label: 'Magang',
                                data: <?= json_encode($totalMagang) ?>,
                                borderColor: 'rgba(255, 206, 86, 1)', // Different color for Magang
                                pointBorderColor: 'rgba(255, 206, 86, 1)',
                                pointBackgroundColor: 'rgba(255, 206, 86, 1)',
                                pointStyle: 'rectRounded',
                                tension: 0.4
                            },
                            {
                                label: 'Tamu',
                                data: <?= json_encode($totalTamu) ?>,
                                borderColor: 'rgba(153, 102, 255, 1)', // Different color for Tamu
                                pointBorderColor: 'rgba(153, 102, 255, 1)',
                                pointBackgroundColor: 'rgba(153, 102, 255, 1)',
                                pointStyle: 'rectRounded',
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Traffic Absen Mingguan'
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Tanggal'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Jumlah Absen'
                                },
                                beginAtZero: true,
                                type: 'linear',
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        return value;
                                    }
                                }
                            }
                        }
                    }
                });
            </script>
            
        </div>
    </div>
   
    <!-- Bootstrap Bundle with Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
</body>

</html>