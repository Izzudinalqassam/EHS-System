<?php
// Test script untuk memeriksa logika minggu
include "koneksi.php";

// Set header untuk mencegah caching
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo "<h2>🗓️ Test Logika Minggu - " . date('Y-m-d H:i:s') . "</h2>";
echo "<hr>";

// Test berbagai cara menghitung minggu
echo "<h3>📅 Informasi Hari Ini:</h3>";
echo "Hari ini: " . date('Y-m-d') . " (" . date('l, d F Y') . ")<br>";
echo "Hari ke-: " . date('N') . " (1=Senin, 7=Minggu)<br>";
echo "<br>";

echo "<h3>🔍 Berbagai Cara Menghitung Minggu:</h3>";

// Cara 1: monday this week
$mondayThisWeek1 = date('Y-m-d', strtotime('monday this week'));
echo "<strong>1. 'monday this week':</strong> $mondayThisWeek1<br>";

// Cara 2: last monday
$mondayThisWeek2 = date('Y-m-d', strtotime('last monday'));
echo "<strong>2. 'last monday':</strong> $mondayThisWeek2<br>";

// Cara 3: manual calculation
$currentDay = date('N'); // 1 (Monday) to 7 (Sunday)
$daysFromMonday = $currentDay - 1;
$mondayThisWeek3 = date('Y-m-d', strtotime("-$daysFromMonday days"));
echo "<strong>3. Manual calculation:</strong> $mondayThisWeek3 (hari ini - $daysFromMonday hari)<br>";

// Cara 4: this week monday
$mondayThisWeek4 = date('Y-m-d', strtotime('this week monday'));
echo "<strong>4. 'this week monday':</strong> $mondayThisWeek4<br>";

echo "<br>";

echo "<h3>📊 Range Minggu yang Akan Digunakan:</h3>";

// Gunakan cara manual yang paling akurat
$currentDay = date('N');
$daysFromMonday = $currentDay - 1;
$mondayThisWeek = date('Y-m-d', strtotime("-$daysFromMonday days"));
$sundayThisWeek = date('Y-m-d', strtotime($mondayThisWeek . " +6 days"));

echo "<strong>Senin minggu ini:</strong> $mondayThisWeek<br>";
echo "<strong>Minggu minggu ini:</strong> $sundayThisWeek<br>";
echo "<br>";

echo "<h3>📋 Tanggal Chart (Senin - Minggu):</h3>";
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime($mondayThisWeek . " +$i days"));
    $dayName = date('D', strtotime($date));
    $isToday = ($date == date('Y-m-d')) ? ' <-- HARI INI' : '';
    echo "$dayName $date" . $isToday . "<br>";
}

echo "<hr>";
echo "<h3>💡 Kesimpulan:</h3>";
echo "<p>Jika hari ini adalah <strong>" . date('l, d F Y') . "</strong>, maka:</p>";
echo "<ul>";
echo "<li>Chart harus menampilkan minggu dari <strong>$mondayThisWeek</strong> sampai <strong>$sundayThisWeek</strong></li>";
echo "<li>Hari ini (<strong>" . date('Y-m-d') . "</strong>) harus muncul di posisi hari <strong>" . date('l') . "</strong></li>";
echo "</ul>";

echo "<br><a href='index.php' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>🔙 Kembali ke Dashboard</a>";
?>