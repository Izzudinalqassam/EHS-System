<?php
session_start();
// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Untuk halaman yang hanya bisa diakses admin
if ($_SESSION['role'] !== 'admin') {
    // Redirect ke halaman yang diizinkan untuk security
    header('Location: index.php');
    exit();
}
?>
<?php
error_reporting(0);
include "koneksi.php"; // Koneksi ke database

// Fetch data counts (existing code)
$totalKaryawanQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM karyawan WHERE departmen != ''");
$totalKaryawanData = mysqli_fetch_assoc($totalKaryawanQuery);
$totalKaryawan = $totalKaryawanData['total'];

$totalMagangQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM karyawan WHERE departmen = 'Magang'");
$totalMagangData = mysqli_fetch_assoc($totalMagangQuery);
$totalMagang = $totalMagangData['total'];

$totalKaryawanNonTamuQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM karyawan WHERE departmen != 'tamu' AND departmen != 'Magang' AND departmen != ''");
$totalKaryawanNonTamuData = mysqli_fetch_assoc($totalKaryawanNonTamuQuery);
$totalKaryawanNonTamu = $totalKaryawanNonTamuData['total'];
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
    <title>Data Karyawan - SISTEM EHS</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        /* Enhanced Dashboard Header */
        .dashboard-header {
            position: relative;
            margin: 20px auto 30px;
            padding: 25px 30px;
            border-radius: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3), 0 8px 16px rgba(0, 0, 0, 0.1);
            color: white;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .dashboard-header h2 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 200%;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            transform: rotate(30deg);
            z-index: 1;
        }

        .dashboard-header .icon {
            margin-right: 20px;
            font-size: 2.2rem;
            color: rgba(255, 255, 255, 0.95);
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        .dashboard-header .badge-counter {
            display: inline-block;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.15));
            border-radius: 25px;
            padding: 8px 18px;
            margin-left: 18px;
            font-size: 1.1rem;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dashboard-header .icon-container {
            position: relative;
            display: inline-block;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(255, 255, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }

        .pulse-icon {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.15));
            border-radius: 50%;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2.5s infinite;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        /* Enhanced Statistics Cards */
        .stats-card {
            position: relative;
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin-bottom: 25px;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            z-index: 1;
        }

        .stats-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .stats-card .card-body {
            position: relative;
            z-index: 2;
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stats-card .card-content {
            flex: 1;
        }

        .stats-card .card-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 8px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stats-card .card-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .stats-card .card-icon {
            font-size: 3rem;
            opacity: 0.8;
            margin-left: 20px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        .stats-card .card-footer {
            position: relative;
            z-index: 2;
            background: rgba(0, 0, 0, 0.1);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 25px;
        }

        .stats-card .card-footer a {
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .stats-card .card-footer a:hover {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Gradient Backgrounds for Cards */
        .card-employees {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .card-interns {
            background: linear-gradient(135deg, #fc466b 0%, #3f5efb 100%);
        }

        .card-total {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-guests {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        /* Enhanced Table Card */
        .table-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .table-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 20px 25px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .table-card .card-header i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* CSS untuk mengubah warna latar belakang baris tabel saat cursor melewati */
        table tbody tr:hover {
            background-color: #f8f9ff;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        
        /* Enhanced Animation for Cards */
        .animated-card {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .animated-card.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .card {
            margin-bottom: 25px;
            transition: all 0.3s ease;
            border-radius: 15px;
        }
        /* Enhanced Action Buttons */
        .btn-primary:hover, .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2) !important;
        }

        /* Modern Table Action Buttons Styles */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-action {
            border-radius: 50px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-weight: 600;
            font-size: 0.85rem;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-decoration: none;
        }

        .btn-action i {
            margin-right: 6px;
            font-size: 0.9rem;
        }

        .btn-delete {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #ff5252 0%, #d32f2f 100%);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
            color: white;
        }

        .btn-edit {
            background: linear-gradient(135deg, #ffa726 0%, #ff9800 100%);
            color: white;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(255, 167, 38, 0.4);
            color: white;
        }

        .btn-action:active {
            transform: scale(0.95);
        }

        /* Responsive Design Enhancements */
        @media (max-width: 768px) {
            .stats-card .card-body {
                flex-direction: column;
                text-align: center;
            }
            
            .stats-card .card-icon {
                margin: 15px 0 0 0;
                font-size: 2.5rem;
            }
            
            .dashboard-header h2 {
                font-size: 1.5rem;
                flex-direction: column;
                text-align: center;
            }
            
            .dashboard-header .badge-counter {
                margin: 10px 0 0 0;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }
        }

        /* Loading Animation */
        @keyframes shimmer {
            0% {
                background-position: -468px 0;
            }
            100% {
                background-position: 468px 0;
            }
        }

        .loading-shimmer {
            animation: shimmer 1.5s ease-in-out infinite;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 400% 100%;
        }
        
        /* Animasi hanya untuk konten utama */
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
    </style>
    <script type="text/javascript">
        $(document).ready(function () {
            setInterval(function () {
                $("#cekkartu").load('bacakartu.php');
            }, 2000);
        });
    </script>
</head>

<body class="sb-nav-fixed">
    <?php include 'components/navbar.php'; ?>

    <div id="layoutSidenav">
        <?php include 'components/sidenav.php'; ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid main-content-animate">
                <div class="dashboard-header animate-header">
            <h2>
                <span class="icon-container">
                    <span class="pulse-icon">
                        <i class="fas fa-users icon"></i>
                    </span>
                </span>
                Data Karyawan Sistem Personal Counting
                <span class="badge-counter"><?= $totalKaryawan ?> Total</span>
            </h2>
        </div>

                    <div class="row">
                        <!-- Total Karyawan Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card stats-card card-employees text-white animated-card">
                                <div class="card-body">
                                    <div class="card-content">
                                        <div class="card-title">Total Karyawan</div>
                                        <h1 class="card-number"><?= $totalKaryawanNonTamu ?></h1>
                                    </div>
                                    <div class="card-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="detail_data_karyawan.php">
                                        <i class="fas fa-chart-line mr-2"></i>View Details
                                    </a>
                                    <div class="small text-white"><i class="fas fa-arrow-right"></i></div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Magang Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card stats-card card-interns text-white animated-card">
                                <div class="card-body">
                                    <div class="card-content">
                                        <div class="card-title">Total Magang</div>
                                        <h1 class="card-number"><?= $totalMagang ?></h1>
                                    </div>
                                    <div class="card-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="detail_data_magang.php">
                                        <i class="fas fa-chart-line mr-2"></i>View Details
                                    </a>
                                    <div class="small text-white"><i class="fas fa-arrow-right"></i></div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Karyawan & Magang Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card stats-card card-total text-white animated-card">
                                <div class="card-body">
                                    <div class="card-content">
                                        <div class="card-title">Total Karyawan & Magang</div>
                                        <h1 class="card-number"><?= $totalKaryawan ?></h1>
                                    </div>
                                    <div class="card-icon">
                                        <i class="fas fa-user-friends"></i>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="detail_data_semua.php">
                                        <i class="fas fa-chart-line mr-2"></i>View Details
                                    </a>
                                    <div class="small text-white"><i class="fas fa-arrow-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Action Buttons -->
                    <div class="d-flex mb-4">
                        <a href="tambah.php" class="btn btn-primary mr-3" style="border-radius: 25px; padding: 12px 24px; font-weight: 600; box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3); transition: all 0.3s ease;">
                            <i class="fas fa-plus mr-2"></i> Tambah Data Karyawan
                        </a>
                        <a href="cetak_data_karyawan.php" class="btn btn-success" target="_blank" style="border-radius: 25px; padding: 12px 24px; font-weight: 600; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3); transition: all 0.3s ease;">
                            <i class="fas fa-file-pdf mr-2"></i> Cetak PDF
                        </a>
                    </div>

                    <!-- Enhanced Data Table Card -->
                    <div class="card table-card mb-4 animated-card">
                        <div class="card-header">
                            <i class="fas fa-database mr-2"></i>
                            Data Karyawan Sistem Personal Counting
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Departmen</th>
                                            <th>Nomor WhatsApp</th>
                                            <th>Nopol Kendaraan</th>
                                            <th>AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = mysqli_query($konek, "SELECT * FROM karyawan WHERE departmen IS NOT NULL AND departmen != ''");
                                        $no = 1;
                                        while ($data = mysqli_fetch_assoc($sql)) {
                                            ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= $data['NIK']; ?></td>
                                                <td><?= $data['nama']; ?></td>
                                                <td><?= $data['departmen']; ?></td>
                                                <td><?= $data['no_wa']; ?></td>
                                                <td><?= $data['nopol']; ?></td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="edit.php?id=<?= $data['id']; ?>" class="btn btn-action btn-edit">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <button class="btn btn-action btn-delete" onclick="konfirmasiHapus('<?= $data['id']; ?>')">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
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

    <div id="cekkartu"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>

    <!-- DataTables Initialization -->
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true
            });
        });
    </script>

    <script>
        function konfirmasiHapus(id) {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: 'Anda tidak akan bisa mengembalikan data ini!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `hapus.php?id=${id}`;
                }
            })
        }
        
        // Enhanced animations and interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Staggered card animations
            const cards = document.querySelectorAll('.animated-card');
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const cardObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.classList.add('show');
                        }, index * 150);
                        cardObserver.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            cards.forEach(card => {
                cardObserver.observe(card);
            });

            // Enhanced button hover effects
            const actionButtons = document.querySelectorAll('.btn-action');
            actionButtons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px) scale(1.05)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Stats card counter animation
            const statsNumbers = document.querySelectorAll('.card-number');
            statsNumbers.forEach(number => {
                const finalValue = parseInt(number.textContent);
                let currentValue = 0;
                const increment = finalValue / 50;
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        number.textContent = finalValue;
                        clearInterval(timer);
                    } else {
                        number.textContent = Math.floor(currentValue);
                    }
                }, 30);
            });

            // Add ripple effect to cards
            const statsCards = document.querySelectorAll('.stats-card');
            statsCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.classList.add('ripple');
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });

        // Add ripple effect CSS
        const rippleStyle = document.createElement('style');
        rippleStyle.textContent = `
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            }
            
            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(rippleStyle);

        // Enhanced table row hover effects
        $(document).ready(function() {
            $('#dataTable tbody tr').hover(
                function() {
                    $(this).addClass('table-row-hover');
                },
                function() {
                    $(this).removeClass('table-row-hover');
                }
            );
        });

        // Add table row hover CSS
         const tableStyle = document.createElement('style');
         tableStyle.textContent = `
             .table-row-hover {
                 background: linear-gradient(90deg, #f8f9ff 0%, #e3f2fd 100%) !important;
                 box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                 transform: scale(1.01);
             }
         `;
         document.head.appendChild(tableStyle);
    </script>
</body>
</html>