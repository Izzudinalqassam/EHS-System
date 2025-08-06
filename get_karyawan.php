<?php
include "koneksi.php";

// Get search term
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare query with search
$query = "SELECT nama, nopol 
          FROM karyawan 
          WHERE departmen IS NOT NULL 
          AND departmen != '' 
          AND nama LIKE ?";

$stmt = $konek->prepare($query);
$searchTerm = "%$search%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = array(
        'nama' => $row['nama'],
        'nopol' => $row['nopol']
    );
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($data);