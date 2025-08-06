<?php
require 'libraries/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Set timezone
date_default_timezone_set('Asia/Jakarta');
$current_datetime = date('d-m-Y H:i:s');

// Initialize dompdf dengan options
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);

// Koneksi database
include "koneksi.php";

// Cek apakah ada tanggal yang dikirim
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$tanggal_formatted = date('d-m-Y', strtotime($tanggal));

// Kendaraan Statistics
function createVehicleStats($konek, $tanggal) {
    // Jumlah kendaraan masuk yang belum keluar
    $masukQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM kendaraan 
        WHERE tanggal_input = '$tanggal' AND jam_keluar = '00:00:00'");
    $masukData = mysqli_fetch_assoc($masukQuery);
    $totalMasuk = $masukData['total'];

    // Jumlah kendaraan yang sudah keluar
    $keluarQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM kendaraan 
        WHERE tanggal_input = '$tanggal' AND jam_keluar != '00:00:00'");
    $keluarData = mysqli_fetch_assoc($keluarQuery);
    $totalKeluar = $keluarData['total'];

    // Total kendaraan pada tanggal tersebut
    $totalQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM kendaraan 
        WHERE tanggal_input = '$tanggal'");
    $totalData = mysqli_fetch_assoc($totalQuery);
    $total = $totalData['total'];

    // Jumlah motor dan mobil
    $motorQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM kendaraan 
        WHERE tanggal_input = '$tanggal' AND jenis_kendaraan = 'Motor'");
    $motorData = mysqli_fetch_assoc($motorQuery);
    $totalMotor = $motorData['total'];

    $mobilQuery = mysqli_query($konek, "SELECT COUNT(*) as total FROM kendaraan 
        WHERE tanggal_input = '$tanggal' AND jenis_kendaraan = 'Mobil'");
    $mobilData = mysqli_fetch_assoc($mobilQuery);
    $totalMobil = $mobilData['total'];

    $summary_html = '<table class="summary-table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Kendaraan</td>
                            <td align="center">' . $total . '</td>
                        </tr>
                        <tr>
                            <td>Kendaraan Masuk (Belum Keluar)</td>
                            <td align="center">' . $totalMasuk . '</td>
                        </tr>
                        <tr>
                            <td>Kendaraan Keluar</td>
                            <td align="center">' . $totalKeluar . '</td>
                        </tr>
                        <tr>
                            <td>Total Motor</td>
                            <td align="center">' . $totalMotor . '</td>
                        </tr>
                        <tr>
                            <td>Total Mobil</td>
                            <td align="center">' . $totalMobil . '</td>
                        </tr>
                        <tr class="total-row">
                            <td>Total Keseluruhan</td>
                            <td align="center">' . $total . '</td>
                        </tr>
                    </tbody>
                </table>';
    
    return $summary_html;
}

// CSS
$html = '<style>
            body { 
                font-family: Arial, sans-serif; 
                margin: 20px;
            }
            .page-break {
                page-break-before: always;
            }
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-bottom: 20px; 
            }
            th, td { 
                border: 1px solid black; 
                padding: 8px; 
                text-align: left; 
                font-size: 12px; 
            }
            th { 
                background-color: #f0f0f0; 
                text-align: center; 
            }
            .header { 
                background-color: #e6e6e6;
                padding: 10px;
                margin: 10px 0;
                font-weight: bold;
                border: 1px solid #ddd;
                text-align: center;
                font-size: 16px;
            }
            h1, h3 { 
                text-align: center; 
                color: #333;
            }
            .summary-table { 
                margin: 20px 0;
                width: 70%;
                margin-left: auto;
                margin-right: auto;
            }
            .summary-table th {
                background-color: #4a4a4a;
                color: white;
            }
            .total-row {
                background-color: #f8f8f8;
                font-weight: bold;
            }
            .timestamp {
                text-align: right;
                font-size: 10px;
                color: #666;
                margin-bottom: 20px;
            }
            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                text-align: center;
                font-size: 10px;
                padding: 10px;
                border-top: 1px solid #ddd;
            }
            .status-masuk {
                color: green;
                font-weight: bold;
            }
            .status-keluar {
                color: red;
                font-weight: bold;
            }
        </style>';

