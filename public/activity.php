<?php
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

$page_title = 'Research & Activities';

// Pagination setup
$limit = 9; // 9 card per page
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Get filter
$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Count total rows for pagination
$count_query = "SELECT COUNT(*) FROM kegiatan WHERE 1=1";
if ($kategori_filter) {
    $count_query .= " AND kategori_kegiatan = :kategori";
}
$count_stmt = $pdo->prepare($count_query);
if ($kategori_filter) {
    $count_stmt->execute(['kategori' => $kategori_filter]);
} else {
    $count_stmt->execute();
}
$total_rows = $count_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Build query with limit and offset
$query = "SELECT * FROM kegiatan WHERE 1=1";
if ($kategori_filter) {
    $query .= " AND kategori_kegiatan = :kategori";
}
$query .= " ORDER BY tanggal DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
if ($kategori_filter) {
    $stmt->bindValue(':kategori', $kategori_filter);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$kegiatan_list = $stmt->fetchAll();

// Get single activity if ID provided
$single_kegiatan = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM kegiatan WHERE uuid = ?");
    $stmt->execute([$_GET['id']]);
    $single_kegiatan = $stmt->fetch();
}
?>

<!-- Page Header -->
<section class="hero-section position-relative py-5 bg-primary text-white">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Activities</h1>
        <p class="lead">Kegiatan seminar, workshop, dan pengabdian masyarakat AI Lab Polinema</p>
    </div>
</section>

<?php if ($single_kegiatan): ?>
<!-- Single Activity Detail -->
<!-- (kode detail kegiatan tetap sama) -->
<?php else: ?>
<!-- Activities List -->
<section class="py-5">
    <div class="container">
        <!-- Filter -->
        <div class="row mb-4">
            <div class="col">
                <div class="btn-group" role="group">
                    <a href="activity.php"
                        class="btn btn-<?php echo !$kategori_filter ? 'primary' : 'outline-primary'; ?>">Semua</a>
                    <a href="activity.php?kategori=workshop"
                        class="btn btn-<?php echo strtolower($kategori_filter) == 'workshop' ? 'primary' : 'outline-primary'; ?>">Workshop</a>
                    <a href="activity.php?kategori=seminar"
                        class="btn btn-<?php echo strtolower($kategori_filter) == 'seminar' ? 'primary' : 'outline-primary'; ?>">Seminar</a>
                    <a href="activity.php?kategori=pengabdian"
                        class="btn btn-<?php echo strtolower($kategori_filter) == 'pengabdian' ? 'primary' : 'outline-primary'; ?>">Pengabdian</a>
                </div>
            </div>
        </div>

        <!-- Activities Grid -->
        <div class="row g-4">
            <?php if (!empty($kegiatan_list)): ?>
            <?php foreach ($kegiatan_list as $kegiatan): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <span class="badge bg-<?php
                                        echo $kegiatan['kategori_kegiatan'] == 'workshop' ? 'success' :
                                             ($kegiatan['kategori_kegiatan'] == 'seminar' ? 'info' : 'warning');
                                    ?> mb-2">
                            <?php echo ucfirst($kegiatan['kategori_kegiatan']); ?>
                        </span>

                        <h5 class="card-title fw-bold">
                            <?php echo htmlspecialchars($kegiatan['nama']); ?>
                        </h5>

                        <p class="text-muted small mb-3">
                            <i class="bi bi-calendar me-2"></i>
                            <?php echo date('d M Y', strtotime($kegiatan['tanggal'])); ?>
                            <br>
                            <i class="bi bi-person me-2"></i>
                            <?php echo htmlspecialchars($kegiatan['pemateri']); ?>
                        </p>

                        <p class="card-text text-muted">
                            <?php echo substr(htmlspecialchars($kegiatan['deskripsi_singkat']), 0, 120) . '...'; ?>
                        </p>

                        <a href="research.php?id=<?php echo $kegiatan['uuid']; ?>"
                            class="btn btn-sm btn-outline-primary">
                            Lihat Detail <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle me-2"></i>
                    Belum ada kegiatan untuk kategori ini
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <!-- Previous button -->
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?<?php
                                $params = $_GET;
                                $params['page'] = max(1, $page - 1);
                                echo http_build_query($params);
                            ?>">&laquo; Sebelumnya</a>
                </li>

                <!-- Page numbers -->
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php
                                    $params = $_GET;
                                    $params['page'] = $i;
                                    echo http_build_query($params);
                                ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>

                <!-- Next button -->
                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?<?php
                                $params = $_GET;
                                $params['page'] = min($total_pages, $page + 1);
                                echo http_build_query($params);
                            ?>">Selanjutnya &raquo;</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>