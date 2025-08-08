<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $konten = trim($_POST['konten']);
    $status = $_POST['status'];
    $prioritas = $_POST['prioritas'];
    $jadwal_mulai = !empty($_POST['jadwal_mulai']) ? $_POST['jadwal_mulai'] : null;
    $jadwal_selesai = !empty($_POST['jadwal_selesai']) ? $_POST['jadwal_selesai'] : null;
    if ($konten === '') {
        header('Location: admin.php?error=Konten%20berita%20tidak%20boleh%20kosong');
        exit;
    }
    $fields = ['konten', 'status', 'prioritas', 'jadwal_mulai', 'jadwal_selesai'];
    $placeholders = [];
    $params = [];
    $types = '';
    foreach ([$konten, $status, $prioritas, $jadwal_mulai, $jadwal_selesai] as $val) {
        if ($val === null) {
            $placeholders[] = 'NULL';
        } else {
            $placeholders[] = '?';
            $params[] = $val;
            $types .= 's';
        }
    }
    $sql = "INSERT INTO berita_berjalan (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    if ($stmt->execute()) {
        header('Location: admin.php?success=Berita%20berjalan%20berhasil%20ditambahkan');
        exit;
    } else {
        header('Location: admin.php?error=Gagal%20menambah%20berita');
        exit;
    }
}
header('Location: admin.php');
exit;