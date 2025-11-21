<?php
require_once '../config/db.php';
$page_title = 'Research & Products';

// Fetch dashboard background
$stmt_bg = $pdo->query("SELECT * FROM dashboard_foto ORDER BY updated_at DESC LIMIT 1");
$dashboard_bg = $stmt_bg->fetch();
$bg_image = '';
if ($dashboard_bg && $dashboard_bg['path_gambar']) {
    $bg_image = '../assets/img/dashboard/' . htmlspecialchars($dashboard_bg['path_gambar']);
}

// Data produk dengan multiple pembuat
$limit_pd = 3;
$page_pd = isset($_GET['product_page']) ? (int)$_GET['product_page'] : 1;
$offset_pd = ($page_pd - 1) * $limit_pd;

$stmt = $pdo->prepare("
    SELECT p.*, 
           STRING_AGG(DISTINCT a.nama, ', ' ORDER BY a.nama) as pembuat_nama
    FROM produk p 
    LEFT JOIN anggota_produk ap ON p.uuid = ap.produk_uuid
    LEFT JOIN anggota a ON ap.anggota_uuid = a.uuid 
    GROUP BY p.uuid
    ORDER BY p.tahun DESC, p.nama ASC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit_pd, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset_pd, PDO::PARAM_INT);
$stmt->execute();

$count_stmt_pd = $pdo->prepare("SELECT COUNT(*) FROM produk");
$count_stmt_pd->execute();
$rows_produk = $count_stmt_pd->fetchColumn();
$pages_produk = ceil($rows_produk / $limit_pd);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data blueprint
$limit_bp = 4;
$page_bp = isset($_GET['blueprint_page']) ? (int)$_GET['blueprint_page'] : 1;
$offset_bp = ($page_bp - 1) * $limit_bp;
$stmt = $pdo->prepare("
    SELECT judul, deskripsi
    FROM blueprint
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit_bp, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset_bp, PDO::PARAM_INT);
$stmt->execute();
$count_stmt_bp = $pdo->prepare("SELECT COUNT(*) FROM blueprint");
$count_stmt_bp->execute();
$rows_blueprint = $count_stmt_bp->fetchColumn();
$pages_blueprint = ceil($rows_blueprint / $limit_bp);
$blueprint = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data topik riset
$limit_tp = 6;
$page_tp = isset($_GET['topic_page']) ? (int)$_GET['topic_page'] : 1;
$offset_tp = ($page_tp - 1) * $limit_tp;
$stmt = $pdo->prepare("
    SELECT topik
    FROM topik_riset
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit_tp, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset_tp, PDO::PARAM_INT);
$stmt->execute();
$count_stmt_tp = $pdo->prepare("SELECT COUNT(*) FROM topik_riset");
$count_stmt_tp->execute();
$rows_topik = $count_stmt_tp->fetchColumn();
$pages_topik = ceil($rows_topik / $limit_tp);
$topik = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- Page Header -->
<section class="hero-section position-relative py-5" id="heroSection" style="color: white; min-height: 500px; overflow: hidden;">
    <!-- Parallax Background Layer -->
    <?php
    $bg_style = $bg_image
        ? "background: url('$bg_image') center/cover no-repeat; z-index: 0;"
        : 'background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%); z-index: 0;';
    ?>
    <div class="parallax-bg position-absolute top-0 start-0 w-100 h-100"
        style="<?php echo $bg_style; ?> transform: translate3d(0, 0, 0); will-change: transform;">
    </div>

    <!-- Gradient Overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(30, 75, 163, 0.25) 0%, rgba(74, 144, 226, 0.25) 100%); z-index: 1; pointer-events: none;"></div>

    <!-- Content -->
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center justify-content-center min-vh-75 py-5">
            <div class="col-lg-10 text-center" data-aos="fade-up" data-aos-duration="1000">
                <h1 class="display-4 fw-bold mb-3">Research & Development</h1>
                <p class="lead">Produk, topik riset, dan roadmap pengembangan AI Lab Polinema</p>
            </div>
        </div>
    </div>
</section>

