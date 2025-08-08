<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header('Location: login.php');
    exit;
}
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM berita_berjalan WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header('Location: admin.php?success=Berita%20berjalan%20berhasil%20dihapus');
        exit;
    } else {
        header('Location: admin.php?error=Gagal%20menghapus%20berita');
        exit;
    }
}
header('Location: admin.php');
exit;