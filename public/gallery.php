<?php
require_once '../config/db.php';
$page_title = 'Gallery';

// Get galleries with photo count
$stmt = $pdo->query("
    SELECT g.*, COUNT(f.uuid) as foto_count 
    FROM galeri g 
    LEFT JOIN foto f ON g.uuid = f.id_galeri 
    GROUP BY g.uuid 
    ORDER BY g.created_at DESC
");
$galleries = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- Page Header -->
<section class="py-5" style="background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%); color: white;">
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h1 class="display-4 fw-bold mb-3">Gallery</h1>
                <p class="lead">Dokumentasi kegiatan dan suasana AI Lab Polinema</p>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="py-5">
    <div class="container">
        <?php if (!empty($galleries)): ?>
            <?php foreach ($galleries as $index => $gallery): ?>
                <?php
                // Get photos for this gallery
                $stmt = $pdo->prepare("SELECT * FROM foto WHERE id_galeri = ? ORDER BY created_at DESC");
                $stmt->execute([$gallery['uuid']]);
                $photos = $stmt->fetchAll();
                ?>

                <div class="mb-5">
                    <div class="row mb-3">
                        <div class="col">
                            <h3 class="fw-bold text-primary mb-2">
                                <?php echo htmlspecialchars($gallery['judul']); ?>
                            </h3>
                            <?php if ($gallery['deskripsi']): ?>
                                <p class="text-muted">
                                    <?php echo htmlspecialchars($gallery['deskripsi']); ?>
                                </p>
                            <?php endif; ?>
                            <p class="text-muted small">
                                <i class="bi bi-images me-1"></i>
                                <?php echo $gallery['foto_count']; ?> foto
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($photos)): ?>
                        <div class="row g-3">
                            <?php foreach ($photos as $photo): ?>
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <img src="../assets/img/<?php echo htmlspecialchars($photo['path_gambar']); ?>"
                                            class="card-img-top gallery-img"
                                            alt="Gallery Photo"
                                            style="height: 200px; object-fit: cover; cursor: pointer;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#imageModal<?php echo $index . '_' . $photo['uuid']; ?>">
                                    </div>

                                    <!-- Modal for full image -->
                                    <div class="modal fade" id="imageModal<?php echo $index . '_' . $photo['uuid']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title">
                                                        <?php echo htmlspecialchars($gallery['judul']); ?>
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-0">
                                                    <img src="../assets/img/<?php echo htmlspecialchars($photo['path_gambar']); ?>"
                                                        class="img-fluid w-100"
                                                        alt="Gallery Photo">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Belum ada foto untuk galeri ini
                        </div>
                    <?php endif; ?>

                    <?php if ($index < count($galleries) - 1): ?>
                        <hr class="my-5">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Belum ada galeri yang tersedia
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
    .gallery-img {
        transition: transform 0.3s ease;
    }

    .gallery-img:hover {
        transform: scale(1.05);
    }
</style>

<?php include '../includes/footer.php'; ?>