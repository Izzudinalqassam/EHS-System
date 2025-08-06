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
?>

<style>
    .scan-container {
        padding: 20px;
        max-width: 900px;
        margin: 0 auto;
    }
    
    .mode-card {
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease;
        margin-bottom: 20px;
        background: white;
        padding: 20px;
    }
    
    .mode-card:hover {
        transform: translateY(-5px);
    }
    
    .mode-title {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: #2c3e50;
        text-align: center;
        padding: 10px 0;
        border-bottom: 2px solid #eee;
    }
    
    .device-status {
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        margin: 10px;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    
    .status-active {
        background: linear-gradient(145deg, #28a745, #20c997);
    }
    
    .status-exit {
        background: linear-gradient(145deg, #dc3545, #c82333);
    }
    
    .status-inactive {
        background: linear-gradient(145deg, #6c757d, #495057);
    }
    
    .device-status h3 {
        font-size: 1.2rem;
        margin-bottom: 15px;
        color: white;
    }
    
    .device-status p {
        font-size: 1.1rem;
        color: white;
        margin: 0;
    }
    
    .device-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
        color: white;
    }
    
    .scan-notification {
        padding: 20px;
        border-radius: 12px;
        margin-top: 20px;
        text-align: center;
        animation: fadeIn 0.5s ease-in;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .scan-notification.success {
        background: linear-gradient(145deg, #28a745, #20c997);
        color: white;
    }
    
    .scan-notification.warning {
        background: linear-gradient(145deg, #ffc107, #ffb300);
        color: #2c3e50;
    }
    
    .scan-notification.danger {
        background: linear-gradient(145deg, #dc3545, #c82333);
        color: white;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .scan-time {
        font-size: 1.2rem;
        font-weight: bold;
        margin-top: 10px;
    }

    .user-name {
        font-size: 1.4rem;
        margin-bottom: 10px;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<div class="scan-container">
    <div class="mode-card">
        <h2 class="mode-title"><i class="fas fa-id-card"></i> Status Alat Scan</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="device-status <?php 
                    echo $mode_absen1 == 1 ? 'status-active' : 
                         ($mode_absen1 == 2 ? 'status-exit' : 'status-inactive'); 
                ?>">
                    <i class="fas <?php 
                        echo $mode_absen1 == 1 ? 'fa-sign-in-alt' : 
                             ($mode_absen1 == 2 ? 'fa-sign-out-alt' : 'fa-power-off'); 
                    ?> device-icon"></i>
                    <h3>Alat Scan 1</h3>
                    <p><?php 
                        echo $mode_absen1 == 1 ? 'Mode Masuk' : 
                             ($mode_absen1 == 2 ? 'Mode Keluar' : 'Tidak Aktif'); 
                    ?></p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="device-status <?php 
                    echo $mode_absen2 == 1 ? 'status-active' : 
                         ($mode_absen2 == 2 ? 'status-exit' : 'status-inactive'); 
                ?>">
                    <i class="fas <?php 
                        echo $mode_absen2 == 1 ? 'fa-sign-in-alt' : 
                             ($mode_absen2 == 2 ? 'fa-sign-out-alt' : 'fa-power-off'); 
                    ?> device-icon"></i>
                    <h3>Alat Scan 2</h3>
                    <p><?php 
                        echo $mode_absen2 == 1 ? 'Mode Masuk' : 
                             ($mode_absen2 == 2 ? 'Mode Keluar' : 'Tidak Aktif'); 
                    ?></p>
                </div>
            </div>
        </div>
    </div>

<?php
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
                
                echo '<div class="scan-notification success">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <div class="user-name">' . $data_karyawan['nama'] . '</div>
                        <div>Berhasil Update Masuk</div>
                        <div class="scan-time">' . $jam . '</div>
                      </div>';
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
                    
                    echo '<div class="scan-notification success">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <div class="user-name">' . $data_karyawan['nama'] . '</div>
                            <div>Berhasil Update Masuk</div>
                            <div class="scan-time">' . $jam . '</div>
                          </div>';
                } else {
                    // Buat record baru
                    mysqli_query($konek, 
                        "INSERT INTO absensi(nokartu, tanggal, jam_masuk, jam_pulang, status, last_update) 
                        VALUES ('$nokartu', '$tanggal', '$jam', '00:00:00', 'IN', NOW())"
                    );
                    
                    echo '<div class="scan-notification success">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <div class="user-name">' . $data_karyawan['nama'] . '</div>
                            <div>Berhasil Masuk</div>
                            <div class="scan-time">' . $jam . '</div>
                          </div>';
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
                
                // Update record dengan status OUT
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
                
                echo '<div class="scan-notification danger">
                        <i class="fas fa-sign-out-alt fa-3x mb-3"></i>
                        <div class="user-name">' . $data_karyawan['nama'] . '</div>
                        <div>Berhasil Keluar</div>
                        <div class="scan-time">' . $jam . '</div>
                      </div>';
            }
        }
    } else {
        echo '<div class="scan-notification warning">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <div class="user-name">Kartu Tidak Terdaftar</div>
                <div>Silahkan hubungi administrator</div>
              </div>';
    }
    
    // Bersihkan data temporary
    mysqli_query($konek, "DELETE FROM tmprfid WHERE nokartu='$nokartu'");
    mysqli_query($konek, "DELETE FROM tmprfid2 WHERE nokartu='$nokartu'");
}
?>
</div>