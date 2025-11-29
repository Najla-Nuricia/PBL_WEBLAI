<?php
$page_title = 'Dashboard';
include 'includes/admin_header.php';

// Get statistics
$stats = [];

// Count berita
$stmt = $pdo->query("SELECT COUNT(*) as total FROM berita");
$stats['berita'] = $stmt->fetch()['total'];

// Count kegiatan
$stmt = $pdo->query("SELECT COUNT(*) as total FROM kegiatan");
$stats['kegiatan'] = $stmt->fetch()['total'];

// Count publikasi
$stmt = $pdo->query("SELECT COUNT(*) as total FROM publikasi");
$stats['publikasi'] = $stmt->fetch()['total'];

// Count anggota
$stmt = $pdo->query("SELECT COUNT(*) as total FROM anggota");
$stats['anggota'] = $stmt->fetch()['total'];

// Count produk
$stmt = $pdo->query("SELECT COUNT(*) as total FROM produk");
$stats['produk'] = $stmt->fetch()['total'];

// Count galeri
$stmt = $pdo->query("SELECT COUNT(*) as total FROM galeri");
$stats['galeri'] = $stmt->fetch()['total'];

//Count Riset
$stmt = $pdo->query("SELECT COUNT(*) as total FROM topik_riset");
$stats['topik_riset'] = $stmt->fetch()['total'];

//Count Blueprint
$stmt = $pdo->query("SELECT COUNT(*) as total FROM blueprint");
$stats['blueprint'] = $stmt->fetch()['total'];

// Get recent news
$stmt = $pdo->query("SELECT * FROM berita ORDER BY created_at DESC LIMIT 5");
$recent_news = $stmt->fetchAll();

// Get recent activities
$stmt = $pdo->query("SELECT * FROM kegiatan ORDER BY tanggal DESC LIMIT 5");
$recent_activities = $stmt->fetchAll();
?>

<link rel="stylesheet" href="../assets/css/dashboard.css">
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card-modern" onclick="window.location.href='manage_news.php'" style="--card-color: #0d6efd;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <p class="stats-label">Total Berita</p>
                    <h3 class="stats-number"><?php echo $stats['berita']; ?></h3>
                </div>
                <div class="stats-icon" style="background: rgba(13, 110, 253, 0.1);">
                    <i class="bi bi-newspaper text-primary fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card-modern" onclick="window.location.href='manage_activities.php'" style="--card-color: #198754;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <p class="stats-label">Total Kegiatan</p>
                    <h3 class="stats-number"><?php echo $stats['kegiatan']; ?></h3>
                </div>
                <div class="stats-icon" style="background: rgba(25, 135, 84, 0.1);">
                    <i class="bi bi-calendar-event text-success fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card-modern" onclick="window.location.href='manage_publications.php'" style="--card-color: #0dcaf0;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <p class="stats-label">Total Publikasi</p>
                    <h3 class="stats-number"><?php echo $stats['publikasi']; ?></h3>
                </div>
                <div class="stats-icon" style="background: rgba(13, 202, 240, 0.1);">
                    <i class="bi bi-journal-text text-info fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card-modern" onclick="window.location.href='manage_members.php'" style="--card-color: #ffc107;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <p class="stats-label">Total Anggota</p>
                    <h3 class="stats-number"><?php echo $stats['anggota']; ?></h3>
                </div>
                <div class="stats-icon" style="background: rgba(255, 193, 7, 0.1);">
                    <i class="bi bi-people text-warning fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card-modern" onclick="window.location.href='manage_products.php'" style="--card-color: #dc3545;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <p class="stats-label">Total Produk</p>
                    <h3 class="stats-number"><?php echo $stats['produk']; ?></h3>
                </div>
                <div class="stats-icon" style="background: rgba(220, 53, 69, 0.1);">
                    <i class="bi bi-box-seam text-danger fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card-modern" onclick="window.location.href='manage_gallery.php'" style="--card-color: #6c757d;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <p class="stats-label">Total Galeri</p>
                    <h3 class="stats-number"><?php echo $stats['galeri']; ?></h3>
                </div>
                <div class="stats-icon" style="background: rgba(108, 117, 125, 0.1);">
                    <i class="bi bi-images text-secondary fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card-modern" onclick="window.location.href='manage_blueprint.php'" style="--card-color: #6f42c1;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <p class="stats-label">Total Blueprint</p>
                    <h3 class="stats-number"><?php echo $stats['blueprint']; ?></h3>
                </div>
                <div class="stats-icon" style="background: rgba(111, 66, 193, 0.1); color: #6f42c1;">
                    <i class="bi bi-diagram-3 fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card-modern" onclick="window.location.href='manage_topik_riset.php'" style="--card-color: #fd7e14;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <p class="stats-label">Topik Riset</p>
                    <h3 class="stats-number"><?php echo $stats['topik_riset']; ?></h3>
                </div>
                <div class="stats-icon" style="background: rgba(253, 126, 20, 0.1); color: #fd7e14;">
                    <i class="bi bi-lightbulb fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Content -->
<div class="row">
    <!-- Recent News -->
    <div class="col-xl-6 mb-4">
        <div class="card card-modern h-100">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-newspaper me-2 text-primary"></i>Berita Terbaru
                </h5>
                <a href="manage_news.php" class="btn btn-sm btn-outline-primary">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_news)): ?>
                    <?php foreach ($recent_news as $news): ?>
                        <div class="list-item-modern">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-2 fw-bold">
                                        <?php echo htmlspecialchars($news['judul']); ?>
                                    </h6>
                                    <p class="mb-2 text-muted small" style="line-height: 1.5;">
                                        <?php echo substr(htmlspecialchars($news['deskripsi']), 0, 80) . '...'; ?>
                                    </p>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?php echo date('d M Y', strtotime($news['tanggal'])); ?>
                                        </small>
                                        <span class="badge badge-modern bg-primary">
                                            <?php echo ucfirst($news['kategori']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Belum ada berita
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-xl-6 mb-4">
        <div class="card card-modern h-100">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-calendar-event me-2 text-success"></i>Kegiatan Terbaru
                </h5>
                <a href="manage_activities.php" class="btn btn-sm btn-outline-success">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_activities)): ?>
                    <?php foreach ($recent_activities as $activity): ?>
                        <div class="list-item-modern">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-2 fw-bold">
                                        <?php echo htmlspecialchars($activity['nama']); ?>
                                    </h6>
                                    <?php if ($activity['pemateri']): ?>
                                        <p class="mb-2 text-muted small">
                                            <i class="bi bi-person-circle me-1"></i>
                                            <?php echo htmlspecialchars($activity['pemateri']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?php echo date('d M Y', strtotime($activity['tanggal'])); ?>
                                        </small>
                                        <span class="badge badge-modern bg-success">
                                            <?php echo ucfirst($activity['kategori_kegiatan']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Belum ada kegiatan
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>