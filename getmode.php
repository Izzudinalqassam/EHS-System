<?php
include "koneksi.php";

$sql = mysqli_query($konek, "SELECT mode FROM status LIMIT 1");
$data = mysqli_fetch_array($sql);
echo $data['mode'];
?>