<?php
session_start();
include "koneksi.php";

$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT id, nama, nopol, jenis_kendaraan, jam_masuk 
          FROM kendaraan 
          WHERE jam_keluar = '00:00:00' 
          AND nama LIKE '%$search%'
          ORDER BY nama ASC";

$result = mysqli_query($konek, $query);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>