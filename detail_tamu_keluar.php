<?php
error_reporting(0);
include "koneksi.php";

date_default_timezone_set('Asia/Jakarta');

// Get filter date (default: today)
$filter_date = isset($_POST['filter-date']) ? $_POST['filter-date'] : date('Y-m-d');
$current_date = date('Y-m-d');

// Query to get all guests who have checked out on the filter date
$tamuQuery = mysqli_query($konek, "
    SELECT * 
    FROM tamu 
    WHERE jam_keluar_tamu != '00:00:00' 
    AND tanggal_tamu = '$filter_date'
    ORDER BY jam_keluar_tamu DESC
");

// Count guests who left on the filtered date
$totalKeluarQuery = mysqli_query($konek, "
    SELECT SUM(jumlah_tamu) as total_keluar 
    FROM tamu 
    WHERE jam_keluar_tamu != '00:00:00' 
    AND tanggal_tamu = '$filter_date'
");
$totalKeluar = mysqli_fetch_assoc($totalKeluarQuery)['total_keluar'] ?? 0;
?>

<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
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
    <title>Tamu yang Sudah Keluar - Sistem EHS</title>

    <!-- Core CSS -->
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
    .dashboard-header {
        position: relative;
        margin: 20px auto 30px;
        padding: 20px 25px;
        border-radius: 12px;
        background: linear-gradient(135deg, #DC3545 0%, #F56565 100%);
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

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-header {
        animation: fadeInUp 0.6s ease-out forwards;
    }
    .animated-card {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }
    
    .animated-card.show {
        opacity: 1;
        transform: translateY(0);
    }
    
    .card {
        margin-bottom: 20px;
        transition: transform 0.2s;
        border-radius: 10px;
    }

    .card:hover {
        transform: translateY(-5px);
    }
    
    /* Animasi untuk konten utama */
    .main-content-animate {
        animation: fadeIn 0.8s ease-out forwards;
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
        /* Hover effect for table rows */
        table tbody tr:hover {
            background-color: #f2f2f2 !important;
        }
        
        /* Additional styling for status indicators */
        .status-complete {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <?php include 'components/navbar.php'; ?>
   
    <div id="layoutSidenav">
        <?php include 'components\sidenav.php'; ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid main-content-animate">
                    <div class="dashboard-header animate-header">
                        <h2>
                            <i class="fas fa-user-slash icon"></i>
                            Tamu yang Sudah Keluar
                            <span class="date-badge" id="tanggal-filter">
                                <?php echo isset($_POST['filter-date']) ? $_POST['filter-date'] : date('Y-m-d'); ?>
                            </span>
                        </h2>
                    </div>

                    <!-- Date filter form -->
                    <form method="post" action="" class="mb-4">
                        <div class="form-row align-items-center">
                            <div class="col-auto">
                                <label for="filter-date" class="mr-2">Filter Tanggal:</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" class="form-control" id="filter-date" name="filter-date"
                                    value="<?php echo isset($_POST['filter-date']) ? $_POST['filter-date'] : date('Y-m-d'); ?>">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Cari</button>
                            </div>
                           
                        </div>
                    </form>

                    <!-- Card for total -->
                    <div class="row mb-4">
                        <div class="col-xl-6 col-md-6">
                            <div class="card text-white bg-danger o-hidden h-100 animated-card">
                                <div class="card-body">
                                    <div class="card-body-icon">
                                        <i class="fas fa-user-slash"></i>
                                    </div>
                                    <div class="mr-5">Total Tamu yang Keluar: <?php echo $totalKeluar > 0 ? $totalKeluar : '0'; ?></div>
                                </div>
                                <div class="card-footer text-white clearfix small z-1">
                                    <span class="float-left">Total tamu yang sudah keluar pada tanggal yang dipilih</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="card mb-4 animated-card">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Data Tamu yang Sudah Keluar pada <?php echo $filter_date; ?>
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
                                            <th>Jumlah tamu</th>
                                            <th>Keperluan</th>
                                            <th>Ingin Bertemu</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Nomor Kendaraan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($tamuQuery)) {
                                            echo "<tr>";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . $row['tanggal_tamu'] . "</td>";
                                            echo "<td>" . $row['petugas'] . "</td>";
                                            echo "<td>" . $row['nama_tamu'] . "</td>";
                                            echo "<td>" . $row['nama_perusahaan'] . "</td>";
                                            echo "<td>" . $row['jumlah_tamu'] . "</td>";
                                            echo "<td>" . $row['keperluan'] . "</td>";
                                            echo "<td>" . $row['ingin_bertemu'] . "</td>";
                                            echo "<td class='text-success font-weight-bold'>" . $row['jam_masuk_tamu'] . "</td>";
                                            echo "<td class='text-danger font-weight-bold'>" . $row['jam_keluar_tamu'] . "</td>";
                                            echo "<td>" . $row['nopol'] . "</td>";
                                            echo "<td><span class='status-complete'>Keluar</span></td>";
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
            <?php include 'components\footer.php'; ?>
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#dataTable').DataTable({
                "searching": true,
                "ordering": true,
                "paging": true,
                "order": [[0, "asc"]],
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
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Animasi kartu
            const cards = document.querySelectorAll('.animated-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('show');
                }, 100 * (index + 1));
            });
        });
    </script>
</body>
</html>