<?php
require_once '../config/db.php';
$page_title = 'Research & Products';

// Pagination setup
$limit = 5;

// Get products with author info
$stmt = $pdo->query("
    SELECT p.*, a.nama as pembuat_nama 
    FROM produk p 
    LEFT JOIN anggota a ON p.pembuat_id = a.uuid 
    ORDER BY p.tahun DESC, p.nama ASC
");
$products = $stmt->fetchAll();

//ambil data blueprint
$page_bp = isset($_GET['blueprint_page']) ? (int)$_GET['blueprint_page'] : 1;
$offset_bp = ($page_bp - 1) * $limit;
$stmt = $pdo->prepare("
    SELECT judul, deskripsi
    FROM blueprint
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset_bp, PDO::PARAM_INT);
$stmt->execute();
$count_stmt_bp = $pdo->prepare("SELECT COUNT(*) FROM blueprint");
$count_stmt_bp->execute();
$rows_blueprint = $count_stmt_bp->fetchColumn();
$pages_blueprint = ceil($rows_blueprint / $limit);
$blueprint = $stmt->fetchAll(PDO::FETCH_ASSOC);

//ambil data topik riset
$page_tp = isset($_GET['topic_page']) ? (int)$_GET['topic_page'] : 1;
$offset_tp = ($page_tp - 1) * $limit;
$stmt = $pdo->prepare("
    SELECT topik
    FROM topik_riset
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset_tp, PDO::PARAM_INT);
$stmt->execute();
$count_stmt_tp = $pdo->prepare("SELECT COUNT(*) FROM topik_riset");
$count_stmt_tp->execute();
$rows_topik = $count_stmt_tp->fetchColumn();
$pages_topik = ceil($rows_topik / $limit);
$topik = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- Page Header -->
<section class="page-header py-5" style="background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%); color: white;">
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h1 class="display-4 fw-bold mb-3">Research & Development</h1>
                <p class="lead">Produk, topik riset, dan roadmap pengembangan AI Lab Polinema</p>
            </div>
        </div>
    </div>
</section>

<!-- Research Topics Section -->
<?php if (!empty($research_topics)): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col text-center">
                    <h2 class="section-title">Research Focus Areas</h2>
                    <p class="section-subtitle">Bidang penelitian utama AI Lab Polinema</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($research_topics as $topic): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <i class="bi bi-lightbulb-fill text-warning" style="font-size: 2.5rem;"></i>
                                </div>
                                <p class="mb-0 text-muted">
                                    <?php echo nl2br(htmlspecialchars($topic['topik'])); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Products Section -->
<?php if (!empty($products)): ?>
    <section class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col text-center">
                    <h2 class="section-title">Research Products</h2>
                    <p class="section-subtitle">Produk hasil penelitian dan pengembangan</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <?php if ($product['path_gambar']): ?>
                                <img src="../assets/img/<?php echo htmlspecialchars($product['path_gambar']); ?>"
                                    class="card-img-top"
                                    alt="<?php echo htmlspecialchars($product['nama']); ?>"
                                    style="height: 200px; object-fit: contain;">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                    style="height: 200px;">
                                    <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column">
                                <span class="badge bg-primary align-self-start mb-2">
                                    <?php echo $product['tahun'] ?: 'N/A'; ?>
                                </span>

                                <h5 class="card-title fw-bold mb-3">
                                    <?php echo htmlspecialchars($product['nama']); ?>
                                </h5>

                                <?php if ($product['pembuat_nama']): ?>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-person me-1"></i>
                                        <strong><?php echo htmlspecialchars($product['pembuat_nama']); ?></strong>
                                    </p>
                                <?php endif; ?>

                                <p class="card-text text-muted flex-grow-1">
                                    <?php echo htmlspecialchars($product['deskripsi']); ?>
                                </p>

                                <?php if ($product['link_demo']): ?>
                                    <a href="<?php echo htmlspecialchars($product['link_demo']); ?>"
                                        target="_blank"
                                        class="btn btn-outline-primary mt-auto">
                                        <i class="bi bi-eye me-2"></i>View Demo
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Blueprint/Roadmap Section -->
<?php if (!empty($blueprints)): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col text-center">
                    <h2 class="section-title">Development Roadmap</h2>
                    <p class="section-subtitle">Blueprint pengembangan laboratorium</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($blueprints as $blueprint): ?>
                    <div class="col-lg-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-4">
                                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                            <i class="bi bi-diagram-3 text-primary" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="fw-bold mb-3">
                                            <?php echo htmlspecialchars($blueprint['judul']); ?>
                                        </h4>
                                        <p class="text-muted mb-0" style="text-align: justify; white-space: pre-line;">
                                            <?php echo htmlspecialchars($blueprint['deskripsi']); ?>
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?php echo date('d F Y', strtotime($blueprint['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- General Research Areas (Static Content) -->
<section class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col text-center">
                <h2 class="section-title">Research Domains</h2>
                <p class="section-subtitle">Domain penelitian yang kami kembangkan</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center h-100 p-4">
                    <div class="mb-3">
                        <i class="bi bi-robot text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">Artificial Intelligence</h5>
                    <p class="text-muted small">Machine Learning, Deep Learning, Computer Vision</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center h-100 p-4">
                    <div class="mb-3">
                        <i class="bi bi-globe text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">Web Technology</h5>
                    <p class="text-muted small">Full-stack Development, Progressive Web Apps</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center h-100 p-4">
                    <div class="mb-3">
                        <i class="bi bi-phone text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">Mobile Development</h5>
                    <p class="text-muted small">Android, iOS, Cross-platform Apps</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center h-100 p-4">
                    <div class="mb-3">
                        <i class="bi bi-database text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">Data Science</h5>
                    <p class="text-muted small">Big Data Analytics, Business Intelligence</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center h-100 p-4">
                    <div class="mb-3">
                        <i class="bi bi-shield-check text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">Cyber Security</h5>
                    <p class="text-muted small">Network Security, Ethical Hacking</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center h-100 p-4">
                    <div class="mb-3">
                        <i class="bi bi-cloud text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">Cloud Computing</h5>
                    <p class="text-muted small">AWS, Azure, Google Cloud Platform</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center h-100 p-4">
                    <div class="mb-3">
                        <i class="bi bi-diagram-3 text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">IoT</h5>
                    <p class="text-muted small">Internet of Things, Smart Systems</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center h-100 p-4">
                    <div class="mb-3">
                        <i class="bi bi-controller text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">Game Development</h5>
                    <p class="text-muted small">2D/3D Games, Virtual Reality</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!--/blueprint-->
<section class="py-5">
    <div class="container">
        <div class="col text-center">
            <h2 class="section-title"> Blueprint</h2>
            <p class="section-subtitle">Blueprint penelitian AI Lab Polinema</p>
        </div>
        <?php if (!empty($blueprint)): ?>
            <div class="row g-4">
                <?php foreach ($blueprint as $bp): ?>
                    <div class="col-md-6 col">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex mb-3">
                                    <h3 class="fw-bold">
                                        <?php echo htmlspecialchars($bp['judul']); ?>
                                    </h3>
                                </div>
                                <p class="text-muted">
                                    <?php echo htmlspecialchars($bp['deskripsi']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Pagination features -->
            <?php if ($pages_blueprint > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mt-4">
                        <li class="page-item <?= ($page_bp <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?blueprint_page=<?= $page_bp - 1 ?>&topic_page=<?= $page_tp ?>">&laquo; Sebelumnya</a>
                        </li>
                        <?php for ($i = 1; $i <= $pages_blueprint; $i++): ?>
                            <li class="page-item <?= ($page_bp == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?blueprint_page=<?= $i ?>&topic_page=<?= $page_tp ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page_bp >= $pages_blueprint) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?blueprint_page=<?= $page_bp + 1 ?>&topic_page=<?= $page_tp ?>">Selanjutnya &raquo;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Belum ada blueprint yang tersedia
            </div>
        <?php endif; ?>
    </div>
</section>
<!--//blueprint-->

<!-- Research Topic Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h2 class="section-title">Topik Riset</h2>
                <p class="section-subtitle">Topik riset prioritas AI Lab Polinema (2025)</p>
            </div>
        </div>
        <?php if (!empty($topik)): ?>
            <div class="row g-4 align-items-center">
                <?php foreach ($topik as $tp): ?>
                    <div class="mt-3">
                        <div class="card border-0 shadow-sm text-center h-100 p-4">
                            <h5 class="fw mb-0">
                                <?php echo htmlspecialchars($tp['topik']); ?>
                            </h5>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Pagination features -->
            <?php if ($pages_topik > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mt-4">
                        <li class="page-item <?= ($page_tp <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?blueprint_page=<?= $page_bp ?>&topic_page=<?= $page_tp - 1 ?>">&laquo; Sebelumnya</a>
                        </li>
                        <?php for ($i = 1; $i <= $pages_topik; $i++): ?>
                            <li class="page-item <?= ($page_tp == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?blueprint_page=<?= $page_bp ?>&topic_page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page_tp >= $pages_topik) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?blueprint_page=<?= $page_bp ?>&topic_page=<?= $page_tp + 1 ?>">Selanjutnya &raquo;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Belum ada topik riset yang tersedia
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>