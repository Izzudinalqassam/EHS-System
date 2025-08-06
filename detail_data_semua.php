<?php
session_start();
// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include koneksi database
include "koneksi.php";

// Query untuk mendapatkan semua data karyawan dan magang (bukan tamu)
$semuaQuery = mysqli_query($konek, "SELECT * FROM karyawan 
    WHERE departmen != 'tamu' AND departmen != ''
    ORDER BY departmen ASC, nama ASC");

// Hitung total karyawan (non-tamu)
$totalSemuaQuery = mysqli_query($konek, "SELECT COUNT(*) as total 
    FROM karyawan 
    WHERE departmen != 'tamu' AND departmen != ''");
$totalSemua = mysqli_fetch_assoc($totalSemuaQuery)['total'];

// Hitung total per departemen
$departemenQuery = mysqli_query($konek, "SELECT departmen, COUNT(*) as total 
    FROM karyawan 
    WHERE departmen != 'tamu' AND departmen != ''
    GROUP BY departmen 
    ORDER BY departmen ASC");

$departemens = [];
while ($row = mysqli_fetch_assoc($departemenQuery)) {
    $departemens[$row['departmen']] = $row['total'];
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
    <title>Detail Data Semua - SISTEM EHS</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
      
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            border-radius: 50px;
            padding: 6px 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.8rem;
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }

        .btn-action i {
            margin-right: 4px;
        }

        .btn-delete {
            background: linear-gradient(135deg, #ff5f6d 0%, #ff8f70 100%);
            color: white;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #ff4757 0%, #ff7f50 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 95, 109, 0.3);
            color: white;
        }

        .btn-edit {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: white;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #F7CA00 0%, #FF9000 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 165, 0, 0.3);
            color: white;
        }

        /* Add animation for the buttons */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        .btn-action:active {
            transform: scale(0.95);
        }
        .dashboard-header {
            position: relative;
            margin: 20px auto 30px;
            padding: 22px 28px;
            border-radius: 12px;
            background: linear-gradient(135deg, #007bff 0%, #66b2ff 100%);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            color: white;
            overflow: hidden;
        }

        .dashboard-header h2 {
            margin: 0;
            font-size: 1.9rem;
            font-weight: 600;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
            z-index: 1;
        }

        .dashboard-header .icon {
            margin-right: 18px;
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .dashboard-header .badge-counter {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            padding: 6px 15px;
            margin-left: 15px;
            font-size: 1rem;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
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
                box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }

        .pulse-icon {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }

        /* CSS untuk mengubah warna latar belakang baris tabel saat cursor melewati */
        table tbody tr:hover {
            background-color: #f2f2f2;
        }
        
        /* Tambahan style untuk cards bergerak */
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

        /* Style untuk indikator departemen */
        .departemen-badge {
            display: inline-block;
            padding: 4px 8px;
            margin-right: 5px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            color: white;
        }

        .dept-karyawan {
            background-color: #28a745;
        }

        .dept-magang {
            background-color: #dc3545;
        }

        .dept-other {
            background-color: #6c757d;
        }

        /* Untuk statistik departemen */
        .dept-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .dept-item {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .dept-name {
            font-weight: bold;
            margin-right: 8px;
        }

        .dept-count {
            background-color: #007bff;
            color: white;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 0.9rem;
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
                    <div class="dashboard-header animate-header">
                        <h2>
                            <span class="icon-container">
                                <span class="pulse-icon">
                                    <i class="fas fa-users icon"></i>
                                </span>
                            </span>
                            Data Semua Karyawan & Magang
                            <span class="badge-counter"><?= $totalSemua ?> Total</span>
                        </h2>
                    </div>

                   
                    <!-- Department statistics -->
                    <div class="card mb-4 animated-card">
                        <div class="card-header">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Statistik per Departemen
                        </div>
                        <div class="card-body">
                            <div class="dept-stats">
                                <?php foreach ($departemens as $dept => $count): ?>
                                <div class="dept-item">
                                    <span class="dept-name"><?= $dept ?></span>
                                    <span class="dept-count"><?= $count ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4 animated-card">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Data Semua Karyawan & Magang
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
                                        $no = 1;
                                        while ($data = mysqli_fetch_assoc($semuaQuery)) {
                                            $deptClass = '';
                                            if ($data['departmen'] == 'Magang') {
                                                $deptClass = 'dept-magang';
                                            } elseif ($data['departmen'] != 'tamu') {
                                                $deptClass = 'dept-karyawan';
                                            } else {
                                                $deptClass = 'dept-other';
                                            }
                                        ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= $data['NIK']; ?></td>
                                                <td><?= $data['nama']; ?></td>
                                                <td>
                                                    <span class="departemen-badge <?= $deptClass ?>"><?= $data['departmen']; ?></span>
                                                </td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>

    <!-- DataTables Initialization -->
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
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

        function printData() {
            // Redirect to PDF generating page
            window.open('pdf_semua.php', '_blank');
        }
        
        // Animasi untuk cards
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