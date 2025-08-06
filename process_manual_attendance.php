<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include "koneksi.php";
date_default_timezone_set('Asia/Jakarta');

$response = array(
    'success' => false,
    'message' => ''
);

try {
    if (!isset($_POST['nokartu']) || !isset($_POST['mode'])) {
        throw new Exception('Parameter tidak lengkap');
    }

    $nokartu = $_POST['nokartu'];
    $mode = $_POST['mode']; // 'in' atau 'out'
    $tanggal = date('Y-m-d');
    $jam = date('H:i:s');

    // Cek apakah karyawan terdaftar
    $cari_karyawan = mysqli_query($konek, "SELECT * FROM karyawan WHERE nokartu='$nokartu'");
    
    if (mysqli_num_rows($cari_karyawan) > 0) {
        mysqli_begin_transaction($konek);
        
        // Mode Tap In
        if ($mode === 'in') {
            // Cek apakah ada record dengan status IN yang belum di tap out
            $cari_record_belum_out = mysqli_query($konek, 
                "SELECT * FROM absensi 
                WHERE nokartu='$nokartu' 
                AND status='IN'
                ORDER BY tanggal DESC, jam_masuk DESC 
                LIMIT 1"
            );
            
            if (mysqli_num_rows($cari_record_belum_out) > 0) {
                // Update record yang masih IN dengan jam masuk baru
                $data_record = mysqli_fetch_array($cari_record_belum_out);
                mysqli_query($konek, 
                    "UPDATE absensi 
                    SET jam_masuk='$jam',
                        tanggal='$tanggal',
                        last_update=NOW()
                    WHERE id='" . $data_record['id'] . "'"
                );
            } else {
                // Cek record hari ini
                $cari_record_hari_ini = mysqli_query($konek, 
                    "SELECT * FROM absensi 
                    WHERE nokartu='$nokartu' 
                    AND tanggal='$tanggal'
                    ORDER BY jam_masuk DESC 
                    LIMIT 1"
                );
                
                if (mysqli_num_rows($cari_record_hari_ini) > 0) {
                    // Update record hari ini
                    $data_record = mysqli_fetch_array($cari_record_hari_ini);
                    mysqli_query($konek, 
                        "UPDATE absensi 
                        SET jam_masuk='$jam',
                            jam_pulang='00:00:00',
                            status='IN',
                            last_update=NOW()
                        WHERE id='" . $data_record['id'] . "'"
                    );
                } else {
                    // Buat record baru
                    mysqli_query($konek, 
                        "INSERT INTO absensi(nokartu, tanggal, jam_masuk, jam_pulang, status, last_update) 
                        VALUES ('$nokartu', '$tanggal', '$jam', '00:00:00', 'IN', NOW())"
                    );
                }
            }

            // Selalu buat record baru di riwayat saat tap in
            mysqli_query($konek, 
                "INSERT INTO riwayat(nokartu, tanggal, jam_masuk, jam_pulang) 
                VALUES ('$nokartu', '$tanggal', '$jam', '00:00:00')"
            );
        }
        // Mode Tap Out
        else if ($mode === 'out') {
            // Cari record yang belum tap out (status IN)
            $cari_record = mysqli_query($konek, 
                "SELECT * FROM absensi 
                WHERE nokartu='$nokartu' 
                AND status='IN'
                ORDER BY tanggal DESC, jam_masuk DESC 
                LIMIT 1"
            );
            
            if (mysqli_num_rows($cari_record) > 0) {
                $data_record = mysqli_fetch_array($cari_record);
                
                // Update record dengan status OUT dan tanggal baru
                mysqli_query($konek, 
                    "UPDATE absensi 
                    SET jam_pulang='$jam',
                        tanggal='$tanggal',
                        status='OUT',
                        last_update=NOW()
                    WHERE id='" . $data_record['id'] . "'"
                );
                
                // Update riwayat terakhir yang belum ada jam pulang
                mysqli_query($konek,
                    "UPDATE riwayat 
                    SET jam_pulang='$jam'
                    WHERE nokartu='$nokartu' 
                    AND jam_pulang='00:00:00'
                    ORDER BY id DESC 
                    LIMIT 1"
                );
            } else {
                throw new Exception('Tidak ditemukan record absensi masuk yang aktif');
            }
        }

        mysqli_commit($konek);
        $response['success'] = true;
        $response['message'] = 'Berhasil mencatat absensi';

    } else {
        throw new Exception('Karyawan tidak ditemukan');
    }

} catch (Exception $e) {
    if (isset($konek)) mysqli_rollback($konek);
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>