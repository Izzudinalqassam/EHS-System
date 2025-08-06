<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "koneksi.php";

// Set timezone dan waktu
date_default_timezone_set('Asia/Jakarta');
$tanggal = date('Y-m-d');
$jam = date('H:i:s');

// Baca mode dari kedua alat
$sql = mysqli_query($konek, "SELECT * FROM status");
$data = mysqli_fetch_array($sql);
$mode_absen1 = $data['mode'];

$sql2 = mysqli_query($konek, "SELECT * FROM status2");
$data2 = mysqli_fetch_array($sql2);
$mode_absen2 = $data2['mode'];

// Baca data kartu dari kedua alat
$baca_kartu1 = mysqli_query($konek, "SELECT * FROM tmprfid");
$nokartu1 = (mysqli_num_rows($baca_kartu1) > 0) ? mysqli_fetch_array($baca_kartu1)['nokartu'] : "";

$baca_kartu2 = mysqli_query($konek, "SELECT * FROM tmprfid2");
$nokartu2 = (mysqli_num_rows($baca_kartu2) > 0) ? mysqli_fetch_array($baca_kartu2)['nokartu'] : "";

$alat_aktif = ($mode_absen1 != 0 && $nokartu1 != "") ? 1 : (($mode_absen2 != 0 && $nokartu2 != "") ? 2 : 0);
$nokartu = ($alat_aktif == 1) ? $nokartu1 : $nokartu2;
$mode = ($alat_aktif == 1) ? $mode_absen1 : $mode_absen2;

if ($nokartu != "") {
    $cari_karyawan = mysqli_query($konek, "SELECT * FROM karyawan WHERE nokartu='$nokartu'");
    
    if (mysqli_num_rows($cari_karyawan) > 0) {
        $data_karyawan = mysqli_fetch_array($cari_karyawan);
        
        // Mode Tap In
        if ($mode == 1) {
            // Cek apakah ada record dengan status IN yang belum di tap out
            $cari_record_belum_out = mysqli_query($konek, 
                "SELECT * FROM absensi 
                WHERE nokartu='$nokartu' 
                AND status='IN'
                ORDER BY tanggal DESC, jam_masuk DESC 
                LIMIT 1"
            );
            
            if (mysqli_num_rows($cari_record_belum_out) > 0) {
                // Update record yang masih IN dengan jam masuk baru
                $data_record = mysqli_fetch_array($cari_record_belum_out);
                mysqli_query($konek, 
                    "UPDATE absensi 
                    SET jam_masuk='$jam',
                        tanggal='$tanggal',
                        last_update=NOW()
                    WHERE id='" . $data_record['id'] . "'"
                );
            } else {
                // Cek record hari ini
                $cari_record_hari_ini = mysqli_query($konek, 
                    "SELECT * FROM absensi 
                    WHERE nokartu='$nokartu' 
                    AND tanggal='$tanggal'
                    ORDER BY jam_masuk DESC 
                    LIMIT 1"
                );
                
                if (mysqli_num_rows($cari_record_hari_ini) > 0) {
                    // Update record hari ini
                    $data_record = mysqli_fetch_array($cari_record_hari_ini);
                    mysqli_query($konek, 
                        "UPDATE absensi 
                        SET jam_masuk='$jam',
                            jam_pulang='00:00:00',
                            status='IN',
                            last_update=NOW()
                        WHERE id='" . $data_record['id'] . "'"
                    );
                } else {
                    // Buat record baru
                    mysqli_query($konek, 
                        "INSERT INTO absensi(nokartu, tanggal, jam_masuk, jam_pulang, status, last_update) 
                        VALUES ('$nokartu', '$tanggal', '$jam', '00:00:00', 'IN', NOW())"
                    );
                }
            }

            // Selalu buat record baru di riwayat saat tap in
            mysqli_query($konek, 
                "INSERT INTO riwayat(nokartu, tanggal, jam_masuk, jam_pulang) 
                VALUES ('$nokartu', '$tanggal', '$jam', '00:00:00')"
            );
        }
        // Mode Tap Out
        else if ($mode == 2) {
            // Cari record yang belum tap out (status IN)
            $cari_record = mysqli_query($konek, 
                "SELECT * FROM absensi 
                WHERE nokartu='$nokartu' 
                AND status='IN'
                ORDER BY tanggal DESC, jam_masuk DESC 
                LIMIT 1"
            );
            
            if (mysqli_num_rows($cari_record) > 0) {
                $data_record = mysqli_fetch_array($cari_record);
                
                // Update record dengan status OUT dan tanggal baru
                mysqli_query($konek, 
                    "UPDATE absensi 
                    SET jam_pulang='$jam',
                        tanggal='$tanggal',
                        status='OUT',
                        last_update=NOW()
                    WHERE id='" . $data_record['id'] . "'"
                );
                
                // Update riwayat terakhir yang belum ada jam pulang
                mysqli_query($konek,
                    "UPDATE riwayat 
                    SET jam_pulang='$jam'
                    WHERE nokartu='$nokartu' 
                    AND jam_pulang='00:00:00'
                    ORDER BY id DESC 
                    LIMIT 1"
                );
            }
        }
    }
    
    // Bersihkan data temporary
    mysqli_query($konek, "DELETE FROM tmprfid WHERE nokartu='$nokartu'");
    mysqli_query($konek, "DELETE FROM tmprfid2 WHERE nokartu='$nokartu'");
}
?>