<?php
// koneksi.php - sudah di-include di bawah
$host = "localhost";
$user = "root";
$password = ""; 
$database = "absenrfid"; 

// Membuat koneksi
$conn = mysqli_connect($host, $user, $password, $database);

// Periksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
<?php
session_start();
include 'koneksi.php';

// Konfigurasi API Fonnte
$fonnte_token = "JruYUTLwwA2E8NxChzno";

// Menangani reset password via WhatsApp
if(isset($_POST['reset_password'])) {
    $karyawan_id = mysqli_real_escape_string($conn, $_POST['karyawan_id']);
    
    // Ambil data karyawan berdasarkan id
    $query_karyawan = "SELECT * FROM karyawan WHERE id = '$karyawan_id'";
    $result_karyawan = mysqli_query($conn, $query_karyawan);
    
    if(mysqli_num_rows($result_karyawan) === 1) {
        $karyawan = mysqli_fetch_assoc($result_karyawan);
        $nik = $karyawan['NIK'];
        $nama = $karyawan['nama'];
        $no_wa = $karyawan['no_wa'];
        
        // Cek apakah karyawan memiliki akun
        $query_user = "SELECT * FROM login WHERE nik = '$nik'";
        $result_user = mysqli_query($conn, $query_user);
        
        if(mysqli_num_rows($result_user) === 1) {
            $user = mysqli_fetch_assoc($result_user);
            $username = $user['username'];
            
            // Generate password baru (6 karakter)
            $new_password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6);
            
            // Update password di database
            $update_query = "UPDATE login SET password = '$new_password' WHERE nik = '$nik'";
            
            if(mysqli_query($conn, $update_query)) {
                // Format nomor WA (hilangkan 0 di awal dan tambahkan 62)
                if(substr($no_wa, 0, 1) == '0') {
                    $no_wa = '62' . substr($no_wa, 1);
                }
                
                // Buat pesan WhatsApp
                $pesan = "Halo $nama,\n\nBerikut adalah informasi login Anda untuk sistem Personal Counting EHS:\n\nUsername: $username\nPassword Baru: $new_password\n\nSilakan login dengan informasi tersebut.\n\nTerima kasih.";
                
                // Kirim pesan via API Fonnte
                $curl = curl_init();
                
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.fonnte.com/send',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array(
                        'target' => $no_wa,
                        'message' => $pesan
                    ),
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: ' . $fonnte_token
                    ),
                ));
                
                $response = curl_exec($curl);
                curl_close($curl);
                
                $success_message = "Reset password berhasil! Password baru telah dikirim ke WhatsApp $karyawan[no_wa]";
            } else {
                $error_message = "Gagal mereset password. Silakan coba lagi.";
            }
        } else {
            // Ubah pesan error sesuai permintaan
            $error_message = "Anda tidak bisa mengakses ini karena anda bukanlah administrator personal counting ataupun security, hubungi departmen EHS atau HR&GA untuk masalah ini";
        }
    } else {
        $error_message = "Data karyawan tidak ditemukan.";
    }
}

// Proses login biasa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM login WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role'];
        
        // Tambahkan status login sukses ke session
        $_SESSION['login_success'] = true;
        
        header('Location: index.php');
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="image/bp.png" type="image/x-icon">
    <link rel="stylesheet" href="assets_login/css/styles.css">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <title>Login - Personal Counting EHS</title>
    
    <style>
        /* Modal custom styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        
        .modal-title {
            font-size: 20px;
            font-weight: 600;
        }
        
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        
        /* Alert styling */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="l-form">
        <div class="shape1"></div>
        <div class="shape2"></div>

        <div class="form">
            <img src="assets_login/img/authentication.svg" alt="" class="form__img">

            <form method="POST" action="login.php" class="form__content">
                <h1 class="form__title">Personal Counting EHS </h1>

                <div class="form__div form__div-one">
                    <div class="form__icon">
                        <i class='bx bx-user-circle'></i>
                    </div>

                    <div class="form__div-input">
                        <label for="username" class="form__label">Username</label>
                        <input type="text" name="username" id="username" class="form__input" required>
                    </div>
                </div>

                <div class="form__div">
                    <div class="form__icon">
                        <i class='bx bx-lock'></i>
                    </div>

                    <div class="form__div-input">
                        <label for="password" class="form__label">Password</label>
                        <input type="password" name="password" id="password" class="form__input" required>
                    </div>
                </div>
                
                <?php if (!empty($error)): ?>
                    <p style="color: red; text-align: center; font-size: 14px;">
                        <?php echo $error; ?>
                    </p>
                <?php endif; ?>

                <a href="#" class="form__forgot" id="forgotPassword">Lupa Password?</a>

                <input type="submit" name="login" class="form__button" value="Login">
            </form>
        </div>
    </div>
    
    <!-- Modal Lupa Password -->
    <div id="forgotPasswordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-lock-open"></i> Lupa Password</h5>
                <span class="close">&times;</span>
            </div>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="karyawan_id">Pilih Nama Karyawan:</label>
                    <select class="form-control select2" id="karyawan_id" name="karyawan_id" required>
                        <option value="">-- Pilih Karyawan --</option>
                        <?php
                        $karyawan_query = "SELECT id, NIK, nama, departmen, no_wa FROM karyawan WHERE no_wa IS NOT NULL AND no_wa != '' ORDER BY nama ASC";
                        $karyawan_result = mysqli_query($conn, $karyawan_query);
                        
                        while ($karyawan = mysqli_fetch_assoc($karyawan_result)) {
                            echo "<option value='" . $karyawan['id'] . "'>" . $karyawan['nama'] . " - " . $karyawan['departmen'] . " (" . $karyawan['NIK'] . ")</option>";
                        }
                        ?>
                    </select>
                </div>
                <p class="text-muted small">Password baru akan dikirim ke nomor WhatsApp yang terdaftar.</p>
                <button type="submit" name="reset_password" class="btn btn-primary btn-block">Reset Password</button>
            </form>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets_login/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Get the modal
        var modal = document.getElementById("forgotPasswordModal");

        // Get the button that opens the modal
        var btn = document.getElementById("forgotPassword");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        
        // Auto close alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.getElementsByClassName('alert');
            for (var i=0; i<alerts.length; i++) {
                alerts[i].style.display = 'none';
            }
        }, 5000);
    </script>
    <script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Pilih Karyawan --",
            width: '100%'
        });

        // Modal show/hide logic
        var modal = document.getElementById("forgotPasswordModal");
        var btn = document.getElementById("forgotPassword");
        var span = document.getElementsByClassName("close")[0];

        btn.onclick = function () {
            modal.style.display = "block";
        };

        span.onclick = function () {
            modal.style.display = "none";
        };

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };
    });
</script>
<script>
    <?php if (isset($success_message)) : ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?php echo $success_message; ?>',
            confirmButtonColor: '#3085d6',
        });
    <?php endif; ?>

    <?php if (isset($error_message)) : ?>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?php echo $error_message; ?>',
            confirmButtonColor: '#d33',
        });
    <?php endif; ?>
</script>

<?php
if (isset($_SESSION['logout_success']) && $_SESSION['logout_success']) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Anda telah berhasil logout',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        });
    </script>";
    unset($_SESSION['logout_success']);
}
?>


</body>
</html>