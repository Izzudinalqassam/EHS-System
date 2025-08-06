<?php
require 'libraries/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Koneksi ke database
include "koneksi.php";

// Ambil NIK dan tanggal filter dari query string
$nik = isset($_GET['nik']) ? $_GET['nik'] : '';
$filter_tanggal = isset($_GET['filter-date']) ? $_GET['filter-date'] : '';

if (empty($nik)) {
    die("NIK tidak ditemukan.");
}

// Query untuk mengambil data karyawan - using prepared statement for security
$stmt = $konek->prepare("SELECT * FROM karyawan WHERE NIK = ?");
$stmt->bind_param("s", $nik);
$stmt->execute();
$result = $stmt->get_result();
$karyawan = $result->fetch_assoc();

if (!$karyawan) {
    die("Karyawan dengan NIK tersebut tidak ditemukan.");
}

// Query untuk mengambil histori riwayat, dengan filter tanggal jika ada
$histori_query = "SELECT * FROM riwayat WHERE nokartu = ?";
$params = array($karyawan['nokartu']);
$types = "s";

if ($filter_tanggal) {
    $histori_query .= " AND tanggal = ?";
    $params[] = $filter_tanggal;
    $types .= "s";
}

$histori_query .= " ORDER BY tanggal, jam_masuk";

$stmt = $konek->prepare($histori_query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$histori_result = $stmt->get_result();

// Variabel total masuk dan keluar
$masuktanggal = 0;
$keluartanggal = 0;

// Perhitungan total masuk dan keluar berdasarkan histori absensi
$rows = [];
while ($row = $histori_result->fetch_assoc()) {
    $rows[] = $row;
    if (!empty($row['jam_masuk']) && $row['jam_masuk'] != '00:00:00') {
        $masuktanggal++;
    }
    if (!empty($row['jam_pulang']) && $row['jam_pulang'] != '00:00:00') {
        $keluartanggal++;
    }
}

// Buat instance Dompdf dengan opsi
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);

