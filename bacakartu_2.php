<?php
error_reporting(0);
include "koneksi.php";

// Baca tabel status untuk mode absensi alat 1
$sql = mysqli_query($konek, "SELECT * FROM status");
$data = mysqli_fetch_array($sql);
$mode_absen1 = $data['mode']; // Mode untuk alat 1

// Baca tabel status2 untuk mode absensi alat 2
$sql2 = mysqli_query($konek, "SELECT * FROM status2");
$data2 = mysqli_fetch_array($sql2);
$mode_absen2 = $data2['mode']; // Mode untuk alat 2

// Baca tabel tmprfid untuk alat 1
$baca_kartu1 = mysqli_query($konek, "SELECT * FROM tmprfid");
$nokartu1 = (mysqli_num_rows($baca_kartu1) > 0) ? mysqli_fetch_array($baca_kartu1)['nokartu'] : "";

// Baca tabel tmprfid2 untuk alat 2
$baca_kartu2 = mysqli_query($konek, "SELECT * FROM tmprfid2");
$nokartu2 = (mysqli_num_rows($baca_kartu2) > 0) ? mysqli_fetch_array($baca_kartu2)['nokartu'] : "";

// Cek apakah salah satu alat aktif
$alat_aktif = ($mode_absen1 != 0 && $nokartu1 != "") ? 1 : (($mode_absen2 != 0 && $nokartu2 != "") ? 2 : 0);

// Tentukan nomor kartu berdasarkan alat aktif
$nokartu = ($alat_aktif == 1) ? $nokartu1 : $nokartu2;

?>