<!-- Parallax JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const parallaxBg = document.querySelector('.parallax-bg');
        const heroSection = document.getElementById('heroSection');

        if (parallaxBg && heroSection) {
            let ticking = false;

            function updateParallax() {
                const scrolled = window.pageYOffset;
                const heroHeight = heroSection.offsetHeight;

                if (scrolled < heroHeight) {
                    const yPos = scrolled * 0.5;
                    parallaxBg.style.transform = `translate3d(0, ${yPos}px, 0)`;
                }
                ticking = false;
            }

            function requestTick() {
                if (!ticking) {
                    window.requestAnimationFrame(updateParallax);
                    ticking = true;
                }
            }

            window.addEventListener('scroll', requestTick, {
                passive: true
            });
        }
    });
</script>

<!-- Products Section -->
<?php if (!empty($products)): ?>
    <section id="products" class="py-5" style="scroll-margin-top:100px;" data-aos="fade-up" data-aos-duration="1000">
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
                                    <div class="mb-2">
                                        <i class="bi bi-people-fill text-muted me-1"></i>
                                        <small class="text-muted">
                                            <?php
                                            $pembuats = explode(', ', $product['pembuat_nama']);
                                            echo implode(', ', array_map('htmlspecialchars', $pembuats));
                                            ?>
                                        </small>
                                    </div>
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

            <!-- Pagination -->
            <?php if ($pages_produk > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page_pd <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?product_page=<?= $page_pd - 1 ?>&blueprint_page=<?= $page_bp ?>&topic_page=<?= $page_tp ?>#products">&laquo; Sebelumnya</a>
                        </li>
                        <?php for ($i = 1; $i <= $pages_produk; $i++): ?>
                            <li class="page-item <?= ($page_pd == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?product_page=<?= $i ?>&blueprint_page=<?= $page_bp ?>&topic_page=<?= $page_tp ?>#products"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page_pd >= $pages_produk) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?product_page=<?= $page_pd + 1 ?>&blueprint_page=<?= $page_bp ?>&topic_page=<?= $page_tp ?>#products">Selanjutnya &raquo;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<!-- Blueprint Section -->
<section id="blueprint" class="py-5 bg-light" style="scroll-margin-top:100px;" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <div class="col text-center">
            <h2 class="section-title">Blueprint</h2>
            <p class="section-subtitle">Blueprint penelitian AI Lab Polinema</p>
        </div>
        <?php if (!empty($blueprint)): ?>
            <div class="row g-4">
                <?php foreach ($blueprint as $bp): ?>
                    <div class="col-md-6">
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

            <!-- Pagination -->
            <?php if ($pages_blueprint > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page_bp <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?product_page=<?= $page_pd ?>&blueprint_page=<?= $page_bp - 1 ?>&topic_page=<?= $page_tp ?>#blueprint">&laquo; Sebelumnya</a>
                        </li>
                        <?php for ($i = 1; $i <= $pages_blueprint; $i++): ?>
                            <li class="page-item <?= ($page_bp == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?product_page=<?= $page_pd ?>&blueprint_page=<?= $i ?>&topic_page=<?= $page_tp ?>#blueprint"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page_bp >= $pages_blueprint) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?product_page=<?= $page_pd ?>&blueprint_page=<?= $page_bp + 1 ?>&topic_page=<?= $page_tp ?>#blueprint">Selanjutnya &raquo;</a>
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

<!-- Research Topic Section -->
<section id="topics" class="py-5" style="scroll-margin-top:100px;" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h2 class="section-title">Topik Riset</h2>
                <p class="section-subtitle">Topik riset prioritas AI Lab Polinema (2025)</p>
            </div>
        </div>
        <?php if (!empty($topik)): ?>
            <div class="row g-4 justify-content-center">
                <?php foreach ($topik as $tp): ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm text-center h-100 p-4">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <h5 class="fw-bold mb-0">
                                    <?php echo htmlspecialchars($tp['topik']); ?>
                                </h5>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($pages_topik > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page_tp <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?product_page=<?= $page_pd ?>&blueprint_page=<?= $page_bp ?>&topic_page=<?= $page_tp - 1 ?>#topics">&laquo; Sebelumnya</a>
                        </li>
                        <?php for ($i = 1; $i <= $pages_topik; $i++): ?>
                            <li class="page-item <?= ($page_tp == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?product_page=<?= $page_pd ?>&blueprint_page=<?= $page_bp ?>&topic_page=<?= $i ?>#topics"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page_tp >= $pages_topik) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?product_page=<?= $page_pd ?>&blueprint_page=<?= $page_bp ?>&topic_page=<?= $page_tp + 1 ?>#topics">Selanjutnya &raquo;</a>
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