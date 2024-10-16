<?php
// Load library Dompdf
require 'libraries/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Set opsi agar tidak ada margin (full page)
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

// Membuat objek Dompdf dengan opsi
$dompdf = new Dompdf($options);

// Koneksi database
include "koneksi.php";

// Ambil data logo dari URL
$path = 'image\jababeka_logo.jpg';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

// Ambil data dari form (dikirim melalui POST)
$filter_date = isset($_POST['filter-date']) ? $_POST['filter-date'] : '';
$filter_department = isset($_POST['filter-department']) ? $_POST['filter-department'] : '';

// Query untuk menampilkan data berdasarkan filter
$sql = "SELECT a.tanggal, b.NIK, b.nama, b.departmen, a.jam_masuk, a.jam_pulang 
        FROM absensi a 
        JOIN karyawan b ON a.nokartu = b.nokartu 
        WHERE b.departmen != '' AND b.departmen IS NOT NULL";

// Filter berdasarkan tanggal jika ada
if (!empty($filter_date)) {
    $sql .= " AND a.tanggal = '$filter_date'";
}

// Filter berdasarkan departemen jika ada
if (!empty($filter_department)) {
    $sql .= " AND b.departmen = '$filter_department'";
}

$result = mysqli_query($konek, $sql);

// Bangun HTML untuk PDF
$html = '<style>
            body { margin: 0; padding: 0; }
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
        <h1 style="text-align: center;">Riwayat Absensi</h1>
    </div>
<h3 style="text-align: center;">Riwayat Absensi ' . date('d-m-Y', time() + (7 * 60 * 60)) . '</h3>';
$html .= '<table>';
$html .= '<thead>';
$html .= '<tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Departemen</th>
            <th>Jam Masuk</th>
            <th>Jam Keluar</th>
         </tr>';
$html .= '</thead>';
$html .= '<tbody>';

// Cek apakah ada data yang ditemukan
if (mysqli_num_rows($result) > 0) {
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>';
        $html .= '<td>' . $no . '</td>';
        $html .= '<td>' . $row['tanggal'] . '</td>';
        $html .= '<td>' . $row['NIK'] . '</td>';
        $html .= '<td>' . $row['nama'] . '</td>';
        $html .= '<td>' . $row['departmen'] . '</td>';
        $html .= '<td>' . $row['jam_masuk'] . '</td>';
        $html .= '<td>' . $row['jam_pulang'] . '</td>';
        $html .= '</tr>';
        $no++;
    }
} else {
    // Jika tidak ada data, tampilkan pesan
    $html .= '<tr><td colspan="7"><h3 style="text-align: center; color: red;">Tidak ada data Absen di tanggal ini.</h3></td></tr>';
}

$html .= '</tbody></table>';

// Masukkan HTML ke Dompdf
$dompdf->loadHtml($html);

// Atur ukuran dan orientasi kertas, serta margin 0
$dompdf->setPaper('A4', 'portrait');

// Render HTML ke PDF
$dompdf->render();

// Output PDF
$dompdf->stream("riwayat_absensi.pdf", ["Attachment" => false]);
