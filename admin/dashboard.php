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

<style>
    /* Enhanced Stats Card */
    .stats-card-modern {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .stats-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--card-color, #007bff), transparent);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .stats-card-modern:hover::before {
        transform: scaleY(1);
    }

    .stats-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border-color: var(--card-color, #007bff);
    }

    .stats-icon {
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .stats-card-modern:hover .stats-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        color: #212529;
        margin: 0;
        line-height: 1;
    }

    .stats-label {
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .stats-trend {
        font-size: 0.8rem;
        color: #28a745;
        font-weight: 600;
    }

    /* Enhanced Card */
    .card-modern {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .card-modern:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .card-header-modern {
        background: white;
        border-bottom: 1px solid #e9ecef;
        padding: 1.25rem 1.5rem;
        border-radius: 12px 12px 0 0;
    }

    /* List Item Enhanced */
    .list-item-modern {
        padding: 1rem 0;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.2s ease;
    }

    .list-item-modern:last-child {
        border-bottom: none;
    }

    .list-item-modern:hover {
        background: #f8f9fa;
        padding-left: 0.5rem;
        margin-left: -0.5rem;
        margin-right: -0.5rem;
        padding-right: 0.5rem;
        border-radius: 8px;
    }

    .badge-modern {
        padding: 0.35rem 0.75rem;
        font-weight: 500;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    /* Welcome Section */
    .welcome-section {
        background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%);
        border-radius: 12px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .welcome-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .welcome-section h4 {
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .welcome-section p {
        opacity: 0.9;
        margin-bottom: 0;
    }

    /* Quick Stats Summary */
    .quick-stat {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
    }

    .quick-stat-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 8px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-number {
            font-size: 1.5rem;
        }

        .welcome-section {
            padding: 1.5rem;
        }
    }
</style>

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