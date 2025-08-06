$(document).ready(function() {
    $('#validateButton').on('click', function() {
        var id_tamu = $('#id_tamu').val().trim();

        if(id_tamu) {
            $.ajax({
                url: 'ambil_data_tamu.php',
                type: 'POST',
                data: { id: id_tamu },
                dataType: 'json',  // Penting!
                success: function(data) {
                    if(data.success) {
                        $('#petugas').val(data.petugas);
                        $('#nama_tamu').val(data.nama);
                        $('#nama_perusahaan').val(data.asal_perusahaan);
                        $('#jumlah_tamu').val(data.jumlah_orang);
                        $('#keperluan').val(data.keperluan);
                        $('#ingin_bertemu').val(data.ingin_bertemu_siapa);
                        $('#nopol').val(data.nopol_kendaraan);
                    } else {
                        alert('ID tidak ditemukan');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat validasi ID');
                }
            });
        } else {
            alert('Masukkan ID terlebih dahulu');
        }
    });
});
