<?php
error_reporting(0);
include "koneksi.php";
date_default_timezone_set('Asia/Jakarta');

// Set header to return JSON
header('Content-Type: application/json');

// Initialize response array
$response = array(
    'status' => 'error',
    'message' => ''
);

try {
    // Get form data
    $petugas = $_POST['petugas'];
    $nama_tamu = $_POST['nama_tamu'];
    $nama_perusahaan = $_POST['nama_perusahaan'];
    $jumlah_tamu = $_POST['jumlah_tamu'];
    $keperluan = $_POST['keperluan'];
    $ingin_bertemu = $_POST['ingin_bertemu'];
    $jam_masuk_tamu = date("H:i:s");
    $jam_keluar_tamu = "00:00:00"; // Initialize with 00:00:00
    $tanggal_tamu = date("Y-m-d");
    $nopol = $_POST['nopol'];

    // Get WhatsApp number from karyawan table
    // Get WhatsApp number and name from karyawan table
    $query = "SELECT nama, no_wa FROM karyawan WHERE nama = ?";
    $stmt = mysqli_prepare($konek, $query);
    mysqli_stmt_bind_param($stmt, "s", $ingin_bertemu);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $no_wa = $row['no_wa'];
    $nama_karyawan = $row['nama'];
    

    // Insert into tamu table
    $insertQuery = "INSERT INTO tamu(petugas, nama_tamu, nama_perusahaan, jumlah_tamu, keperluan, ingin_bertemu, jam_masuk_tamu, jam_keluar_tamu, tanggal_tamu, nopol) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($konek, $insertQuery);
    mysqli_stmt_bind_param($stmt, "sssissssss", 
        $petugas, $nama_tamu, $nama_perusahaan, $jumlah_tamu, 
        $keperluan, $ingin_bertemu, $jam_masuk_tamu, $jam_keluar_tamu, 
        $tanggal_tamu, $nopol
    );

    if (mysqli_stmt_execute($stmt)) {
        // Send WhatsApp notification using Fonnte API
        if ($no_wa) {
            $message = "Yth. Bapak/Ibu {$nama_karyawan},\n\n".
          "Dengan hormat,\n".
          "Diberitahukan bahwa saat ini ada tamu atas nama *{$nama_tamu}* dari *{$nama_perusahaan}* yang ingin bertemu dengan Anda dengan keperluan *{$keperluan}*.\n\n".
          "\n".
          "*Pesan ini dikirim secara otomatis. Mohon untuk tidak membalas pesan ini.*\n".
          "©EHS Personal Counting System";
            
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'target' => $no_wa,
                    'message' => $message
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: JruYUTLwwA2E8NxChzno' 
                ),
            ));
            
            $curl_response = curl_exec($curl);
            curl_close($curl);
        }

        $response['status'] = 'success';
        $response['message'] = 'Data berhasil disimpan dan notifikasi WhatsApp telah dikirim';
    } else {
        throw new Exception("Gagal menyimpan data tamu");
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);