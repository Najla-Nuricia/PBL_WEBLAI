<?php
require_once '../config/db.php';
$page_title = 'Publikasi';

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

// Query untuk mendapatkan publikasi dengan logika yang benar
if ($filter_year || $filter_category) {
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

    // Group by year untuk filtered view
    $publications_by_year = [];
    foreach ($publications as $pub) {
        $year = $pub['tahun'] ?: 'Tidak Diketahui';
        $publications_by_year[$year][] = $pub;
    }
    krsort($publications_by_year);
} else {
    $stmt = $pdo->prepare("
        SELECT *
        FROM view_publikasi_penulis
        ORDER BY tahun DESC, judul ASC
    ");
    $stmt->execute();
    $all_publications = $stmt->fetchAll();

    $publications_by_year = [];
    foreach ($all_publications as $pub) {
        $year = $pub['tahun'] ?: 'Tidak Diketahui';
        if (!isset($publications_by_year[$year])) {
            $publications_by_year[$year] = [];
        }
        if (count($publications_by_year[$year]) < 5) {
            $publications_by_year[$year][] = $pub;
        }
    }
    krsort($publications_by_year);
    $publications = $all_publications;
}

// Total rows untuk pagination (hanya untuk filtered view)
if ($filter_year || $filter_category) {
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) FROM view_publikasi_penulis $where_sql
    ");
    foreach ($params as $k => $v) $count_stmt->bindValue($k, $v);
    $count_stmt->execute();
    $total_rows = $count_stmt->fetchColumn();
} else {
    $count_stmt = $pdo->query("SELECT COUNT(*) FROM view_publikasi_penulis");
    $total_rows = $count_stmt->fetchColumn();
}

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

include '../includes/header.php';
include '../includes/navbar.php';
?>

<link rel="stylesheet" href="../assets/css/public_publications.css">
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
                <h1 class="display-4 fw-bold mb-3">Publikasi</h1>
                <p class="lead">Publikasi penelitian dan karya ilmiah AI Lab Polinema</p>
            </div>
        </div>
    </div>
</section>

<script src="../assets/js/parallax.js"></script>

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
                                <?php foreach ($pubs as $pub): ?>
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
                                                                <i class="bi bi-box-arrow-up-right me-2"></i>Lihat
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Tombol "Lihat Selengkapnya" untuk  all -->
                            <?php if (!$filter_year && !$filter_category): ?>
                                <?php
                                $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM view_publikasi_penulis WHERE tahun = ?");
                                $count_stmt->execute([$year]);
                                $total_for_year = $count_stmt->fetchColumn();
                                ?>
                                <?php if ($total_for_year > 5): ?>
                                    <div class="text-center mt-3">
                                        <a href="?year=<?= $year ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-arrow-right-circle me-2"></i>Lihat Selengkapnya
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <!-- Pagination untuk filtered view -->
                    <?php if (($filter_year || $filter_category) && $total_pages > 1): ?>
                        <?php
                        $limit_page = 5;
                        $start_page = max(1, $page - floor($limit_page / 2));
                        $end_page = min($total_pages, $page + floor($limit_page / 2));
                        if ($end_page - $start_page + 1 < $limit_page) {
                            if ($start_page > 1) {
                                $start_page = max(1, $total_pages - $limit_page + 1);
                            } elseif ($end_page < $total_pages) {
                                $end_page = min($total_pages, $limit_page);
                            }
                        }
                        ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link"
                                        href="?year=<?= $filter_year ?: 'all' ?>&category=<?= $filter_category ?: 'all' ?>&page=<?= $page - 1 ?>">&laquo;
                                        Sebelumnya</a>
                                </li>
                                <?php if ($start_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="?year=<?= $filter_year ?: 'all' ?>&category=<?= $filter_category ?: 'all' ?>&page=1">1</a>
                                    </li>
                                    <?php if ($start_page > 2): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                        <a class="page-link"
                                            href="?year=<?= $filter_year ?: 'all' ?>&category=<?= $filter_category ?: 'all' ?>&page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <?php if ($end_page < $total_pages): ?>
                                    <?php if ($end_page < $total_pages - 1): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="?year=<?= $filter_year ?: 'all' ?>&category=<?= $filter_category ?: 'all' ?>&page=<?= $total_pages ?>"><?= $total_pages ?></a>
                                    </li>
                                <?php endif; ?>
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
                                <i class="bi bi-calendar3 me-2"></i>Filter per Tahun
                            </h5>
                            <!-- Dropdown Select -->
                            <div class="mb-3">
                                <label class="form-label small text-muted">
                                    <i class="bi bi-funnel me-1"></i>Pilih Tahun
                                </label>
                                <select id="year-select" name="year" class="form-select select-enhanced">
                                    <option value="all">Semua Tahun</option>
                                    <?php foreach ($years as $year): ?>
                                        <option value="<?= $year ?>" <?= ($filter_year == $year) ? 'selected' : '' ?>>
                                            <?= $year ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Clear Filter Button -->
                            <?php if ($filter_year !== null): ?>
                                <div class="mt-3 pt-3 border-top">
                                    <a href="?year=all&category=<?= $filter_category ?: 'all' ?>"
                                        class="btn btn-sm btn-outline-primary w-100">
                                        <i class="bi bi-x-circle me-2"></i>Hapus Filter Tahun
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Filter Kategori -->
                    <?php if (!empty($categories)): ?>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3 text-primary">
                                    <i class="bi bi-tags me-2"></i>Filter per Kategori
                                </h5>
                                <!-- Dropdown Select -->
                                <div class="mb-3">
                                    <label class="form-label small text-muted">
                                        <i class="bi bi-funnel me-1"></i>Pilih Kategori
                                    </label>
                                    <select id="category-select" name="category" class="form-select select-enhanced">
                                        <option value="all">Semua Kategori</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= htmlspecialchars($cat) ?>" <?= ($filter_category == $cat) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Clear Filter Button -->
                                <?php if ($filter_category !== null): ?>
                                    <div class="mt-3 pt-3 border-top">
                                        <a href="?year=<?= $filter_year ?: 'all' ?>&category=all"
                                            class="btn btn-sm btn-outline-primary w-100">
                                            <i class="bi bi-x-circle me-2"></i>Hapus Filter Kategori
                                        </a>
                                    </div>
                                <?php endif; ?>
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
                        <p class="text-muted mb-0">Total Publikasi</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="card border-0 shadow-sm h-100 p-4">
                        <h2 class="display-4 fw-bold text-primary mb-2">
                            <?= count($publications_by_year) ?>
                        </h2>
                        <p class="text-muted mb-0">Tahun Penelitian</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 p-4">
                        <h2 class="display-4 fw-bold text-primary mb-2">
                            <?= $total_authors ?>
                        </h2>
                        <p class="text-muted mb-0">Penulis Berkontribusi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<script>
    document.getElementById('year-select').addEventListener('change', function() {
        let year = this.value;
        let category = "<?= $filter_category ?: 'all' ?>";
        window.location.href = "?year=" + year + "&category=" + category;
    });

    document.getElementById('category-select').addEventListener('change', function() {
        let category = this.value;
        let year = "<?= $filter_year ?: 'all' ?>";
        window.location.href = "?year=" + year + "&category=" + category;
    });
</script>

<?php include '../includes/footer.php'; ?>