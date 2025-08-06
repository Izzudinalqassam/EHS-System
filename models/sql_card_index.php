<?php
// Include your database connection
include "koneksi.php";

// Get today's date
$tanggal_hari_ini = date('Y-m-d');

// Menghitung jumlah karyawan (selain magang) yang sedang berada di dalam
$sql_karyawan_didalam = "SELECT COUNT(*) AS total_masuk 
    FROM absensi a 
    JOIN karyawan b ON a.nokartu = b.nokartu
    WHERE a.jam_masuk != '00:00:00' 
    AND (a.jam_pulang = '00:00:00' OR a.jam_masuk > a.jam_pulang) 
    AND b.departmen NOT IN ('Magang', 'tamu') 
    AND a.tanggal = '$tanggal_hari_ini'";
$result_karyawan_didalam = mysqli_query($konek, $sql_karyawan_didalam);
$karyawan_didalam = mysqli_fetch_assoc($result_karyawan_didalam)['total_masuk'];

// Menghitung jumlah magang yang sedang berada di dalam
$sql_magang_didalam = "SELECT COUNT(*) AS total_magang 
    FROM absensi a 
    JOIN karyawan b ON a.nokartu = b.nokartu
    WHERE a.jam_masuk != '00:00:00' 
    AND (a.jam_pulang = '00:00:00' OR a.jam_masuk > a.jam_pulang) 
    AND b.departmen = 'Magang' 
    AND a.tanggal = '$tanggal_hari_ini'";
$result_magang_didalam = mysqli_query($konek, $sql_magang_didalam);
$magang_didalam = mysqli_fetch_assoc($result_magang_didalam)['total_magang'];

// Menghitung jumlah tamu yang sedang berada di dalam
// Query untuk menghitung jumlah tamu yang masih di dalam
$sql_tamu_didalam = "SELECT SUM(jumlah_tamu) AS total_tamu FROM tamu WHERE jam_keluar_tamu = '00:00:00'";

$result_tamu_didalam = mysqli_query($konek, $sql_tamu_didalam);

if ($result_tamu_didalam) {
    $tamu_didalam = mysqli_fetch_assoc($result_tamu_didalam)['total_tamu'] ?? 0;
} else {
    $tamu_didalam = 0; // Beri nilai default jika query gagal
}

// Menampilkan jumlah tamu yang masih di dalam



// Menghitung total orang yang sedang berada di dalam (karyawan, magang, dan tamu)
$total_Didalam = $karyawan_didalam + $magang_didalam + $tamu_didalam;
?>