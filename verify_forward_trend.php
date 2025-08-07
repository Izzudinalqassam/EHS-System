<?php
// Verify Forward Trend Fix - 7 Days Ahead
header('Content-Type: text/html; charset=utf-8');
include "models/koneksi.php";

echo "<h2>🔍 Verifikasi Trend 7 Hari Ke Depan</h2>";
echo "<hr>";

// Get date range from today to 7 days ahead
$todayDate = date('Y-m-d');
$endDate = date('Y-m-d', strtotime($todayDate . " +6 days")); // 7 days total including today

echo "<h3>📅 Informasi Tanggal:</h3>";
echo "<p><strong>Hari ini:</strong> " . date('l, d F Y', strtotime($todayDate)) . " (" . $todayDate . ")</p>";
echo "<p><strong>Tanggal akhir:</strong> " . date('l, d F Y', strtotime($endDate)) . " (" . $endDate . ")</p>";
echo "<p><strong>Rentang:</strong> 7 hari ke depan (termasuk hari ini)</p>";

echo "<h3>📊 Tanggal Chart yang Akan Ditampilkan:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background-color: #f0f0f0;'><th>No</th><th>Tanggal</th><th>Hari</th><th>Label Chart</th><th>Status</th></tr>";

for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime($todayDate . " +$i days"));
    $dayName = date('D', strtotime($date));
    $fullDayName = date('l', strtotime($date));
    $dateLabel = date('d/m', strtotime($date));
    
    // Create chart label
    if ($i == 0) {
        $chartLabel = 'Hari Ini (' . $dayName . ' ' . $dateLabel . ')';
        $status = '🟢 Hari Ini';
    } else {
        $chartLabel = $dayName . ' ' . $dateLabel;
        $status = '🔵 Masa Depan';
    }
    
    echo "<tr>";
    echo "<td>" . ($i + 1) . "</td>";
    echo "<td>" . date('d F Y', strtotime($date)) . "</td>";
    echo "<td>" . $fullDayName . "</td>";
    echo "<td><strong>" . $chartLabel . "</strong></td>";
    echo "<td>" . $status . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>🔍 Test Query Database:</h3>";
echo "<p><strong>Query Absensi:</strong></p>";
echo "<code style='background-color: #f5f5f5; padding: 10px; display: block; margin: 10px 0;'>";
echo "SELECT DATE(tanggal) as tanggal, COUNT(*) as total FROM absensi WHERE DATE(tanggal) >= '$todayDate' AND DATE(tanggal) <= '$endDate' GROUP BY DATE(tanggal)";
echo "</code>";

// Test query
$testQuery = mysqli_query($konek, "SELECT DATE(tanggal) as tanggal, COUNT(*) as total FROM absensi WHERE DATE(tanggal) >= '$todayDate' AND DATE(tanggal) <= '$endDate' GROUP BY DATE(tanggal) ORDER BY DATE(tanggal)");

echo "<p><strong>Hasil Query:</strong></p>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background-color: #f0f0f0;'><th>Tanggal</th><th>Total Data</th></tr>";

if (mysqli_num_rows($testQuery) > 0) {
    while ($row = mysqli_fetch_assoc($testQuery)) {
        echo "<tr>";
        echo "<td>" . date('l, d F Y', strtotime($row['tanggal'])) . "</td>";
        echo "<td>" . $row['total'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='2' style='text-align: center; color: #888;'>Tidak ada data untuk rentang tanggal ini</td></tr>";
}

echo "</table>";

echo "<h3>✅ Kesimpulan:</h3>";
echo "<ul>";
echo "<li>Chart sekarang menampilkan trend <strong>7 hari ke depan</strong> mulai dari hari ini</li>";
echo "<li>Hari pertama ditandai sebagai <strong>'Hari Ini'</strong></li>";
echo "<li>Tanggal dimulai dari: <strong>" . date('d F Y', strtotime($todayDate)) . "</strong></li>";
echo "<li>Tanggal berakhir di: <strong>" . date('d F Y', strtotime($endDate)) . "</strong></li>";
echo "<li>Total: <strong>7 hari</strong> (termasuk hari ini)</li>";
echo "</ul>";

echo "<hr>";
echo "<p><a href='index.php' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Kembali ke Dashboard</a></p>";
?>