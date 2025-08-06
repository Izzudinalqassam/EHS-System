<?php
error_reporting(0);
include "koneksi.php";

//baca nomor kartu dari NodeMCU
$nokartu = $_GET['nokartu'];

//cari nama karyawan berdasarkan nokartu
$cari_karyawan = mysqli_query($konek, "SELECT nama FROM karyawan WHERE nokartu='$nokartu'");
$data_karyawan = mysqli_fetch_array($cari_karyawan);

if ($data_karyawan) {
    // Jika kartu terdaftar
    $nama = $data_karyawan['nama'];
    
    //kosongkan tabel tmprfid2
    mysqli_query($konek, "delete from tmprfid");

    //simpan nomor kartu ke tabel tmprfid2
    $simpan = mysqli_query($konek, "insert into tmprfid(nokartu)values('$nokartu')");
    if ($simpan) {
        echo "Berhasil|" . $nama;
    } else {
        echo "Gagal";
    }
} else {
    // Jika kartu tidak terdaftar
    //kosongkan tabel tmprfid2
    mysqli_query($konek, "delete from tmprfid");
    
    //simpan nomor kartu ke tabel tmprfid2
    $simpan = mysqli_query($konek, "insert into tmprfid(nokartu)values('$nokartu')");
    
    echo "Kartu Tidak Terdaftar";
}
?>