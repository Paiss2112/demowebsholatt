<?php
$server = "localhost";
$user = "root";
$password = "Given9456";
$nama_database = "jadwal_sholat";

$conn = new mysqli($server, $user, $password, $nama_database);
if ($conn->connect_error) {
    die("Gagal terhubung dengan database: " . $conn->connect_error);
}
?>