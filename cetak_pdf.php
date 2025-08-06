<?php

require 'libraries/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Set timezone dan tambahkan offset 7 jam untuk WIB
date_default_timezone_set('Asia/Jakarta');
$current_datetime = date('d-m-Y H:i:s');

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);
include "koneksi.php";

// Get filter values from form submission
$filter_date_start = isset($_POST['filter-date-start']) ? $_POST['filter-date-start'] : date('Y-m-d');
$filter_date_end = isset($_POST['filter-date-end']) ? $_POST['filter-date-end'] : date('Y-m-d');
$filter_department = isset($_POST['filter-department']) ? $_POST['filter-department'] : '';

// Format dates for display
$date_start_display = date('d-m-Y', strtotime($filter_date_start));
$date_end_display = date('d-m-Y', strtotime($filter_date_end));
$period_text = ($filter_date_start == $filter_date_end) ? $date_start_display : $date_start_display . ' s/d ' . $date_end_display;

// Buat variabel waktu yang akan digunakan
$current_datetime = date('d-m-Y H:i:s');

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
        </style>';

// Build the main SQL query with date range and department filtering
$sql = "SELECT a.*, b.NIK, b.nama, b.departmen 
        FROM absensi a 
        JOIN karyawan b ON a.nokartu = b.nokartu
        WHERE (
            (a.tanggal BETWEEN ? AND ?) -- date range filter
            OR (a.jam_pulang = '00:00:00' AND a.tanggal < ? AND a.tanggal >= ?) -- belum tap out dalam range
            OR (DATE(a.last_update) BETWEEN ? AND ? AND a.jam_pulang != '00:00:00') -- tap out dalam range
        )
        AND b.departmen NOT IN ('tamu')";

// Add department filter if selected
$params = [$filter_date_start, $filter_date_end, $filter_date_end, $filter_date_start, $filter_date_start, $filter_date_end];
$param_types = "ssssss";

if (!empty($filter_department)) {
    $sql .= " AND b.departmen = ?";
    $params[] = $filter_department;
    $param_types .= "s";
}

$sql .= " ORDER BY b.departmen, b.nama, a.tanggal DESC, a.last_update DESC";

// Prepare and execute query
$stmt = mysqli_prepare($konek, $sql);
mysqli_stmt_bind_param($stmt, $param_types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Generate PDF content
$html .= '<h1>LAPORAN RIWAYAT ABSENSI</h1>';
$html .= '<div class="department-header">Periode: ' . $period_text . '</div>';
if (!empty($filter_department)) {
    $html .= '<div class="department-header">Departemen: ' . $filter_department . '</div>';
}
$html .= '<div class="timestamp">Generated on: ' . $current_datetime . '</div>';

// Create attendance table
$html .= '<table>
            <thead><tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="12%">NIK</th>
                <th width="25%">Nama</th>
                <th width="15%">Departemen</th>
                <th width="12%">Jam Masuk</th>
                <th width="12%">Jam Keluar</th>
                <th width="7%">Status</th>
            </tr></thead><tbody>';

if ($result && mysqli_num_rows($result) > 0) {
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $status_color = ($row['status'] == 'IN') ? 'color: green;' : 'color: red;';
        $jam_keluar = ($row['jam_pulang'] == '00:00:00') ? '-' : $row['jam_pulang'];
        
        $html .= '<tr>
                    <td align="center">' . $no++ . '</td>
                    <td align="center">' . date('d-m-Y', strtotime($row['tanggal'])) . '</td>
                    <td>' . $row['NIK'] . '</td>
                    <td>' . $row['nama'] . '</td>
                    <td>' . $row['departmen'] . '</td>
                    <td align="center" style="color: green; font-weight: bold;">' . $row['jam_masuk'] . '</td>
                    <td align="center" style="color: red; font-weight: bold;">' . $jam_keluar . '</td>
                    <td align="center" style="' . $status_color . ' font-weight: bold;">' . $row['status'] . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="8" align="center">Tidak ada data absensi untuk periode yang dipilih</td></tr>';
}

$html .= '</tbody></table>';

// Footer
$html .= '<div class="footer">PT. Bekasi Power - Departemen EHS - ' . date('Y') . '</div>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Generate filename with date range
$filename = "riwayat_absensi_" . str_replace('-', '', $filter_date_start);
if ($filter_date_start != $filter_date_end) {
    $filename .= "_" . str_replace('-', '', $filter_date_end);
}
if (!empty($filter_department)) {
    $filename .= "_" . str_replace(' ', '_', $filter_department);
}
$filename .= ".pdf";

$dompdf->stream($filename, array("Attachment" => 0));
?>