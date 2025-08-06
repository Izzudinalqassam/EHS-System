<?php
error_reporting(0);
include "koneksi.php";
date_default_timezone_set('Asia/Jakarta');

// Query to get all motorcycles that are still inside
$kendaraanQuery = mysqli_query($konek, "
    SELECT *, 
    DATEDIFF(CURDATE(), tanggal_input) as days_inside
    FROM kendaraan 
    WHERE jam_keluar = '00:00:00' AND jenis_kendaraan = 'Motor'
    ORDER BY tanggal_input DESC, jam_masuk DESC
");

// Count motorcycles that are still inside
$totalMotorQuery = mysqli_query($konek, "
    SELECT COUNT(*) as total 
    FROM kendaraan 
    WHERE jenis_kendaraan = 'Motor' AND jam_keluar = '00:00:00'
");
$totalMotor = mysqli_fetch_assoc($totalMotorQuery)['total'] ?? 0;

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
    <title>Motor di Dalam - Sistem EHS</title>

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
        background: linear-gradient(135deg, #007BFF 0%, #66B2FF 100%);
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

    table tbody tr:hover {
        background-color: #f2f2f2 !important;
    }
    
    .vehicle-inside {
        background-color: #e8f5e9 !important;
    }
    
    .table-striped tbody tr:nth-of-type(odd).vehicle-inside {
        background-color: #c8e6c9 !important;
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
        <?php include 'components\sidenav.php'; ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid main-content-animate">
                    <div class="dashboard-header animate-header">
                        <h2>
                            <i class="fas fa-motorcycle icon"></i>
                            Motor yang Masih Di Dalam (<?php echo $totalMotor; ?>)
                        </h2>
                    </div>

         

                    <!-- Data Table -->
                    <div class="card mb-4 animated-card">
                        <div class="card-header">
                            <i class="fas fa-motorcycle mr-1"></i>
                            Motor yang Masih Di Dalam
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Petugas</th>
                                            <th>Nama</th>
                                            <th>Nomor Polisi</th>
                                            <th>Jam Masuk</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($kendaraanQuery)) {
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
                                            
                                            echo "<tr " . ($days_inside > 0 ? "class='table-warning'" : "class='vehicle-inside'") . ">";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . $row['tanggal_input'] . "</td>";
                                            echo "<td>" . $row['petugas'] . "</td>";
                                            echo "<td>" . $row['nama'] . "</td>";
                                            echo "<td>" . $row['nopol'] . "</td>";
                                            echo "<td style='color: green; font-weight: bold;'>" . $row['jam_masuk'] . "</td>";
                                            echo "<td><span class='" . $status_class . "'>" . $status_text . "</span></td>";
                                            echo '<td>
                                                    <form method="post" id="exitForm_' . $row['id'] . '" action="data_kendaraan.php">
                                                        <input type="hidden" name="id" value="' . $row['id'] . '">
                                                        <button type="button" 
                                                            class="btn btn-danger" 
                                                            id="exitButton_' . $row['id'] . '" 
                                                            onclick="confirmExit(' . $row['id'] . ')">
                                                            Keluar
                                                        </button>
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
            <?php include 'components\footer.php'; ?>
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        // Function for confirming vehicle exit
        function confirmExit(id) {
            let button = document.getElementById('exitButton_' + id);
            button.classList.add('btn-secondary');
            button.disabled = true;

            Swal.fire({
                title: 'Konfirmasi Kendaraan Keluar',
                text: 'Apakah Anda yakin kendaraan ini akan keluar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Keluarkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('exitForm_' + id).submit();
                } else {
                    button.classList.remove('btn-secondary');
                    button.disabled = false;
                }
            });
        }

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