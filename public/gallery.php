<?php
require_once '../config/db.php';
require_once '../lang/init.php';
$page_title = 'Gallery';

// Fetch dashboard background
$stmt_bg = $pdo->query("SELECT * FROM dashboard_foto ORDER BY updated_at DESC LIMIT 1");
$dashboard_bg = $stmt_bg->fetch();
$bg_image = '';
if ($dashboard_bg && $dashboard_bg['path_gambar']) {
    $bg_image = '../assets/img/dashboard/' . htmlspecialchars($dashboard_bg['path_gambar']);
}

// Pagination untuk Gallery
$galleries_per_page = 3;
$gallery_page = isset($_GET['gallery_page']) ? max(1, (int)$_GET['gallery_page']) : 1;
$gallery_offset = ($gallery_page - 1) * $galleries_per_page;

// Count total galleries
$count_stmt = $pdo->query("SELECT COUNT(*) FROM view_galeri_with_foto_count");
$total_galleries = $count_stmt->fetchColumn();
$total_gallery_pages = ceil($total_galleries / $galleries_per_page);

// Get galleries with photo count and pagination
$stmt = $pdo->prepare("
    SELECT *
    FROM view_galeri_with_foto_count
    ORDER BY created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $galleries_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $gallery_offset, PDO::PARAM_INT);
$stmt->execute();
$galleries = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<section class="hero-section position-relative py-5" id="heroSection"
    style="color: white; min-height: 500px; overflow: hidden;">
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
    <div class="position-absolute top-0 start-0 w-100 h-100"
        style="background: linear-gradient(135deg, rgba(30, 75, 163, 0.25) 0%, rgba(74, 144, 226, 0.25) 100%); z-index: 1; pointer-events: none;">
    </div>
    <!-- Content -->
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center justify-content-center min-vh-75 py-5">
            <div class="col-lg-6 text-center">
                <h1 class="display-4 fw-bold mb-3"><?= __('gallery_hero_title') ?></h1>
                <p class="lead"><?= __('gallery_hero_desc') ?></p>
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

