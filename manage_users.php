<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

include "koneksi.php"; // Koneksi ke database

// Perlu menambahkan kolom nik ke tabel login jika belum ada
$checkNikColumn = mysqli_query($konek, "SHOW COLUMNS FROM login LIKE 'nik'");
if (mysqli_num_rows($checkNikColumn) == 0) {
    mysqli_query($konek, "ALTER TABLE login ADD COLUMN nik varchar(255) DEFAULT NULL");
}

// Handle form submission untuk tambah user baru
if (isset($_POST['tambah_user'])) {
    $nik = mysqli_real_escape_string($konek, $_POST['nik']);
    $username = mysqli_real_escape_string($konek, $_POST['username']);
    $password = mysqli_real_escape_string($konek, $_POST['password']);
    $role = mysqli_real_escape_string($konek, $_POST['role']);
    
    // Cek apakah NIK ada di data karyawan
    $checkNIK = mysqli_query($konek, "SELECT * FROM karyawan WHERE NIK = '$nik'");
    
    if (mysqli_num_rows($checkNIK) > 0) {
        // NIK valid, cek apakah username sudah ada
        $checkUsername = mysqli_query($konek, "SELECT * FROM login WHERE username = '$username'");
        
        if (mysqli_num_rows($checkUsername) == 0) {
            // Username belum digunakan, bisa ditambahkan
            $query = "INSERT INTO login (username, password, role, nik) VALUES ('$username', '$password', '$role', '$nik')";
            $result = mysqli_query($konek, $query);
            
            if ($result) {
                $success_message = "User berhasil ditambahkan!";
            } else {
                $error_message = "Gagal menambahkan user: " . mysqli_error($konek);
            }
        } else {
            $error_message = "Username sudah digunakan. Silakan pilih username lain.";
        }
    } else {
        $error_message = "NIK tidak ditemukan di data karyawan. User harus terdaftar sebagai karyawan terlebih dahulu.";
    }
}

// Handle hapus user
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($konek, $_GET['delete_id']);
    
    // Jangan hapus user yang sedang login
    if ($_SESSION['username'] != $delete_id) {
        $delete_query = "DELETE FROM login WHERE username = '$delete_id'";
        $delete_result = mysqli_query($konek, $delete_query);
        
        if ($delete_result) {
            $success_message = "User berhasil dihapus!";
        } else {
            $error_message = "Gagal menghapus user: " . mysqli_error($konek);
        }
    } else {
        $error_message = "Anda tidak dapat menghapus akun yang sedang digunakan.";
    }
}

// Ambil daftar user
$user_query = "SELECT l.*, k.nama, k.departmen 
               FROM login l 
               LEFT JOIN karyawan k ON l.nik = k.NIK 
               ORDER BY l.role ASC, l.username ASC";
$user_result = mysqli_query($konek, $user_query);

