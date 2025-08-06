<?php
/**
 * Helper Log Aktivitas
 * 
 * File ini menyediakan fungsi-fungsi pembantu untuk pencatatan aktivitas sistem.
 * Sertakan file ini di file PHP lain yang memerlukan pencatatan aktivitas.
 */

// Pastikan koneksi database tersedia sebelum menggunakan fungsi-fungsi di file ini
if (!function_exists('get_client_ip')) {
    /**
     * Mendapatkan alamat IP klien
     * 
     * @return string Alamat IP klien
     */
    function get_client_ip() {
        $ipaddress = '';
        
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
            
        return $ipaddress;
    }
}

/**
 * Mencatat aktivitas sistem
 * 
 * @param string $activity_type Jenis aktivitas (login, logout, attendance, visitor, vehicle, user, password, system)
 * @param string $activity_description Deskripsi singkat tentang aktivitas
 * @param string $additional_details Informasi rinci opsional tentang aktivitas
 * @return boolean True jika log berhasil dicatat, false jika gagal
 */
function log_activity($activity_type, $activity_description, $additional_details = null) {
    global $konek;
    
    // Dapatkan informasi pengguna saat ini dari sesi
    $user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'tamu';
    $user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
    
    // Dapatkan alamat IP klien
    $ip_address = get_client_ip();
    
    // Validasi jenis aktivitas
    $valid_types = ['login', 'logout', 'attendance', 'visitor', 'vehicle', 'user', 'password', 'system'];
    if (!in_array($activity_type, $valid_types)) {
        $activity_type = 'system';
    }
    
    // Siapkan query SQL
    $stmt = mysqli_prepare(
        $konek, 
        "INSERT INTO activity_logs (user_name, user_role, ip_address, activity_type, activity_description, additional_details) 
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    
    mysqli_stmt_bind_param(
        $stmt, 
        "ssssss", 
        $user_name, 
        $user_role, 
        $ip_address, 
        $activity_type, 
        $activity_description, 
        $additional_details
    );
    
    // Jalankan query
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $result;
}

/**
 * Mencatat aktivitas login
 * 
 * @param string $username Username yang mencoba login
 * @param string $role Peran pengguna
 * @param boolean $success Apakah login berhasil
 * @return boolean True jika log berhasil dicatat
 */
function log_login($username, $role, $success = true) {
    $description = $success ? "Pengguna berhasil login" : "Percobaan login gagal";
    $details = $success ? "Peran: $role" : null;
    
    return log_activity('login', $description, $details);
}

/**
 * Mencatat aktivitas logout
 * 
 * @return boolean True jika log berhasil dicatat
 */
function log_logout() {
    return log_activity('logout', "Pengguna logout");
}

/**
 * Mencatat aktivitas absensi
 * 
 * @param string $employee_name Nama karyawan
 * @param string $action Tindakan absensi (masuk, keluar)
 * @param string $time Waktu tindakan
 * @return boolean True jika log berhasil dicatat
 */
function log_attendance($employee_name, $action, $time) {
    $description = "Absensi manual telah dicatat";
    $details = "Karyawan: $employee_name, Aksi: $action, Waktu: $time";
    
    return log_activity('attendance', $description, $details);
}

/**
 * Mencatat aktivitas tamu
 * 
 * @param string $action Tindakan tamu (masuk, keluar)
 * @param string $visitor_name Nama tamu
 * @param array $details Detail tambahan tamu
 * @return boolean True jika log berhasil dicatat
 */
function log_visitor($action, $visitor_name, $details = []) {
    $description = "";
    $additional_details = "";
    
    switch ($action) {
        case 'masuk':
            $description = "Tamu telah masuk";
            $company = isset($details['company']) ? $details['company'] : '-';
            $contact = isset($details['contact']) ? $details['contact'] : '-';
            $guests = isset($details['guests']) ? $details['guests'] : 1;
            $additional_details = "Tamu: $visitor_name, Perusahaan: $company, Bertemu: $contact, Jumlah Tamu: $guests";
            break;
            
        case 'keluar':
            $description = "Tamu telah keluar";
            $duration = isset($details['duration']) ? $details['duration'] : '-';
            $additional_details = "Tamu: $visitor_name, Durasi: $duration";
            break;
            
        case 'daftar':
            $description = "Tamu baru terdaftar";
            $company = isset($details['company']) ? $details['company'] : '-';
            $contact = isset($details['contact']) ? $details['contact'] : '-';
            $guests = isset($details['guests']) ? $details['guests'] : 1;
            $additional_details = "Tamu: $visitor_name, Perusahaan: $company, Bertemu: $contact, Jumlah Tamu: $guests";
            break;
            
        default:
            $description = "Aktivitas tamu dicatat";
            $additional_details = "Tamu: $visitor_name";
            break;
    }
    
    return log_activity('visitor', $description, $additional_details);
}

/**
 * Mencatat aktivitas kendaraan
 * 
 * @param string $action Tindakan kendaraan (masuk, keluar)
 * @param string $owner_name Nama pemilik
 * @param array $details Detail tambahan kendaraan
 * @return boolean True jika log berhasil dicatat
 */
function log_vehicle($action, $owner_name, $details = []) {
    $description = "";
    $additional_details = "";
    
    $vehicle_type = isset($details['type']) ? $details['type'] : '-';
    $license = isset($details['license']) ? $details['license'] : '-';
    
    switch ($action) {
        case 'masuk':
            $description = "Kendaraan masuk tercatat";
            $additional_details = "Pemilik: $owner_name, Kendaraan: $vehicle_type, Nomor Polisi: $license";
            break;
            
        case 'keluar':
            $description = "Kendaraan keluar tercatat";
            $duration = isset($details['duration']) ? $details['duration'] : '-';
            $additional_details = "Pemilik: $owner_name, Kendaraan: $vehicle_type, Nomor Polisi: $license, Durasi: $duration";
            break;
            
        default:
            $description = "Aktivitas kendaraan dicatat";
            $additional_details = "Pemilik: $owner_name, Kendaraan: $vehicle_type, Nomor Polisi: $license";
            break;
    }
    
    return log_activity('vehicle', $description, $additional_details);
}

/**
 * Mencatat aktivitas pengguna/akun
 * 
 * @param string $action Tindakan pada akun (tambah, edit, hapus)
 * @param string $target_username Username yang dipengaruhi
 * @param array $details Detail tambahan perubahan
 * @return boolean True jika log berhasil dicatat
 */
function log_user_activity($action, $target_username, $details = []) {
    $description = "";
    $additional_details = "";
    
    switch ($action) {
        case 'tambah':
            $description = "Akun pengguna baru dibuat";
            $role = isset($details['role']) ? $details['role'] : '-';
            $additional_details = "Username: $target_username, Peran: $role";
            break;
            
        case 'edit':
            $description = "Akun pengguna diperbarui";
            $role = isset($details['role']) ? $details['role'] : '-';
            $fields = isset($details['fields']) ? $details['fields'] : '-';
            $additional_details = "Perbarui pengguna: $target_username, Peran: $role, Field yang diubah: $fields";
            break;
            
        case 'hapus':
            $description = "Akun pengguna dihapus";
            $additional_details = "Username: $target_username";
            break;
            
        default:
            $description = "Aktivitas pada akun pengguna";
            $additional_details = "Pengguna: $target_username";
            break;
    }
    
    return log_activity('user', $description, $additional_details);
}

/**
 * Mencatat aktivitas terkait password
 * 
 * @param string $action Tindakan password (reset, ubah)
 * @param string $username Username yang terlibat
 * @param array $details Detail tambahan
 * @return boolean True jika log berhasil dicatat
 */
function log_password_activity($action, $username, $details = []) {
    $description = "";
    $additional_details = "";
    
    switch ($action) {
        case 'reset':
            $description = "Permintaan reset password";
            $method = isset($details['method']) ? $details['method'] : 'email';
            $additional_details = "Link reset dikirimkan ke " . $method . " terdaftar";
            break;
            
        case 'ubah':
            $description = "Password berhasil diubah";
            $initiator = isset($details['initiator']) ? $details['initiator'] : 'pengguna';
            if ($initiator != 'pengguna') {
                $additional_details = "Password diubah oleh: $initiator";
            }
            break;
            
        case 'gagal':
            $description = "Upaya pengubahan password gagal";
            $reason = isset($details['reason']) ? $details['reason'] : 'alasan tidak diketahui';
            $additional_details = "Alasan: $reason";
            break;
            
        default:
            $description = "Aktivitas terkait password";
            break;
    }
    
    return log_activity('password', $description, $additional_details);
}

/**
 * Mencatat aktivitas sistem
 * 
 * @param string $action Tindakan sistem
 * @param array $details Detail tambahan
 * @return boolean True jika log berhasil dicatat
 */
function log_system_activity($action, $details = []) {
    $description = "";
    $additional_details = "";
    
    switch ($action) {
        case 'backup':
            $description = "Backup sistem selesai";
            $file = isset($details['file']) ? $details['file'] : '-';
            $size = isset($details['size']) ? $details['size'] : '-';
            $additional_details = "File backup: $file, Ukuran: $size";
            break;
            
        case 'update':
            $description = "Pengaturan sistem diperbarui";
            $settings = isset($details['settings']) ? $details['settings'] : '-';
            $additional_details = "Pengaturan yang diubah: $settings";
            break;
            
        case 'error':
            $description = "Kesalahan sistem terdeteksi";
            $error = isset($details['error']) ? $details['error'] : '-';
            $additional_details = "Detail kesalahan: $error";
            break;
            
        default:
            $description = "Aktivitas sistem";
            if (!empty($details)) {
                $additional_details = "Detail: " . json_encode($details);
            }
            break;
    }
    
    return log_activity('system', $description, $additional_details);
}

/**
 * Format durasi waktu menjadi bentuk yang mudah dibaca
 * 
 * @param int $seconds Jumlah detik
 * @return string Durasi dalam format mudah dibaca
 */
function format_duration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    
    $formatted = "";
    if ($hours > 0) {
        $formatted .= $hours . " jam ";
    }
    if ($minutes > 0 || $hours == 0) {
        $formatted .= $minutes . " menit";
    }
    
    return trim($formatted);
}

/**
 * Mendapatkan log aktivitas terbaru
 * 
 * @param int $limit Jumlah log yang akan diambil
 * @param string $type Jenis aktivitas, gunakan 'all' untuk semua jenis
 * @return array Array dari baris log
 */
function get_recent_logs($limit = 10, $type = 'all') {
    global $konek;
    
    $query = "SELECT * FROM activity_logs ";
    if ($type != 'all') {
        $query .= "WHERE activity_type = '$type' ";
    }
    $query .= "ORDER BY log_timestamp DESC LIMIT $limit";
    
    $result = mysqli_query($konek, $query);
    $logs = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $logs[] = $row;
        }
    }
    
    return $logs;
}