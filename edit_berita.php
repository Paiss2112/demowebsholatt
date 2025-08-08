<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $konten = trim($_POST['konten']);
    $status = $_POST['status'];
    $prioritas = $_POST['prioritas'];
    $jadwal_mulai = !empty($_POST['jadwal_mulai']) ? $_POST['jadwal_mulai'] : null;
    $jadwal_selesai = !empty($_POST['jadwal_selesai']) ? $_POST['jadwal_selesai'] : null;
    if ($konten === '') {
        header('Location: admin.php?error=Konten%20berita%20tidak%20boleh%20kosong');
        exit;
    }
    $sql = "UPDATE berita_berjalan SET konten=?, status=?, prioritas=?, ";
    $params = [$konten, $status, $prioritas];
    $types = "sss";
    if ($jadwal_mulai === null) {
        $sql .= "jadwal_mulai=NULL, ";
    } else {
        $sql .= "jadwal_mulai=?, ";
        $params[] = $jadwal_mulai;
        $types .= "s";
    }
    if ($jadwal_selesai === null) {
        $sql .= "jadwal_selesai=NULL ";
    } else {
        $sql .= "jadwal_selesai=? ";
        $params[] = $jadwal_selesai;
        $types .= "s";
    }
    $sql .= " WHERE id=?";
    $params[] = $id;
    $types .= "i";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        header('Location: admin.php?success=Berita%20berjalan%20berhasil%20diupdate');
        exit;
    } else {
        header('Location: admin.php?error=Gagal%20update%20berita');
        exit;
    }
}
header('Location: admin.php');
exit;