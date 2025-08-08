<?php
session_start();
include 'config.php';
// Toggle status aktif/draft
if (isset($_GET['toggle']) && isset($_GET['to'])) {
    $id = intval($_GET['toggle']);
    $to = ($_GET['to'] === 'Aktif') ? 'Aktif' : 'Draft';
    $stmt = $conn->prepare("UPDATE berita_berjalan SET status=? WHERE id=?");
    $stmt->bind_param("si", $to, $id);
    if ($stmt->execute()) {
        header('Location: admin.php?success=Status%20berita%20berhasil%20diubah');
        exit;
    } else {
        header('Location: admin.php?error=Gagal%20mengubah%20status%20berita');
        exit;
    }
}
header('Location: admin.php');
exit;