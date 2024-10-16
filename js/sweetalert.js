if ($update) {
    Swal.fire({
        icon: 'success',
        title: 'Tersimpan',
        text: 'Data berhasil diupdate!'
    }).then(function() {
        window.location.href = 'datakaryawan.php';
    });
} else {
    Swal.fire({
        icon: 'error',
        title: 'Gagal Tersimpan',
        text: 'Data gagal diupdate!'
    }).then(function() {
        window.location.href = 'datakaryawan.php';
    });
}
