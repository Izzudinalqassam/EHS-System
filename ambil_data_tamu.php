<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

function getSheetDataByID($id) {
    $path = __DIR__.  '/tamu-440707-20b29181ef30.json';
    $client = new Client();
    $client->setApplicationName('Google Sheets API PHP');
    $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
    $client->setAuthConfig($path);

    $service = new Sheets($client);
    $spreadsheetId = '1DCvWaJLzZEEcbqcWG2R6j9yAEqqt1H5BG5wlMT4XyPY';
    $range = 'tamu!A:I'; // Adjust this based on your Google Sheet columns

    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();

    foreach ($values as $row) {
        if ($row[8] == $id) { // Assuming the ID is in the first column (A)
            return [
                'success' => true,
                'Timestamp' => $row[0],
                'Email_Address' => $row[1],
                'nama' => $row[2],
                'asal_perusahaan' => $row[3],
                'jumlah_orang' => $row[4],
                'keperluan' => $row[5],
                'ingin_bertemu_siapa' => $row[6],
                'nopol_kendaraan' => $row[7],
                'kode_unik' => $row[8]
            ];
        }
    }
    return ['success' => false];
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $data = getSheetDataByID($id);
    echo json_encode($data);
}
?>
