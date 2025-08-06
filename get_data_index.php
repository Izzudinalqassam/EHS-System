<?php
include "koneksi.php";

// Hitung karyawan yang sedang di dalam (status IN)
$sql_karyawan = "SELECT COUNT(DISTINCT a.nokartu) as total 
                 FROM absensi a 
                 JOIN karyawan b ON a.nokartu = b.nokartu
                 WHERE a.status = 'IN'
                 AND b.departmen NOT IN ('tamu', 'Magang')
                 AND b.departmen != ''";

$result_karyawan = mysqli_query($konek, $sql_karyawan);
$karyawan_didalam = mysqli_fetch_assoc($result_karyawan)['total'];

// Hitung magang yang sedang di dalam (status IN)
$sql_magang = "SELECT COUNT(DISTINCT a.nokartu) as total 
               FROM absensi a 
               JOIN karyawan b ON a.nokartu = b.nokartu
               WHERE a.status = 'IN'
               AND b.departmen LIKE '%Magang%'";

$result_magang = mysqli_query($konek, $sql_magang);
$magang_didalam = mysqli_fetch_assoc($result_magang)['total'];

// Hitung tamu yang sedang di dalam
$sql_tamu = "SELECT SUM(jumlah_tamu) as total 
             FROM tamu 
             WHERE jam_keluar_tamu = '00:00:00'";

$result_tamu = mysqli_query($konek, $sql_tamu);
$tamu_didalam = mysqli_fetch_assoc($result_tamu)['total'] ?? 0;

// Total semua orang di dalam
$total_Didalam = $karyawan_didalam + $magang_didalam + $tamu_didalam;

// Kirim response JSON
header('Content-Type: application/json');
echo json_encode([
    'karyawan_didalam' => $karyawan_didalam,
    'magang_didalam' => $magang_didalam,
    'tamu_didalam' => $tamu_didalam,
    'total_Didalam' => $total_Didalam
]);
?>