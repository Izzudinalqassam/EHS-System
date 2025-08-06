<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "koneksi.php";
date_default_timezone_set('Asia/Jakarta');

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Terjadi kesalahan yang tidak diketahui.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_input_type = $_POST['nama_input_type'] ?? 'database';
    $nama = ($nama_input_type == 'manual') ? ($_POST['nama_manual'] ?? '') : ($_POST['nama_database'] ?? '');
    $nopol = $_POST['nopol'] ?? '';
    $jenis_kendaraan = $_POST['jenis_kendaraan'] ?? '';
    $petugas = $_POST['petugas'] ?? '';
    $tanggal_input = date('Y-m-d');
    $jam_masuk = date('H:i:s');

    if (!empty($nama) && !empty($nopol) && !empty($jenis_kendaraan) && !empty($petugas)) {
        $stmt = mysqli_prepare($konek, "INSERT INTO kendaraan (nama, nopol, jenis_kendaraan, petugas, tanggal_input, jam_masuk) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssss", $nama, $nopol, $jenis_kendaraan, $petugas, $tanggal_input, $jam_masuk);
        
        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 'success';
            $response['message'] = 'Data kendaraan berhasil ditambahkan!';
        } else {
            $response['message'] = 'Gagal menyimpan data ke database: ' . mysqli_error($konek);
        }
        mysqli_stmt_close($stmt);
    } else {
        $response['message'] = 'Semua field harus diisi!';
    }
} else {
    $response['message'] = 'Metode request tidak valid.';
}

mysqli_close($konek);

echo json_encode($response);
?>
