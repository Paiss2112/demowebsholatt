<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header('Location: login.php');
    exit;
}
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php?success=Anda%20berhasil%20logout.');
    exit;
}

$perPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Get total news count
$totalRes = $conn->query("SELECT COUNT(*) as total FROM berita_berjalan");
$totalRows = $totalRes->fetch_assoc()['total'];

// Get active news count
$activeRes = $conn->query("SELECT COUNT(*) as total FROM berita_berjalan WHERE status='Aktif'");
$activeRows = $activeRes->fetch_assoc()['total'];

// Get draft news count
$draftRes = $conn->query("SELECT COUNT(*) as total FROM berita_berjalan WHERE status='Draft'");
$draftRows = $draftRes->fetch_assoc()['total'];

// Get today's views count
$todayViewsRes = $conn->query("SELECT COUNT(*) as total FROM visitor_log WHERE DATE(viewed_at) = CURDATE()");
$todayViews = $todayViewsRes->fetch_assoc()['total'];

// Get weekly and monthly stats
$weeklyViewsRes = $conn->query("SELECT COUNT(*) as total FROM visitor_log WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$weeklyViews = $weeklyViewsRes->fetch_assoc()['total'];

$monthlyViewsRes = $conn->query("SELECT COUNT(*) as total FROM visitor_log WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$monthlyViews = $monthlyViewsRes->fetch_assoc()['total'];

$totalPages = ceil($totalRows / $perPage);
$res = $conn->query("SELECT * FROM berita_berjalan ORDER BY id DESC LIMIT $perPage OFFSET $offset");
$beritaList = [];
while ($row = $res->fetch_assoc()) {
    $beritaList[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Berita Berjalan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            border-radius: 12px;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .status-badge {
            font-size: 0.75rem;
        }
        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        /* Stats Cards Styling */
        .stats-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .stats-card .card-body {
            padding: 1.5rem;
        }
        
        .stats-card .fs-1 {
            transition: all 0.3s ease;
        }
        
        .stats-card:hover .fs-1 {
            transform: scale(1.1);
        }
        
        .stats-number {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            font-size: 0.875rem;
            font-weight: 500;
            opacity: 0.8;
        }
        
        .news-ticker-preview {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .ticker-wrapper {
            overflow: hidden;
            white-space: nowrap;
            position: relative;
        }

        .ticker-content-preview {
            display: inline-block;
            animation: scroll-left-preview 45s linear infinite;
            font-weight: 500;
            font-size: 1rem;
        }

        .ticker-content-preview:hover {
            animation-play-state: paused;
        }

        .ticker-item {
            display: inline-block;
            margin-right: 40px;
        }

        .ticker-item.priority-high {
            color: #ffd700;
            font-weight: 600;
        }

        .ticker-item.priority-medium {
            color: #ffffff;
            font-weight: 500;
        }

        .ticker-item.priority-low {
            color: #e0e0e0;
            font-size: 0.95rem;
            font-weight: 400;
        }

        .ticker-label {
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-right: 8px;
            font-weight: 600;
            display: inline-block;
        }

        .ticker-separator {
            margin: 0 20px;
            opacity: 0.7;
        }

        @keyframes scroll-left-preview {
            0% { transform: translate3d(100%, 0, 0); }
            100% { transform: translate3d(-100%, 0, 0); }
        }

        /* Responsive adjustments for preview */
        @media (max-width: 768px) {
            .ticker-content-preview {
                font-size: 0.9rem;
            }
            
            .ticker-label {
                font-size: 0.75rem;
                padding: 2px 6px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <div class="d-flex align-items-center mb-4">
                        <img src="image/logotelkom.png" alt="Telkom University Logo" class="img-fluid" style="height: 60px; max-width: 100%; opacity: 0.95; display: block; margin: 20px auto 0 auto;">
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="#news">
                            <i class="bi bi-newspaper me-2"></i>Berita Berjalan
                        </a>
                        <hr class="text-white-50">
                        <a class="nav-link" href="?logout=1">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content p-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-1">Kelola Berita Berjalan</h2>
                        <p class="text-muted mb-0">Tambah dan kelola berita yang tampil di ticker bawah website</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#previewModal">
                            <i class="bi bi-eye me-1"></i>Preview
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Berita
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-newspaper fs-1 text-primary mb-2"></i>
                                <div class="stats-number text-primary"><?= $totalRows ?></div>
                                <div class="stats-label">Total Berita</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-check-circle fs-1 text-success mb-2"></i>
                                <div class="stats-number text-success"><?= $activeRows ?></div>
                                <div class="stats-label">Aktif</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-pause-circle fs-1 text-warning mb-2"></i>
                                <div class="stats-number text-warning"><?= $draftRows ?></div>
                                <div class="stats-label">Draft</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-eye fs-1 text-info mb-2"></i>
                                <div class="stats-number text-info"><?= $todayViews ?></div>
                                <div class="stats-label">Views Hari Ini</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Stats -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Statistik Pengunjung</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <div class="border-end">
                                            <h5 class="text-primary mb-1"><?= $todayViews ?></h5>
                                            <small class="text-muted">Hari Ini</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border-end">
                                            <h5 class="text-success mb-1"><?= $weeklyViews ?></h5>
                                            <small class="text-muted">Minggu Ini</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div>
                                            <h5 class="text-info mb-1"><?= $monthlyViews ?></h5>
                                            <small class="text-muted">Bulan Ini</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- News Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>Daftar Berita Berjalan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="40%">Konten Berita</th>
                                        <th width="15%">Status</th>
                                        <th width="15%">Tanggal Dibuat</th>
                                        <th width="10%">Prioritas</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($beritaList as $i => $b): ?>
                                    <tr>
                                        <td><?= $i+1 ?></td>
                                        <td>
                                            <div class="fw-medium"><?= htmlspecialchars($b['konten']) ?></div>
                                            <small class="text-muted">
                                                <?php if ($b['jadwal_mulai']) echo 'Mulai: '.date('d M Y H:i', strtotime($b['jadwal_mulai'])).' '; ?>
                                                <?php if ($b['jadwal_selesai']) echo 'Selesai: '.date('d M Y H:i', strtotime($b['jadwal_selesai'])); ?>
                                            </small>
                                        </td>
                                        <td><span class="badge <?= $b['status']==='Aktif'?'bg-success':'bg-warning' ?> status-badge"><?= $b['status'] ?></span></td>
                                        <td><?= date('d M Y', strtotime($b['tanggal_dibuat'])) ?></td>
                                        <td><span class="badge <?= $b['prioritas']==='Tinggi'?'bg-danger':($b['prioritas']==='Sedang'?'bg-primary':'bg-secondary') ?>">
                                            <?= $b['prioritas'] ?></span></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-outline-primary btn-sm btn-edit-berita" 
                                                    data-id="<?= $b['id'] ?>"
                                                    data-bs-toggle="modal" data-bs-target="#editNewsModal">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <?php if ($b['status'] === 'Aktif'): ?>
                                                    <a href="actived_action.php?toggle=<?= $b['id'] ?>&to=Draft" class="btn btn-outline-warning btn-sm" title="Jadikan Draft">
                                                        <i class="bi bi-pause"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="actived_action.php?toggle=<?= $b['id'] ?>&to=Aktif" class="btn btn-outline-success btn-sm" title="Aktifkan">
                                                        <i class="bi bi-play"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <button class="btn btn-outline-danger btn-sm btn-delete-berita" data-id="<?= $b['id'] ?>" onclick="if(confirm('Apakah Anda yakin ingin menghapus berita ini?')){window.location.href='delete_berita.php?id=<?= $b['id'] ?>';} return false;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mb-0">
                                <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page-1 ?>" tabindex="-1">Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item<?= $i == $page ? ' active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page+1 ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add News Modal -->
    <div class="modal fade" id="addNewsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post" action="tambah_berita.php">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Berita Berjalan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="newsContent" class="form-label">Konten Berita *</label>
                            <textarea class="form-control" id="newsContent" name="konten" rows="3" placeholder="Masukkan konten berita yang akan ditampilkan..." maxlength="200" required></textarea>
                            <div class="form-text">Maksimal 200 karakter.</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="newsPriority" class="form-label">Prioritas</label>
                                <select class="form-select" id="newsPriority" name="prioritas">
                                    <option value="Tinggi">Tinggi</option>
                                    <option value="Sedang" selected>Sedang</option>
                                    <option value="Rendah">Rendah</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="newsStatus" class="form-label">Status</label>
                                <select class="form-select" id="newsStatus" name="status">
                                    <option value="Aktif" selected>Aktif</option>
                                    <option value="Draft">Draft</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="jadwalMulai" class="form-label">Jadwal Tayang (Opsional)</label>
                                <input type="datetime-local" class="form-control" id="jadwalMulai" name="jadwal_mulai">
                            </div>
                            <div class="col-md-6">
                                <label for="jadwalSelesai" class="form-label">Selesai Tayang (Opsional)</label>
                                <input type="datetime-local" class="form-control" id="jadwalSelesai" name="jadwal_selesai">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan Berita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Berita -->
    <div class="modal fade" id="editNewsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post" action="edit_berita.php">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="editId">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Berita Berjalan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Konten Berita *</label>
                            <textarea class="form-control" id="editKonten" name="konten" rows="3" maxlength="200" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Prioritas</label>
                                <select class="form-select" id="editPrioritas" name="prioritas">
                                    <option value="Tinggi">Tinggi</option>
                                    <option value="Sedang">Sedang</option>
                                    <option value="Rendah">Rendah</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="editStatus" name="status">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Draft">Draft</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Jadwal Tayang (Opsional)</label>
                                <input type="datetime-local" class="form-control" id="editJadwalMulai" name="jadwal_mulai">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Selesai Tayang (Opsional)</label>
                                <input type="datetime-local" class="form-control" id="editJadwalSelesai" name="jadwal_selesai">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Berita</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-eye me-2"></i>Preview Berita Berjalan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Preview menampilkan bagaimana berita akan terlihat di website utama
                    </div>
                    
                    <!-- Enhanced News Ticker Preview -->
                    <div class="news-ticker-preview">
                        <div class="ticker-wrapper">
                            <div class="ticker-content-preview">
                                <?php 
                                // Sort news by priority for preview
                                $priorityOrder = ['Tinggi' => 3, 'Sedang' => 2, 'Rendah' => 1];
                                $activeNews = array_filter($beritaList, function($item) {
                                    return $item['status'] === 'Aktif';
                                });
                                
                                usort($activeNews, function($a, $b) use ($priorityOrder) {
                                    return ($priorityOrder[$b['prioritas']] ?? 0) - ($priorityOrder[$a['prioritas']] ?? 0);
                                });
                                
                                foreach ($activeNews as $i => $b): 
                                    $priorityClass = '';
                                    $label = '';
                                    switch($b['prioritas']) {
                                        case 'Tinggi':
                                            $priorityClass = 'priority-high';
                                            $label = 'PENTING';
                                            break;
                                        case 'Sedang':
                                            $priorityClass = 'priority-medium';
                                            $label = 'INFO';
                                            break;
                                        case 'Rendah':
                                            $priorityClass = 'priority-low';
                                            $label = 'TIPS';
                                            break;
                                    }
                                ?>
                                    <span class="ticker-item <?= $priorityClass ?>">
                                        <span class="ticker-label"><?= $label ?></span>
                                        <?= htmlspecialchars($b['konten']) ?>
                                    </span>
                                    <?php if ($i < count($activeNews)-1): ?>
                                        <span class="ticker-separator">•</span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                
                                <?php if (empty($activeNews)): ?>
                                    <span class="ticker-item priority-medium">
                                        <span class="ticker-label">INFO</span>
                                        Tidak ada berita aktif untuk ditampilkan
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Keterangan Prioritas:</strong>
                            <span class="badge bg-danger ms-2">PENTING</span> Prioritas Tinggi
                            <span class="badge bg-primary ms-2">INFO</span> Prioritas Sedang  
                            <span class="badge bg-secondary ms-2">TIPS</span> Prioritas Rendah
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index:9999; min-width:300px;">
            <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <script>setTimeout(function(){ window.location.href = 'admin.php'; }, 1500);</script>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index:9999; min-width:300px;">
            <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <script>setTimeout(function(){ window.location.href = 'admin.php'; }, 1500);</script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confirmation for delete actions
        document.querySelectorAll('.btn-delete-berita').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus berita ini?')) {
                    // Handle delete action
                    console.log('Delete confirmed');
                }
            });
        });

        // Toggle status actions
        document.querySelectorAll('.btn-outline-warning, .btn-outline-success').forEach(button => {
            button.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (icon.classList.contains('bi-pause')) {
                    // Pause action
                    console.log('Pausing news item');
                } else if (icon.classList.contains('bi-play')) {
                    // Activate action
                    console.log('Activating news item');
                }
            });
        });

        // AJAX isi otomatis modal edit berita
        const editModal = document.getElementById('editNewsModal');
        document.querySelectorAll('.btn-edit-berita').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch('get_berita.php?id=' + id)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('editId').value = data.id;
                        document.getElementById('editKonten').value = data.konten;
                        document.getElementById('editPrioritas').value = data.prioritas;
                        document.getElementById('editStatus').value = data.status;
                        document.getElementById('editJadwalMulai').value = data.jadwal_mulai ? data.jadwal_mulai.replace(' ', 'T') : '';
                        document.getElementById('editJadwalSelesai').value = data.jadwal_selesai ? data.jadwal_selesai.replace(' ', 'T') : '';
                        document.getElementById('editAutoScroll').checked = data.auto_scroll == '1';
                    });
            });
        });

        // Animate stats counters
        function animateCounter(element, target, duration = 1000) {
            let start = 0;
            const increment = target / (duration / 16);
            
            function updateCounter() {
                start += increment;
                if (start < target) {
                    element.textContent = Math.floor(start);
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = target;
                }
            }
            updateCounter();
        }

        // Start counter animation when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const statsNumbers = document.querySelectorAll('.stats-number');
            statsNumbers.forEach(number => {
                const target = parseInt(number.textContent);
                number.textContent = '0';
                animateCounter(number, target);
            });
        });
    </script>
</body>
</html>