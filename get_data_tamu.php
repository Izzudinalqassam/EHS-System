<?php
include "koneksi.php";

// Query untuk mengambil data tamu
$tamuQuery = mysqli_query($konek, "SELECT * FROM tamu");

// Buat array untuk menampung hasil query
$data = [];
while ($row = mysqli_fetch_assoc($tamuQuery)) {
    $data[] = $row;
}

// Menghitung jumlah tamu
$totalTamuQuery = mysqli_query($konek, "SELECT SUM(jumlah_tamu) as total FROM tamu WHERE jumlah_tamu != '' AND jam_keluar_tamu = '00:00:00' ");
$totalTamu = mysqli_fetch_assoc($totalTamuQuery)['total'];

// Menghitung jumlah tamu yang sudah keluar
$totalKeluarQuery = mysqli_query($konek, "SELECT SUM(jumlah_tamu) as total_keluar FROM tamu WHERE jam_keluar_tamu != '00:00:00'");
$totalKeluar = mysqli_fetch_assoc($totalKeluarQuery)['total_keluar'];

// Kirim data dalam format JSON
echo json_encode([
    'data' => $data,
    'totalTamu' => $totalTamu,
    'totalKeluar' => $totalKeluar
]);
?>
