<?php
error_reporting(0);
include "koneksi.php";

$nokartu = $_GET['nokartu'];
$cari_karyawan = mysqli_query($konek, "SELECT nama FROM karyawan WHERE nokartu='$nokartu'");
$data_karyawan = mysqli_fetch_array($cari_karyawan);

if ($data_karyawan) {
    echo "Kartu Sudah Terdaftar|" . $data_karyawan['nama'];
} else {
    // Simpan ke tabel temporary khusus pendaftaran
    mysqli_query($konek, "DELETE FROM tmp_pendaftaran");
    $simpan = mysqli_query($konek, "INSERT INTO tmp_pendaftaran(nokartu) VALUES('$nokartu')");
    if ($simpan) {
        echo "Berhasil";
    } else {
        echo "Gagal";
    }
}
?>