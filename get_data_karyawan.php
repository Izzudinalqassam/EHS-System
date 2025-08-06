<?php
include "koneksi.php";

date_default_timezone_set('Asia/Jakarta');
$tanggal_hari_ini = date('Y-m-d');

// Query untuk mendapatkan semua entri yang perlu ditampilkan
$sql_active_entries = "SELECT a.*, b.NIK, b.nama, b.departmen 
                      FROM absensi a 
                      JOIN karyawan b ON a.nokartu = b.nokartu
                      WHERE (
                          a.tanggal = '$tanggal_hari_ini' -- data hari ini
                          OR (a.jam_pulang = '00:00:00' AND a.tanggal < '$tanggal_hari_ini') -- belum tap out
                          OR (DATE(a.last_update) = '$tanggal_hari_ini' AND a.jam_pulang != '00:00:00') -- tap out hari ini
                      )
                      AND b.departmen NOT IN ('tamu') 
                      AND b.departmen != ''
                      ORDER BY 
                          CASE 
                              WHEN a.status = 'IN' THEN 1
                              ELSE 2
                          END,
                          a.tanggal DESC, 
                          a.last_update DESC";

$result = mysqli_query($konek, $sql_active_entries);
$absensi = [];

// Hitung total masuk
$sql_total_masuk = "SELECT COUNT(DISTINCT a.nokartu) as total FROM absensi a 
                    JOIN karyawan b ON a.nokartu = b.nokartu
                    WHERE (
                        (a.tanggal = '$tanggal_hari_ini' AND a.jam_masuk != '00:00:00' AND a.status = 'IN')
                        OR (a.tanggal < '$tanggal_hari_ini' AND a.jam_pulang = '00:00:00')
                    )
                    AND b.departmen NOT IN ('tamu')";

$result_masuk = mysqli_query($konek, $sql_total_masuk);
$data_masuk = mysqli_fetch_assoc($result_masuk);
$total_orang_masuk = $data_masuk['total'];

// Hitung total keluar
$sql_total_keluar = "SELECT COUNT(DISTINCT a.nokartu) as total FROM absensi a 
                     JOIN karyawan b ON a.nokartu = b.nokartu
                     WHERE DATE(a.last_update) = '$tanggal_hari_ini'
                     AND a.status = 'OUT'
                     AND a.jam_pulang != '00:00:00'
                     AND b.departmen NOT IN ('tamu')";

$result_keluar = mysqli_query($konek, $sql_total_keluar);
$data_keluar = mysqli_fetch_assoc($result_keluar);
$total_orang_keluar = $data_keluar['total'];

// Hitung total dalam
$sql_total_dalam = "SELECT COUNT(DISTINCT a.nokartu) as total FROM absensi a 
                    JOIN karyawan b ON a.nokartu = b.nokartu
                    WHERE (
                        (a.tanggal = '$tanggal_hari_ini' AND a.status = 'IN')
                        OR (a.tanggal < '$tanggal_hari_ini' AND a.jam_pulang = '00:00:00')
                    )
                    AND b.departmen NOT IN ('tamu')";

$result_dalam = mysqli_query($konek, $sql_total_dalam);
$data_dalam = mysqli_fetch_assoc($result_dalam);
$total_keseluruhan = $data_dalam['total'];

// Ambil data untuk tabel
while ($row = mysqli_fetch_assoc($result)) {
    $absensi[] = $row;
}

// Format tanggal untuk display
$tanggal_display = date('d-m-Y');

// Kirim response JSON
header('Content-Type: application/json');
echo json_encode([
    'total_masuk' => $total_orang_masuk,
    'total_keluar' => $total_orang_keluar,
    'total_keseluruhan' => $total_keseluruhan,
    'tanggal_display' => $tanggal_display,
    'absensi' => $absensi
], JSON_PRETTY_PRINT);
?>