<!-- Gallery Section -->
<section class="py-5" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <?php if (!empty($galleries)): ?>
            <?php foreach ($galleries as $index => $gallery): ?>
                <?php
                // Pagination untuk foto di setiap gallery
                $photos_per_page = 4; // 4 foto per halaman (4 kolom x 3 baris)
                $photo_page_key = 'photo_page_' . $gallery['uuid'];
                $photo_page = isset($_GET[$photo_page_key]) ? max(1, (int)$_GET[$photo_page_key]) : 1;
                $photo_offset = ($photo_page - 1) * $photos_per_page;

                // Count total photos for this gallery
                $count_photo_stmt = $pdo->prepare("SELECT COUNT(*) FROM foto WHERE id_galeri = ?");
                $count_photo_stmt->execute([$gallery['uuid']]);
                $total_photos = $count_photo_stmt->fetchColumn();
                $total_photo_pages = ceil($total_photos / $photos_per_page);

                // Get photos for this gallery with pagination
                $stmt = $pdo->prepare("
                    SELECT * FROM foto 
                    WHERE id_galeri = :galeri_id 
                    ORDER BY created_at DESC 
                    LIMIT :limit OFFSET :offset
                ");
                $stmt->bindValue(':galeri_id', $gallery['uuid']);
                $stmt->bindValue(':limit', $photos_per_page, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $photo_offset, PDO::PARAM_INT);
                $stmt->execute();
                $photos = $stmt->fetchAll();
                ?>

                <div class="mb-5" id="gallery-<?php echo $gallery['uuid']; ?>">
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
                                <?php echo $gallery['foto_count']; ?> <?= __('photos') ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($photos)): ?>
                        <div class="row g-3 gallery-row">
                            <?php foreach ($photos as $photo): ?>
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <img src="../assets/img/<?php echo htmlspecialchars($photo['path_gambar']); ?>"
                                            class="card-img-top gallery-img" alt="Gallery Photo"
                                            style="height: 200px; object-fit: cover; cursor: pointer;" data-bs-toggle="modal"
                                            data-bs-target="#imageModal" data-title="<?php echo htmlspecialchars($gallery['judul']); ?>"
                                            data-src="../assets/img/<?php echo htmlspecialchars($photo['path_gambar']); ?>"
                                            onerror="this.src='../assets/img/placeholder.jpg'">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Photo Pagination -->
                        <?php if ($total_photo_pages > 1): ?>
                            <nav aria-label="Photo pagination" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?= $photo_page <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?gallery_page=<?= $gallery_page ?>&<?= $photo_page_key ?>=<?= $photo_page - 1 ?>#gallery-<?= $gallery['uuid'] ?>">
                                            &laquo; <?= __('previous') ?>
                                        </a>
                                    </li>

                                    <?php
                                    $start_page = max(1, $photo_page - 2);
                                    $end_page = min($total_photo_pages, $photo_page + 2);

                                    if ($start_page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?gallery_page=<?= $gallery_page ?>&<?= $photo_page_key ?>=1#gallery-<?= $gallery['uuid'] ?>">
                                                1
                                            </a>
                                        </li>
                                        <?php if ($start_page > 2): ?>
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                        <li class="page-item <?= $photo_page == $i ? 'active' : '' ?>">
                                            <a class="page-link"
                                                href="?gallery_page=<?= $gallery_page ?>&<?= $photo_page_key ?>=<?= $i ?>#gallery-<?= $gallery['uuid'] ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($end_page < $total_photo_pages): ?>
                                        <?php if ($end_page < $total_photo_pages - 1): ?>
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                        <?php endif; ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?gallery_page=<?= $gallery_page ?>&<?= $photo_page_key ?>=<?= $total_photo_pages ?>#gallery-<?= $gallery['uuid'] ?>">
                                                <?= $total_photo_pages ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <li class="page-item <?= $photo_page >= $total_photo_pages ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?gallery_page=<?= $gallery_page ?>&<?= $photo_page_key ?>=<?= $photo_page + 1 ?>#gallery-<?= $gallery['uuid'] ?>">
                                            <?= __('next') ?> &raquo;
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <?= __('no_photos') ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($index < count($galleries) - 1): ?>
                        <hr class="my-5">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <!-- Gallery Pagination -->
            <?php if ($total_gallery_pages > 1): ?>
                <nav aria-label="Gallery pagination" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $gallery_page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?gallery_page=<?= $gallery_page - 1 ?>">
                                &laquo; <?= __('previous') ?>
                            </a>
                        </li>

                        <?php
                        $start_page = max(1, $gallery_page - 2);
                        $end_page = min($total_gallery_pages, $gallery_page + 2);

                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?gallery_page=1">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= $gallery_page == $i ? 'active' : '' ?>">
                                <a class="page-link" href="?gallery_page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($end_page < $total_gallery_pages): ?>
                            <?php if ($end_page < $total_gallery_pages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?gallery_page=<?= $total_gallery_pages ?>">
                                    <?= $total_gallery_pages ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="page-item <?= $gallery_page >= $total_gallery_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?gallery_page=<?= $gallery_page + 1 ?>">
                                <?= __('next') ?> &raquo;
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                <?= __('no_galleries') ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Universal Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <img src="" class="img-fluid w-100" alt="Gallery Photo">
            </div>
        </div>
    </div>
</div>

<!-- Script untuk modal universal -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageModal = document.getElementById('imageModal');
        const modalTitle = imageModal.querySelector('.modal-title');
        const modalImg = imageModal.querySelector('img');

        // Event listener untuk semua gambar gallery
        document.querySelectorAll('.gallery-img').forEach(img => {
            img.addEventListener('click', function() {
                modalTitle.textContent = this.dataset.title;
                modalImg.src = this.dataset.src;
            });
        });

        // Smooth scroll to gallery anchor after page load
        if (window.location.hash) {
            setTimeout(function() {
                const target = document.querySelector(window.location.hash);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }, 100);
        }
    });
</script>

<style>
    .gallery-img {
        transition: transform 0.3s ease;
    }

    .gallery-img:hover {
        transform: scale(1.05);
    }

    /* Smooth scroll behavior */
    html {
        scroll-behavior: smooth;
    }
</style>

<?php include '../includes/footer.php'; ?>