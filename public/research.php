<?php
require_once '../config/db.php';
$page_title = 'Research & Products';

// Get products with author info
$stmt = $pdo->query("
    SELECT p.*, a.nama as pembuat_nama 
    FROM produk p 
    LEFT JOIN anggota a ON p.pembuat_id = a.uuid 
    ORDER BY p.tahun DESC, p.nama ASC
");
$products = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- Page Header -->
<section class="py-5" style="background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%); color: white;">
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h1 class="display-4 fw-bold mb-3">Research & Products</h1>
                <p class="lead">Produk dan hasil penelitian AI Lab Polinema</p>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="py-5">
    <div class="container">
        <?php if (!empty($products)): ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <?php if ($product['path_gambar']): ?>
                                <img src="assets/img/<?php echo htmlspecialchars($product['path_gambar']); ?>"
                                    class="card-img-top"
                                    alt="<?php echo htmlspecialchars($product['nama']); ?>"
                                    style="height: 200px; object-fit: cover;">
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
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Belum ada produk yang tersedia
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Research Areas Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col text-center">
                <h2 class="section-title">Research Areas</h2>
                <p class="section-subtitle">Bidang penelitian utama AI Lab Polinema</p>
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

<?php include '../includes/footer.php'; ?>