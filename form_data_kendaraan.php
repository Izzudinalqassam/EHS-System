<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

error_reporting(0);
include "koneksi.php";
date_default_timezone_set('Asia/Jakarta');

$status_message = '';

if (isset($_POST['btnSimpan'])) {
    $petugas = $_POST['petugas'];
    $nama = $_POST['jenis_input'] === 'database' ? $_POST['nama_database'] : $_POST['nama_manual'];
    $jenis_kendaraan = $_POST['jenis_kendaraan'];
    $nopol = $_POST['nopol'];
    $jam_masuk = date("H:i:s");
    $jam_keluar = "00:00:00";
    $tanggal_input = date("Y-m-d");

    if (!empty($nama)) {
        $simpan = mysqli_query($konek, "INSERT INTO kendaraan(petugas, nama, jenis_kendaraan, nopol, jam_masuk, jam_keluar, tanggal_input) 
                                       VALUES('$petugas', '$nama', '$jenis_kendaraan', '$nopol', '$jam_masuk', '$jam_keluar', '$tanggal_input')");

        if ($simpan) {
            $status_message = 'Tersimpan';
        } else {
            $status_message = 'Gagal Tersimpan';
        }
    } else {
        $status_message = 'Nama harus diisi';
    }
}
?>
 <script type="text/javascript">
        $(document).ready(function () {
            setInterval(function () {
                $("#cekkartu").load('bacakartu.php');
            }, 2000);
        });
    </script>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link rel="icon" href="image/bp.png" type="image/x-icon">
    <title>Input Data Kendaraan - EHS System</title>
    
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <style>
        body {
            background-image: url('image/inibackground.webp');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .custom-radio .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #28a745;
            border-color: #28a745;
        }

        .select2-container--bootstrap4 .select2-selection {
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">
            <img src="image/logo bp.png" alt="Logo Perusahaan" style="height: 40px; width: auto; margin-right: 10px;">
            SISTEM EHS
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ml-auto ml-md-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Layout Sidenav -->
    <div id="layoutSidenav">
        <?php include 'components\sidenav.php'; ?>

        <!-- Main Content -->
        <div id="layoutSidenav_content">
            <div class="container-fluid mt-4">
                <form method="POST" class="p-4 rounded shadow-sm" 
                      style="background: rgba(0,0,0,0.3); max-width: 600px; margin: auto; color: #fff; backdrop-filter: blur(5px);">
                    <h4 class="mb-4">Input Data Kendaraan</h4>

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

                    <!-- Jenis Input Selection -->
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
                            <select name="nama_database" id="nama_database" class="form-control" required>
                                <option value="" disabled selected>Pilih Nama</option>
                                <?php
                                $query = "SELECT nama, nopol FROM karyawan WHERE departmen IS NOT NULL AND departmen != ''";
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

                    <button type="submit" name="btnSimpan" class="btn btn-success btn-block mt-4">
                        <i class="fas fa-save"></i> Simpan
                    </button>

                    <p style="font-style: italic; text-align: center;" class="mt-3">
                        Note: Setelah Klik Simpan, Pastikan Tunggu Hingga Berhasil Tersimpan Dan Pastikan Internet Bagus.
                    </p>
                </form>
            </div>

            <!-- Footer -->
            <?php include 'components\footer.php'; ?>
        </div>
    </div>

    <script src="js/scripts.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#nama_database').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Nama'
            });

            // Initialize Select2 for petugas
            $('#petugas').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Petugas'
            });

            // Handle change event pada select nama
            $('#nama_database').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var nopol = selectedOption.data('nopol');
                if ($('input[name="jenis_input"]:checked').val() === 'database') {
                    $('#nopol').val(nopol);
                }
            });

            // Load last selected petugas
            const lastPetugas = localStorage.getItem('lastPetugas');
            if (lastPetugas) {
                $('#petugas').val(lastPetugas).trigger('change');
            }

            // Save selected petugas
            $('#petugas').on('change', function() {
                localStorage.setItem('lastPetugas', $(this).val());
            });
        });

        // Function to toggle between input methods
        function toggleNamaInput(type) {
            if (type === 'database') {
                $('#nama_database_div').show();
                $('#nama_manual_div').hide();
                $('#nama_database').prop('required', true);
                $('#nama_manual').prop('required', false);
                // Auto-fill nopol if name is selected
                var selectedOption = $('#nama_database').find('option:selected');
                var nopol = selectedOption.data('nopol');
                $('#nopol').val(nopol);
            } else {
                $('#nama_database_div').hide();
                $('#nama_manual_div').show();
                $('#nama_database').prop('required', false);
                $('#nama_manual').prop('required', true);
                // Clear nopol input for manual entry
                $('#nopol').val('');
            }
        }

        // SweetAlert untuk status message
        <?php if ($status_message): ?>
            Swal.fire({
                icon: '<?php echo $status_message === "Tersimpan" ? "success" : "error"; ?>',
                title: '<?php echo $status_message; ?>',
                showConfirmButton: true,
            }).then(function() {
                if ('<?php echo $status_message; ?>' === 'Tersimpan') {
                    // Reset form kecuali petugas
                    const currentPetugas = $('#petugas').val();
                    $('form')[0].reset();
                    $('#petugas').val(currentPetugas).trigger('change');
                    $('#nama_database').val(null).trigger('change');
                    // Reset radio button to database
                    $('#pilih_database').prop('checked', true);
                    toggleNamaInput('database');
                }
            });
        <?php endif; ?>
    </script>
</body>
<div id="cekkartu"></div>
</html>