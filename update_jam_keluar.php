<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include "koneksi.php";

// Set default timezone
date_default_timezone_set('Asia/Jakarta');

// Initialize response array
$response = array(
    'success' => false,
    'message' => ''
);

try {
    // Check if required parameters are set
    if (!isset($_POST['nokartu']) || !isset($_POST['tanggal'])) {
        throw new Exception('Missing required parameters');
    }

    // Get parameters
    $nokartu = $_POST['nokartu'];
    $tanggal = $_POST['tanggal'];
    $jam_keluar = date('H:i:s');

    // Start transaction
    mysqli_begin_transaction($konek);

    // Update absensi table
    $update_absensi = mysqli_query($konek, 
        "UPDATE absensi 
        SET jam_pulang = '$jam_keluar',
            status = 'OUT',
            last_update = NOW()
        WHERE nokartu = '$nokartu' 
        AND tanggal = '$tanggal'
        AND status = 'IN'"
    );

    if (!$update_absensi) {
        throw new Exception('Failed to update absensi table');
    }

    // Update riwayat table
    $update_riwayat = mysqli_query($konek,
        "UPDATE riwayat 
        SET jam_pulang = '$jam_keluar'
        WHERE nokartu = '$nokartu' 
        AND tanggal = '$tanggal'
        AND jam_pulang = '00:00:00'
        ORDER BY id DESC 
        LIMIT 1"
    );

    if (!$update_riwayat) {
        throw new Exception('Failed to update riwayat table');
    }

    // If both updates successful, commit transaction
    mysqli_commit($konek);
    
    $response['success'] = true;
    $response['message'] = 'Successfully updated checkout time';

} catch (Exception $e) {
    // If any error occurs, rollback changes
    mysqli_rollback($konek);
    $response['message'] = $e->getMessage();
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>