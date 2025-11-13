<?php
require_once '../config/db.php';
$page_title = 'Publications';

// Pagination setup
$filter_year = isset($_GET['year']) && $_GET['year'] !== 'all' ? (int)$_GET['year'] : null;
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil daftar tahun untuk sidebar
$years_stmt = $pdo->query("SELECT DISTINCT tahun FROM publikasi ORDER BY tahun DESC");
$years = $years_stmt->fetchAll(PDO::FETCH_COLUMN);

// Jika filter per tahun
if ($filter_year) {
    $stmt = $pdo->prepare("
        SELECT p.*, a.nama as penulis_nama
        FROM publikasi p
        LEFT JOIN anggota a ON p.penulis_id = a.uuid
        WHERE p.tahun = :tahun
        ORDER BY p.judul ASC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':tahun', $filter_year, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $publications = $stmt->fetchAll();

    // Hitung total data untuk tahun itu
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM publikasi WHERE tahun = :tahun");
    $count_stmt->execute(['tahun' => $filter_year]);
    $total_rows = $count_stmt->fetchColumn();
    $total_pages = ceil($total_rows / $limit);

    // Ambil total contributor
    $author_stmt = $pdo->prepare("
        SELECT DISTINCT a.uuid
        FROM publikasi p
        JOIN anggota a ON p.penulis_id = a.uuid
        WHERE p.tahun = :tahun
    ");
    $author_stmt->bindValue(':tahun', $filter_year, PDO::PARAM_INT);
    $author_stmt->execute();
    $authors = $author_stmt->fetchAll(PDO::FETCH_COLUMN);
} else {
    // Semua tahun: ambil semua data tanpa limit
    $stmt = $pdo->query("
        SELECT p.*, a.nama as penulis_nama
        FROM publikasi p
        LEFT JOIN anggota a ON p.penulis_id = a.uuid
        ORDER BY p.tahun DESC, p.judul ASC
    ");
    $publications = $stmt->fetchAll();

    // Ambil total contributor
    $author_stmt = $pdo->query("
        SELECT DISTINCT a.uuid
        FROM publikasi p
        JOIN anggota a ON p.penulis_id = a.uuid"
    );
    $authors = $author_stmt->fetchAll(PDO::FETCH_COLUMN);
}

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
<section class="py-5" style="background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%); color: white;">
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h1 class="display-4 fw-bold mb-3">Publications</h1>
                <p class="lead">Publikasi penelitian dan karya ilmiah AI Lab Polinema</p>
            </div>
        </div>
    </div>
</section>

<!-- Publications Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Publications -->
            <div class="col-lg-8 order-2 order-lg-1">
                <?php if (!empty($publications_by_year)): ?>
                    <?php foreach ($publications_by_year as $year => $pubs): ?>
                        <div class="mb-5 publication-year" data-year="<?= $year ?>">
                            <h3 class="fw-bold mb-4 text-primary">
                                <i class="bi bi-calendar3 me-2"></i><?= $year ?>
                            </h3>

                            <div class="row g-4">
                                <?php
                                // Menampilkan 5 pubs per tahun
                                $pubs_year = $filter_year ? $pubs : array_slice($pubs, 0, 5);
                                ?>
                                <?php foreach ($pubs_year as $pub): ?>
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body p-4">
                                                <div class="row align-items-center">
                                                    <div class="col-md-1 text-center mb-3 mb-md-0">
                                                        <i class="bi bi-file-earmark-text text-primary" style="font-size: 3rem;"></i>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <h5 class="fw-bold mb-2"><?= htmlspecialchars($pub['judul']); ?></h5>
                                                        <p class="text-muted mb-2">
                                                        <?php if ($pub['penulis_nama']): ?>
                                                            <i class="bi bi-person me-1"></i><strong><?= htmlspecialchars($pub['penulis_nama']); ?></strong>
                                                        <?php endif; ?>
                                                        <?php if ($pub['kategori']): ?>
                                                            <span class="ms-3"><i class="bi bi-tag me-1"></i><?= htmlspecialchars($pub['kategori']); ?></span>
                                                        <?php endif; ?>
                                                        </p>
                                                        <p class="text-muted small mb-0">
                                                        <i class="bi bi-calendar3 me-1"></i><?= $pub['tahun']; ?>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-2 text-md-end">
                                                        <?php if ($pub['tautan']): ?>
                                                        <a href="<?= htmlspecialchars($pub['tautan']); ?>" target="_blank" class="btn btn-primary">
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
                            <!-- Pagination features -->
                            <?php if ($filter_year && $total_pages > 1): ?>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center mt-4">
                                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?year=<?= $year ?>&page=<?= $page - 1 ?>">&laquo; Sebelumnya</a>
                                        </li>
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                                <a class="page-link" href="?year=<?= $year ?>&page=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?year=<?= $year ?>&page=<?= $page + 1 ?>">Selanjutnya &raquo;</a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php elseif (!$filter_year && count($pubs) > 5): ?>
                                <div class="text-center mt-3">
                                    <a href="?year=<?= $year ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-arrow-right-circle me-2"></i>Lihat Selengkapnya
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle me-2"></i>Belum ada publikasi yang tersedia
                    </div>
                <?php endif; ?>
            </div>

            <!-- Filter Tahun Sidebar -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px; z-index: 1;">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 text-primary">
                        <i class="bi bi-funnel me-2"></i>Filter Tahun
                    </h5>
                    <div class="list-group">
                        <a href="?year=all" class="list-group-item list-group-item-action <?= !$filter_year ? 'active' : '' ?>">
                            <i class="bi bi-collection me-2"></i>Semua Tahun
                        </a>
                        <?php foreach ($years as $year): ?>
                            <a href="?year=<?= $year ?>" class="list-group-item list-group-item-action <?= ($filter_year == $year) ? 'active' : '' ?>">
                            <i class="bi bi-calendar-event me-2"></i><?= $year ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
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
                            <?php if ($filter_year){
                                echo ($total_rows);
                            } else {
                                echo count($publications);
                            } ?>
                        </h2>
                        <p class="text-muted mb-0">Total Publications</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="card border-0 shadow-sm h-100 p-4">
                        <h2 class="display-4 fw-bold text-primary mb-2">
                            <?php echo count($publications_by_year); ?>
                        </h2>
                        <p class="text-muted mb-0">Years of Research</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 p-4">
                        <h2 class="display-4 fw-bold text-primary mb-2">
                            <?php
                                echo count($authors);
                            ?>
                        </h2>
                        <p class="text-muted mb-0">Contributing Authors</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<script>
// Filter Tahun
document.addEventListener("DOMContentLoaded", function() {
    const buttons = document.querySelectorAll(".filter-year");
    const sections = document.querySelectorAll(".publication-year");

    if (!buttons.length || !sections.length) return;

    buttons.forEach((btn) => {
        btn.addEventListener("click", function () {
        const year = this.dataset.year;

        buttons.forEach((b) => b.classList.remove("active"));
        this.classList.add("active");
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>