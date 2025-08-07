<?php
// Script untuk menambahkan data sample ke database agar chart terlihat lebih informatif
include "koneksi.php";

// Set header untuk mencegah caching
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo "<h2>📊 Menambahkan Data Sample untuk Chart</h2>";
echo "<hr>";

// Get current week dates
$mondayThisWeek = date('Y-m-d', strtotime('monday this week'));
$sundayThisWeek = date('Y-m-d', strtotime('sunday this week'));

echo "<p><strong>Minggu ini:</strong> $mondayThisWeek sampai $sundayThisWeek</p>";

// Sample data untuk setiap hari dalam minggu ini
$sampleData = [
    0 => ['karyawan' => 15, 'magang' => 3, 'tamu' => 8],   // Monday
    1 => ['karyawan' => 18, 'magang' => 4, 'tamu' => 12],  // Tuesday
    2 => ['karyawan' => 16, 'magang' => 2, 'tamu' => 6],   // Wednesday
    3 => ['karyawan' => 20, 'magang' => 5, 'tamu' => 15],  // Thursday (today)
    4 => ['karyawan' => 14, 'magang' => 3, 'tamu' => 9],   // Friday
    5 => ['karyawan' => 8, 'magang' => 1, 'tamu' => 4],    // Saturday
    6 => ['karyawan' => 5, 'magang' => 0, 'tamu' => 2]     // Sunday
];

echo "<h3>🔄 Menambahkan Data Sample...</h3>";

// Loop untuk setiap hari dalam minggu
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime($mondayThisWeek . " +$i days"));
    $dayName = date('l', strtotime($date));
    $data = $sampleData[$i];
    
    echo "<p><strong>$dayName ($date):</strong></p>";
    
    // Tambahkan data karyawan (simulasi absensi)
    for ($k = 1; $k <= $data['karyawan']; $k++) {
        $nokartu = 'EMP' . str_pad($k, 3, '0', STR_PAD_LEFT);
        $timestamp = $date . ' ' . sprintf('%02d:%02d:%02d', rand(7, 9), rand(0, 59), rand(0, 59));
        
        // Check if employee exists in karyawan table
        $checkEmp = mysqli_query($konek, "SELECT nokartu FROM karyawan WHERE nokartu = '$nokartu'");
        if (mysqli_num_rows($checkEmp) == 0) {
            // Add employee to karyawan table
            $nama = 'Karyawan ' . $k;
            $departmen = ['Operation', 'Distribution', 'Warehouse', 'IT', 'HR & GA'][rand(0, 4)];
            mysqli_query($konek, "INSERT INTO karyawan (nokartu, nama, departmen) VALUES ('$nokartu', '$nama', '$departmen')");
        }
        
        // Check if attendance already exists
        $checkAbs = mysqli_query($konek, "SELECT * FROM absensi WHERE nokartu = '$nokartu' AND DATE(tanggal) = '$date'");
        if (mysqli_num_rows($checkAbs) == 0) {
            mysqli_query($konek, "INSERT INTO absensi (nokartu, tanggal) VALUES ('$nokartu', '$timestamp')");
        }
    }
    
    // Tambahkan data magang
    for ($m = 1; $m <= $data['magang']; $m++) {
        $nokartu = 'INT' . str_pad($m, 3, '0', STR_PAD_LEFT);
        $timestamp = $date . ' ' . sprintf('%02d:%02d:%02d', rand(8, 10), rand(0, 59), rand(0, 59));
        
        // Check if intern exists in karyawan table
        $checkInt = mysqli_query($konek, "SELECT nokartu FROM karyawan WHERE nokartu = '$nokartu'");
        if (mysqli_num_rows($checkInt) == 0) {
            // Add intern to karyawan table
            $nama = 'Magang ' . $m;
            mysqli_query($konek, "INSERT INTO karyawan (nokartu, nama, departmen) VALUES ('$nokartu', '$nama', 'Magang')");
        }
        
        // Check if attendance already exists
        $checkAbs = mysqli_query($konek, "SELECT * FROM absensi WHERE nokartu = '$nokartu' AND DATE(tanggal) = '$date'");
        if (mysqli_num_rows($checkAbs) == 0) {
            mysqli_query($konek, "INSERT INTO absensi (nokartu, tanggal) VALUES ('$nokartu', '$timestamp')");
        }
    }
    
    // Tambahkan data tamu
    if ($data['tamu'] > 0) {
        $timestamp = $date . ' ' . sprintf('%02d:%02d:%02d', rand(9, 11), rand(0, 59), rand(0, 59));
        
        // Check if guest data already exists
        $checkTamu = mysqli_query($konek, "SELECT * FROM tamu WHERE DATE(tanggal_tamu) = '$date'");
        if (mysqli_num_rows($checkTamu) == 0) {
            $nama_tamu = 'Tamu Hari ' . $dayName;
            mysqli_query($konek, "INSERT INTO tamu (nama_tamu, tanggal_tamu, jumlah_tamu) VALUES ('$nama_tamu', '$timestamp', {$data['tamu']})");
        }
    }
    
    echo "&nbsp;&nbsp;✅ Karyawan: {$data['karyawan']}, Magang: {$data['magang']}, Tamu: {$data['tamu']}<br>";
}

echo "<hr>";
echo "<p><strong>✅ Data sample berhasil ditambahkan!</strong></p>";
echo "<p>Chart sekarang akan menampilkan data yang lebih realistis untuk minggu ini.</p>";

echo "<br><a href='index.php' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>🔙 Lihat Dashboard</a>";
echo "&nbsp;<a href='debug_chart_data.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>🔍 Debug Data</a>";
?>