<?php
// Script untuk memverifikasi perbaikan perhitungan minggu
include "koneksi.php";

// Set header untuk mencegah caching
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo "<h2>✅ Verifikasi Perbaikan Perhitungan Minggu</h2>";
echo "<hr>";

echo "<h3>📅 Informasi Hari Ini:</h3>";
echo "<p><strong>Hari ini:</strong> " . date('Y-m-d') . " (" . date('l, d F Y') . ")</p>";
echo "<p><strong>Hari ke-:</strong> " . date('N') . " (1=Senin, 7=Minggu)</p>";

echo "<h3>🔧 Perhitungan Minggu (Metode Baru):</h3>";

// Menggunakan perhitungan manual yang sama dengan yang diperbaiki
$currentDay = date('N'); // 1 (Monday) to 7 (Sunday)
$daysFromMonday = $currentDay - 1;
$mondayThisWeek = date('Y-m-d', strtotime("-$daysFromMonday days"));
$sundayThisWeek = date('Y-m-d', strtotime($mondayThisWeek . " +6 days"));

echo "<p><strong>Hari saat ini:</strong> " . date('N') . " (Kamis = 4)</p>";
echo "<p><strong>Hari dari Senin:</strong> $daysFromMonday hari</p>";
echo "<p><strong>Senin minggu ini:</strong> $mondayThisWeek</p>";
echo "<p><strong>Minggu minggu ini:</strong> $sundayThisWeek</p>";

echo "<h3>📊 Tanggal Chart yang Seharusnya Ditampilkan:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
echo "<tr style='background: #f8f9fa;'><th>Hari</th><th>Tanggal</th><th>Label Chart</th><th>Status</th></tr>";

for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime($mondayThisWeek . " +$i days"));
    $dayName = date('D', strtotime($date));
    $fullDayName = date('l', strtotime($date));
    $label = $dayName . ' ' . date('d/m', strtotime($date));
    $isToday = ($date == date('Y-m-d')) ? '🔥 HARI INI' : '';
    $bgColor = ($date == date('Y-m-d')) ? 'background: #fff3cd;' : '';
    
    echo "<tr style='$bgColor'>";
    echo "<td><strong>$fullDayName</strong></td>";
    echo "<td>$date</td>";
    echo "<td><code>$label</code></td>";
    echo "<td>$isToday</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>🎯 Ekspektasi vs Realita:</h3>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<p><strong>✅ Yang Diharapkan:</strong></p>";
echo "<ul>";
echo "<li>Chart menampilkan 7 hari: Senin ($mondayThisWeek) sampai Minggu ($sundayThisWeek)</li>";
echo "<li>Hari ini (" . date('Y-m-d') . ") muncul di posisi " . date('l') . "</li>";
echo "<li>Label chart: Mon 05/08, Tue 06/08, Wed 07/08, <strong>Thu 08/08</strong>, Fri 09/08, Sat 10/08, Sun 11/08</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<p><strong>❌ Masalah Sebelumnya:</strong></p>";
echo "<ul>";
echo "<li>Chart menampilkan Mon 04/08 (yang salah)</li>";
echo "<li>Menggunakan 'monday this week' yang tidak akurat untuk hari Kamis</li>";
echo "</ul>";
echo "</div>";

echo "<h3>🔍 Test Query Database:</h3>";
$testQuery = mysqli_query($konek, "
    SELECT 
        DATE(tanggal) as tanggal,
        COUNT(*) as total_records
    FROM absensi a
    JOIN karyawan b ON a.nokartu = b.nokartu
    WHERE DATE(tanggal) >= '$mondayThisWeek' AND DATE(tanggal) <= '$sundayThisWeek'
    GROUP BY DATE(tanggal)
    ORDER BY DATE(tanggal)
");

echo "<p><strong>Query:</strong> <code>WHERE DATE(tanggal) >= '$mondayThisWeek' AND DATE(tanggal) <= '$sundayThisWeek'</code></p>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f8f9fa;'><th>Tanggal</th><th>Total Records</th></tr>";
while ($row = mysqli_fetch_assoc($testQuery)) {
    echo "<tr>";
    echo "<td>" . $row['tanggal'] . "</td>";
    echo "<td>" . $row['total_records'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<div style='text-align: center; margin: 20px 0;'>";
echo "<a href='index.php' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🔙 Lihat Dashboard</a>";
echo "<a href='debug_chart_data.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🔍 Debug Data</a>";
echo "</div>";
?>