// Ambil daftar karyawan untuk dropdown
$karyawan_query = "SELECT NIK, nama, departmen FROM karyawan WHERE NIK IS NOT NULL AND NIK != '' ORDER BY nama ASC";
$karyawan_result = mysqli_query($konek, $karyawan_query);
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
    <title>Kelola Pengguna - SISTEM EHS</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Add this for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        .dashboard-header {
            position: relative;
            margin: 20px auto 30px;
            padding: 20px 25px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3a1c71 0%, #d76d77 50%, #ffaf7b 100%);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            color: white;
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        
        .dashboard-header.show {
            opacity: 1;
            transform: translateY(0);
        }

        .dashboard-header h2 {
            margin: 0;
            font-size: 1.8rem;
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

        .dashboard-header .badge {
            display: inline-block;
            margin-left: 15px;
            padding: 5px 12px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        @keyframes glow {
            0% {
                filter: drop-shadow(0 0 2px rgba(255, 255, 255, 0.7));
            }
            50% {
                filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.9));
            }
            100% {
                filter: drop-shadow(0 0 2px rgba(255, 255, 255, 0.7));
            }
        }

        .glow-icon {
            animation: glow 2s infinite;
        }
        
        table tbody tr:hover {
            background-color: #f2f2f2;
        }
        
        .badge-admin {
            background-color: #28a745;
        }
        
        .badge-security {
            background-color: #ffc107;
            color: #212529;
        }
        
        .modal-header {
            background-color: #343a40;
            color: white;
        }
        
        .user-info-preview {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        
        /* Modern Action Buttons Styles */
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
        
        .btn-reset {
            background: linear-gradient(135deg, #56CCF2 0%, #2F80ED 100%);
            color: white;
        }
        
        .btn-reset:hover {
            background: linear-gradient(135deg, #2FBCF2 0%, #1F70DD 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(47, 128, 237, 0.3);
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
        
        .alert {
            animation: slideInDown 0.5s forwards;
        }
        
        @keyframes slideInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
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
                    <div class="dashboard-header" id="animatedHeader">
                        <h2>
                            <i class="fas fa-users-cog icon glow-icon"></i>
                            Kelola Pengguna
                            <span class="badge">Admin Panel</span>
                        </h2>
                    </div>
                    
                    <!-- Alert Messages -->
                    <?php if(isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php endif; ?>

                    <?php if(isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error_message; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php endif; ?>

                    <!-- Button to trigger modal -->
                    <button type="button" class="btn btn-primary mb-3 animated-card" id="addUserBtn" data-toggle="modal" data-target="#addUserModal">
                        <i class="fas fa-user-plus"></i> Tambah Pengguna Baru
                    </button>

                    <!-- Tabel Daftar User -->
                    <div class="card mb-4 animated-card" id="userCard">
                        <div class="card-header">
                            <i class="fas fa-users mr-1"></i>
                            Daftar Pengguna
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="userTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Username</th>
                                            <th>Role</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Departemen</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        $user_result = mysqli_query($konek, $user_query); // Re-query untuk refresh data
                                        while ($user = mysqli_fetch_assoc($user_result)): 
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $user['username']; ?></td>
                                            <td>
                                                <span class="badge <?= ($user['role'] == 'admin') ? 'badge-admin' : 'badge-security'; ?> p-2">
                                                    <?= ucfirst($user['role']); ?>
                                                </span>
                                            </td>
                                            <td><?= $user['nik'] ?? '-'; ?></td>
                                            <td><?= $user['nama'] ?? '-'; ?></td>
                                            <td><?= $user['departmen'] ?? '-'; ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn btn-action btn-delete" onclick="konfirmasiHapus('<?= $user['username']; ?>')">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                    <button class="btn btn-action btn-reset" onclick="resetPassword('<?= $user['username']; ?>')">
                                                        <i class="fas fa-key"></i> Reset
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
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

    <!-- Modal Tambah User -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel"><i class="fas fa-user-plus"></i> Tambah Pengguna Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="nik">Pilih Karyawan (NIK)</label>
                                <select name="nik" id="nik" class="form-control" required onchange="updatePreview(this)">
                                    <option value="">-- Pilih Karyawan --</option>
                                    <?php 
                                    $karyawan_result = mysqli_query($konek, $karyawan_query); // Re-query untuk refresh data
                                    while($karyawan = mysqli_fetch_assoc($karyawan_result)): 
                                    ?>
                                        <option 
                                            value="<?= $karyawan['NIK']; ?>" 
                                            data-nama="<?= htmlspecialchars($karyawan['nama']); ?>" 
                                            data-departmen="<?= htmlspecialchars($karyawan['departmen']); ?>"
                                        >
                                            <?= $karyawan['NIK']; ?> - <?= $karyawan['nama']; ?> (<?= $karyawan['departmen']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="role">Role</label>
                                <select name="role" id="role" class="form-control" required>
                                    <option value="admin">Admin</option>
                                    <option value="security">Security</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="user-info-preview">
                            <h6>Preview Informasi Karyawan:</h6>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nama Karyawan</label>
                                    <input type="text" class="form-control" id="nama_preview" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Departemen</label>
                                    <input type="text" class="form-control" id="departmen_preview" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_user" class="btn btn-primary">Tambah User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>

    <script>
        // Fungsi untuk mengupdate preview karyawan
        function updatePreview(selectElement) {
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var nama = selectedOption.getAttribute('data-nama') || '';
            var departmen = selectedOption.getAttribute('data-departmen') || '';
            
            document.getElementById('nama_preview').value = nama;
            document.getElementById('departmen_preview').value = departmen;
            
            // Set username default berdasarkan nama
            if (nama) {
                var username = nama.toLowerCase().replace(/\s+/g, '.');
                document.getElementById('username').value = username;
            }
            
            console.log("Updated preview - Nama: " + nama + ", Departmen: " + departmen);
        }
        
        $(document).ready(function() {
            // Initialize DataTable
            $('#userTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true
            });
            
            // Tambahan event handler jQuery (untuk kompatibilitas)
            $('#nik').on('change', function() {
                updatePreview(this);
            });
            
            // Menampilkan modal jika ada error
            <?php if(isset($error_message) && (strpos($error_message, 'NIK') !== false || strpos($error_message, 'Username') !== false)): ?>
                $('#addUserModal').modal('show');
            <?php endif; ?>
            
            // Animasi elemen saat halaman dimuat
            setTimeout(function() {
                document.getElementById('animatedHeader').classList.add('show');
            }, 100);
            
            setTimeout(function() {
                document.getElementById('addUserBtn').classList.add('show');
            }, 200);
            
            setTimeout(function() {
                document.getElementById('userCard').classList.add('show');
            }, 300);
        });

        // Konfirmasi hapus user dengan desain modern
        function konfirmasiHapus(username) {
            Swal.fire({
                title: 'Hapus Pengguna',
                text: 'Apakah Anda yakin ingin menghapus "' + username + '"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff5f6d',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> Ya, Hapus!',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                background: '#fff',
                borderRadius: '15px',
                iconColor: '#ff5f6d',
                customClass: {
                    confirmButton: 'btn btn-lg',
                    cancelButton: 'btn btn-lg'
                },
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'manage_users.php?delete_id=' + username;
                }
            });
        }

        // Reset password dengan desain modern
        function resetPassword(username) {
            Swal.fire({
                title: 'Reset Password',
                html: '<div class="text-center mb-4"><i class="fas fa-key fa-2x text-info mb-3"></i><p>Masukkan password baru untuk pengguna <strong>"' + username + '"</strong></p></div>',
                input: 'password',
                inputPlaceholder: 'Password baru',
                inputAttributes: {
                    autocapitalize: 'off',
                    required: true,
                    minlength: 6
                },
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-sync-alt"></i> Reset',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                background: '#fff',
                borderRadius: '15px',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                },
                preConfirm: (password) => {
                    if (password.length < 6) {
                        Swal.showValidationMessage('Password minimal 6 karakter!')
                        return false;
                    }
                    return fetch('reset_password.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            return data;
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error.message}`)
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Password berhasil diubah',
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            });
        }
    </script>
</body>
</html>