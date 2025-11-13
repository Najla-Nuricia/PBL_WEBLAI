<?php
require_once '../config/db.php';
$page_title = 'Publications';

// Get publications with author info
$stmt = $pdo->query("
    SELECT p.*, a.nama as penulis_nama 
    FROM publikasi p 
    LEFT JOIN anggota a ON p.penulis_id = a.uuid 
    ORDER BY p.tahun DESC, p.judul ASC
");
$publications = $stmt->fetchAll();

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
<section class="page-header py-5" style="background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%); color: white;">
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
        <?php if (!empty($publications_by_year)): ?>
            <?php foreach ($publications_by_year as $year => $pubs): ?>
                <div class="mb-5">
                    <h3 class="fw-bold mb-4 text-primary">
                        <i class="bi bi-calendar3 me-2"></i><?php echo $year; ?>
                    </h3>

                    <div class="row g-4">
                        <?php foreach ($pubs as $pub): ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-1 text-center mb-3 mb-md-0">
                                                <i class="bi bi-file-earmark-text text-primary" style="font-size: 3rem;"></i>
                                            </div>
                                            <div class="col-md-9">
                                                <h5 class="fw-bold mb-2">
                                                    <?php echo htmlspecialchars($pub['judul']); ?>
                                                </h5>

                                                <p class="text-muted mb-2">
                                                    <?php if ($pub['penulis_nama']): ?>
                                                        <i class="bi bi-person me-1"></i>
                                                        <strong><?php echo htmlspecialchars($pub['penulis_nama']); ?></strong>
                                                    <?php endif; ?>

                                                    <?php if ($pub['kategori']): ?>
                                                        <span class="ms-3">
                                                            <i class="bi bi-tag me-1"></i>
                                                            <?php echo htmlspecialchars($pub['kategori']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </p>

                                                <p class="text-muted small mb-0">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    <?php echo $pub['tahun']; ?>
                                                </p>
                                            </div>
                                            <div class="col-md-2 text-md-end">
                                                <?php if ($pub['tautan']): ?>
                                                    <a href="<?php echo htmlspecialchars($pub['tautan']); ?>"
                                                        target="_blank"
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
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Belum ada publikasi yang tersedia
            </div>
        <?php endif; ?>
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
                            <?php echo count($publications); ?>
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
                            $unique_authors = array_unique(array_column($publications, 'penulis_id'));
                            echo count(array_filter($unique_authors));
                            ?>
                        </h2>
                        <p class="text-muted mb-0">Contributing Authors</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>