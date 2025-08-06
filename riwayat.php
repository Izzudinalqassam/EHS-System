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
    <title>Riwayat Absensi - SB Admin</title>
    <style>
        .dashboard-header {
        position: relative;
        margin: 20px auto 30px;
        padding: 20px 25px;
        border-radius: 12px;
        background: linear-gradient(135deg, #6A11CB 0%, #2575FC 100%);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        color: white;
        overflow: hidden;
        }

        .dashboard-header h2 {
            margin: 0;
            font-size: 2rem;
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
            margin-right: 15px;
            font-size: 1.8rem;
        }
        /* Modern Action Buttons Styles */
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

    .btn-info-grad {
        background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
        color: white;
    }

    .btn-info-grad:hover {
        background: linear-gradient(135deg, #1c7f99 0%, #5abdd5 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(33, 147, 176, 0.3);
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

    /* Pulse effect for the icon */
    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.8;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .pulse-icon {
        animation: pulse 2s infinite;
    }
    /* CSS untuk mengubah warna latar belakang baris tabel saat cursor melewati */
    table tbody tr:hover {
        background-color: #f2f2f2;
    }
    
    /* Animation styles */
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

    /* Animasi untuk header */
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
</style>

<!-- Add animate.css library for more animation options -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
        crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
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
                <i class="fas fa-history icon pulse-icon"></i>
                Riwayat Absensi
            </h2>
        </div>

                    <form method="post" action="" class="animated-card">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter-date-start">Tanggal Mulai:</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="filter-date-start" name="filter-date-start"
                                            value="<?php echo isset($_POST['filter-date-start']) ? htmlspecialchars($_POST['filter-date-start']) : date('Y-m-d'); ?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter-date-end">Tanggal Akhir:</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="filter-date-end" name="filter-date-end"
                                            value="<?php echo isset($_POST['filter-date-end']) ? htmlspecialchars($_POST['filter-date-end']) : date('Y-m-d'); ?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter-department">Pilih Departemen:</label>
                                    <select class="form-control" id="filter-department" name="filter-department">
                                        <option value="">Semua Departemen</option>
                                        <?php
                                        include "koneksi.php";
                                        $departments = mysqli_query($konek, "SELECT DISTINCT departmen FROM karyawan WHERE departmen IS NOT NULL AND departmen != '' ORDER BY departmen");
                                        while ($dept = mysqli_fetch_assoc($departments)) {
                                            $selected = isset($_POST['filter-department']) && $_POST['filter-department'] == $dept['departmen'] ? 'selected' : '';
                                            echo "<option value=\"{$dept['departmen']}\" $selected>{$dept['departmen']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search"></i> Tampilkan</button>
                                        <button type="submit" name="cetak_pdf" class="btn btn-danger" formaction="cetak_pdf.php"
                                            formtarget="_blank"><i class="fas fa-file-pdf"></i> Cetak PDF</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="card mb-4 mt-4 animated-card">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Data Absensi
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
                                            <th>Departmen</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Get filter values from form submission
                                        $filter_date_start = isset($_POST['filter-date-start']) ? $_POST['filter-date-start'] : date('Y-m-d');
                                        $filter_date_end = isset($_POST['filter-date-end']) ? $_POST['filter-date-end'] : date('Y-m-d');
                                        $filter_department = isset($_POST['filter-department']) ? $_POST['filter-department'] : '';

                                        // Build the base SQL query
                                        $sql = "SELECT a.*, b.NIK, b.nama, b.departmen 
                                                FROM absensi a 
                                                JOIN karyawan b ON a.nokartu = b.nokartu
                                                WHERE (
                                                    (a.tanggal BETWEEN ? AND ?) -- date range filter
                                                    OR (a.jam_pulang = '00:00:00' AND a.tanggal < ? AND a.tanggal >= ?) -- belum tap out dalam range
                                                    OR (DATE(a.last_update) BETWEEN ? AND ? AND a.jam_pulang != '00:00:00') -- tap out dalam range
                                                )
                                                AND b.departmen NOT IN ('tamu')";

                                        // Add department filter if selected
                                        $params = [$filter_date_start, $filter_date_end, $filter_date_end, $filter_date_start, $filter_date_start, $filter_date_end];
                                        $param_types = "ssssss";
                                        
                                        if (!empty($filter_department)) {
                                            $sql .= " AND b.departmen = ?";
                                            $params[] = $filter_department;
                                            $param_types .= "s";
                                        }

                                        $sql .= " ORDER BY 
                                                    CASE 
                                                        WHEN a.status = 'IN' THEN 1
                                                        ELSE 2
                                                    END,
                                                    a.tanggal DESC, 
                                                    a.last_update DESC";

                                        // Prepare and execute query
                                        $stmt = mysqli_prepare($konek, $sql);
                                        mysqli_stmt_bind_param($stmt, $param_types, ...$params);
                                        mysqli_stmt_execute($stmt);
                                        $result = mysqli_stmt_get_result($stmt);

                                        if ($result && mysqli_num_rows($result) > 0) {
                                            $no = 1;
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<tr>";
                                                echo "<td>{$no}</td>";
                                                echo "<td>{$row['tanggal']}</td>";
                                                echo "<td>{$row['NIK']}</td>";
                                                echo "<td>{$row['nama']}</td>";
                                                echo "<td>{$row['departmen']}</td>";
                                                echo "<td style='color: green; font-weight: bold;'>{$row['jam_masuk']}</td>";
                                                echo "<td style='color: red; font-weight: bold;'>{$row['jam_pulang']}</td>";
                                                echo "<td><a href='detail_profil.php?nik={$row['NIK']}' class='btn-action btn-info-grad'><i class='fas fa-info-circle'></i> Detail</a></td>";
                                                echo "</tr>";
                                                $no++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='8' class='text-center'>Tidak ada data absensi untuk periode yang dipilih</td></tr>";
                                        }

                                        // Tutup statement
                                        mysqli_stmt_close($stmt);
                                        ?>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <?php include 'components/footer.php'; ?>
        </div>
        <div id="cekkartu"></div>
    </div>

    <script>
        $(document).ready(function () {
            // Initialize DataTable
            $('#dataTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "responsive": true,
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data yang tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "search": "Cari:",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });

            // Date inputs are already set to today's date via PHP
        });
    </script>
    <script src="js/scripts.js"></script>
    <script>
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