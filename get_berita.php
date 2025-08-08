<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

// Handle request for active news (for user.php) - no authentication required
if (isset($_GET['status']) && $_GET['status'] === 'active') {
    $stmt = $conn->prepare("SELECT * FROM berita_berjalan WHERE status='Aktif' ORDER BY prioritas DESC, id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $news = [];
    while ($row = $result->fetch_assoc()) {
        $news[] = [
            'id' => $row['id'],
            'content' => $row['konten'],
            'priority' => strtolower($row['prioritas']),
            'status' => $row['status']
        ];
    }
    echo json_encode(['news' => $news]);
    exit;
}

// Handle request for specific news by ID (for admin.php) - requires authentication
if (isset($_GET['id'])) {
    if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM berita_berjalan WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
        exit;
    }
}

http_response_code(404);
echo json_encode(['error' => 'Not found']);