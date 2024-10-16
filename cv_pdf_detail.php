<?php
require 'libraries/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include "koneksi.php"; // Koneksi ke database

// Ambil data logo dari URL
$path = 'image/jababeka_logo.jpg';
if (!file_exists($path)) {
    die("Logo file not found.");
}
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

// Ambil NIK dan tanggal filter dari query string
$nik = $_GET['nik'];
$filter_tanggal = isset($_GET['filter-date']) ? $_GET['filter-date'] : '';

// Query untuk mengambil data karyawan
$stmt = $konek->prepare("SELECT * FROM karyawan WHERE NIK = ?");
$stmt->bind_param("s", $nik);
$stmt->execute();
$result = $stmt->get_result();
$karyawan = $result->fetch_assoc();

// Query untuk mengambil histori riwayat, dengan filter tanggal jika ada
$histori_query = "SELECT * FROM riwayat WHERE nokartu = '{$karyawan['nokartu']}'";
if ($filter_tanggal) {
    $histori_query .= " AND tanggal = '$filter_tanggal'";
}
$histori_query .= " ORDER BY tanggal";
$histori_result = mysqli_query($konek, $histori_query);

// Buat instance Dompdf dengan opsi margin 0
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);

// Buat HTML untuk PDF
$html = '<style>
            body { margin: 0; padding: 0; }
            h2, h3 { text-align: center; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid black; padding: 5px; text-align: center; }
         </style>';
$html .= '<div style="position: relative;">
        <div style="background-image: url(' . $base64 . '); 
                    background-size: contain; 
                    background-position: center center; 
                    background-repeat: no-repeat; 
                    opacity: 0.2; 
                    position: absolute; 
                    top: 0; 
                    left: 0; 
                    width: 100%; 
                    height: 100%; 
                    z-index: -1;"></div>
        <h1 style="text-align: center;">Detail Profil</h1>
    </div>';

if ($karyawan) {
    $html .= "<p><strong>NIK:</strong> " . htmlspecialchars($karyawan['NIK']) . "</p>";
    $html .= "<p><strong>Nama:</strong> " . htmlspecialchars($karyawan['nama']) . "</p>";
    $html .= "<p><strong>Nokartu:</strong> " . htmlspecialchars($karyawan['nokartu']) . "</p>";
    $html .= "<p><strong>Departmen:</strong> " . htmlspecialchars($karyawan['departmen']) . "</p>";

    // Tabel histori absensi
    $html .= '<h3>Histori Absensi</h3>';
    $html .= '<table>';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Tanggal</th>';
    $html .= '<th>Jam Masuk</th>';
    $html .= '<th>Jam Pulang</th>';
    $html .= '<th>Lama Waktu Keluar</th>';  // Kolom selisih waktu
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    
    // Variabel untuk menyimpan baris sebelumnya
    $prev_row = null; 
    while ($row = mysqli_fetch_assoc($histori_result)) {
        $html .= '<tr>';
        $html .= "<td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
        $html .= "<td>{$row['jam_masuk']}</td>";
        $html .= "<td>{$row['jam_pulang']}</td>";
        
        // Hitung selisih waktu jika ada baris sebelumnya
        if ($prev_row && $prev_row['tanggal'] == $row['tanggal']) {
            // Hitung selisih waktu
            $jamMasukSekarang = new DateTime($row['jam_masuk']);
            $jamPulangSebelumnya = new DateTime($prev_row['jam_pulang']);
            $selisih = $jamPulangSebelumnya->diff($jamMasukSekarang);
            $lamaKeluar = $selisih->format('%h jam %i menit %s detik');
            $html .= "<td>$lamaKeluar</td>";
        } else {
            $html .= "<td>N/A</td>";
        }
        
        $html .= '</tr>';
        $prev_row = $row; // Simpan baris sekarang sebagai baris sebelumnya untuk iterasi berikutnya
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
} else {
    $html .= '<p>Data karyawan tidak ditemukan.</p>';
}

// Load HTML ke Dompdf
$dompdf->loadHtml($html);

// Set ukuran kertas dan orientasi, serta atur margin menjadi 0
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output file PDF
$dompdf->stream("detail_profil_{$nik}_" . date('Y-m-d_H-i-s') . ".pdf", ["Attachment" => false]);
?>
