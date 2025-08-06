<?php
                    // Include your database connection
                    include "koneksi.php";

                    // Get today's date
                    $tanggal_hari_ini = date('Y-m-d');

                    // Query to count the number of employees who have entered today
                    $sql_orang_masuk = "SELECT COUNT(*) AS total_masuk 
                    FROM absensi a 
                    JOIN karyawan b ON a.nokartu = b.nokartu
                    WHERE a.jam_masuk != '00:00:00' 
                    AND b.departmen != '' 
                    AND b.departmen IS NOT NULL 
                    AND b.departmen NOT IN ('Magang') 
                    AND a.tanggal = '$tanggal_hari_ini'";
                    $result_orang_masuk = mysqli_query($konek, $sql_orang_masuk);
                    $data_orang_masuk = mysqli_fetch_assoc($result_orang_masuk);
                    $total_orang_masuk = $data_orang_masuk['total_masuk'];

                    // Query to count the number of employees who have left today (improved logic)
                    $sql_orang_keluar = "SELECT COUNT(DISTINCT a.nokartu) AS total_keluar 
                    FROM absensi a 
                    JOIN karyawan b ON a.nokartu = b.nokartu
                    WHERE (
                        (a.tanggal = '$tanggal_hari_ini' AND a.jam_pulang != '00:00:00' AND a.status = 'OUT')
                        OR 
                        (DATE(a.last_update) = '$tanggal_hari_ini' AND a.jam_pulang != '00:00:00' AND a.status = 'OUT')
                    )
                    AND b.departmen NOT IN ('tamu', 'Magang')";
                    $result_orang_keluar = mysqli_query($konek, $sql_orang_keluar);
                    $data_orang_keluar = mysqli_fetch_assoc($result_orang_keluar);
                    $total_orang_keluar = $data_orang_keluar['total_keluar'];

                    // Note: Removed the problematic logic that compared jam_masuk > jam_pulang
                    // Now using proper status field and last_update timestamp for accurate counting

                    // Calculate the total number of people currently inside
                    $total_keseluruhan = $total_orang_masuk - $total_orang_keluar;
                    ?>