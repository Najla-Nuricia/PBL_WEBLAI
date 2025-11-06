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

// Get recent news
$stmt = $pdo->query("SELECT * FROM berita ORDER BY created_at DESC LIMIT 5");
$recent_news = $stmt->fetchAll();

// Get recent activities
$stmt = $pdo->query("SELECT * FROM kegiatan ORDER BY tanggal DESC LIMIT 5");
$recent_activities = $stmt->fetchAll();
?>


<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card clickable-card" onclick="window.location.href='manage_news.php'">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Berita</p>
                    <h3 class="fw-bold mb-0"><?php echo $stats['berita']; ?></h3>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded">
                    <i class="bi bi-newspaper text-primary fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card clickable-card" onclick="window.location.href='manage_activities.php'">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Kegiatan</p>
                    <h3 class="fw-bold mb-0"><?php echo $stats['kegiatan']; ?></h3>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded">
                    <i class="bi bi-calendar-event text-success fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Publikasi</p>
                    <h3 class="fw-bold mb-0"><?php echo $stats['publikasi']; ?></h3>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded">
                    <i class="bi bi-journal-text text-info fs-2"></i>
                </div>
            </div>
            <!-- <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-black stretched-link" href="manage_publications.php">Lihat Detail</a>
            </div> -->
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Anggota</p>
                    <h3 class="fw-bold mb-0"><?php echo $stats['anggota']; ?></h3>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded">
                    <i class="bi bi-people text-warning fs-2"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Produk</p>
                    <h3 class="fw-bold mb-0"><?php echo $stats['produk']; ?></h3>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded">
                    <i class="bi bi-box-seam text-danger fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Galeri</p>
                    <h3 class="fw-bold mb-0"><?php echo $stats['galeri']; ?></h3>
                </div>
                <div class="bg-secondary bg-opacity-10 p-3 rounded">
                    <i class="bi bi-images text-secondary fs-2"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Content -->
<div class="row">
    <!-- Recent News -->
    <div class="col-xl-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-newspaper me-2"></i>Berita Terbaru
                </h5>
                <a href="manage_news.php" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_news)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_news as $news): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">
                                            <?php echo htmlspecialchars($news['judul']); ?>
                                        </h6>
                                        <p class="mb-1 text-muted small">
                                            <?php echo substr(htmlspecialchars($news['deskripsi']), 0, 100) . '...'; ?>
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            <?php echo date('d M Y', strtotime($news['tanggal'])); ?>
                                            <span class="badge bg-primary ms-2">
                                                <?php echo ucfirst($news['kategori']); ?>
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
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
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-calendar-event me-2"></i>Kegiatan Terbaru
                </h5>
                <a href="manage_activities.php" class="btn btn-sm btn-outline-success">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_activities)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">
                                            <?php echo htmlspecialchars($activity['nama']); ?>
                                        </h6>
                                        <?php if ($activity['pemateri']): ?>
                                            <p class="mb-1 text-muted small">
                                                <i class="bi bi-person me-1"></i>
                                                <?php echo htmlspecialchars($activity['pemateri']); ?>
                                            </p>
                                        <?php endif; ?>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            <?php echo date('d M Y', strtotime($activity['tanggal'])); ?>
                                            <span class="badge bg-success ms-2">
                                                <?php echo ucfirst($activity['kategori_kegiatan']); ?>
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
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

<!-- Quick Actions
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-lightning-fill me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="manage_news.php" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Berita
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="manage_activities.php" class="btn btn-outline-success w-100 py-3">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Kegiatan
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="manage_publications.php" class="btn btn-outline-info w-100 py-3">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Publikasi
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="manage_members.php" class="btn btn-outline-warning w-100 py-3">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Anggota
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->

<?php include 'includes/admin_footer.php'; ?>