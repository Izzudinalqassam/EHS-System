<?php
include "koneksi.php";
date_default_timezone_set('Asia/Jakarta');

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $jam_keluar = date('H:i:s');
    
    // Update absensi
    $sql = "UPDATE absensi SET 
            jam_pulang = '$jam_keluar',
            status = 'OUT',
            last_update = CURRENT_TIMESTAMP 
            WHERE id = '$id'";
    
    if (mysqli_query($konek, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Berhasil checkout']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal melakukan checkout']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
}
?>