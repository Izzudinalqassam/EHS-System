<?php
session_start();
include 'models/koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nokartu'])) {
    $nokartu = $_POST['nokartu'];
    $tanggal = date('Y-m-d');
    $jam_pulang = date('H:i:s');
    
    // Update jam pulang di database
    $sql = "UPDATE absensi SET jam_pulang = '$jam_pulang' 
            WHERE nokartu = '$nokartu' AND tanggal = '$tanggal'";
    
    if (mysqli_query($konek, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>