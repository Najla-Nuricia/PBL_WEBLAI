<?php
require_once '../config/db.php';
$page_title = 'Publications';

// Background
$stmt_bg = $pdo->query("SELECT * FROM dashboard_foto ORDER BY updated_at DESC LIMIT 1");
$dashboard_bg = $stmt_bg->fetch();
$bg_image = $dashboard_bg && $dashboard_bg['path_gambar']
    ? '../assets/img/dashboard/' . htmlspecialchars($dashboard_bg['path_gambar'])
    : '';

// Pagination
$filter_year = isset($_GET['year']) && $_GET['year'] !== 'all' ? (int)$_GET['year'] : null;
$filter_category = isset($_GET['category']) && $_GET['category'] !== 'all' ? $_GET['category'] : null;
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Sidebar data
$years = $pdo->query("SELECT DISTINCT tahun FROM publikasi ORDER BY tahun DESC")
             ->fetchAll(PDO::FETCH_COLUMN);

$categories = $pdo->query("SELECT DISTINCT kategori FROM publikasi WHERE kategori IS NOT NULL ORDER BY kategori ASC")
                  ->fetchAll(PDO::FETCH_COLUMN);

// Filters
$where_clauses = [];
$params = [];

if ($filter_year) {
    $where_clauses[] = "tahun = :tahun";
    $params[':tahun'] = $filter_year;
}

if ($filter_category) {
    $where_clauses[] = "kategori = :kategori";
    $params[':kategori'] = $filter_category;
}

