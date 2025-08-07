<?php
// Debug script untuk memeriksa data chart
include "koneksi.php";

// Set header untuk mencegah caching
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo "<h2>🔍 Debug Chart Data - " . date('Y-m-d H:i:s') . "</h2>";
echo "<hr>";

// 1. Cek timezone setting
echo "<h3>⏰ Timezone Settings:</h3>";
echo "PHP Timezone: " . date_default_timezone_get() . "<br>";
echo "PHP Current Time: " . date('Y-m-d H:i:s') . "<br>";

$mysqlTime = mysqli_query($konek, "SELECT NOW() as current_time, @@session.time_zone as timezone");
$timeData = mysqli_fetch_assoc($mysqlTime);
echo "MySQL Timezone: " . $timeData['timezone'] . "<br>";
echo "MySQL Current Time: " . $timeData['current_time'] . "<br>";
echo "<hr>";

// 2. Cek data absensi 7 hari terakhir
echo "<h3>📊 Data Absensi 7 Hari Terakhir:</h3>";
$absensiQuery = mysqli_query($konek, "
    SELECT 
        DATE(tanggal) as tanggal,
        COUNT(CASE WHEN departmen IN ('Operation', 'Distribution', 'Warehouse', 'After Sales', 'Maintenance', 'Elektrikal', 'Instrument', 'Engineering', 'CSR', 'Planer', 'Commercial', 'EHS', 'Procurement', 'Accounting', 'HR & GA', 'Finance', 'Fin & Adm', 'IT', 'BOD', 'Niaga & Perencaan', 'Project', 'Distribusi') THEN 1 END) as total_karyawan,
        COUNT(CASE WHEN departmen = 'Magang' THEN 1 END) as total_magang,
        COUNT(*) as total_records
    FROM absensi a
    JOIN karyawan b ON a.nokartu = b.nokartu
    WHERE DATE(tanggal) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(tanggal)
    ORDER BY DATE(tanggal) DESC
");

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Tanggal</th><th>Karyawan</th><th>Magang</th><th>Total Records</th></tr>";
while ($row = mysqli_fetch_assoc($absensiQuery)) {
    echo "<tr>";
    echo "<td>" . $row['tanggal'] . "</td>";
    echo "<td>" . $row['total_karyawan'] . "</td>";
    echo "<td>" . $row['total_magang'] . "</td>";
    echo "<td>" . $row['total_records'] . "</td>";
    echo "</tr>";
}
echo "</table><br>";

// 3. Cek data tamu 7 hari terakhir
echo "<h3>🏃 Data Tamu 7 Hari Terakhir:</h3>";
$tamuQuery = mysqli_query($konek, "
    SELECT 
        DATE(tanggal_tamu) as tanggal,
        SUM(jumlah_tamu) as total_tamu,
        COUNT(*) as total_records
    FROM tamu 
    WHERE DATE(tanggal_tamu) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(tanggal_tamu)
    ORDER BY DATE(tanggal_tamu) DESC
");

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Tanggal</th><th>Total Tamu</th><th>Total Records</th></tr>";
while ($row = mysqli_fetch_assoc($tamuQuery)) {
    echo "<tr>";
    echo "<td>" . $row['tanggal'] . "</td>";
    echo "<td>" . $row['total_tamu'] . "</td>";
    echo "<td>" . $row['total_records'] . "</td>";
    echo "</tr>";
}
echo "</table><br>";

// 4. Cek range tanggal yang digunakan untuk chart
echo "<h3>📅 Range Tanggal Chart (7 hari terakhir):</h3>";
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = date('d M', strtotime($date));
    echo "Day -$i: $date ($label)<br>";
}
echo "<hr>";

// 5. Cek data mentah dari tabel absensi hari ini
echo "<h3>📋 Data Absensi Hari Ini (" . date('Y-m-d') . "):</h3>";
$todayQuery = mysqli_query($konek, "
    SELECT a.tanggal, b.nama, b.departmen 
    FROM absensi a 
    JOIN karyawan b ON a.nokartu = b.nokartu 
    WHERE DATE(a.tanggal) = CURDATE() 
    ORDER BY a.tanggal DESC 
    LIMIT 10
");

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Tanggal</th><th>Nama</th><th>Departemen</th></tr>";
while ($row = mysqli_fetch_assoc($todayQuery)) {
    echo "<tr>";
    echo "<td>" . $row['tanggal'] . "</td>";
    echo "<td>" . $row['nama'] . "</td>";
    echo "<td>" . $row['departmen'] . "</td>";
    echo "</tr>";
}
echo "</table><br>";

// 6. Cek data mentah dari tabel tamu hari ini
echo "<h3>🏃 Data Tamu Hari Ini (" . date('Y-m-d') . "):</h3>";
$todayTamuQuery = mysqli_query($konek, "
    SELECT tanggal_tamu, nama_tamu, jumlah_tamu 
    FROM tamu 
    WHERE DATE(tanggal_tamu) = CURDATE() 
    ORDER BY tanggal_tamu DESC 
    LIMIT 10
");

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Tanggal</th><th>Nama Tamu</th><th>Jumlah</th></tr>";
while ($row = mysqli_fetch_assoc($todayTamuQuery)) {
    echo "<tr>";
    echo "<td>" . $row['tanggal_tamu'] . "</td>";
    echo "<td>" . $row['nama_tamu'] . "</td>";
    echo "<td>" . $row['jumlah_tamu'] . "</td>";
    echo "</tr>";
}
echo "</table><br>";

echo "<hr>";
echo "<p><strong>💡 Saran:</strong></p>";
echo "<ul>";
echo "<li>Jika tidak ada data untuk tanggal terbaru, pastikan sistem absensi berjalan dengan baik</li>";
echo "<li>Periksa apakah ada data yang ter-input dengan format tanggal yang salah</li>";
echo "<li>Pastikan tidak ada masalah dengan timezone antara PHP dan MySQL</li>";
echo "<li>Cek apakah ada proses yang menghapus atau mengubah data secara otomatis</li>";
echo "</ul>";

echo "<br><a href='index.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>🔙 Kembali ke Dashboard</a>";
?>