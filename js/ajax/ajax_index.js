$(document).ready(function () {
  setInterval(function () {
    $("#cekkartu").load("bacakartu.php");
  }, 2000);
});

$(document).ready(function () {
  function updateData() {
    $.ajax({
      url: "get_data_index.php",
      type: "GET",
      dataType: "json",
      success: function (data) {
        $("#total_masuk").text(data.karyawan_didalam);
        $("#total_keluar").text(data.magang_didalam);
        $("#total_keseluruhan").text(data.tamu_didalam);
        $("#total_semua").text(data.total_Didalam);
      },
      error: function () {
        console.log("Data gagal diperbarui");
      },
    });
  }

  // Panggil fungsi pertama kali
  updateData();

  // Perbarui data setiap 2 detik
  setInterval(updateData, 2000);
});
