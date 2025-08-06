
        $(document).ready(function () {
            let lastData = {};
            let shownNotifications = new Set();

            function fetchData() {
                // Fetch data untuk tabel sesuai halaman (tidak diubah)
                $.ajax({
                    url: window.location.pathname.includes('magang') ? 'get_data_magang.php' : 'get_data_karyawan.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        // Update normal table dan counter (kode yang sudah ada)
                        $('#total_masuk').text(response.total_masuk);
                        $('#total_keluar').text(response.total_keluar);
                        $('#total_keseluruhan').text(response.total_keseluruhan);
                        $('#tanggalhariini').text(response.tanggal_display);

                        updateTable(response.absensi);
                    }
                });

                // Fetch data khusus untuk notifikasi (kedua jenis data)
                Promise.all([
                    $.ajax({ url: 'get_data_karyawan.php', type: 'GET', dataType: 'json' }),
                    $.ajax({ url: 'get_data_magang.php', type: 'GET', dataType: 'json' })
                ]).then(([karyawanData, magangData]) => {
                    processNotifications([...karyawanData.absensi, ...magangData.absensi]);
                });
            }

            function processNotifications(allData) {
                let currentData = {};

                allData.forEach(data => {
                    let recordKey = `${data.NIK}_${data.tanggal}_${data.jam_masuk}_${data.jam_pulang}`;

                    if (!lastData[data.NIK] || lastData[data.NIK].recordKey !== recordKey) {
                        if (!shownNotifications.has(recordKey)) {
                            let isMagang = data.departmen.toLowerCase().includes('magang');
                            let inColor = isMagang ? 'purple' : 'green';
                            let outColor = isMagang ? 'orange' : 'red';

                            // New entry atau update
                            if ((!lastData[data.NIK] || lastData[data.NIK].jam_masuk !== data.jam_masuk) && data.jam_masuk !== '00:00:00') {
                                showToast(`${data.nama} (${data.departmen}) | Tap In: ${data.jam_masuk}`, inColor);
                            }
                            if (lastData[data.NIK] && lastData[data.NIK].jam_pulang !== data.jam_pulang && data.jam_pulang !== '00:00:00') {
                                showToast(`${data.nama} (${data.departmen}) | Tap Out: ${data.jam_pulang}`, outColor);
                            }

                            shownNotifications.add(recordKey);
                        }
                    }

                    currentData[data.NIK] = {
                        jam_masuk: data.jam_masuk,
                        jam_pulang: data.jam_pulang,
                        recordKey: recordKey
                    };
                });

                lastData = currentData;
            }

            function showToast(message, backgroundColor) {
                Toastify({
                    text: message,
                    backgroundColor: backgroundColor,
                    className: "info",
                    duration: 3000,
                    gravity: "top",
                    position: 'right',
                    stopOnFocus: true,
                    style: {
                        borderRadius: '10px',
                        border: '2px solid white',
                        padding: '10px',
                        boxShadow: '0 0 10px rgba(0,0,0,0.5)',
                        fontSize: '16px'
                    }
                }).showToast();
            }

            function updateTable(data) {
                $('#absensi-table-body').empty();
                data.forEach((item, index) => {
                    let status = '';
                    if (item.jam_masuk > item.jam_pulang) {
                        status = '<td style="color: green"><b>IN</b></td>';
                    } else if (item.jam_pulang != '00:00:00') {
                        status = '<td style="color: red"><b>OUT</b></td>';
                    } else if (item.jam_masuk != '00:00:00') {
                        status = '<td style="color: green"><b>IN</b></td>';
                    }

                    $('#absensi-table-body').append(
                        '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + item.tanggal + '</td>' +
                        '<td>' + item.NIK + '</td>' +
                        '<td>' + item.nama + '</td>' +
                        '<td>' + item.departmen + '</td>' +
                        '<td style="color: green; font-weight: bold;">' + item.jam_masuk + '</td>' +
                        '<td style="color: red; font-weight: bold;">' + item.jam_pulang + '</td>' +
                        status +
                        '</tr>'
                    );
                });
            }

            fetchData();
            setInterval(fetchData, 1000);
        });
    