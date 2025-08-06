<?php
error_reporting(0);
include "koneksi.php";
date_default_timezone_set('Asia/Jakarta');

// Periksa apakah ada permintaan untuk mengeluarkan kendaraan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $jam_keluar = date('H:i:s');

    $updateQuery = "UPDATE kendaraan SET jam_keluar = '$jam_keluar' WHERE id = '$id'";
    mysqli_query($konek, $updateQuery);
    
    header("Location: data_kendaraan.php");
    exit();
}

// Set current date and filter
$today = date('Y-m-d');
$filter_date = isset($_POST['filter-date']) ? $_POST['filter-date'] : $today;

// Initial queries for first load
$totalMotorQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM kendaraan 
    WHERE jenis_kendaraan = 'Motor' AND jam_keluar = '00:00:00'");
$totalMotor = mysqli_fetch_assoc($totalMotorQuery)['total'] ?? 0;

$totalMobilQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM kendaraan 
    WHERE jenis_kendaraan = 'Mobil' AND jam_keluar = '00:00:00'");
$totalMobil = mysqli_fetch_assoc($totalMobilQuery)['total'] ?? 0;

$kendaraanQuery = mysqli_query($konek, "SELECT * FROM kendaraan 
    WHERE (jam_keluar = '00:00:00' OR tanggal_input = '$filter_date')
    ORDER BY 
        CASE 
            WHEN jam_keluar = '00:00:00' AND tanggal_input = '$today' THEN 0
            WHEN jam_keluar = '00:00:00' THEN 1
            ELSE 2 
        END,
        tanggal_input DESC,
        jam_masuk DESC");

// Ambil data kendaraan yang masih di dalam untuk dropdown
$kendaraanMasukQuery = mysqli_query($konek, "SELECT id, nama, nopol, jenis_kendaraan, jam_masuk 
    FROM kendaraan 
    WHERE jam_keluar = '00:00:00' 
    ORDER BY nama ASC");

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
    <title>Data Kendaraan - EHS System</title>
    
    <style>
/* Modern Vehicle Cards */
.vehicle-card {
    border-radius: 15px;
    overflow: hidden;
    position: relative;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.vehicle-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 25px rgba(0,0,0,0.15);
}

.motor-card {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.mobil-card {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
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

.vehicle-card:hover .icon-circle {
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
    background: linear-gradient(135deg, #184e68 0%, #57ca85 100%);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    color: white;
    overflow: hidden;
    }

    .dashboard-header .title-section {
        display: flex;
        align-items: center;
        z-index: 2;
    }
    .animate-header {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.5s ease, transform 0.5s ease;
}

.animate-header.show {
    opacity: 1;
    transform: translateY(0);
    animation: fadeInUp 0.6s ease-out forwards;
}
    .dashboard-header h2 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }

    .dashboard-header .date-badge {
        display: inline-block;
        padding: 4px 12px;
        margin-left: 15px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 400;
        backdrop-filter: blur(5px);
        position: relative;
        z-index: 2;
        border: 1px solid rgba(255, 255, 255, 0.3);
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
        font-size: 2rem;
        color: rgba(255, 255, 255, 0.9);
    }

    @keyframes floating {
        0% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-5px);
        }
        100% {
            transform: translateY(0px);
        }
    }

    .float-icon {
        animation: floating 3s ease-in-out infinite;
    }
    table tbody tr:hover {
        background-color: #f2f2f2;
    }
    .vehicle-inside {
        background-color: #e8f5e9 !important;
    }
    .table-striped tbody tr:nth-of-type(odd).vehicle-inside {
        background-color: #c8e6c9 !important;
    }
    @keyframes highlight {
        0% { background-color: #fff3cd; }
        100% { background-color: inherit; }
    }
    .highlight-update {
        animation: highlight 2s;
    }
    .date-badge {
        display: inline-block;
        padding: 5px 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
        margin-left: 10px;
        font-size: 0.9em;
    }
    
    /* Animation styles - add these */
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
    
    /* Searchable dropdown styling */
    .searchable-dropdown {
        position: relative;
    }
    
    .searchable-dropdown-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 1rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="%236c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>');
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 16px;
        cursor: pointer;
    }
    
    .searchable-dropdown-input:focus {
        outline: none;
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .searchable-dropdown-list {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 999;
        display: none;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        margin-top: 2px;
    }
    
    .searchable-dropdown-list.show {
        display: block;
    }
    
    .searchable-dropdown-item {
        padding: 8px 12px;
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    
    .searchable-dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    .searchable-dropdown-item.active {
        background-color: #e9ecef;
    }
    
    .info-value {
        font-weight: 600;
        color: #495057;
    }
    
    .vehicle-info {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 10px;
        margin-top: 15px;
        border-left: 4px solid #28a745;
    }
    
    /* Searchbox dalam dropdown */
    .searchable-dropdown-search {
        position: sticky;
        top: 0;
        padding: 8px;
        background-color: #fff;
        border-bottom: 1px solid #ddd;
        z-index: 2;
    }
    
    .searchable-dropdown-search input {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }
    
    .searchable-dropdown-search input:focus {
        outline: none;
        border-color: #80bdff;
    }
</style>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="sb-nav-fixed">
    <?php include 'components/navbar.php'; ?>

    <!-- Layout Sidenav -->
    <div id="layoutSidenav">
        <?php include 'components/sidenav.php'; ?>

        <!-- Main Content -->
        <div id="layoutSidenav_content">
            <main>
            <div class="container-fluid main-content-animate">
            <div class="dashboard-header animate-header">
            <div class="title-section">
                <i class="fas fa-car-alt icon float-icon"></i>
                <h2>
                    Dashboard Kendaraan
                    <span class="date-badge" id="current-date"><?php echo date('d F Y'); ?></span>
                </h2>
            </div>
        </div>

                    <!-- Kartu Jumlah Kendaraan -->
                    <div class="row mt-4">
                        <div class="col-lg-6 mb-4">
                            <div class="vehicle-card motor-card animated-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle motor-circle">
                                            <i class="fas fa-motorcycle fa-2x text-white"></i>
                                        </div>
                                        <div class="ml-4">
                                            <h5 class="card-title text-white mb-1">Motor di Dalam</h5>
                                            <p class="card-text text-white font-weight-bold display-4"><?php echo $totalMotor > 0 ? $totalMotor : '0'; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <a href="detail_motor_didalam.php" class="card-footer-link">
                                    <span>Lihat Detail</span>
                                    <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="vehicle-card mobil-card animated-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle mobil-circle">
                                            <i class="fas fa-car fa-2x text-white"></i>
                                        </div>
                                        <div class="ml-4">
                                            <h5 class="card-title text-white mb-1">Mobil di Dalam</h5>
                                            <p class="card-text text-white font-weight-bold display-4"><?php echo $totalMobil > 0 ? $totalMobil : '0'; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <a href="detail_mobil_didalam.php" class="card-footer-link">
                                    <span>Lihat Detail</span>
                                    <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                  
                    <form method="post" action="" id="filterForm">
                        <div class="form-row align-items-center mb-3">
                            <div class="col-auto">
                                <label for="filter-date">Filter Tanggal:</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" class="form-control" id="filter-date" name="filter-date" value="<?php echo $filter_date; ?>">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Cari</button>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-info" id="cetakPDF">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="mb-3">
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#kendaraanModal">
                            Tambah Kendaraan
                        </button>
                        <button type="button" class="btn btn-warning ml-2" data-toggle="modal" data-target="#keluarkanModal">
                            Keluarkan Kendaraan
                        </button>
                    </div>

                    <div class="card mb-4 mt-3 animated-card">
                        <div class="card-header">
                            <i class="fas fa-car mr-1"></i>
                            Data Kendaraan
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
                                            <th>Jenis Kendaraan</th>
                                            <th>Nomor Polisi</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($kendaraanQuery)) {
                                            $rowClass = $row['jam_keluar'] == '00:00:00' ? 'class="vehicle-inside"' : '';
                                            echo "<tr {$rowClass}>";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . $row['tanggal_input'] . "</td>";
                                            echo "<td>" . $row['petugas'] . "</td>";
                                            echo "<td>" . $row['nama'] . "</td>";
                                            echo "<td>" . $row['jenis_kendaraan'] . "</td>";
                                            echo "<td>" . $row['nopol'] . "</td>";
                                            echo "<td style='color: green; font-weight: bold;'>" . $row['jam_masuk'] . "</td>";
                                            echo "<td style='color: red; font-weight: bold;'>" . $row['jam_keluar'] . "</td>";
                                            echo "<td>" . ($row['jam_keluar'] != '00:00:00' ? '<strong style="color:red;">Keluar</strong>' : '<strong style="color:green;">Masuk</strong>') . "</td>";
                                            echo '<td>
                                            <form method="post" id="exitForm_' . $row['id'] . '" action="">
                                                <input type="hidden" name="id" value="' . $row['id'] . '">
                                                <button type="button" 
                                                    class="btn-action ' . ($row['jam_keluar'] != '00:00:00' ? 'btn-secondary-grad' : 'btn-danger-grad') . '" 
                                                    id="exitButton_' . $row['id'] . '" 
                                                    onclick="confirmExit(' . $row['id'] . ')" 
                                                    ' . ($row['jam_keluar'] != '00:00:00' ? 'disabled' : '') . '>
                                                    <i class="fas fa-sign-out-alt"></i> ' . ($row['jam_keluar'] != '00:00:00' ? 'Sudah Keluar' : 'Keluar') . '
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

            <!-- Footer -->
            <?php include 'components\footer.php'; ?>
        </div>
    </div>

    <!-- Modal Keluarkan Kendaraan (dengan Dropdown yang Bisa Diketik) -->
    <div class="modal fade" id="keluarkanModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-sign-out-alt mr-2"></i>Keluarkan Kendaraan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="keluarkanForm" method="post">
                        <div class="form-group">
                            <label for="kendaraan_input"><i class="fas fa-car mr-1"></i>Pilih Kendaraan</label>
                            <div class="searchable-dropdown">
                                <input type="text" id="kendaraan_input" class="searchable-dropdown-input" placeholder="Pilih atau ketik nama kendaraan..." autocomplete="off">
                                <div class="searchable-dropdown-list">
                                    <div class="searchable-dropdown-search">
                                        <input type="text" id="dropdown_search" placeholder="Cari...">
                                    </div>
                                    <?php
                                    mysqli_data_seek($kendaraanMasukQuery, 0); // Reset pointer
                                    while ($row = mysqli_fetch_assoc($kendaraanMasukQuery)) {
                                        echo '<div class="searchable-dropdown-item" 
                                            data-id="'.$row['id'].'" 
                                            data-nopol="'.$row['nopol'].'" 
                                            data-jenis="'.$row['jenis_kendaraan'].'" 
                                            data-jam="'.$row['jam_masuk'].'" 
                                            data-text="'.$row['nama'].' - '.$row['nopol'].'">
                                            '.$row['nama'].' - '.$row['nopol'].'
                                        </div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <div id="vehicleInfoContainer" class="vehicle-info" style="display: none;">
                            <div class="row mb-2">
                                <div class="col-sm-4">Nomor Polisi:</div>
                                <div class="col-sm-8 info-value" id="nopol_info">-</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4">Jenis Kendaraan:</div>
                                <div class="col-sm-8 info-value" id="jenis_info">-</div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">Jam Masuk:</div>
                                <div class="col-sm-8 info-value" id="jam_masuk_info">-</div>
                            </div>
                        </div>
                        
                        <input type="hidden" id="id_keluar" name="id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger" id="btnSubmitKeluarkan" disabled>
                        <i class="fas fa-sign-out-alt mr-1"></i>Keluarkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Kendaraan -->
    <div class="modal fade" id="kendaraanModal" tabindex="-1" role="dialog" aria-labelledby="kendaraanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kendaraanModalLabel">Input Data Kendaraan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="kendaraanForm" method="POST">
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
                            <label><i class="fas fa-user"></i> Jenis Input Nama <span class="text-danger">*</span></label>
                            <div class="mb-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="pilih_database" name="jenis_input" value="database" 
                                           class="custom-control-input" checked onclick="toggleNamaInput('database')">
                                    <label class="custom-control-label" for="pilih_database">Pilih dari Database</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="input_manual" name="jenis_input" value="manual" 
                                           class="custom-control-input" onclick="toggleNamaInput('manual')">
                                    <label class="custom-control-label" for="input_manual">Input Manual</label>
                                </div>
                            </div>

                            <div id="nama_database_div">
                                <select name="nama_database" id="nama_database" class="form-control" required onchange="setNomorPolisi()">
                                    <option value="" disabled selected>Pilih Nama</option>
                                    <?php
                                    $query = "SELECT nama, nopol FROM karyawan WHERE departmen IS NOT NULL AND departmen != '' ORDER BY nama ASC";
                                    $result = mysqli_query($konek, $query);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value='" . $row['nama'] . "' data-nopol='" . $row['nopol'] . "'>" . $row['nama'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div id="nama_manual_div" style="display: none;">
                                <input type="text" name="nama_manual" id="nama_manual" placeholder="Masukkan Nama Manual" 
                                       class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-car"></i> Jenis Kendaraan <span class="text-danger">*</span></label>
                            <div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="mobil" name="jenis_kendaraan" value="Mobil" 
                                           class="custom-control-input" required>
                                    <label class="custom-control-label" for="mobil">Mobil</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="motor" name="jenis_kendaraan" value="Motor" 
                                           class="custom-control-input" required>
                                    <label class="custom-control-label" for="motor">Motor</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nopol"><i class="fas fa-id-card"></i> Nomor Polisi <span class="text-danger">*</span></label>
                            <input required type="text" name="nopol" id="nopol" placeholder="Nomor Polisi" 
                                   class="form-control">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success" id="btnSimpanKendaraan">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/scripts.js"></script>
    <script>
       $(document).ready(function() {
            moment.locale('id');
            
            // DataTable utama
            var table = $('#dataTable').DataTable({
                "searching": true,
                "ordering": true,
                "paging": true,
                "order": [], 
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
            });

            // Searchable dropdown untuk keluarkan kendaraan
            const dropdownInput = $('#kendaraan_input');
            const dropdownList = $('.searchable-dropdown-list');
            const dropdownItems = $('.searchable-dropdown-item');
            const dropdownSearch = $('#dropdown_search');
            
            // Buka dropdown saat input diklik
            dropdownInput.on('click', function() {
                dropdownList.addClass('show');
            });
            
            // Tutup dropdown saat klik di luar
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.searchable-dropdown').length) {
                    dropdownList.removeClass('show');
                }
            });
            
            // Pencarian di dalam dropdown
            dropdownSearch.on('keyup', function(e) {
                e.stopPropagation();
                
                const value = $(this).val().toLowerCase();
                
                dropdownItems.each(function() {
                    const text = $(this).data('text').toLowerCase();
                    $(this).toggle(text.indexOf(value) > -1);
                });
            });
            
            // Saat item dipilih
            dropdownItems.on('click', function() {
                const text = $(this).data('text');
                const id = $(this).data('id');
                const nopol = $(this).data('nopol');
                const jenis = $(this).data('jenis');
                const jam = $(this).data('jam');
                
                // Set nilai input dan hidden field
                dropdownInput.val(text);
                $('#id_keluar').val(id);
                
                // Tampilkan info kendaraan
                $('#nopol_info').text(nopol);
                $('#jenis_info').text(jenis);
                $('#jam_masuk_info').text(jam);
                $('#vehicleInfoContainer').fadeIn();
                
                // Aktifkan tombol keluarkan
                $('#btnSubmitKeluarkan').prop('disabled', false);
                
                // Tutup dropdown
                dropdownList.removeClass('show');
            });
            
            // Pencarian saat mengetik di input utama
            dropdownInput.on('keyup', function() {
                const value = $(this).val().toLowerCase();
                
                if (value.length > 0) {
                    // Buka dropdown jika tidak terbuka
                    dropdownList.addClass('show');
                    
                    // Filter items berdasarkan input
                    dropdownItems.each(function() {
                        const text = $(this).data('text').toLowerCase();
                        $(this).toggle(text.indexOf(value) > -1);
                    });
                    
                    // Atur input pencarian di dalam dropdown
                    dropdownSearch.val(value);
                } else {
                    // Jika input kosong, tampilkan semua item
                    dropdownItems.show();
                    dropdownSearch.val('');
                }
            });

            // Handle form submission
            $('#btnSimpanKendaraan').click(function() {
                var form = $('#kendaraanForm');
                var formData = new FormData(form[0]);
                formData.append('btnSimpan', 'true');

                if (!form[0].checkValidity()) {
                    form[0].reportValidity();
                    return;
                }

                $.ajax({
                    url: 'save_kendaraan.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            var result = JSON.parse(response);
                            if (result.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: result.message
                                }).then(function() {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: result.message
                                });
                            }
                        } catch (e) {
                            console.error(e);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat menyimpan data'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menghubungi server'
                        });
                    }
                });
            });

            // Handle modal keluarkan
            $('#btnSubmitKeluarkan').click(function() {
                var id = $('#id_keluar').val();
                if (!id) {
                    Swal.fire('Perhatian', 'Pilih kendaraan terlebih dahulu', 'warning');
                    return;
                }
                
                // Submit form
                $('#keluarkanForm').submit();
            });

            function updateTableData() {
                $.ajax({
                    url: 'get_data_kendaraan.php',
                    method: 'POST',
                    data: {
                        filter_date: $('#filter-date').val()
                    },
                    success: function(response) {
                        const data = JSON.parse(response);
                        
                        const currentDate = moment().format('DD MMMM YYYY');
                        $('#current-date').text(currentDate);

                        if($('#motorCount').text() !== data.counts.motor.toString()) {
                            $('#motorCount').parent().addClass('highlight-update');
                            $('#motorCount').text(data.counts.motor > 0 ? data.counts.motor : '❌');
                            setTimeout(() => {
                                $('#motorCount').parent().removeClass('highlight-update');
                            }, 2000);
                        }
                        
                        if($('#mobilCount').text() !== data.counts.mobil.toString()) {
                            $('#mobilCount').parent().addClass('highlight-update');
                            $('#mobilCount').text(data.counts.mobil > 0 ? data.counts.mobil : '❌');
                            setTimeout(() => {
                                $('#mobilCount').parent().removeClass('highlight-update');
                            }, 2000);
                        }

                        table.clear();
                        data.data.forEach(function(row) {
                            const rowClass = row.jam_keluar === '00:00:00' ? 'vehicle-inside' : '';
                            const statusColor = row.status === 'Masuk' ? 'green' : 'red';
                            const buttonClass = row.status === 'Keluar' ? 'btn-secondary-grad' : 'btn-danger-grad';
                            const buttonDisabled = row.status === 'Keluar' ? 'disabled' : '';

                            table.row.add([
                                row.no,
                                row.tanggal,
                                row.petugas,
                                row.nama,
                                row.jenis_kendaraan,
                                row.nopol,
                                `<span style="color: green; font-weight: bold;">${row.jam_masuk}</span>`,
                                `<span style="color: red; font-weight: bold;">${row.jam_keluar}</span>`,
                                `<strong style="color:${statusColor};">${row.status}</strong>`,
                               `<form method="post" id="exitForm_${row.id}" action="">
                                    <input type="hidden" name="id" value="${row.id}">
                                    <button type="button" class="btn-action ${buttonClass}" 
                                        id="exitButton_${row.id}" 
                                        onclick="confirmExit(${row.id})" ${buttonDisabled}>
                                        <i class="fas fa-sign-out-alt"></i> ${row.status === 'Keluar' ? 'Sudah Keluar' : 'Keluar'}
                                    </button>
                                </form>`
                            ]).node().className = rowClass;
                        });
                        table.draw(false);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX Error: ", textStatus, errorThrown);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Memuat Data',
                            text: 'Terjadi kesalahan saat memuat ulang data tabel. Silakan coba lagi.'
                        });
                    }
                });
            }

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                updateTableData();
            });

            setInterval(updateTableData, 30000);
            updateTableData();
        });

        // Function untuk mengisi nomor polisi otomatis saat memilih nama
        function setNomorPolisi() {
            var selectedOption = $('#nama_database option:selected');
            if (selectedOption.val() && selectedOption.data('nopol')) {
                $('#nopol').val(selectedOption.data('nopol'));
            }
        }

        function toggleNamaInput(type) {
            if (type === 'database') {
                $('#nama_database_div').show();
                $('#nama_manual_div').hide();
                $('#nama_database').prop('required', true);
                $('#nama_manual').prop('required', false);
                
                // Isi nomor polisi otomatis
                setNomorPolisi();
            } else {
                $('#nama_database_div').hide();
                $('#nama_manual_div').show();
                $('#nama_database').prop('required', false);
                $('#nama_manual').prop('required', true);
                $('#nopol').val('');
            }
        }

        function resetForm() {
            const currentPetugas = $('#petugas').val();
            $('#kendaraanForm')[0].reset();
            $('#petugas').val(currentPetugas);
            $('#pilih_database').prop('checked', true);
            toggleNamaInput('database');
        }

        function confirmExit(id) {
            let button = document.getElementById('exitButton_' + id);
            if (button.disabled) {
                return;
            }
            
            Swal.fire({
                title: 'Apakah Anda yakin kendaraan akan keluar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, keluarkan!',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('exitForm_' + id).submit();
                }
            });
        }
        
        // Reset keluarkan form when modal is closed
        $('#keluarkanModal').on('hidden.bs.modal', function () {
            $('#kendaraan_input').val('');
            $('#nopol_info').text('-');
            $('#jenis_info').text('-');
            $('#jam_masuk_info').text('-');
            $('#id_keluar').val('');
            $('#btnSubmitKeluarkan').prop('disabled', true);
            $('#vehicleInfoContainer').hide();
            $('.searchable-dropdown-list').removeClass('show');
            $('.searchable-dropdown-item').show();
            $('#dropdown_search').val('');
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
            
            // Animasi header
            const header = document.querySelector('.animate-header');
            if (header) {
                setTimeout(() => {
                    header.classList.add('show');
                }, 50);
            }
        });
        
        // Handle PDF button click
        $('#cetakPDF').click(function() {
            const selectedDate = $('#filter-date').val();
            if (!selectedDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Silakan pilih tanggal terlebih dahulu!'
                });
                return;
            }
            
            // Open PDF in new tab
            window.open('cetak_pdf_kendaraan.php?tanggal=' + selectedDate, '_blank');
        });
    </script>
</body>
</html>