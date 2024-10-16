<?php  
	error_reporting(0);
	include "koneksi.php";

	// Baca mode absensi terakhir
	$mode = mysqli_query($konek, "SELECT * FROM status2");
	$data_mode = mysqli_fetch_array($mode);
	$mode_absen = $data_mode['mode'];

	// Cek apakah ada permintaan untuk mengubah mode
	if (isset($_GET['mode'])) {
		// Ubah mode absensi berdasarkan parameter yang diterima
		$mode_absen = intval($_GET['mode']); // Ambil nilai mode dari parameter URL dan ubah ke integer

		// Simpan mode absen di tabel status dengan cara update
		$simpan = mysqli_query($konek, "UPDATE status2 SET mode='$mode_absen'");

		if ($simpan) {
			if ($mode_absen == 1) {
				echo "Berhasil Ubah Mode ke Masuk";
			} else if ($mode_absen == 2) {
				echo "Berhasil Ubah Mode ke Keluar";
			} else {
				echo "Mode tidak valid";
			}
		} else {
			echo "Gagal Ubah Mode: " . mysqli_error($konek);
		}
	} else {
		// Jika tidak ada permintaan untuk mengubah mode, tampilkan mode saat ini
		echo "Mode absensi saat ini: " . $mode_absen;
	}
?>