<div class="container-fluid" style="text-align: center;">
    <?php if ($nokartu == "") { ?>
        <h3>Silahkan Tempelkan Kartu RFID Anda</h3>
        <img src="images/rfid.png" style="width: 200px"> <br>
        <img src="images/animasi2.gif">
        <h4>Alat 1: <?php echo ($mode_absen1 == 1) ? "Masuk" : ($mode_absen1 == 2 ? "Keluar" : "Tidak Aktif"); ?></h4>
        <h4>Alat 2: <?php echo ($mode_absen2 == 1) ? "Masuk" : ($mode_absen2 == 2 ? "Keluar" : "Tidak Aktif"); ?></h4>
    <?php } else {
        // Cek apakah nomor kartu terdaftar di tabel karyawan
        $cari_karyawan = mysqli_query($konek, "SELECT * FROM karyawan WHERE nokartu='$nokartu'");
        $jumlah_data = mysqli_num_rows($cari_karyawan);

        if ($jumlah_data == 0) {
            mysqli_query($konek, "DELETE FROM tmprfid WHERE nokartu='$nokartu'");
            mysqli_query($konek, "DELETE FROM tmprfid2 WHERE nokartu='$nokartu'");
            echo "<h1>Maaf! Kartu Tidak Dikenali</h1>";
        } else {
            // Ambil nama karyawan
            $data_karyawan = mysqli_fetch_array($cari_karyawan);
            $nama = $data_karyawan['nama'];

            // Tanggal dan jam saat ini
            date_default_timezone_set('Asia/Jakarta');
            $tanggal = date('Y-m-d');
            $jam = date('H:i:s');

            // Cek apakah kartu sudah absen pada tanggal ini
            $cari_absen = mysqli_query($konek, "SELECT * FROM absensi WHERE nokartu='$nokartu' AND tanggal='$tanggal'");
            $jumlah_absen = mysqli_num_rows($cari_absen);

            if ($jumlah_absen == 0) {
                // Jika belum ada data absen, insert data baru sesuai mode absen
                echo "<h1>Selamat Datang <br> $nama</h1>";
                if ($mode_absen1 == 1 || $mode_absen2 == 1) { // Mode masuk
                    mysqli_query($konek, "INSERT INTO absensi(nokartu, tanggal, jam_masuk) VALUES ('$nokartu', '$tanggal', '$jam')");
                    mysqli_query($konek, "INSERT INTO riwayat(nokartu, tanggal, jam_masuk) VALUES ('$nokartu', '$tanggal', '$jam')");
                    echo "<h3>Anda telah melakukan absensi masuk.</h3>";
                } elseif ($mode_absen1 == 2 || $mode_absen2 == 2) { // Mode keluar
                    mysqli_query($konek, "INSERT INTO absensi(nokartu, tanggal, jam_pulang) VALUES ('$nokartu', '$tanggal', '$jam')");
                    mysqli_query($konek, "INSERT INTO riwayat(nokartu, tanggal, jam_pulang) VALUES ('$nokartu', '$tanggal', '$jam')");
                    echo "<h3>Anda telah melakukan absensi keluar.</h3>";
                }
            } else {
                // Update data absensi berdasarkan mode absen
                $data_absen = mysqli_fetch_array($cari_absen);
                $jam_masuk = $data_absen['jam_masuk'];
                $jam_pulang = $data_absen['jam_pulang'];

                if ($alat_aktif == 1) { // Jika alat 1 aktif
                    if ($mode_absen1 == 1) { // Mode masuk
                        // Update jam_masuk jika jam_masuk sudah ada
                        mysqli_query($konek, "UPDATE absensi SET jam_masuk='$jam' WHERE nokartu='$nokartu' AND tanggal='$tanggal'");
                        echo "<h1>Welcome Back (Alat 1) <br> $nama</h1>";
                        echo "<h3>Jam masuk telah diperbarui.</h3>";
                    } elseif ($mode_absen1 == 2) { // Mode keluar
                        // Update jam_pulang jika jam_masuk sudah ada
                        if ($jam_masuk != null) {
                            mysqli_query($konek, "UPDATE absensi SET jam_pulang='$jam' WHERE nokartu='$nokartu' AND tanggal='$tanggal'");
                            echo "<h1>Selamat Jalan (Alat 1) <br> $nama</h1>";
                            echo "<h3>Jam keluar telah diperbarui.</h3>";
                        } else {
                            echo "<h1>Maaf, Anda belum melakukan absensi masuk.</h1>";
                        }
                    }
                } elseif ($alat_aktif == 2) { // Jika alat 2 aktif
                    if ($mode_absen2 == 1) { // Mode masuk
                        // Update jam_masuk jika jam_masuk sudah ada
                        mysqli_query($konek, "UPDATE absensi SET jam_masuk='$jam' WHERE nokartu='$nokartu' AND tanggal='$tanggal'");
                        echo "<h1>Welcome Back (Alat 2) <br> $nama</h1>";
                        echo "<h3>Jam masuk telah diperbarui.</h3>";
                    } elseif ($mode_absen2 == 2) { // Mode keluar
                        // Update jam_pulang jika jam_masuk sudah ada
                        if ($jam_masuk != null) {
                            mysqli_query($konek, "UPDATE absensi SET jam_pulang='$jam' WHERE nokartu='$nokartu' AND tanggal='$tanggal'");
                            echo "<h1>Selamat Jalan (Alat 2) <br> $nama</h1>";
                            echo "<h3>Jam keluar telah diperbarui.</h3>";
                        } else {
                            echo "<h1>Maaf, Anda belum melakukan absensi masuk.</h1>";
                        }
                    }
                }

                // Cek apakah ada entri di tabel riwayat yang belum punya jam_pulang (masih kosong)
                $cek_riwayat = mysqli_query($konek, "SELECT * FROM riwayat WHERE nokartu='$nokartu' AND jam_pulang='00:00:00' ORDER BY id DESC LIMIT 1");

                if (mysqli_num_rows($cek_riwayat) > 0) {
                    // Update jam_pulang pada entri terakhir yang belum punya jam_pulang
                    mysqli_query($konek, "UPDATE riwayat SET jam_pulang='$jam' WHERE nokartu='$nokartu' AND jam_pulang='00:00:00'");
                    echo "<h2>Debug: Jam keluar diupdate di riwayat</h2>";
                } else {
                    // Jika tidak ada entri sebelumnya dengan jam_pulang kosong, buat entri baru
                    if ($alat_aktif == 1) {
                        // Insert ke riwayat untuk alat 1
                        if ($mode_absen1 == 1) {
                            mysqli_query($konek, "INSERT INTO riwayat(nokartu, tanggal, jam_masuk) VALUES ('$nokartu', '$tanggal', '$jam')");
                            echo "<h2>Debug: Jam masuk diinsert ke riwayat untuk alat 1</h2>";
                        } elseif ($mode_absen1 == 2) {
                            mysqli_query($konek, "INSERT INTO riwayat(nokartu, tanggal, jam_pulang) VALUES ('$nokartu', '$tanggal', '$jam')");
                            echo "<h2>Debug: Jam keluar diinsert ke riwayat untuk alat 1</h2>";
                        }
                    } elseif ($alat_aktif == 2) {
                        // Insert ke riwayat untuk alat 2
                        if ($mode_absen2 == 1) {
                            mysqli_query($konek, "INSERT INTO riwayat(nokartu, tanggal, jam_masuk) VALUES ('$nokartu', '$tanggal', '$jam')");
                            echo "<h2>Debug: Jam masuk diinsert ke riwayat untuk alat 2</h2>";
                        } elseif ($mode_absen2 == 2) {
                            mysqli_query($konek, "INSERT INTO riwayat(nokartu, tanggal, jam_pulang) VALUES ('$nokartu', '$tanggal', '$jam')");
                            echo "<h2>Debug: Jam keluar diinsert ke riwayat untuk alat 2</h2>";
                        }
                    }
                }
            }
            // Hapus data dari tabel tmprfid dan tmprfid2 setelah proses selesai
            mysqli_query($konek, "DELETE FROM tmprfid WHERE nokartu='$nokartu'");
            mysqli_query($konek, "DELETE FROM tmprfid2 WHERE nokartu='$nokartu'");
        }
    }
    ?>
</div>
