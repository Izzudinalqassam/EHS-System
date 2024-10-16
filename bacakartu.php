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

if ($nokartu != "") {
    // Cek apakah nomor kartu terdaftar di tabel karyawan
    $cari_karyawan = mysqli_query($konek, "SELECT * FROM karyawan WHERE nokartu='$nokartu'");
    $jumlah_data = mysqli_num_rows($cari_karyawan);

    if ($jumlah_data == 0) {
        mysqli_query($konek, "DELETE FROM tmprfid WHERE nokartu='$nokartu'");
        mysqli_query($konek, "DELETE FROM tmprfid2 WHERE nokartu='$nokartu'");
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
            // Insert data baru sesuai mode absen
            if ($mode_absen1 == 1 || $mode_absen2 == 1) { // Mode masuk
                mysqli_query($konek, "INSERT INTO absensi(nokartu, tanggal, jam_masuk) VALUES ('$nokartu', '$tanggal', '$jam')");
                mysqli_query($konek, "INSERT INTO riwayat(nokartu, tanggal, jam_masuk) VALUES ('$nokartu', '$tanggal', '$jam')");
            } elseif ($mode_absen1 == 2 || $mode_absen2 == 2) { // Mode keluar
                mysqli_query($konek, "INSERT INTO absensi(nokartu, tanggal, jam_pulang) VALUES ('$nokartu', '$tanggal', '$jam')");
                mysqli_query($konek, "INSERT INTO riwayat(nokartu, tanggal, jam_pulang) VALUES ('$nokartu', '$tanggal', '$jam')");
            }
        } else {
            // Update data absensi berdasarkan mode absen
            $data_absen = mysqli_fetch_array($cari_absen);
            $jam_masuk = $data_absen['jam_masuk'];
            $jam_pulang = $data_absen['jam_pulang'];

            if ($alat_aktif == 1) {
                if ($mode_absen1 == 1) {
                    // Update jam_masuk
                    mysqli_query($konek, "UPDATE absensi SET jam_masuk='$jam' WHERE nokartu='$nokartu' AND tanggal='$tanggal'");
                } elseif ($mode_absen1 == 2 && $jam_masuk != null) {
                    // Update jam_pulang
                    mysqli_query($konek, "UPDATE absensi SET jam_pulang='$jam' WHERE nokartu='$nokartu' AND tanggal='$tanggal'");
                }
            } elseif ($alat_aktif == 2) {
                if ($mode_absen2 == 1) {
                    // Update jam_masuk
                    mysqli_query($konek, "UPDATE absensi SET jam_masuk='$jam' WHERE nokartu='$nokartu' AND tanggal='$tanggal'");
                } elseif ($mode_absen2 == 2 && $jam_masuk != null) {
                    // Update jam_pulang
                    mysqli_query($konek, "UPDATE absensi SET jam_pulang='$jam' WHERE nokartu='$nokartu' AND tanggal='$tanggal'");
                }
            }

            // Update atau insert data riwayat
            $cek_riwayat = mysqli_query($konek, "SELECT * FROM riwayat WHERE nokartu='$nokartu' AND jam_pulang='00:00:00' ORDER BY id DESC LIMIT 1");

            if (mysqli_num_rows($cek_riwayat) > 0) {
                // Update jam_pulang pada riwayat
                mysqli_query($konek, "UPDATE riwayat SET jam_pulang='$jam' WHERE nokartu='$nokartu' AND jam_pulang='00:00:00'");
            } else {
                // Insert ke riwayat
                if ($alat_aktif == 1) {
                    if ($mode_absen1 == 1) {
                        mysqli_query($konek, "INSERT INTO riwayat(nokartu, tanggal, jam_masuk) VALUES ('$nokartu', '$tanggal', '$jam')");
                    } elseif ($mode_absen1 == 2) {
                        mysqli_query($konek, "INSERT INTO riwayat(nokartu, tanggal, jam_pulang) VALUES ('$nokartu', '$tanggal', '$jam')");
                    }
                } elseif ($alat_aktif == 2) {
                    if ($mode_absen2 == 1) {
                        mysqli_query($konek, "INSERT INTO riwayat(nokartu, tanggal, jam_masuk) VALUES ('$nokartu', '$tanggal', '$jam')");
                    } elseif ($mode_absen2 == 2) {
                        mysqli_query($konek, "INSERT INTO riwayat(nokartu, tanggal, jam_pulang) VALUES ('$nokartu', '$tanggal', '$jam')");
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
