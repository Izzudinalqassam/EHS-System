<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

error_reporting(0);
include "koneksi.php";
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $petugas = $_POST['petugas'];
    $nama = $_POST['jenis_input'] === 'database' ? $_POST['nama_database'] : $_POST['nama_manual'];
    $jenis_kendaraan = $_POST['jenis_kendaraan'];
    $nopol = $_POST['nopol'];
    $jam_masuk = date("H:i:s");
    $jam_keluar = "00:00:00";
    $tanggal_input = date("Y-m-d");

    if (empty($nama)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama harus diisi']);
        exit();
    }

    $simpan = mysqli_query($konek, "INSERT INTO kendaraan(petugas, nama, jenis_kendaraan, nopol, jam_masuk, jam_keluar, tanggal_input) 
                                   VALUES('$petugas', '$nama', '$jenis_kendaraan', '$nopol', '$jam_masuk', '$jam_keluar', '$tanggal_input')");

    if ($simpan) {
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . mysqli_error($konek)]);
    }
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
?>