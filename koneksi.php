<?php
	// Set timezone untuk Indonesia (WIB)
	date_default_timezone_set('Asia/Jakarta');
	
	//urutan = server, userdb, passdb, namadb
	$konek = mysqli_connect("localhost", "root", "", "absenrfid");
	
	// Set timezone MySQL ke Asia/Jakarta
	if ($konek) {
		mysqli_query($konek, "SET time_zone = '+07:00'");
	}
?>