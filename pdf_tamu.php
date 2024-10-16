<?php
// Include autoload dari dompdf
require 'libraries/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil data logo dari URL
$path = 'image\jababeka_logo.jpg';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);


// Koneksi ke database
include 'koneksi.php';

// Ambil filter date dari URL jika ada
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';

// Ambil data dari form (dikirim melalui POST)
if (isset($_POST['filter-date'])) {
    $filter_date = $_POST['filter-date'];
}

// Query untuk mengambil data tamu sesuai tanggal yang difilter
if ($filter_date != '') {
    $tamuQuery = mysqli_query($konek, "SELECT * FROM karyawan WHERE nama_tamu != '' AND DATE(tanggal_tamu) = '$filter_date'");
} else {
    // Jika tidak ada filter, ambil semua data
    $tamuQuery = mysqli_query($konek, "SELECT * FROM karyawan WHERE nama_tamu != ''");
}

// Persiapkan output HTML untuk PDF
$html = '
    <div style="position: relative;">
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
        <h1 style="text-align: center;">Data Tamu</h1>
    </div>';

if ($filter_date != '') {
    $html .= '<h1 style="text-align: center;">Tanggal: ' . $filter_date . '</h1>'; // Menambahkan tanggal filter di elemen h1
}

// Cek apakah ada data tamu
if (mysqli_num_rows($tamuQuery) > 0) {
    $html .= '
        
        <table border="1" cellspacing="0" cellpadding="10" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Tamu</th>
                    <th>Nama Perusahaan</th>
                    <th>Keperluan</th>
                    <th>Ingin Bertemu</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Nomor Kendaraan</th>
                </tr>
            </thead>
            <tbody>';

    // Loop untuk menampilkan data tamu dalam tabel
    $no = 1;
    while ($row = mysqli_fetch_assoc($tamuQuery)) {
        $html .= '
            <tr>
                <td style="text-align: center;">' . $no . '</td>
                <td style="text-align: center;">' . $row['tanggal_tamu'] . '</td>
                <td>' . $row['nama_tamu'] . '</td>
                <td>' . $row['nama_perusahaan'] . '</td>
                <td>' . $row['keperluan'] . '</td>
                <td>' . $row['ingin_bertemu'] . '</td>
                <td style="text-align: center;">' . $row['jam_masuk_tamu'] . '</td>
                <td style="text-align: center;">' . $row['jam_keluar_tamu'] . '</td>
                <td>' . $row['nopol'] . '</td>
            </tr>';
        $no++;
    }

    $html .= '
            </tbody>
        </table>';
} else {
    // Jika tidak ada data, tampilkan pesan
    $html .= '<h3 style="text-align: center; color: red;">Tidak ada data tamu di tanggal ini.</h3>';
}

// Inisialisasi DOMPDF dengan opsi
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Muat HTML ke dalam DOMPDF
$dompdf->loadHtml($html);

// Set ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output PDF ke browser
$dompdf->stream("data_tamu.pdf", array("Attachment" => 0));
?>