$where_sql = $where_clauses ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Query: data publikasi
$stmt = $pdo->prepare("
    SELECT *
    FROM view_publikasi_penulis
    $where_sql
    ORDER BY tahun DESC, judul ASC
    LIMIT :limit OFFSET :offset
");

foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$publications = $stmt->fetchAll();

// Total rows (pagination)
$count_stmt = $pdo->prepare("
    SELECT COUNT(*) FROM view_publikasi_penulis $where_sql
");
foreach ($params as $k => $v) $count_stmt->bindValue($k, $v);
$count_stmt->execute();

$total_rows = $count_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Count unique authors
$author_stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT ap.anggota_uuid)
    FROM publikasi p
    JOIN anggota_publikasi ap ON p.uuid = ap.publikasi_uuid
    $where_sql
");

foreach ($params as $k => $v) $author_stmt->bindValue($k, $v);
$author_stmt->execute();
$total_authors = $author_stmt->fetchColumn();

// Group by year
$publications_by_year = [];
foreach ($publications as $pub) {
    $year = $pub['tahun'] ?: 'Tidak Diketahui';
    $publications_by_year[$year][] = $pub;
}
krsort($publications_by_year);

include '../includes/header.php';
include '../includes/navbar.php';
?>
<!-- Page Header -->
<section class="hero-section position-relative py-5" id="heroSection"
    style="color: white; min-height: 500px; overflow: hidden;">
    <?php
    $bg_style = $bg_image
        ? "background: url('$bg_image') center/cover no-repeat; z-index: 0;"
        : 'background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%); z-index: 0;';
    ?>
    <div class="parallax-bg position-absolute top-0 start-0 w-100 h-100"
        style="<?php echo $bg_style; ?> transform: translate3d(0, 0, 0); will-change: transform;">
    </div>

    <div class="position-absolute top-0 start-0 w-100 h-100"
        style="background: linear-gradient(135deg, rgba(30, 75, 163, 0.25) 0%, rgba(74, 144, 226, 0.25) 100%); z-index: 1; pointer-events: none;">
    </div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center justify-content-center min-vh-75 py-5">
            <div class="col-lg-6 text-center">
                <h1 class="display-4 fw-bold mb-3">Publications</h1>
                <p class="lead">Publikasi penelitian dan karya ilmiah AI Lab Polinema</p>
            </div>
        </div>
    </div>
</section>

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

<!-- Publications Section -->
<section class="py-5" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <div class="row g-4">
            <!-- Publications Content -->
            <div class="col-lg-8 order-2 order-lg-1">
                <?php if (!empty($publications_by_year)): ?>
                <?php foreach ($publications_by_year as $year => $pubs): ?>
                <div class="mb-5 publication-year" data-year="<?= $year ?>">
                    <h3 class="fw-bold mb-4 text-primary">
                        <i class="bi bi-calendar3 me-2"></i><?= $year ?>
                    </h3>

                    <div class="row g-4">
                        <?php
                                // Untuk filtered view, tampilkan semua hasil
                                // Untuk view all, limit 5 per tahun
                                $pubs_display = ($filter_year || $filter_category) ? $pubs : array_slice($pubs, 0, 5);
                                ?>
                        <?php foreach ($pubs_display as $pub): ?>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <div class="col-md-1 text-center mb-3 mb-md-0">
                                            <i class="bi bi-file-earmark-text text-primary"
                                                style="font-size: 3rem;"></i>
                                        </div>
                                        <div class="col-md-9">
                                            <h5 class="fw-bold mb-2"><?= htmlspecialchars($pub['judul']); ?></h5>
                                            <div class="mb-2">
                                                <?php if ($pub['penulis_nama']): ?>
                                                <span class="text-muted me-3">
                                                    <i class="bi bi-people-fill me-1"></i>
                                                    <strong><?= htmlspecialchars($pub['penulis_nama']); ?></strong>
                                                </span>
                                                <?php endif; ?>
                                                <?php if ($pub['kategori']): ?>
                                                <span class="badge bg-info me-2">
                                                    <?= htmlspecialchars($pub['kategori']); ?>
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-muted small mb-0">
                                                <i class="bi bi-calendar3 me-1"></i><?= $pub['tahun']; ?>
                                            </p>
                                        </div>
                                        <div class="col-md-2 text-md-end">
                                            <?php if ($pub['tautan']): ?>
                                            <a href="<?= htmlspecialchars($pub['tautan']); ?>" target="_blank"
                                                class="btn btn-primary">
                                                <i class="bi bi-box-arrow-up-right me-2"></i>View
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Tombol "Lihat Selengkapnya" untuk view all -->
                    <?php if (!$filter_year && !$filter_category && count($pubs) > 5): ?>
                    <div class="text-center mt-3">
                        <a href="?year=<?= $year ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-arrow-right-circle me-2"></i>Lihat Selengkapnya (<?= count($pubs) - 5 ?>
                            publikasi lainnya)
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

                <!-- Pagination untuk filtered view -->
                <?php if (($filter_year || $filter_category) && $total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="?year=<?= $filter_year ?: 'all' ?>&category=<?= $filter_category ?: 'all' ?>&page=<?= $page - 1 ?>">&laquo;
                                Sebelumnya</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link"
                                href="?year=<?= $filter_year ?: 'all' ?>&category=<?= $filter_category ?: 'all' ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="?year=<?= $filter_year ?: 'all' ?>&category=<?= $filter_category ?: 'all' ?>&page=<?= $page + 1 ?>">Selanjutnya
                                &raquo;</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
                <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle me-2"></i>Belum ada publikasi yang tersedia
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar Filter -->
            <div class="col-lg-4 order-1 order-lg-2">
                <div class="sticky-top" style="top: 100px; z-index: 1;">
                    <!-- Filter Tahun -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3 text-primary">
                                <i class="bi bi-calendar3 me-2"></i>Filter Tahun
                            </h5>
                            <div class="list-group">
                                <a href="?year=all&category=<?= $filter_category ?: 'all' ?>"
                                    class="list-group-item list-group-item-action <?= !$filter_year ? 'active' : '' ?>">
                                    <i class="bi bi-collection me-2"></i>Semua Tahun
                                </a>
                                <?php foreach ($years as $year): ?>
                                <a href="?year=<?= $year ?>&category=<?= $filter_category ?: 'all' ?>"
                                    class="list-group-item list-group-item-action <?= ($filter_year == $year) ? 'active' : '' ?>">
                                    <i class="bi bi-calendar-event me-2"></i><?= $year ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Kategori -->
                    <?php if (!empty($categories)): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3 text-primary">
                                <i class="bi bi-tags me-2"></i>Filter Kategori
                            </h5>
                            <div class="list-group">
                                <a href="?year=<?= $filter_year ?: 'all' ?>&category=all"
                                    class="list-group-item list-group-item-action <?= !$filter_category ? 'active' : '' ?>">
                                    <i class="bi bi-collection me-2"></i>Semua Kategori
                                </a>
                                <?php foreach ($categories as $cat): ?>
                                <a href="?year=<?= $filter_year ?: 'all' ?>&category=<?= urlencode($cat) ?>"
                                    class="list-group-item list-group-item-action <?= ($filter_category == $cat) ? 'active' : '' ?>">
                                    <i class="bi bi-tag me-2"></i><?= htmlspecialchars($cat) ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<?php if (!empty($publications)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card border-0 shadow-sm h-100 p-4">
                    <h2 class="display-4 fw-bold text-primary mb-2">
                        <?= $total_rows ?>
                    </h2>
                    <p class="text-muted mb-0">Total Publications</p>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card border-0 shadow-sm h-100 p-4">
                    <h2 class="display-4 fw-bold text-primary mb-2">
                        <?= count($publications_by_year) ?>
                    </h2>
                    <p class="text-muted mb-0">Years of Research</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4">
                    <h2 class="display-4 fw-bold text-primary mb-2">
                        <?= $total_authors ?>
                    </h2>
                    <p class="text-muted mb-0">Contributing Authors</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>