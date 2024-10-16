<?php
include "koneksi.php"; // Include your database connection

// Function to fetch updated attendance data
function fetchAttendanceData($konek) {
    $absensiMingguanQuery = mysqli_query($konek, "
        SELECT tanggal, 
               COUNT(CASE WHEN departmen = 'Karyawan' THEN 1 END) as total_karyawan,
               COUNT(CASE WHEN departmen = 'Magang' THEN 1 END) as total_magang,
               COUNT(CASE WHEN departmen = 'tamu' THEN 1 END) as total_tamu 
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

    return $absensiData;
}

// Fetch the updated attendance data
$absensiData = fetchAttendanceData($konek);

// Prepare data for JSON response
$response = [
    'labels' => array_column($absensiData, 'tanggal'),
    'total_karyawan' => array_column($absensiData, 'total_karyawan'),
    'total_magang' => array_column($absensiData, 'total_magang'),
    'total_tamu' => array_column($absensiData, 'total_tamu')
];

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
