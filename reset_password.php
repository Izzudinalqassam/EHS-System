<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($konek, $_POST['username']);
    $password = mysqli_real_escape_string($konek, $_POST['password']);
    
    if (empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Password tidak boleh kosong']);
        exit();
    }
    
    $update_query = "UPDATE login SET password = '$password' WHERE username = '$username'";
    $result = mysqli_query($konek, $update_query);
    
    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($konek)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>