<?php
// Prevent caching to ensure fresh data
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

include "koneksi.php"; // Include your database connection

// Query to count total employees, interns, and guests
$totalKaryawanQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM karyawan");
$totalKaryawan = mysqli_fetch_assoc($totalKaryawanQuery)['total'];

$totalMagangQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM karyawan WHERE departmen = 'Magang'");
$totalMagang = mysqli_fetch_assoc($totalMagangQuery)['total'];

$totalTamuQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM tamu WHERE nama_tamu != ''");
$totalTamu = mysqli_fetch_assoc($totalTamuQuery)['total'];

// Get date range from today to 7 days ahead for forward-looking trend
$todayDate = date('Y-m-d');
$endDate = date('Y-m-d', strtotime($todayDate . " +6 days")); // 7 days total including today

// Query untuk data absensi 7 hari ke depan (karyawan dan magang) - Hari ini sampai 6 hari ke depan
// Using DATE() function to ensure consistent date format
$absensiMingguanQuery = mysqli_query($konek, "
    SELECT 
        DATE(tanggal) as tanggal,
        COUNT(CASE WHEN departmen IN ('Operation', 'Distribution', 'Warehouse', 'After Sales', 'Maintenance', 'Elektrikal', 'Instrument', 'Engineering', 'CSR', 'Planer', 'Commercial', 'EHS', 'Procurement', 'Accounting', 'HR & GA', 'Finance', 'Fin & Adm', 'IT', 'BOD', 'Niaga & Perencaan', 'Project', 'Distribusi') THEN 1 END) as total_karyawan,
        COUNT(CASE WHEN departmen = 'Magang' THEN 1 END) as total_magang
    FROM absensi a
    JOIN karyawan b ON a.nokartu = b.nokartu
    WHERE DATE(tanggal) >= '$todayDate' AND DATE(tanggal) <= '$endDate'
    GROUP BY DATE(tanggal)
    ORDER BY DATE(tanggal) ASC
");

// Query untuk data tamu 7 hari ke depan - Hari ini sampai 6 hari ke depan
// Using DATE() function to ensure consistent date format
$tamuMingguanQuery = mysqli_query($konek, "
    SELECT 
        DATE(tanggal_tamu) as tanggal,
        SUM(jumlah_tamu) as total_tamu
    FROM tamu 
    WHERE DATE(tanggal_tamu) >= '$todayDate' AND DATE(tanggal_tamu) <= '$endDate'
    GROUP BY DATE(tanggal_tamu)
    ORDER BY DATE(tanggal_tamu) ASC
");

// Force fresh query for tamu data
mysqli_query($konek, "FLUSH QUERY CACHE");
// Collect data from both queries
$absensiData = [];
while ($row = mysqli_fetch_assoc($absensiMingguanQuery)) {
    $absensiData[$row['tanggal']] = [
        'total_karyawan' => $row['total_karyawan'],
        'total_magang' => $row['total_magang'],
        'total_tamu' => 0 // Default value
    ];
}

// Add tamu data
while ($row = mysqli_fetch_assoc($tamuMingguanQuery)) {
    if (isset($absensiData[$row['tanggal']])) {
        $absensiData[$row['tanggal']]['total_tamu'] = $row['total_tamu'];
    } else {
        $absensiData[$row['tanggal']] = [
            'total_karyawan' => 0,
            'total_magang' => 0,
            'total_tamu' => $row['total_tamu']
        ];
    }
}

// Ensure we have 7 days of data (today to 6 days ahead)
$labels = [];
$karyawan_didalam = [];
$magang_didalam = [];
$tamu_didalam = [];

// Get forward-looking dates (today to 6 days ahead)
$dates = [];

// Generate dates from today to 6 days ahead (7 days total)
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime($todayDate . " +$i days"));
    $dates[] = $date;
    
    // Format label with day name and date
    $dayName = date('D', strtotime($date)); // Mon, Tue, Wed, etc.
    $dateLabel = date('d/m', strtotime($date));
    
    // Add "Hari Ini" for today's date
    if ($i == 0) {
        $labels[] = 'Hari Ini (' . $dayName . ' ' . $dateLabel . ')';
    } else {
        $labels[] = $dayName . ' ' . $dateLabel;
    }
    
    $karyawan_didalam[] = $absensiData[$date]['total_karyawan'] ?? 0;
    $magang_didalam[] = $absensiData[$date]['total_magang'] ?? 0;
    $tamu_didalam[] = $absensiData[$date]['total_tamu'] ?? 0;
    
    // Debug: Log the actual data being sent to the chart
    error_log('CHART DATA: ' . $date . ' (' . $dayName . ') = K:' . ($absensiData[$date]['total_karyawan'] ?? 0) . ' M:' . ($absensiData[$date]['total_magang'] ?? 0) . ' T:' . ($absensiData[$date]['total_tamu'] ?? 0));
} 

// Debug: Print the complete arrays and date range info
error_log('=== CHART DEBUG INFO ===');
error_log('DATE RANGE: ' . $todayDate . ' to ' . $endDate . ' (7 days forward)');
error_log('TODAY: ' . date('Y-m-d') . ' (' . date('D') . ')');
error_log('LABELS: ' . implode(', ', $labels));
error_log('KARYAWAN: ' . implode(', ', $karyawan_didalam));
error_log('MAGANG: ' . implode(', ', $magang_didalam));
error_log('TAMU: ' . implode(', ', $tamu_didalam));
error_log('ABSENSI DATA: ' . print_r($absensiData, true));
error_log('=== END DEBUG INFO ===');
?>