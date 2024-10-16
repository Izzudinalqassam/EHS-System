<?php
error_reporting(0);
include "koneksi.php";

//baca nomor kartu dari NodeMCU
$nokartu = $_GET['nokartu'];
//kosongkan tabel tmprfid2
mysqli_query($konek, "delete from tmprfid2");

//simpan nomor kartu yang baru ke tabel tmprfid2
$simpan = mysqli_query($konek, "insert into tmprfid2(nokartu)values('$nokartu')");
if ($simpan)
	echo "Berhasil";
else
	echo "Gagal";
