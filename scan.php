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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"
        crossorigin="anonymous"></script>

    <!-- Menggunakan versi jQuery penuh -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Style khusus untuk status scanner */
        #cekkartu {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .scan-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            overflow: hidden;
        }
        
        .scan-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .scan-body {
            padding: 1.5rem;
        }
        
        .scan-status {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 0.5rem;
        }
        
        .status-online {
            background-color: #1cc88a;
            color: white;
        }
        
        .status-offline {
            background-color: #e74a3b;
            color: white;
        }
        
        .status-waiting {
            background-color: #f6c23e;
            color: white;
        }
        
        .scan-info {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f8f9fc;
            border-radius: 8px;
        }
        
        .scan-icon {
            font-size: 2rem;
            margin-right: 0.5rem;
        }
        
        .pulse {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #1cc88a;
            margin-right: 6px;
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(0.9);
                box-shadow: 0 0 0 0 rgba(28, 200, 138, 0.7);
            }
            
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(28, 200, 138, 0);
            }
            
            100% {
                transform: scale(0.9);
                box-shadow: 0 0 0 0 rgba(28, 200, 138, 0);
            }
        }
        
        .refresh-btn {
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .refresh-btn:hover {
            background-color: #2e59d9;
            transform: translateY(-2px);
        }
        
        /* Animasi loading */
        .loader-dots {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100px;
        }
        
        .loader-dots div {
            width: 12px;
            height: 12px;
            background-color: #4e73df;
            border-radius: 50%;
            margin: 0 5px;
            animation: bounce 1.4s infinite ease-in-out both;
        }
        
        .loader-dots div:nth-child(1) {
            animation-delay: -0.32s;
        }
        
        .loader-dots div:nth-child(2) {
            animation-delay: -0.16s;
        }
        
        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0);
            }
            40% {
                transform: scale(1);
            }
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function () {
            setInterval(function () {
                $("#cekkartu").load('bacakartu_2.php');
            }, 2000);
            
            // Tambahan fungsi untuk tombol refresh manual
            $(document).on('click', '#refresh-scanner', function() {
                $("#cekkartu").html(`
                    <div class="text-center">
                        <div class="loader-dots">
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                        <p class="text-muted">Memuat status scanner...</p>
                    </div>
                `);
                
                $("#cekkartu").load('bacakartu_2.php');
            });
        });
    </script>
</head>

<body class="sb-nav-fixed">
    <?php include 'components/navbar.php'; ?>

    <div id="layoutSidenav">
        <?php include 'components/sidenav.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <!-- isi -->
                <div class="container-fluid" style="padding-top: 20px">
                    <div class="card scan-card">
                        <div class="scan-header">
                            <div>
                                <i class="fas fa-wifi scan-icon"></i>
                                Status Scanner
                            </div>
                            <button id="refresh-scanner" class="refresh-btn">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                        <div class="scan-body">
                            <div id="cekkartu">
                                <div class="text-center">
                                    <div class="loader-dots">
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                    </div>
                                    <p class="text-muted">Memuat status scanner...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            </main>
            <!-- Footer -->
            <?php include 'components/footer.php'; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>

</html>