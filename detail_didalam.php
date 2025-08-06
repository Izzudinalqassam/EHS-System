<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'koneksi.php';

// Mendapatkan tanggal hari ini
$tanggal_hari_ini = date('Y-m-d');

// Query untuk mendapatkan data karyawan (bukan tamu) yang masih di dalam
$sql = "SELECT a.tanggal, a.nokartu, b.NIK, b.nama, b.departmen, a.jam_masuk 
        FROM absensi a 
        JOIN karyawan b ON a.nokartu = b.nokartu
        WHERE b.departmen NOT IN ('tamu') 
        AND b.departmen != '' 
        AND a.tanggal = '$tanggal_hari_ini'
        AND a.jam_masuk != '00:00:00'
        AND (a.jam_pulang = '00:00:00' OR a.jam_masuk > a.jam_pulang)
        ORDER BY a.jam_masuk DESC";

$result = mysqli_query($konek, $sql);

// Menghitung total karyawan yang masih di dalam
$total_didalam = mysqli_num_rows($result);
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
    <title>Karyawan Didalam - PT. Bekasi Power</title>
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
            background: linear-gradient(135deg, #007bff 0%, #00a5ff 100%);
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
                            <i class="fas fa-users icon"></i>
                            Karyawan Yang Masih Didalam
                            <span class="date-badge" id="tanggalhariini"><?= date('d F Y', strtotime($tanggal_hari_ini)) ?></span>
                        </h2>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Data Karyawan Didalam
                            <div class="float-right">
                                <span class="badge badge-primary p-2">Total: <?= $total_didalam ?> Orang</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Departemen</th>
                                            <th>Jam Masuk</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $no . "</td>";
                                            echo "<td>" . htmlspecialchars($row['NIK']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['departmen']) . "</td>";
                                            echo "<td style='color: green; font-weight: bold;'>" . $row['jam_masuk'] . "</td>";
                                            echo "<td style='color: green'><b>IN</b></td>";
                                            echo "<td><button class='btn btn-danger btn-sm keluar-btn' 
                                                    data-nokartu='" . htmlspecialchars($row['nokartu']) . "' 
                                                    data-tanggal='" . htmlspecialchars($row['tanggal']) . "'
                                                    data-nama='" . htmlspecialchars($row['nama']) . "'>
                                                    Keluar</button></td>";
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
                "order": [[4, "desc"]] // Sort by jam masuk column (column index 4) in descending order
            });

            // Handle keluar button clicks
            $(document).on('click', '.keluar-btn', function(e) {
                e.preventDefault();
                const btn = $(this);
                const nokartu = btn.data('nokartu');
                const tanggal = btn.data('tanggal');
                const nama = btn.data('nama');
                
                Swal.fire({
                    title: 'Konfirmasi',
                    html: `Apakah anda yakin bahwa <b>${nama}</b> akan keluar dari PT. Bekasi Power Sekarang?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'update_jam_keluar.php',
                            type: 'POST',
                            data: { nokartu, tanggal },
                            dataType: 'json',
                            success: function(response) {
                                if(response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Berhasil mencatat waktu keluar',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Reload halaman setelah sukses
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: 'Gagal mencatat waktu keluar: ' + response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan sistem',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>