// Buat HTML untuk PDF - REMOVED WATERMARK BACKGROUND IMAGE
$html = '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { 
            font-family: Arial, sans-serif;
            margin: 20px; 
            padding: 0; 
        }
        h1, h2, h3 { 
            text-align: center; 
            margin-bottom: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
        }
        th, td { 
            border: 1px solid black; 
            padding: 8px; 
            text-align: center; 
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .summary-box {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            width: 45%;
        }
        .footer {
            text-align: right;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>';

// Header with text only (no image)
$html .= '<div class="header">
            <h1>PT.BEKASI POWER</h1>
            <h2>Detail Profil Karyawan</h2>
          </div>';

if ($karyawan) {
    // Informasi karyawan
    $html .= '<div class="info">';
    $html .= "<p><strong>NIK:</strong> " . htmlspecialchars($karyawan['NIK']) . "</p>";
    $html .= "<p><strong>Nama:</strong> " . htmlspecialchars($karyawan['nama']) . "</p>";
    $html .= "<p><strong>Nokartu:</strong> " . htmlspecialchars($karyawan['nokartu']) . "</p>";
    $html .= "<p><strong>Departmen:</strong> " . htmlspecialchars($karyawan['departmen']) . "</p>";
    if ($filter_tanggal) {
        $html .= "<p><strong>Filter Tanggal:</strong> " . date('d-m-Y', strtotime($filter_tanggal)) . "</p>";
    }
    $html .= '</div>';

    // Summary section
    $html .= '<table style="width: 100%; margin-bottom: 20px;">';
    $html .= '<tr>';
    $html .= '<td style="width: 50%; background-color: #d4edda; border: 1px solid #c3e6cb; text-align: center; padding: 10px;">';
    $html .= '<h3>Total Masuk ' . ($filter_tanggal ? "Tanggal " . date('d-m-Y', strtotime($filter_tanggal)) : "Keseluruhan") . '</h3>';
    $html .= '<h2>' . $masuktanggal . '</h2>';
    $html .= '</td>';
    
    $html .= '<td style="width: 50%; background-color: #f8d7da; border: 1px solid #f5c6cb; text-align: center; padding: 10px;">';
    $html .= '<h3>Total Keluar ' . ($filter_tanggal ? "Tanggal " . date('d-m-Y', strtotime($filter_tanggal)) : "Keseluruhan") . '</h3>';
    $html .= '<h2>' . $keluartanggal . '</h2>';
    $html .= '</td>';
    $html .= '</tr>';
    $html .= '</table>';

    // Tabel histori absensi
    $html .= '<h3>Riwayat Absensi</h3>';
    $html .= '<table>';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>No</th>';
    $html .= '<th>Tanggal</th>';
    $html .= '<th>Jam Masuk</th>';
    $html .= '<th>Jam Pulang</th>';
    $html .= '<th>Lama Waktu IN</th>';
    $html .= '<th>Lama Waktu OUT</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    
    // Variabel untuk menyimpan baris sebelumnya
    $prev_row = null;
    $no = 1;
    
    foreach ($rows as $row) {
        $html .= '<tr>';
        $html .= "<td>" . $no . "</td>";
        $html .= "<td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
        $html .= "<td>" . htmlspecialchars($row['jam_masuk']) . "</td>";
        $html .= "<td>" . htmlspecialchars($row['jam_pulang']) . "</td>";
        
        // Lama Waktu IN: dari jam masuk ke jam pulang pada hari yang sama
        if (!empty($row['jam_masuk']) && !empty($row['jam_pulang']) && 
            $row['jam_masuk'] != '00:00:00' && $row['jam_pulang'] != '00:00:00') {
            
            // Hitung selisih waktu
            $stmt = $konek->prepare("SELECT SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(?, ?))) AS lama_waktu_in");
            $stmt->bind_param("ss", $row['jam_pulang'], $row['jam_masuk']);
            $stmt->execute();
            $lama_waktu_result = $stmt->get_result();
            $lama_waktu_row = $lama_waktu_result->fetch_assoc();
            
            $html .= "<td style='color: green;'>" . 
                    htmlspecialchars($lama_waktu_row['lama_waktu_in']) . "</td>";
        } else {
            $html .= "<td>N/A</td>";
        }
        
        // Lama Waktu OUT: dari jam pulang sebelumnya ke jam masuk hari ini
        if ($prev_row && !empty($prev_row['jam_pulang']) && !empty($row['jam_masuk']) && 
            $prev_row['jam_pulang'] != '00:00:00' && $row['jam_masuk'] != '00:00:00') {
            
            // Hitung selisih waktu
            $stmt = $konek->prepare("SELECT SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(?, ?))) AS lama_waktu_out");
            $stmt->bind_param("ss", $row['jam_masuk'], $prev_row['jam_pulang']);
            $stmt->execute();
            $lama_out_result = $stmt->get_result();
            $lama_out_row = $lama_out_result->fetch_assoc();
            
            $html .= "<td style='color: red;'>" . 
                    htmlspecialchars($lama_out_row['lama_waktu_out']) . "</td>";
        } else {
            $html .= "<td>N/A</td>";
        }
        
        $html .= '</tr>';
        $prev_row = $row;
        $no++;
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    
    // Footer with date
    $html .= '<div class="footer">';
    $html .= '<p>Dicetak pada: ' . date('d-m-Y H:i:s') . '</p>';
    $html .= '</div>';
    
} else {
    $html .= '<p>Data karyawan tidak ditemukan.</p>';
}

$html .= '</body></html>';

// Load HTML ke Dompdf
$dompdf->loadHtml($html);

// Set ukuran kertas dan orientasi
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output file PDF
$dompdf->stream("detail_profil_{$nik}_" . date('Y-m-d_H-i-s') . ".pdf", ["Attachment" => false]);
?>