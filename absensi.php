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
    <title>Dashboard - SB Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="js/realtime-updates.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <style>
        .dashboard-header {
            position: relative;
            margin: 20px auto 30px;
            padding: 25px 30px;
            border-radius: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.08);
            color: white;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dashboard-header h2 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .dashboard-header .date-badge {
            display: inline-block;
            margin-left: 15px;
            padding: 8px 20px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.1));
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
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
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(25deg);
            z-index: 1;
        }

        .dashboard-header .icon {
            margin-right: 12px;
            font-size: 2rem;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1), 0 1px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--card-gradient);
            z-index: 1;
        }

        .card-employees {
            --card-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-interns {
            --card-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .card-total {
            --card-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .card-guests {
            --card-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .card-content {
            position: relative;
            z-index: 2;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--card-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            line-height: 1;
        }

        .card-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--card-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            opacity: 0.9;
            z-index: 1;
        }

        .animated-card {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .animated-card.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .card {
            margin-bottom: 25px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 15px;
            border: none;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        /* Animasi untuk konten utama */
        .main-content-animate {
            animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Enhanced button styles */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .btn-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(86, 171, 47, 0.4);
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(86, 171, 47, 0.6);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .dashboard-header {
                margin: 15px auto 20px;
                padding: 20px;
            }
            
            .dashboard-header h2 {
                font-size: 1.5rem;
                flex-direction: column;
                align-items: flex-start;
            }
            
            .dashboard-header .date-badge {
                margin-left: 0;
                margin-top: 10px;
            }
            
            .stats-card {
                padding: 20px;
            }
            
            .card-number {
                font-size: 2rem;
            }
        }
    </style>
<script type="text/javascript">
$(document).ready(function () {
    // Inisialisasi DataTable
    let dataTable = $('#dataTable').DataTable();
    let lastData = {};
    let shownNotifications = new Set();
    let isSearching = false;
    let refreshInterval;
    let autoRefreshEnabled = true;

    // Setup kartu reader interval
    setInterval(function () {
        $("#cekkartu").load('bacakartu.php');
    }, 2000);

    // Deteksi pencarian
    $('#dataTable_filter input').on('keyup', function() {
        isSearching = $(this).val().length > 0;
        handleRefreshState();
    });

    // Fungsi untuk mengatur state refresh
    function handleRefreshState() {
        if (isSearching || !autoRefreshEnabled) {
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
        } else {
            if (!refreshInterval) {
                refreshInterval = setInterval(fetchData, 3000);
            }
        }
    }

    function fetchData() {
        // Tambahkan pengecekan autoRefreshEnabled
        if (isSearching || !autoRefreshEnabled) return;

        $.ajax({
            url: 'get_data_karyawan.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                // Update counters
                $('#total_masuk').text(response.total_masuk);
                $('#total_keluar').text(response.total_keluar);
                $('#total_keseluruhan').text(response.total_keseluruhan);
                $('#tanggalhariini').text(response.tanggal_display);

                // Update table jika data berubah dan tidak sedang mencari
                if (!isSearching && hasDataChanged(response.absensi)) {
                    updateTable(response.absensi);
                    processNotifications(response.absensi);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    }

    function hasDataChanged(newData) {
        const newDataString = JSON.stringify(newData);
        const lastDataString = JSON.stringify(lastData);
        if (newDataString !== lastDataString) {
            lastData = JSON.parse(newDataString);
            return true;
        }
        return false;
    }

    function updateTable(data) {
        dataTable.clear();
        
        data.forEach((item, index) => {
            let status = '';
            let actionButton = '';
            
            if (item.jam_masuk > item.jam_pulang) {
                status = '<span style="color: green"><b>IN</b></span>';
                actionButton = `<button class='btn btn-danger btn-sm keluar-btn' 
                    data-nokartu='${item.nokartu}' 
                    data-tanggal='${item.tanggal}'
                    data-nama='${item.nama}'>Keluar</button>`;
            } else if (item.jam_pulang != '00:00:00') {
                status = '<span style="color: red"><b>OUT</b></span>';
                actionButton = '-';
            } else if (item.jam_masuk != '00:00:00') {
                status = '<span style="color: green"><b>IN</b></span>';
                actionButton = `<button class='btn btn-danger btn-sm keluar-btn' 
                    data-nokartu='${item.nokartu}' 
                    data-tanggal='${item.tanggal}'
                    data-nama='${item.nama}'>Keluar</button>`;
            }

            dataTable.row.add([
                index + 1,
                item.tanggal,
                item.NIK,
                item.nama,
                item.departmen,
                `<span style="color: green; font-weight: bold;">${item.jam_masuk}</span>`,
                `<span style="color: red; font-weight: bold;">${item.jam_pulang}</span>`,
                status,
                actionButton
            ]);
        });
        
        dataTable.draw(false);
    }

    function processNotifications(data) {
        data.forEach(item => {
            // Create a unique key for each notification that includes the status
            let recordKey = `${item.NIK}_${item.tanggal}_${item.status}_${item.jam_masuk}_${item.jam_pulang}`;
            
            if (!shownNotifications.has(recordKey)) {
                let status = '';
                let notificationTime = '';
                let backgroundColor = '';
                
                // Determine status based on the same logic as the table
                if (item.jam_masuk > item.jam_pulang || 
                    (item.jam_masuk !== '00:00:00' && item.jam_pulang === '00:00:00')) {
                    status = 'IN';
                    notificationTime = item.jam_masuk;
                    backgroundColor = 'green';
                } else if (item.jam_pulang !== '00:00:00') {
                    status = 'OUT';
                    notificationTime = item.jam_pulang;
                    backgroundColor = 'red';
                }
                
                // Only show notification if we have a valid status and the record is new
                if (status && (!lastData[item.NIK] || 
                    (status === 'IN' && lastData[item.NIK]?.jam_masuk !== item.jam_masuk) ||
                    (status === 'OUT' && lastData[item.NIK]?.jam_pulang !== item.jam_pulang))) {
                    
                    showToast(
                        `${item.nama} (${item.departmen}) | Tap ${status}: ${notificationTime}`,
                        backgroundColor
                    );
                }
                
                shownNotifications.add(recordKey);
            }
        });
    }

    function showToast(message, backgroundColor) {
        Toastify({
            text: message,
            backgroundColor: backgroundColor,
            duration: 3000,
            gravity: "top",
            position: 'right',
            style: {
                borderRadius: '10px',
                border: '2px solid white',
                padding: '10px',
                boxShadow: '0 0 10px rgba(0,0,0,0.5)',
                fontSize: '16px'
            }
        }).showToast();
    }

    // Clear interval when page is hidden/inactive
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
        } else {
            if (!isSearching && !refreshInterval && autoRefreshEnabled) {
                refreshInterval = setInterval(fetchData, 3000);
            }
        }
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
                            });
                            fetchData();
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

    // Tambahkan tombol toggle refresh
    $('.card-header').append(`
        <div class="float-right">
            <button id="toggleRefresh" class="btn btn-primary btn-sm">
                <i class="fas fa-sync"></i> Auto Refresh: ON
            </button>
        </div>
    `);

    // Handle toggle refresh
    $('#toggleRefresh').click(function() {
        autoRefreshEnabled = !autoRefreshEnabled;
        if (autoRefreshEnabled) {
            $(this).html('<i class="fas fa-sync"></i> Auto Refresh: ON');
            if (!isSearching && !refreshInterval) {
                refreshInterval = setInterval(fetchData, 3000);
            }
        } else {
            $(this).html('<i class="fas fa-sync"></i> Auto Refresh: OFF');
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
        }
    });

    // Initial fetch
    fetchData();
    
    // Start refresh interval
    refreshInterval = setInterval(fetchData, 3000);
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
                    <?php include 'models/sql_card_karyawan.php'; ?>

                    <div class="dashboard-header animate-header">
    <h2>
        <i class="fas fa-chart-line icon"></i>
        Dashboard Sistem EHS Tanggal
        <span class="date-badge" id="tanggalhariini"></span>
    </h2>
</div>

                    <div class="row">
                        <div class="col-xl-4 col-md-6">
                            <div class="stats-card card-employees animated-card">
                                <div class="card-content">
                                    <div class="card-title">Tap-In Hari Ini</div>
                                    <div class="card-number" id="total_masuk"><?= $total_orang_masuk ?></div>
                                    <a href="detail_orangmasuk.php" class="btn btn-sm" style="background: var(--card-gradient); color: white; border: none; border-radius: 8px; padding: 8px 16px; text-decoration: none; font-weight: 600;">View Details <i class="fas fa-arrow-right ml-1"></i></a>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="stats-card card-interns animated-card">
                                <div class="card-content">
                                    <div class="card-title">Tap-Out Hari Ini</div>
                                    <div class="card-number" id="total_keluar"><?= $total_orang_keluar ?></div>
                                    <a href="detail_orangkeluar.php" class="btn btn-sm" style="background: var(--card-gradient); color: white; border: none; border-radius: 8px; padding: 8px 16px; text-decoration: none; font-weight: 600;">View Details <i class="fas fa-arrow-right ml-1"></i></a>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="stats-card card-total animated-card">
                                <div class="card-content">
                                    <div class="card-title">Sedang Didalam</div>
                                    <div class="card-number" id="total_keseluruhan"><?= $total_keseluruhan ?></div>
                                    <a href="detail_didalam.php" class="btn btn-sm" style="background: var(--card-gradient); color: white; border: none; border-radius: 8px; padding: 8px 16px; text-decoration: none; font-weight: 600;">View Details <i class="fas fa-arrow-right ml-1"></i></a>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tombol untuk membuka modal -->
<div class="mb-4">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#manualAttendanceModal">
        <i class="fas fa-user-clock mr-2"></i>Absen Manual
    </button>
</div>

<!-- Tambahkan CSS Select2 di bagian head -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Modal Absensi Manual -->
<div class="modal fade" id="manualAttendanceModal" tabindex="-1" aria-labelledby="manualAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manualAttendanceModalLabel">
                    <i class="fas fa-user-clock mr-2"></i>Tambah Absensi Manual
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="manualAttendanceForm">
                    <div class="form-group">
                        <label for="employeeSelect">Pilih Karyawan:</label>
                        <select class="form-control select2" id="employeeSelect" required style="width: 100%;">
                            <option value="">-- Pilih Karyawan --</option>
                            <?php
                            $query = "SELECT nokartu, nama, departmen FROM karyawan WHERE departmen NOT IN ('tamu') AND departmen != '' ORDER BY nama ASC";
                            $result = mysqli_query($konek, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . htmlspecialchars($row['nokartu']) . "'>" . 
                                     htmlspecialchars($row['nama']) . " (" . htmlspecialchars($row['departmen']) . ")</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis Absensi:</label>
                        <div class="btn-group d-flex" role="group">
                            <button type="button" class="btn btn-success flex-fill attendance-btn" data-mode="in">
                                <i class="fas fa-sign-in-alt mr-2"></i>Masuk (IN)
                            </button>
                            <button type="button" class="btn btn-danger flex-fill attendance-btn" data-mode="out">
                                <i class="fas fa-sign-out-alt mr-2"></i>Keluar (OUT)
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('#employeeSelect').select2({
        theme: 'bootstrap4',
        placeholder: "Cari karyawan...",
        allowClear: true,
        dropdownParent: $('#manualAttendanceModal'),
        language: {
            noResults: function() {
                return "Data tidak ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    });

    // Handle attendance button clicks
    $('.attendance-btn').click(function() {
        const nokartu = $('#employeeSelect').val();
        const mode = $(this).data('mode');
        
        if (!nokartu) {
            Swal.fire({
                title: 'Error!',
                text: 'Silakan pilih karyawan terlebih dahulu',
                icon: 'error'
            });
            return;
        }

        const selectedText = $('#employeeSelect option:selected').text();
        
        Swal.fire({
            title: 'Konfirmasi',
            html: `Apakah anda yakin ingin mencatat absensi <b>${mode.toUpperCase()}</b> untuk<br><b>${selectedText}</b>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'process_manual_attendance.php',
                    type: 'POST',
                    data: { 
                        nokartu: nokartu,
                        mode: mode
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#manualAttendanceModal').modal('hide');
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Berhasil mencatat absensi',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            // Reset selection
                            $('#employeeSelect').val('').trigger('change');
                            // Refresh table data
                            fetchData();
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal mencatat absensi: ' + response.message,
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

    // Reset form when modal is closed
    $('#manualAttendanceModal').on('hidden.bs.modal', function () {
        $('#employeeSelect').val('').trigger('change');
    });
});
</script>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Data Absensi Karyawan Hari Ini
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
                                            <th>Status</th>
                                            <th>Aksi</th> <!-- Tambah kolom aksi -->
                                        </tr>
                                    </thead>
                                    <tbody id="absensi-table-body">
                                        <?php
                                        // Query data absensi dari tabel karyawan dan absensi, hanya untuk karyawan (bukan tamu atau magang) hari ini
                                        $sql = "SELECT a.tanggal, a.nokartu, b.NIK, b.nama, b.departmen, a.jam_masuk, a.jam_pulang 
                                                FROM absensi a 
                                                JOIN karyawan b ON a.nokartu = b.nokartu
                                                WHERE b.departmen NOT IN ('tamu') AND b.departmen != '' AND a.tanggal = '$tanggal_hari_ini'";
                                        $result = mysqli_query($konek, $sql);
                                        $no = 1;

                                        // Looping data hasil query
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $no . "</td>";
                                            echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['NIK']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['departmen']) . "</td>";
                                            echo "<td style='color: green; font-weight: bold;'>{$row['jam_masuk']}</td>";
                                            echo "<td style='color: red; font-weight: bold;'>{$row['jam_pulang']}</td>";

                                            // Cek status absensi
                                            if ($row['jam_masuk'] > $row['jam_pulang']) {
                                                // Jika jam masuk lebih baru dari jam keluar, status jadi IN
                                                echo "<td style='color: green'><b>IN</b></td>";
                                                echo "<td><button class='btn btn-danger btn-sm keluar-btn' 
                                                        data-nokartu='" . htmlspecialchars($row['nokartu']) . "' 
                                                        data-tanggal='" . htmlspecialchars($row['tanggal']) . "'>
                                                        Keluar</button></td>";
                                            } elseif ($row['jam_pulang'] != '00:00:00') {
                                                // Jika jam keluar terisi, status jadi OUT
                                                echo "<td style='color: red'><b>OUT</b></td>";
                                                echo "<td>-</td>";
                                            } elseif ($row['jam_masuk'] != '00:00:00') {
                                                // Jika hanya jam masuk terisi, status IN
                                                echo "<td style='color: green'><b>IN</b></td>";
                                                echo "<td><button class='btn btn-danger btn-sm keluar-btn' 
                                                        data-nokartu='" . htmlspecialchars($row['nokartu']) . "' 
                                                        data-tanggal='" . htmlspecialchars($row['tanggal']) . "'
                                                        data-nama='" . htmlspecialchars($row['nama']) . "'>
                                                        Keluar</button></td>";
                                            }else {
                                                // Kondisi lain jika tidak ada jam masuk dan jam keluar
                                                echo "<td></td>";
                                                echo "<td>-</td>";
                                            }

                                            echo "</tr>";
                                            $no++;
                                        }
                                        ?>
                                    </tbody>
                                </table>

                                <!-- Tambahkan script untuk handle tombol keluar -->
                                
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php include 'components/footer.php'; ?>
        </div>
    </div>
    <div id="cekkartu"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced card animations with IntersectionObserver
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
                }
            });
        }, observerOptions);

        // Observe all animated cards
        document.querySelectorAll('.animated-card').forEach(card => {
            cardObserver.observe(card);
        });

        // Enhanced hover effects for action buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.05)';
            });
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Counter animation for stats cards
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current);
            }, 30);
        }

        // Animate counters when cards become visible
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const numberElement = entry.target.querySelector('.card-number');
                    if (numberElement && !numberElement.dataset.animated) {
                        const targetValue = parseInt(numberElement.textContent) || 0;
                        numberElement.dataset.animated = 'true';
                        animateCounter(numberElement, targetValue);
                    }
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.stats-card').forEach(card => {
            statsObserver.observe(card);
        });

        // Ripple effect on card click
        document.querySelectorAll('.stats-card').forEach(card => {
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

        // Add CSS for ripple effect
        const style = document.createElement('style');
        style.textContent = `
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
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
            
            .table-responsive table tbody tr {
                transition: all 0.3s ease;
            }
            
            .table-responsive table tbody tr:hover {
                background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)) !important;
                transform: scale(1.02);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }
        `;
        document.head.appendChild(style);
    });
</script>
</html>