// Query untuk mengambil data kendaraan berdasarkan tanggal
$html .= '<h1>LAPORAN DATA KENDARAAN</h1>';
$html .= '<div class="header">Tanggal: ' . $tanggal_formatted . '</div>';
$html .= '<div class="timestamp">Generated on: ' . $current_datetime . '</div>';

// Tambahkan tabel ringkasan
$html .= createVehicleStats($konek, $tanggal);

// Query untuk mendapatkan kendaraan yang masih di dalam (belum keluar)
$kendaraanMasukQuery = "SELECT * FROM kendaraan 
                        WHERE tanggal_input = '$tanggal' 
                        AND jam_keluar = '00:00:00' 
                        ORDER BY jam_masuk";
$kendaraanMasukResult = mysqli_query($konek, $kendaraanMasukQuery);
$totalMasuk = mysqli_num_rows($kendaraanMasukResult);

// Jika ada kendaraan yang masih di dalam
if ($totalMasuk > 0) {
    $html .= '<div class="header">Kendaraan Yang Masih Di Dalam</div>';
    $html .= '<table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">Nama</th>
                        <th width="15%">Jenis</th>
                        <th width="20%">Nomor Polisi</th>
                        <th width="15%">Jam Masuk</th>
                        <th width="20%">Status</th>
                    </tr>
                </thead>
                <tbody>';
    
    $no = 1;
    while ($row = mysqli_fetch_assoc($kendaraanMasukResult)) {
        $html .= '<tr>
                    <td align="center">' . $no++ . '</td>
                    <td>' . $row['nama'] . '</td>
                    <td align="center">' . $row['jenis_kendaraan'] . '</td>
                    <td align="center">' . $row['nopol'] . '</td>
                    <td align="center">' . $row['jam_masuk'] . '</td>
                    <td align="center" class="status-masuk">Masuk</td>
                  </tr>';
    }
    
    $html .= '</tbody></table>';
}

// Query untuk kendaraan yang sudah keluar
$kendaraanKeluarQuery = "SELECT * FROM kendaraan 
                         WHERE tanggal_input = '$tanggal' 
                         AND jam_keluar != '00:00:00' 
                         ORDER BY jam_keluar";
$kendaraanKeluarResult = mysqli_query($konek, $kendaraanKeluarQuery);
$totalKeluar = mysqli_num_rows($kendaraanKeluarResult);

// Jika ada kendaraan yang sudah keluar
if ($totalKeluar > 0) {
    $html .= '<div class="' . ($totalMasuk > 0 ? 'page-break' : '') . '">';
    $html .= '<h1>LAPORAN DATA KENDARAAN</h1>';
    $html .= '<div class="header">Tanggal: ' . $tanggal_formatted . '</div>';
    $html .= '<div class="timestamp">Generated on: ' . $current_datetime . '</div>';
    
    // Tambahkan tabel ringkasan
    if ($totalMasuk > 0) {
        $html .= createVehicleStats($konek, $tanggal);
    }
    
    $html .= '<div class="header">Kendaraan Yang Sudah Keluar</div>';
    $html .= '<table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Nama</th>
                        <th width="15%">Jenis</th>
                        <th width="20%">Nomor Polisi</th>
                        <th width="15%">Jam Masuk</th>
                        <th width="15%">Jam Keluar</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>';
    
    $no = 1;
    while ($row = mysqli_fetch_assoc($kendaraanKeluarResult)) {
        $html .= '<tr>
                    <td align="center">' . $no++ . '</td>
                    <td>' . $row['nama'] . '</td>
                    <td align="center">' . $row['jenis_kendaraan'] . '</td>
                    <td align="center">' . $row['nopol'] . '</td>
                    <td align="center">' . $row['jam_masuk'] . '</td>
                    <td align="center">' . $row['jam_keluar'] . '</td>
                    <td align="center" class="status-keluar">Keluar</td>
                  </tr>';
    }
    
    $html .= '</tbody></table></div>';
}

// Footer
$html .= '<div class="footer">PT. Bekasi Power - Departemen EHS - ' . date('Y') . '</div>';

// Load HTML ke Dompdf
$dompdf->loadHtml($html);

// Set ukuran kertas dan orientasi
$dompdf->setPaper('A4', 'portrait');

// Render HTML menjadi PDF
$dompdf->render();

// Output PDF (1 = download dan 0 = preview di browser)
$dompdf->stream("Laporan_Kendaraan_" . $tanggal . ".pdf", array("Attachment" => 0));
?>