<?php
error_reporting(0);
include "koneksi.php";

session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

date_default_timezone_set('Asia/Jakarta');

// Periksa apakah ada permintaan untuk mengeluarkan tamu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $jam_keluar = date('H:i:s');
    $updateQuery = "UPDATE tamu SET jam_keluar_tamu = '$jam_keluar' WHERE id = '$id'";
    mysqli_query($konek, $updateQuery);
    header("Location: datatamu.php");
    exit();
}

// Date range filtering
$filter_date_start = isset($_POST['filter-date-start']) ? $_POST['filter-date-start'] : date('Y-m-d');
$filter_date_end = isset($_POST['filter-date-end']) ? $_POST['filter-date-end'] : date('Y-m-d');
$current_date = date('Y-m-d');

// Build the query based on filters
if (isset($_POST['filter-date-start']) && isset($_POST['filter-date-end'])) {
    // Date range filtering
    $tamuQuery = mysqli_query($konek, "
        SELECT *, 
        DATEDIFF('$current_date', tanggal_tamu) as days_inside
        FROM tamu 
        WHERE tanggal_tamu BETWEEN '$filter_date_start' AND '$filter_date_end'
        ORDER BY tanggal_tamu DESC, jam_masuk_tamu DESC
    ");
    
    // Count guests in the date range who are still inside
    $totalTamuQuery = mysqli_query($konek, "
        SELECT SUM(jumlah_tamu) as total 
        FROM tamu 
        WHERE jumlah_tamu != '' 
        AND jam_keluar_tamu = '00:00:00'
        AND tanggal_tamu BETWEEN '$filter_date_start' AND '$filter_date_end'
    ");
    
    // Count guests who left in the date range
    $totalKeluarQuery = mysqli_query($konek, "
        SELECT SUM(jumlah_tamu) as total_keluar 
        FROM tamu 
        WHERE jam_keluar_tamu != '00:00:00' 
        AND tanggal_tamu BETWEEN '$filter_date_start' AND '$filter_date_end'
    ");
} else {
    // Default behavior - show all guests who haven't checked out + today's guests
    $tamuQuery = mysqli_query($konek, "
        SELECT *, 
        DATEDIFF('$current_date', tanggal_tamu) as days_inside
        FROM tamu 
        WHERE (jam_keluar_tamu = '00:00:00' OR tanggal_tamu = '$current_date')
        ORDER BY 
            CASE WHEN jam_keluar_tamu = '00:00:00' THEN 0 ELSE 1 END,
            tanggal_tamu DESC,
            jam_masuk_tamu DESC
    ");
    
    // Count active guests (still inside)
    $totalTamuQuery = mysqli_query($konek, "
        SELECT SUM(jumlah_tamu) as total 
        FROM tamu 
        WHERE jumlah_tamu != '' 
        AND jam_keluar_tamu = '00:00:00'
    ");
    
    // Count guests who left today
    $totalKeluarQuery = mysqli_query($konek, "
        SELECT SUM(jumlah_tamu) as total_keluar 
        FROM tamu 
        WHERE jam_keluar_tamu != '00:00:00' 
        AND tanggal_tamu = '$current_date'
    ");
}

$totalTamu = mysqli_fetch_assoc($totalTamuQuery)['total'] ?? 0;
$totalKeluar = mysqli_fetch_assoc($totalKeluarQuery)['total_keluar'] ?? 0;
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
    <title>Data Tamu - Sistem EHS</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-theme@0.1.0-beta.10/dist/select2-bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
/* Modern Guest Cards */
.guest-card {
    border-radius: 15px;
    overflow: hidden;
    position: relative;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    color: white;
}

.guest-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 25px rgba(0,0,0,0.15);
}

.guest-in-card {
    background: linear-gradient(135deg, #28a745 0%, #218838 100%);
}

.guest-out-card {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.2);
    transition: background 0.3s ease;
}

.guest-card:hover .icon-circle {
    background: rgba(255,255,255,0.3);
}

.card-footer-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    background: rgba(0,0,0,0.1);
    color: white;
    text-decoration: none;
    transition: background 0.3s ease;
}

.card-footer-link:hover {
    background: rgba(0,0,0,0.2);
    color: white;
}

.card-footer-link span {
    font-weight: 500;
}

.card-title {
    font-size: 1.2rem;
}

.display-4 {
    font-size: 3rem;
    line-height: 1;
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
        cursor: pointer;
    }

    .btn-action i {
        margin-right: 4px;
    }

    .btn-danger-grad {
        background: linear-gradient(135deg, #ff5f6d 0%, #ff8f70 100%);
        color: white;
    }

    .btn-danger-grad:hover {
        background: linear-gradient(135deg, #ff4757 0%, #ff7f50 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 95, 109, 0.3);
        color: white;
    }

    .btn-secondary-grad {
        background: linear-gradient(135deg, #808080 0%, #a9a9a9 100%);
        color: white;
        opacity: 0.8;
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
    padding: 20px 25px;
    border-radius: 12px;
    background: linear-gradient(135deg, #FF5F6D 0%, #FFC371 100%);
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
        
        /* Style for guests who have stayed multiple days */
        .table-warning {
            background-color: #fff3cd !important;
        }
        .table-warning:hover {
            background-color: #ffeeba !important;
        }
        
        /* Additional styling for status indicators */
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
        
        .status-complete {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }

        /* Select2 custom styling */
        .select2-container--bootstrap .select2-selection--single {
            height: 38px;
            padding: 8px 12px;
            font-size: 1rem;
            line-height: 1.5;
        }

        /* Modal custom styling */
        .modal-lg {
            max-width: 800px;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .form-group label {
            font-weight: 600;
        }
        
        .required-field::after {
            content: " *";
            color: red;
        }
        /* Fixed Select2 in Bootstrap Modal */
.select2-container--open {
    z-index: 9999999;
}

.modal-body .select2-container {
    width: 100% !important;
}

/* Ensure dropdown appears over modal */
.select2-dropdown {
    z-index: 99999999 !important;
}

/* Fix Select2 search box in modal */
.select2-search--dropdown {
    display: block;
    padding: 4px;
}

.select2-container--bootstrap {
    display: block;
    width: 100% !important;
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
                    <i class="fas fa-user-friends icon"></i>
                    Dashboard Sistem EHS Data Tamu
                    <?php if(isset($_POST['filter-date-start']) && isset($_POST['filter-date-end'])): ?>
                        <span class="date-badge" id="tanggal-filter">
                            <?php echo date('d/m/Y', strtotime($_POST['filter-date-start'])) . ' - ' . date('d/m/Y', strtotime($_POST['filter-date-end'])); ?>
                        </span>
                    <?php endif; ?>
                </h2>
            </div>

                    <!-- Cards for guest count -->
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="guest-card guest-in-card animated-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle guest-in-circle">
                                            <i class="fas fa-user-check fa-2x text-white"></i>
                                        </div>
                                        <div class="ml-4">
                                            <h5 class="card-title text-white mb-1">Tamu Masih Ada</h5>
                                            <p class="card-text text-white font-weight-bold display-4"><?php echo $totalTamu > 0 ? $totalTamu : '0'; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <a href="detail_tamu_didalam.php" class="card-footer-link">
                                    <span>Lihat Detail</span>
                                    <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="guest-card guest-out-card animated-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle guest-out-circle">
                                            <i class="fas fa-user-minus fa-2x text-white"></i>
                                        </div>
                                        <div class="ml-4">
                                            <h5 class="card-title text-white mb-1">Tamu Sudah Keluar</h5>
                                            <p class="card-text text-white font-weight-bold display-4"><?php echo $totalKeluar > 0 ? $totalKeluar : '0'; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <a href="detail_tamu_keluar.php" class="card-footer-link">
                                    <span>Lihat Detail</span>
                                    <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Date range filter form -->
                    <form method="post" action="" class="mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <label for="filter-date-start" class="form-label">Dari Tanggal:</label>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="filter-date-start" name="filter-date-start"
                                    value="<?php echo isset($_POST['filter-date-start']) ? $_POST['filter-date-start'] : date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="filter-date-end" class="form-label">Sampai Tanggal:</label>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="filter-date-end" name="filter-date-end"
                                    value="<?php echo isset($_POST['filter-date-end']) ? $_POST['filter-date-end'] : date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search mr-1"></i>Tampilkan
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Action buttons -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary a" data-toggle="modal" data-target="#tambahTamuModal">
                            <i class="fas fa-plus mr-1"></i>Tambah Tamu
                        </button>
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#aturTamuModal">
                            <i class="fas fa-cog mr-1"></i>Atur Tamu
                        </button>
                        <a href="pdf_tamu.php?filter_date_start=<?php echo isset($filter_date_start) ? $filter_date_start : ''; ?>&filter_date_end=<?php echo isset($filter_date_end) ? $filter_date_end : ''; ?>" 
                           target="_blank" class="btn btn-danger">
                            <i class="fas fa-file-pdf mr-1"></i>Cetak PDF
                        </a>
                    </div>

                    <!-- Data Table -->
                    <div class="card mb-4 animated-card">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Data Tamu
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
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($tamuQuery)) {
                                            $days_inside = $row['days_inside'];
                                            $status_class = '';
                                            $status_text = '';
                                            
                                            if ($row['jam_keluar_tamu'] != '00:00:00') {
                                                $status_class = 'status-complete';
                                                $status_text = 'Keluar';
                                            } else {
                                                if ($days_inside > 0) {
                                                    $status_class = 'status-overdue';
                                                    $status_text = 'Belum Keluar (' . $days_inside . ' hari)';
                                                } else {
                                                    $status_class = 'status-active';
                                                    $status_text = 'Masuk';
                                                }
                                            }
                                            
                                            echo "<tr " . ($days_inside > 0 && $row['jam_keluar_tamu'] == '00:00:00' ? "class='table-warning'" : "") . ">";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . $row['tanggal_tamu'] . "</td>";
                                            echo "<td>" . $row['petugas'] . "</td>";
                                            echo "<td>" . $row['nama_tamu'] . "</td>";
                                            echo "<td>" . $row['nama_perusahaan'] . "</td>";
                                            echo "<td>" . $row['jumlah_tamu'] . "</td>";
                                            echo "<td>" . $row['keperluan'] . "</td>";
                                            echo "<td>" . $row['ingin_bertemu'] . "</td>";
                                            echo "<td class='text-success font-weight-bold'>" . $row['jam_masuk_tamu'] . "</td>";
                                            echo "<td class='text-danger font-weight-bold'>" . ($row['jam_keluar_tamu'] == '00:00:00' ? '-' : $row['jam_keluar_tamu']) . "</td>";
                                            echo "<td>" . $row['nopol'] . "</td>";
                                            echo "<td><span class='" . $status_class . "'>" . $status_text . "</span></td>";
                                            echo '<td>
                                            <form method="post" id="exitForm_' . $row['id'] . '" action="">
                                                <input type="hidden" name="id" value="' . $row['id'] . '">
                                                <button type="button" 
                                                    class="btn-action ' . ($row['jam_keluar_tamu'] != '00:00:00' ? 'btn-secondary-grad' : 'btn-danger-grad') . '" 
                                                    id="exitButton_' . $row['id'] . '" 
                                                    onclick="confirmExit(' . $row['id'] . ')"
                                                    ' . ($row['jam_keluar_tamu'] != '00:00:00' ? 'disabled' : '') . '>
                                                    <i class="fas fa-sign-out-alt"></i> ' . ($row['jam_keluar_tamu'] != '00:00:00' ? 'Sudah Keluar' : 'Keluar') . '
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
            <div id="cekkartu"></div>
            <?php include 'components\footer.php'; ?>
        </div>
    </div>

    <!-- Modal Atur Tamu -->
    <div class="modal fade" id="aturTamuModal" tabindex="-1" role="dialog" aria-labelledby="aturTamuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aturTamuModalLabel">Atur Tamu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="guestSelect">Pilih Tamu:</label>
                        <select class="form-control" id="guestSelect">
                            <option value="">Cari nama tamu...</option>
                            <?php
                            $activeGuestsQuery = mysqli_query($konek, "
                                SELECT id, nama_tamu, nama_perusahaan, jam_masuk_tamu, tanggal_tamu
                                FROM tamu 
                                WHERE jam_keluar_tamu = '00:00:00'
                                ORDER BY tanggal_tamu DESC, jam_masuk_tamu DESC
                            ");
                            
                            while ($guest = mysqli_fetch_assoc($activeGuestsQuery)) {
                                echo '<option value="' . $guest['id'] . '">' . 
                                    $guest['nama_tamu'] . ' - ' . 
                                    $guest['nama_perusahaan'] . ' (Masuk: ' . 
                                    date('d/m/Y', strtotime($guest['tanggal_tamu'])) . ' ' .
                                    $guest['jam_masuk_tamu'] . ')' . 
                                    '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" id="exitGuestBtn" class="btn btn-danger btn-lg" disabled>
                            <i class="fas fa-sign-out-alt mr-1"></i>Keluar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Tamu -->
    <div class="modal fade" id="tambahTamuModal" tabindex="-1" role="dialog" aria-labelledby="tambahTamuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahTamuModalLabel">Tambah Data Tamu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="formTambahTamu">
                        <div class="form-group">
                            <label for="id_tamu"><i class="fas fa-id-card"></i> ID Tamu</label>
                            <div class="input-group">
                                <input type="text" id="id_tamu" name="id_tamu" placeholder="Masukkan ID Unique Dari Tamu" class="form-control">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="validateButton">Validasi ID</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="petugas"><i class="fas fa-user"></i> Petugas <span class="text-danger">*</span></label>
                            <select required name="petugas" id="petugas" class="form-control">
                                <option value="" disabled selected>Pilih Petugas</option>
                                <option value="M. SUBUR">M. SUBUR</option>
                                <option value="SUSANTO">SUSANTO</option>
                                <option value="ADE RESA">ADE RESA</option>
                                <option value="SOPHIAN HADI">SOPHIAN HADI</option>
                                <option value="DAHRI FAI">DAHRI FAI</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nama_tamu" class="required-field"><i class="fas fa-user"></i> Nama Tamu</label>
                            <input required type="text" name="nama_tamu" id="nama_tamu" placeholder="Nama Tamu" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="nama_perusahaan"><i class="fas fa-building"></i> Nama Perusahaan</label>
                            <input type="text" name="nama_perusahaan" id="nama_perusahaan" placeholder="Nama Perusahaan" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="jumlah_tamu" class="required-field"><i class="fas fa-users"></i> Jumlah Tamu</label>
                            <input required type="number" name="jumlah_tamu" id="jumlah_tamu" placeholder="Masukan jumlah tamu" class="form-control" pattern="[0-9]*">
                        </div>

                        <div class="form-group">
                            <label for="keperluan"><i class="fas fa-file-alt"></i> Keperluan</label>
                            <input type="text" name="keperluan" id="keperluan" placeholder="Keperluan" class="form-control">
                        </div>

                        <div class="form-group">
    <label for="ingin_bertemu" class="required-field">
        <i class="fas fa-user-friends"></i> Ingin Bertemu
    </label>
    <div class="select2-full-width">
        <select required name="ingin_bertemu" id="ingin_bertemu" class="form-control select2">
            <option value="" disabled selected>Pilih Karyawan</option>
            <?php
            $query = "SELECT * FROM karyawan WHERE departmen IS NOT NULL AND departmen != ''";
            $result = mysqli_query($konek, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['nama'] . "'>" . $row['nama'] . "</option>";
            }
            ?>
        </select>
    </div>
</div>

                        <div class="form-group">
                            <label for="nopol"><i class="fas fa-car"></i> Nomor Kendaraan</label>
                            <input type="text" name="nopol" id="nopol" placeholder="Nomor Kendaraan" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-success btn-block mt-4">
                            <i class="fas fa-save mr-1"></i>Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/gettamu.js"></script>
    <!-- Custom Scripts -->
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

            // Initialize Select2 in modals
            $('#guestSelect').select2({
                theme: 'bootstrap',
                placeholder: 'Cari nama tamu...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#aturTamuModal')
            });

            $('#ingin_bertemu').select2({
                theme: 'bootstrap',
                placeholder: 'Pilih Karyawan',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#tambahTamuModal')
            });

            // Handle guest selection change
            $('#guestSelect').on('change', function() {
                $('#exitGuestBtn').prop('disabled', !$(this).val());
            });

            // Handle form submission
            // Handle form submission with WhatsApp integration
$('#formTambahTamu').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        type: 'POST',
        url: 'process_tamu.php', 
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersimpan',
                    text: 'Data tamu berhasil disimpan dan notifikasi WhatsApp telah dikirim',
                    showConfirmButton: true,
                }).then(function() {
                    $('#tambahTamuModal').modal('hide');
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Tersimpan',
                    text: response.message || 'Terjadi kesalahan saat memproses permintaan.'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memproses permintaan.'
            });
        }
    });
});

            // Exit guest button in modal
            $('#exitGuestBtn').on('click', function() {
                const guestId = $('#guestSelect').val();
                if (!guestId) return;

                const guestName = $('#guestSelect option:selected').text();

                confirmExit(guestId, guestName);
            });
        });

        // Function for confirming guest exit
        function confirmExit(id, name = '') {
            let button = document.getElementById('exitButton_' + id);
            if (button && button.classList.contains('btn-secondary')) {
                return;
            }

            if (button) {
                button.classList.add('btn-secondary');
                button.disabled = true;
            }

            Swal.fire({
                title: 'Konfirmasi Tamu Keluar',
                text: name ? `Apakah Anda yakin ingin mengeluarkan ${name}?` : 'Apakah Anda yakin tamu ini akan keluar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Keluarkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('exitForm_' + id) || 
                                $('<form>', {
                                    'method': 'post',
                                    'action': ''
                                }).append($('<input>', {
                                    'type': 'hidden',
                                    'name': 'id',
                                    'value': id
                                }));
                    
                    if (typeof form.submit === 'function') {
                        form.submit();
                    } else {
                        $(form).appendTo('body').submit();
                    }
                } else if (button) {
                    button.classList.remove('btn-secondary');
                    button.disabled = false;
                }
            }).catch(error => {
                console.error('Error:', error);
                if (button) {
                    button.classList.remove('btn-secondary');
                    button.disabled = false;
                }
                Swal.fire(
                    'Error!',
                    'Terjadi kesalahan saat memproses permintaan.',
                    'error'
                );
            });
        }

        // Auto-refresh page every 5 minutes
        setTimeout(function() {
            location.reload();
        }, 300000);
    </script>
    <script >
    $(document).ready(function () {
    setInterval(function () {
        $("#cekkartu").load('bacakartu.php');
    }, 2000);
});
    </script>
     <script >
        // Reset posisi dropdown saat modal dibuka
$('#tambahTamuModal').on('shown.bs.modal', function () {
    $('#ingin_bertemu').select2('close');  // Tutup dropdown jika terbuka
    $('#ingin_bertemu').select2('destroy'); // Destroy instance lama
    // Reinisialisasi Select2
    $('#ingin_bertemu').select2({
        theme: 'bootstrap',
        placeholder: 'Pilih Karyawan',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#tambahTamuModal .modal-body'),
        containerCssClass: 'select2-container--full',
        dropdownCssClass: 'select2-dropdown--above'
    });
});
</script>
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
Step 3: Update HTM
</body>
</html>