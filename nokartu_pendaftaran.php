<?php
error_reporting(0);
include "koneksi.php";

// Baca dari tabel temporary khusus pendaftaran
$sql = mysqli_query($konek, "SELECT nokartu FROM tmp_pendaftaran ORDER BY id DESC LIMIT 1");
$data = mysqli_fetch_array($sql);
$nokartu = $data['nokartu'] ?? '';
?>

<div class="form-group">
    <label for="nokartu"><i class="fas fa-id-card"></i> No. Kartu <span class="text-danger">*</span></label>
    <input type="text" name="nokartu" id="nokartu" class="form-control" 
           placeholder="tempelkan kartu rfid Anda" value="<?php echo $nokartu; ?>" 
           required readonly>
</div>