<?php
include "koneksi.php"; // Include your database connection

// Query to count total employees, interns, and guests
$totalKaryawanQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM karyawan");
$totalKaryawan = mysqli_fetch_assoc($totalKaryawanQuery)['total'];

$totalMagangQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM karyawan WHERE departmen = 'Magang'");
$totalMagang = mysqli_fetch_assoc($totalMagangQuery)['total'];

$totalTamuQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM tamu WHERE nama_tamu != ''");
$totalTamu = mysqli_fetch_assoc($totalTamuQuery)['total'];

// Query to get attendance data for the last week
$absensiMingguanQuery = mysqli_query($konek, "
    SELECT tanggal,
           COUNT(CASE WHEN departmen IN ('Operation', 'Distribution', 'Warehouse', 'After Sales', 'Maintenance', 'Elektrikal', 'Instrument', 'Engineering', 'CSR', 'Planer', 'Commercial', 'EHS', 'Procurement', 'Accounting', 'HR & GA', 'Finance', 'Fin & Adm', 'IT', 'BOD', 'Niaga & Perencaan', 'Project', 'Distribusi') THEN 1 END) as total_karyawan,
           COUNT(CASE WHEN departmen = 'Magang' THEN 1 END) as total_magang,
           $totalTamu as total_tamu
    FROM absensi a
    JOIN karyawan b ON a.nokartu = b.nokartu
    WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY tanggal
    ORDER BY tanggal ASC
");
$absensiData = [];
while ($row = mysqli_fetch_assoc($absensiMingguanQuery)) {
    $absensiData[] = [
        'tanggal' => $row['tanggal'],
        'total_karyawan' => $row['total_karyawan'],
        'total_magang' => $row['total_magang'],
        'total_tamu' => $row['total_tamu']
    ];
}

// Prepare data for the chart
$labels = [];
$totalKaryawan = [];
$totalMagang = [];
$totalTamu = [];

foreach ($absensiData as $item) {
    $labels[] = $item['tanggal'];
    $totalKaryawan[] = $item['total_karyawan'];
    $totalMagang[] = $item['total_magang'];
    $totalTamu[] = $item['total_tamu'];
}
?>