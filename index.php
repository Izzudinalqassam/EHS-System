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
    <title>Dashboard Chart</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="icon" href="image/bp.png" type="image/x-icon">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
        crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            height: 40vh;
            width: 100%;
            margin: 20px 0;
            padding: 25px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .chart-container:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
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
                        <h2 class="text-center mx-auto d-block">Dashboard Admin - Traffic Absen Mingguan</h2>
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
                    <h2 class="mt-4">Traffic Absen Mingguan</h2>
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
                                                0
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
                                                0
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
                                                0
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
                                                0
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

                    <!-- Line Chart -->
                    <div class="chart-container animated-card">
                        <canvas id="myLineChart"></canvas>
                    </div>
                </div>
            </main>

            <?php include 'components/footer.php'; ?>

            <script>
                // Line Chart
                var ctx = document.getElementById('myLineChart').getContext('2d');
                var gradientKaryawan = ctx.createLinearGradient(0, 0, 0, 400);
                gradientKaryawan.addColorStop(0, 'rgba(34, 197, 94, 0.2)');
                gradientKaryawan.addColorStop(1, 'rgba(34, 197, 94, 0)');

                var gradientMagang = ctx.createLinearGradient(0, 0, 0, 400);
                gradientMagang.addColorStop(0, 'rgba(239, 68, 68, 0.2)');
                gradientMagang.addColorStop(1, 'rgba(239, 68, 68, 0)');

                var gradientTamu = ctx.createLinearGradient(0, 0, 0, 400);
                gradientTamu.addColorStop(0, 'rgba(234, 179, 8, 0.2)');
                gradientTamu.addColorStop(1, 'rgba(234, 179, 8, 0)');

                var myLineChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?= json_encode($labels) ?>,
                        datasets: [{
                            label: 'Karyawan',
                            data: <?= json_encode($karyawan_didalam) ?>,
                            borderColor: '#22c55e',
                            backgroundColor: gradientKaryawan,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4
                        }, {
                            label: 'Magang',
                            data: <?= json_encode($magang_didalam) ?>,
                            borderColor: '#ef4444',
                            backgroundColor: gradientMagang,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4
                        }, {
                            label: 'Tamu',
                            data: <?= json_encode($tamu_didalam) ?>,
                            borderColor: '#eab308',
                            backgroundColor: gradientTamu,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Trend Kehadiran Mingguan',
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                },
                                padding: {
                                    top: 10,
                                    bottom: 30
                                }
                            },
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#1f2937',
                                titleFont: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                bodyColor: '#4b5563',
                                bodyFont: {
                                    size: 12
                                },
                                padding: 12,
                                borderColor: '#e5e7eb',
                                borderWidth: 1,
                                displayColors: true
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    borderDash: [2, 2],
                                    color: '#e5e7eb'
                                },
                                ticks: {
                                    stepSize: 1,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });

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
                    
                    // Counter animation function
                    function animateCounter(element) {
                        const target = parseInt(element.getAttribute('data-target'));
                        const duration = 2000;
                        const step = target / (duration / 16);
                        let current = 0;
                        
                        const timer = setInterval(() => {
                            current += step;
                            if (current >= target) {
                                element.textContent = target;
                                clearInterval(timer);
                            } else {
                                element.textContent = Math.floor(current);
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