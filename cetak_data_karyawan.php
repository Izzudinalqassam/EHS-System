<?php
require 'libraries/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Set timezone untuk WIB
date_default_timezone_set('Asia/Jakarta');
$current_datetime = date('d-m-Y H:i:s');

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);
include "koneksi.php";

// Fungsi untuk membuat tabel ringkasan per departemen
function createDepartmentSummary($konek) {
    $summary_html = '<table class="summary-table">
                    <thead>
                        <tr>
                            <th>Departemen</th>
                            <th>Jumlah Karyawan</th>
                        </tr>
                    </thead>
                    <tbody>';

    // Query untuk menghitung jumlah per departemen
    $dept_count_query = "SELECT departmen, COUNT(*) as total 
                        FROM karyawan 
                        WHERE departmen NOT IN ('tamu', 'Magang') 
                        AND departmen != ''
                        GROUP BY departmen
                        ORDER BY departmen";
    
    $dept_result = mysqli_query($konek, $dept_count_query);
    
    // Total karyawan (non tamu & non magang)
    $total_karyawan = 0;
    
    // Tampilkan jumlah per departemen
    while($dept = mysqli_fetch_assoc($dept_result)) {
        $summary_html .= "<tr>
                            <td>{$dept['departmen']}</td>
                            <td align='center'>{$dept['total']}</td>
                         </tr>";
        $total_karyawan += $dept['total'];
    }

    // Hitung magang
    $magang_query = "SELECT COUNT(*) as total FROM karyawan WHERE departmen = 'Magang'";
    $magang_result = mysqli_query($konek, $magang_query);
    $magang_count = mysqli_fetch_assoc($magang_result)['total'];

    // Tambahkan magang dan total ke tabel
    $summary_html .= "<tr><td>Magang</td><td align='center'>{$magang_count}</td></tr>";
    $summary_html .= "<tr class='total-row'>
                        <td>Total Keseluruhan</td>
                        <td align='center'>" . ($total_karyawan + $magang_count) . "</td>
                     </tr>";

    $summary_html .= '</tbody></table>';
    
    return $summary_html;
}

// Style
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
            .department-header { 
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
                width: 50%;
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
            .logo {
                text-align: center;
                margin-bottom: 20px;
            }
            .logo img {
                height: 60px;
            }
        </style>';

// Header
          
$html .= '<h1>DATA KARYAWAN PT. BEKASI POWER</h1>';
$html .= '<div class="timestamp">Tanggal Cetak: ' . $current_datetime . '</div>';

// Tambahkan tabel ringkasan
$html .= createDepartmentSummary($konek);

// Tabel Data Karyawan
$html .= '<h3>Daftar Karyawan</h3>';
$html .= '<table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">NIK</th>
                    <th width="25%">Nama</th>
                    <th width="20%">Departemen</th>
                    <th width="20%">No. WhatsApp</th>
                    <th width="20%">Nopol Kendaraan</th>
                </tr>
            </thead>
            <tbody>';

// Query untuk karyawan (non-tamu & non-magang)
$karyawan_query = "SELECT * FROM karyawan 
                  WHERE departmen NOT IN ('tamu', 'Magang') 
                  AND departmen != ''
                  ORDER BY departmen, nama";

$karyawan_result = mysqli_query($konek, $karyawan_query);

$no = 1;
$current_dept = '';

while ($row = mysqli_fetch_assoc($karyawan_result)) {
    // Tambahkan header departemen jika berganti departemen
    if ($current_dept != $row['departmen']) {
        $current_dept = $row['departmen'];
        $html .= '<tr>
                    <td colspan="6" class="department-header">Departemen: ' . $current_dept . '</td>
                  </tr>';
    }
    
    $html .= '<tr>
                <td align="center">' . $no++ . '</td>
                <td>' . $row['NIK'] . '</td>
                <td>' . $row['nama'] . '</td>
                <td>' . $row['departmen'] . '</td>
                <td>' . $row['no_wa'] . '</td>
                <td>' . $row['nopol'] . '</td>
              </tr>';
}

$html .= '</tbody></table>';

// Halaman baru untuk data magang jika ada
$magang_query = "SELECT * FROM karyawan WHERE departmen = 'Magang' ORDER BY nama";
$magang_result = mysqli_query($konek, $magang_query);

if (mysqli_num_rows($magang_result) > 0) {
    $html .= '<div class="page-break">';
    $html .= '<h1>DATA MAGANG PT. BEKASI POWER</h1>';
    $html .= '<div class="timestamp">Tanggal Cetak: ' . $current_datetime . '</div>';
    
    // Tambahkan tabel ringkasan
    $html .= createDepartmentSummary($konek);
    
    $html .= '<h3>Daftar Magang</h3>';
    $html .= '<table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">NIK</th>
                        <th width="35%">Nama</th>
                        <th width="25%">No. WhatsApp</th>
                        <th width="25%">Nopol Kendaraan</th>
                    </tr>
                </thead>
                <tbody>';
    
    $no = 1;
    while ($row = mysqli_fetch_assoc($magang_result)) {
        $html .= '<tr>
                    <td align="center">' . $no++ . '</td>
                    <td>' . $row['NIK'] . '</td>
                    <td>' . $row['nama'] . '</td>
                    <td>' . $row['no_wa'] . '</td>
                    <td>' . $row['nopol'] . '</td>
                  </tr>';
    }
    
    $html .= '</tbody></table>';
    $html .= '</div>';
}

// Footer
$html .= '<div class="footer">PT. Bekasi Power - Departemen EHS - ' . date('Y') . '</div>';

// Generate PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("data_karyawan_bekasi_power.pdf", array("Attachment" => 0));
?>