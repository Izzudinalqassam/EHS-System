<?php
include "koneksi.php";
date_default_timezone_set('Asia/Jakarta');

$filter_date = isset($_POST['filter_date']) ? $_POST['filter_date'] : date('Y-m-d');
$today = date('Y-m-d');

// Get vehicles data
$kendaraanQuery = mysqli_query($konek, "SELECT * FROM kendaraan 
    WHERE (jam_keluar = '00:00:00' OR tanggal_input = '$filter_date')
    ORDER BY 
        CASE 
            WHEN jam_keluar = '00:00:00' AND tanggal_input = '$today' THEN 0
            WHEN jam_keluar = '00:00:00' THEN 1
            ELSE 2 
        END,
        tanggal_input DESC,
        jam_masuk DESC");

$data = array();
$no = 1;
while ($row = mysqli_fetch_assoc($kendaraanQuery)) {
    $data[] = array(
        'no' => $no++,
        'tanggal' => $row['tanggal_input'],
        'petugas' => $row['petugas'],
        'nama' => $row['nama'],
        'jenis_kendaraan' => $row['jenis_kendaraan'],
        'nopol' => $row['nopol'],
        'jam_masuk' => $row['jam_masuk'],
        'jam_keluar' => $row['jam_keluar'],
        'id' => $row['id'],
        'status' => $row['jam_keluar'] != '00:00:00' ? 'Keluar' : 'Masuk'
    );
}

// Get counts
$totalMotorQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM kendaraan 
    WHERE jenis_kendaraan = 'Motor' AND jam_keluar = '00:00:00'");
$totalMotor = mysqli_fetch_assoc($totalMotorQuery)['total'] ?? 0;

$totalMobilQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM kendaraan 
    WHERE jenis_kendaraan = 'Mobil' AND jam_keluar = '00:00:00'");
$totalMobil = mysqli_fetch_assoc($totalMobilQuery)['total'] ?? 0;

echo json_encode([
    'data' => $data,
    'counts' => [
        'motor' => $totalMotor,
        'mobil' => $totalMobil
    ],
    'current_date' => date('d F Y')
]);
?>