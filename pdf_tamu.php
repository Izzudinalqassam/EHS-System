<?php
require 'libraries/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

date_default_timezone_set('Asia/Jakarta');

// Koneksi dan pengaturan awal
include 'koneksi.php';
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Ambil filter date range
$filter_date_start = isset($_GET['filter_date_start']) ? $_GET['filter_date_start'] : date('Y-m-d');
$filter_date_end = isset($_GET['filter_date_end']) ? $_GET['filter_date_end'] : date('Y-m-d');

// Format tanggal untuk display
$date_display = '';
if ($filter_date_start == $filter_date_end) {
    $date_display = date('d-m-Y', strtotime($filter_date_start));
} else {
    $date_display = date('d-m-Y', strtotime($filter_date_start)) . ' s/d ' . date('d-m-Y', strtotime($filter_date_end));
}

// Query untuk summary
$totalTamuQuery = mysqli_query($konek, "SELECT SUM(jumlah_tamu) as total FROM tamu WHERE jumlah_tamu != '' AND jam_keluar_tamu = '00:00:00' AND tanggal_tamu BETWEEN '$filter_date_start' AND '$filter_date_end'");
$totalTamu = mysqli_fetch_assoc($totalTamuQuery)['total'] ?? 0;

$totalKeluarQuery = mysqli_query($konek, "SELECT SUM(jumlah_tamu) as total_keluar FROM tamu WHERE jam_keluar_tamu != '00:00:00' AND tanggal_tamu BETWEEN '$filter_date_start' AND '$filter_date_end'");
$totalKeluar = mysqli_fetch_assoc($totalKeluarQuery)['total_keluar'] ?? 0;

// Query untuk data tamu
$tamuQuery = mysqli_query($konek, "SELECT * FROM tamu WHERE tanggal_tamu BETWEEN '$filter_date_start' AND '$filter_date_end' ORDER BY tanggal_tamu DESC, jam_masuk_tamu DESC");

// Style HTML
$html = '
<style>
    body { 
        font-family: Arial, sans-serif;
        font-size: 11pt;
        margin: 15px;
    }
    .header {
        text-align: center;
        margin-bottom: 20px;
    }
    .header h2 {
        margin: 5px 0;
    }
    .header h3 {
        margin: 5px 0;
        color: #444;
    }
    .header p {
        margin: 5px 0;
        font-size: 10pt;
    }
    .summary-box {
        margin: 15px 0;
        padding: 10px;
        border: 1px solid #ddd;
        background-color: #f9f9f9;
        border-radius: 5px;
    }
    .summary-item {
        margin: 5px 0;
        font-size: 10pt;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
        font-size: 9pt;
    }
    th, td {
        border: 0.5px solid #ddd;
        padding: 5px;
    }
    th {
        background-color: #f5f5f5;
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
    }
    td {
        vertical-align: top;
    }
    .timestamp {
        font-size: 8pt;
        text-align: right;
        color: #666;
        margin-top: 10px;
    }
    .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        text-align: center;
        font-size: 8pt;
        border-top: 1px solid #ddd;
        padding-top: 5px;
    }
    .status-in {
        color: green;
        font-weight: bold;
    }
    .status-out {
        color: red;
        font-weight: bold;
    }
    .page-number {
        position: fixed;
        bottom: 0;
        width: 100%;
        text-align: center;
        font-size: 8pt;
    }
    .page-break {
        page-break-before: always;
    }
</style>

<div class="header">
    <h2>LAPORAN DATA TAMU</h2>
    <h3>PT. Bekasi Power - Departemen EHS</h3>
    <p>Periode: ' . $date_display . '</p>
</div>

<div class="summary-box">
    <div class="summary-item">
        <strong>Total Tamu Masih Ada:</strong> ' . ($totalTamu ?: 0) . ' orang
    </div>
    <div class="summary-item">
        <strong>Total Tamu Sudah Keluar:</strong> ' . ($totalKeluar ?: 0) . ' orang
    </div>
    <div class="summary-item">
        <strong>Total Keseluruhan:</strong> ' . (($totalTamu ?: 0) + ($totalKeluar ?: 0)) . ' orang
    </div>
</div>

<table>
    <thead>
        <tr>
            <th width="4%">No</th>
            <th width="8%">Tanggal</th>
            <th width="11%">Nama</th>
            <th width="13%">Perusahaan</th>
            <th width="4%">Jml</th>
            <th width="18%">Keperluan</th>
            <th width="11%">Bertemu</th>
            <th width="9%">No. Kendaraan</th>
            <th width="6%">Masuk</th>
            <th width="6%">Keluar</th>
            <th width="10%">Status</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
while ($row = mysqli_fetch_assoc($tamuQuery)) {
    $status = $row['jam_keluar_tamu'] != '00:00:00' ? 
        '<span class="status-out">Keluar</span>' : 
        '<span class="status-in">Masuk</span>';
    
    $html .= '<tr>
        <td align="center">' . $no++ . '</td>
        <td align="center">' . date('d/m/Y', strtotime($row['tanggal_tamu'])) . '</td>
        <td>' . $row['nama_tamu'] . '</td>
        <td>' . $row['nama_perusahaan'] . '</td>
        <td align="center">' . $row['jumlah_tamu'] . '</td>
        <td>' . $row['keperluan'] . '</td>
        <td>' . $row['ingin_bertemu'] . '</td>
        <td align="center">' . $row['nopol'] . '</td>
        <td align="center" class="status-in">' . $row['jam_masuk_tamu'] . '</td>
        <td align="center" class="status-out">' . ($row['jam_keluar_tamu'] != '00:00:00' ? $row['jam_keluar_tamu'] : '-') . '</td>
        <td align="center">' . $status . '</td>
    </tr>';
}

$html .= '</tbody></table>
<div class="timestamp">Dicetak pada: ' . date('d-m-Y H:i:s') . '</div>
<div class="footer">PT. Bekasi Power - Departemen EHS - ' . date('Y') . '</div>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
// Generate filename based on date range
$filename = "laporan_tamu_" . $filter_date_start;
if ($filter_date_start != $filter_date_end) {
    $filename .= "_sampai_" . $filter_date_end;
}
$filename .= ".pdf";

$dompdf->stream($filename, array("Attachment" => 0));
?>