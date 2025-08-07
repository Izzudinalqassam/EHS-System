<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
include "models/sql_chart_index.php";
include "models/sql_card_index.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Dashboard Chart</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="icon" href="image/bp.png" type="image/x-icon">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
        crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <script src="js/ajax/ajax_index.js"></script>

    <style>
        .dashboard-header {
    position: relative;
    margin: 20px auto 40px;
    padding: 25px 30px;
    border-radius: 15px;
    background: linear-gradient(135deg, #4158D0 0%, #C850C0 46%, #FFCC70 100%);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    color: white;
    overflow: hidden;
    text-align: left;
}

.dashboard-header h2 {
    margin: 0;
    font-size: 2rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    position: relative;
    z-index: 2;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: rgba(255, 255, 255, 0.1);
    transform: rotate(30deg);
    z-index: 1;
}

.dashboard-header::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 100%;
    height: 10px;
    background: rgba(0, 0, 0, 0.05);
    border-radius: 50%;
    filter: blur(5px);
}

.role-badge {
    display: inline-block;
    margin-top: 10px;
    padding: 5px 15px;
    background-color: rgba(255, 255, 255, 0.25);
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    backdrop-filter: blur(5px);
    position: relative;
    z-index: 2;
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
        /* Old styles removed - replaced by enhanced versions below */

        .mt-4 {
            background-color: #ffffff;
            border-radius: 15px;
            padding: 20px;
            margin: 20px auto;
        }

        .row.mt-4 {
            margin-right: 0;
            margin-left: 0;
        }

        /* Tambahan style untuk charts */
        .donut-chart-container {
            height: 45vh;
        }
        
        /* Badge styles */
        .role-badge {
            padding: 5px 10px;
            border-radius: 5px;
            margin-left: 10px;
            font-size: 12px;
            color: white;
        }
        
        .admin-badge {
            background-color: #4CAF50;
        }
        
        .security-badge {
            background-color: #ff9800;
        }
        
        /* Quick action buttons style */
        .quick-action-btn {
            padding: 15px;
            margin-bottom: 10px;
            font-size: 16px;
            text-align: center;
            transition: all 0.3s;
            display: block;
            color: white;
            text-decoration: none;
        }
        
        .quick-action-btn:hover {
            transform: scale(1.05);
            text-decoration: none;
            color: white;
        }
        
        .action-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .page-enter {
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
        
        /* Enhanced card animations and effects */
        .animated-card {
            opacity: 0;
            transform: translateY(30px) scale(0.95);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .animated-card.show {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        
        /* Enhanced card hover effects */
        .card {
            margin-bottom: 20px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        
        .card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
            z-index: 1;
        }
        
        .card:hover::before {
            left: 100%;
        }
        
        /* Ripple effect */
        .card-ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        /* Counter animation */
        .counter {
            font-weight: 700;
            font-size: 2.5rem !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            cursor: default;
        }
        
        .counter:hover {
            transform: scale(1.05);
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
        }
        
        .counter[data-target="0"] {
            color: rgba(255, 255, 255, 0.6) !important;
            font-size: 2.2rem !important;
            opacity: 0.8;
        }
        
        .counter[data-target="0"]:hover {
            color: rgba(255, 255, 255, 0.9) !important;
            transform: none;
        }
        
        /* Enhanced admin buttons */
        .btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 10px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s;
        }
        
        .btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        /* Chart container enhancement */
        .chart-container {
            position: relative;
            min-height: 500px;
            width: 100%;
            margin: 30px 0;
            padding: 30px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .chart-container:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
            transform: translateY(-3px);
        }
        
        /* Chart header styling */
        .chart-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
        }
        
        .chart-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .chart-subtitle {
            font-size: 1rem;
            color: #64748b;
            margin: 0;
            font-weight: 500;
        }
        
        /* Chart container responsive */
        @media (max-width: 768px) {
            .chart-container {
                margin: 20px 0;
                padding: 20px;
                min-height: 400px;
            }
            
            .chart-title {
                font-size: 1.5rem;
            }
            
            .chart-subtitle {
                font-size: 0.9rem;
            }
        }
        
        /* Card body enhancements */
        .card-body {
            position: relative;
            z-index: 2;
            padding: 1.5rem;
        }
        
        .card-footer {
            position: relative;
            z-index: 2;
            background: rgba(0, 0, 0, 0.1) !important;
            border-top: none;
            padding: 1rem 1.5rem;
        }
        
        /* Icon animations */
        .fas {
            transition: transform 0.3s ease;
        }
        
        .card:hover .fas {
            transform: scale(1.1) rotate(5deg);
        }
        
        /* Staggered animation delays */
        .animated-card:nth-child(1) { animation-delay: 0.1s; }
        .animated-card:nth-child(2) { animation-delay: 0.2s; }
        .animated-card:nth-child(3) { animation-delay: 0.3s; }
        .animated-card:nth-child(4) { animation-delay: 0.4s; }
        .animated-card:nth-child(5) { animation-delay: 0.5s; }
    </style>
</head>

<body class="sb-nav-fixed">
    <?php include 'components/navbar.php'; ?>

    <div id="layoutSidenav">
        <?php include 'components/sidenav.php'; ?>
   

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid text-center">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <!-- KONTEN UNTUK ADMIN -->
                    <div class="dashboard-header animate-header ">
                        <h2 class="text-center mx-auto d-block">Dashboard Admin - Trend Absen 7 Hari Ke Depan</h2>
                        <div style="display: flex; justify-content: center;">
                            <span class="role-badge">Admin Access</span>
                        </div>
                    </div>
                                        
                    <!-- Menu Administrasi Khusus Admin -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light animated-card">
                                <div class="card-body">
                                    <h5 class="card-title">Menu Administrasi</h5>
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <a href="manage_users.php" class="btn btn-primary btn-block">
                                                <i class="fas fa-users"></i> Kelola Pengguna
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <a href="cetak_pdf.php" class="btn btn-success btn-block">
                                                <i class="fas fa-file-export"></i> Export Laporan
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <a href="scan.php" class="btn btn-info btn-block">
                                                <i class="fas fa-cogs"></i> Mode Alat
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
    <a href="datakaryawan.php" class="btn btn-secondary btn-block">
        <i class="fas fa-users"></i> Data Karyawan
    </a>
</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'security'): ?>
                    <!-- KONTEN UNTUK SECURITY -->
                    <div class="dashboard-header animate-header" style="background: linear-gradient(135deg, #FF9800 0%, #F44336 100%);">
                        <h2 class="text-center mx-auto d-block">Dashboard Security  - Personal Counting</h2>
                        <div style="display: flex; justify-content: center;">
                            <span class="role-badge">Security Access</span>
                        </div>
                    </div>
                    
                   
                    <?php else: ?>
                    <!-- Default Content (jika role tidak dikenali) -->
                    <h2 class="mt-4">Trend Absen 7 Hari Ke Depan</h2>
                    <?php endif; ?>

                    <!-- Cards - Konten yang sama untuk semua role -->
                    <div class="row">
                        <!-- Karyawan Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-success text-white mb-4 animated-card" data-card="karyawan">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="text-white-75 small">Karyawan Didalam</div>
                                            <div class="counter" id="total_masuk" data-target="<?= $karyawan_didalam ?>">
                                                <?= $karyawan_didalam ?>
                                            </div>
                                        </div>
                                        <div class="text-white-25">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="detail_dashboard_karyawan.php">Karyawan Di PT.Bekasi Power</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- Magang Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-danger text-white mb-4 animated-card" data-card="magang">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="text-white-75 small">Magang Didalam</div>
                                            <div class="counter" id="total_keluar" data-target="<?= $magang_didalam ?>">
                                                <?= $magang_didalam ?>
                                            </div>
                                        </div>
                                        <div class="text-white-25">
                                            <i class="fas fa-user-graduate fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="detail_dashboard_magang.php">Pemagang Di PT.Bekasi Power</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- Tamu Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-warning text-white mb-4 animated-card" data-card="tamu">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="text-white-75 small">Tamu Didalam</div>
                                            <div class="counter" id="total_keseluruhan" data-target="<?= $tamu_didalam ?>">
                                                <?= $tamu_didalam ?>
                                            </div>
                                        </div>
                                        <div class="text-white-25">
                                            <i class="fas fa-user-friends fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="detail_dashboard_tamu.php">Tamu Di PT.Bekasi Power</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- Total Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white mb-4 animated-card" data-card="total">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="text-white-75 small">Total Didalam</div>
                                            <div class="counter" id="total_semua" data-target="<?= $total_Didalam ?>">
                                                <?= $total_Didalam ?>
                                            </div>
                                        </div>
                                        <div class="text-white-25">
                                            <i class="fas fa-building fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="detail_dashboard_total.php">Total Orang Didalam</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modern Line Chart -->
                    <div class="chart-container animated-card">
                        <div class="chart-header">
                            <h3 class="chart-title">📊 Trend Kehadiran 7 Hari Ke Depan</h3>
                            <p class="chart-subtitle">Data real-time kehadiran 7 hari ke depan</p>
                        </div>
                        <div id="modernLineChart"></div>
                    </div>
                </div>
            </main>

            <?php include 'components/footer.php'; ?>

            <script>
                // Modern ApexCharts Implementation
                // Force cache busting for chart data
                var chartData = {
                    labels: <?= json_encode($labels) ?>,
                    karyawan: <?= json_encode($karyawan_didalam) ?>,
                    magang: <?= json_encode($magang_didalam) ?>,
                    tamu: <?= json_encode($tamu_didalam) ?>,
                    timestamp: <?= time() ?>
                };
                
                console.log('🚀 Modern Chart Data:', chartData);
                console.log('📊 Labels:', chartData.labels);
                console.log('👥 Karyawan:', chartData.karyawan);
                console.log('🎓 Magang:', chartData.magang);
                console.log('🏃 Tamu:', chartData.tamu);
                
                // Transform data for ApexCharts format
                var transformedData = {
                    karyawan: chartData.labels.map((label, index) => ({
                        x: label,
                        y: chartData.karyawan[index] || 0
                    })),
                    magang: chartData.labels.map((label, index) => ({
                        x: label,
                        y: chartData.magang[index] || 0
                    })),
                    tamu: chartData.labels.map((label, index) => ({
                        x: label,
                        y: chartData.tamu[index] || 0
                    }))
                };
                
                console.log('🔄 Transformed Data for ApexCharts:', transformedData);
                
                // ApexCharts Configuration
                var options = {
                    series: [{
                        name: '👥 Karyawan',
                        data: transformedData.karyawan,
                        color: '#22c55e'
                    }, {
                        name: '🎓 Magang',
                        data: transformedData.magang,
                        color: '#ef4444'
                    }, {
                        name: '🏃 Tamu',
                        data: transformedData.tamu,
                        color: '#eab308'
                    }],
                    chart: {
                        type: 'line',
                        height: 420,
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: false,
                                zoom: true,
                                zoomin: true,
                                zoomout: true,
                                pan: false,
                                reset: true
                            }
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                            animateGradually: {
                                enabled: true,
                                delay: 150
                            },
                            dynamicAnimation: {
                                enabled: true,
                                speed: 350
                            }
                        },
                        background: 'transparent',
                        dropShadow: {
                            enabled: true,
                            color: '#000',
                            top: 18,
                            left: 7,
                            blur: 10,
                            opacity: 0.1
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3,
                        lineCap: 'round'
                    },
                    grid: {
                        borderColor: '#e7e7e7',
                        strokeDashArray: 5,
                        xaxis: {
                            lines: {
                                show: false
                            }
                        },
                        yaxis: {
                            lines: {
                                show: true
                            }
                        },
                        padding: {
                            top: 0,
                            right: 30,
                            bottom: 0,
                            left: 20
                        }
                    },
                    markers: {
                        size: 6,
                        strokeColors: '#fff',
                        strokeWidth: 2,
                        hover: {
                            size: 8,
                            sizeOffset: 3
                        }
                    },
                    xaxis: {
                        type: 'category',
                        title: {
                            text: 'Tanggal',
                            style: {
                                fontSize: '14px',
                                fontWeight: 600,
                                color: '#374151'
                            }
                        },
                        labels: {
                            style: {
                                colors: '#6b7280',
                                fontSize: '12px',
                                fontWeight: 500
                            },
                            rotate: -45,
                            rotateAlways: false
                        },
                        axisBorder: {
                            show: true,
                            color: '#e5e7eb'
                        },
                        axisTicks: {
                            show: true,
                            color: '#e5e7eb'
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah Orang',
                            style: {
                                fontSize: '14px',
                                fontWeight: 600,
                                color: '#374151'
                            }
                        },
                        labels: {
                            style: {
                                colors: '#6b7280',
                                fontSize: '12px',
                                fontWeight: 500
                            },
                            formatter: function (val) {
                                return Math.floor(val);
                            }
                        },
                        min: 0
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'center',
                        floating: false,
                        fontSize: '14px',
                        fontWeight: 500,
                        offsetY: -10,
                        markers: {
                            width: 12,
                            height: 12,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            radius: 6
                        },
                        itemMargin: {
                            horizontal: 20,
                            vertical: 5
                        }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        theme: 'light',
                        style: {
                            fontSize: '12px'
                        },
                        x: {
                            format: 'dd MMM'
                        },
                        y: {
                            formatter: function (val, { series, seriesIndex, dataPointIndex, w }) {
                                return val + ' orang';
                            }
                        },
                        marker: {
                            show: true
                        },
                        custom: function({ series, seriesIndex, dataPointIndex, w }) {
                            const date = chartData.labels[dataPointIndex];
                            const karyawan = series[0][dataPointIndex];
                            const magang = series[1][dataPointIndex];
                            const tamu = series[2][dataPointIndex];
                            const total = karyawan + magang + tamu;
                            
                            return `
                                <div class="custom-tooltip" style="padding: 12px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                    <div style="font-weight: 600; margin-bottom: 8px; color: #1f2937;">${date}</div>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span style="width: 12px; height: 12px; background: #22c55e; border-radius: 50%; display: inline-block;"></span>
                                            <span style="color: #374151;">Karyawan: <strong>${karyawan}</strong></span>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span style="width: 12px; height: 12px; background: #ef4444; border-radius: 50%; display: inline-block;"></span>
                                            <span style="color: #374151;">Magang: <strong>${magang}</strong></span>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span style="width: 12px; height: 12px; background: #eab308; border-radius: 50%; display: inline-block;"></span>
                                            <span style="color: #374151;">Tamu: <strong>${tamu}</strong></span>
                                        </div>
                                        <hr style="margin: 8px 0; border: none; border-top: 1px solid #e5e7eb;">
                                        <div style="font-weight: 600; color: #1f2937;">Total: ${total} orang</div>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            shadeIntensity: 0.5,
                            gradientToColors: undefined,
                            inverseColors: true,
                            opacityFrom: 0.8,
                            opacityTo: 0.1,
                            stops: [0, 100]
                        }
                    },
                    responsive: [{
                        breakpoint: 768,
                        options: {
                            chart: {
                                height: 300
                            },
                            legend: {
                                position: 'bottom'
                            },
                            xaxis: {
                                labels: {
                                    rotate: -90
                                }
                            }
                        }
                    }]
                };

                // Render the chart
                var chart = new ApexCharts(document.querySelector("#modernLineChart"), options);
                chart.render();
                
                // Auto-refresh chart data every 30 seconds
                setInterval(function() {
                    fetch(window.location.href)
                        .then(response => response.text())
                        .then(html => {
                            // Extract new data from response (simplified approach)
                            console.log('🔄 Auto-refreshing chart data...');
                            // In production, you'd want to fetch JSON data from an API endpoint
                        })
                        .catch(error => console.error('❌ Error refreshing chart:', error));
                }, 30000);

                // Setup kartu reader interval
                setInterval(function () {
                    $("#cekkartu").load('bacakartu.php');
                }, 2000);
                
                // Enhanced animations and interactions
                document.addEventListener('DOMContentLoaded', function() {
                    // Intersection Observer for staggered card animations
                    const observerOptions = {
                        threshold: 0.1,
                        rootMargin: '0px 0px -50px 0px'
                    };
                    
                    const cardObserver = new IntersectionObserver((entries) => {
                        entries.forEach((entry, index) => {
                            if (entry.isIntersecting) {
                                setTimeout(() => {
                                    entry.target.classList.add('show');
                                    // Start counter animation after card appears
                                    const counter = entry.target.querySelector('.counter');
                                    if (counter) {
                                        animateCounter(counter);
                                    }
                                }, index * 150);
                            }
                        });
                    }, observerOptions);
                    
                    // Observe all animated cards
                    document.querySelectorAll('.animated-card').forEach(card => {
                        cardObserver.observe(card);
                    });
                    
                    // Counter animation function with NaN prevention and enhanced UX
                    function animateCounter(element) {
                        const target = parseInt(element.getAttribute('data-target')) || 0;
                        const currentValue = parseInt(element.textContent) || 0;
                        
                        // Skip animation if value is already correct
                        if (currentValue === target) {
                            return;
                        }
                        
                        // Handle zero values with special styling
                        if (target === 0) {
                            element.style.transition = 'all 0.3s ease';
                            element.textContent = '0';
                            element.style.color = 'rgba(255, 255, 255, 0.8)';
                            element.style.fontSize = '2.2rem';
                            return;
                        }
                        
                        // Enhanced animation for non-zero values
                        const duration = Math.min(500, Math.max(150, target * 12)); // Faster: 150-500ms
                        const step = target / (duration / 16);
                        let current = currentValue;
                        
                        // Add pulsing effect during animation
                        element.style.transition = 'all 0.2s ease';
                        element.style.color = '#ffffff';
                        element.style.fontSize = '2.8rem';
                        
                        const timer = setInterval(() => {
                            if (current < target) {
                                current = Math.min(current + step, target);
                            } else if (current > target) {
                                current = Math.max(current - step, target);
                            }
                            
                            element.textContent = Math.floor(current);
                            
                            if (current === target) {
                                element.textContent = target;
                                element.style.fontSize = '2.5rem';
                                element.style.textShadow = '0 0 15px rgba(255,255,255,0.6)';
                                clearInterval(timer);
                                
                                // Smooth reset
                                setTimeout(() => {
                                    element.style.textShadow = '0 2px 4px rgba(0, 0, 0, 0.3)';
                                }, 200);
                            }
                        }, 16);
                    }
                    
                    // Ripple effect for cards
                    document.querySelectorAll('.card').forEach(card => {
                        card.addEventListener('click', function(e) {
                            const ripple = document.createElement('span');
                            const rect = this.getBoundingClientRect();
                            const size = Math.max(rect.width, rect.height);
                            const x = e.clientX - rect.left - size / 2;
                            const y = e.clientY - rect.top - size / 2;
                            
                            ripple.style.width = ripple.style.height = size + 'px';
                            ripple.style.left = x + 'px';
                            ripple.style.top = y + 'px';
                            ripple.classList.add('card-ripple');
                            
                            this.appendChild(ripple);
                            
                            setTimeout(() => {
                                ripple.remove();
                            }, 600);
                        });
                    });
                    
                    // Enhanced button hover effects
                    document.querySelectorAll('.btn').forEach(btn => {
                        btn.addEventListener('mouseenter', function() {
                            this.style.transform = 'translateY(-2px) scale(1.05)';
                        });
                        
                        btn.addEventListener('mouseleave', function() {
                            this.style.transform = 'translateY(0) scale(1)';
                        });
                    });
                    
                    // Chart container animation
                    const chartContainer = document.querySelector('.chart-container');
                    if (chartContainer) {
                        const chartObserver = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    entry.target.style.opacity = '1';
                                    entry.target.style.transform = 'translateY(0)';
                                }
                            });
                        }, { threshold: 0.2 });
                        
                        chartContainer.style.opacity = '0';
                        chartContainer.style.transform = 'translateY(30px)';
                        chartContainer.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
                        chartObserver.observe(chartContainer);
                    }
                });
            </script>
        </div>
    </div>
    <div id="cekkartu"></div>
    <script src="js/scripts.js"></script>
    <script>
    // Hanya tampilkan jika status login_success ada
    <?php if(isset($_SESSION['login_success']) && $_SESSION['login_success']): ?>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Login Berhasil!',
            html: 'Selamat datang <b><?php echo $_SESSION['username']; ?></b><br>Anda login sebagai <b><?php echo isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'User'; ?></b>',
            icon: 'success',
            allowOutsideClick: false,
            confirmButtonText: 'OK',
            confirmButtonColor: '#28a745'
        });
    });
    <?php 
    // Hapus status login sukses
    unset($_SESSION['login_success']); 
    ?>
    <?php endif; ?>
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>