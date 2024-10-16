<?php
	error_reporting(0);
	include "koneksi.php";
	//baca isi tabel tmprfid dan tmprfid2
	$sql = mysqli_query($konek, "select * from tmprfid union all select * from tmprfid2");
	$data = mysqli_fetch_array($sql);
	//baca nokartu
	$nokartu = $data['nokartu'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
<div class="form-group">
	<label for="nokartu"><i class="fas fa-id-card"></i> No. Kartu <span class="text-danger">*</span></label>
	<input type="text" name="nokartu" id="nokartu" class="form-control" placeholder="tempelkan kartu rfid Anda" value="<?php echo $nokartu; ?>"required readonly >
</div>
</body>
</html>