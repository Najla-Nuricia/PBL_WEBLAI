<?php
require_once '../config/db.php';
// require_once '../helpers/background.php';
$page_title = 'Gallery';

// Fetch dashboard background
$stmt_bg = $pdo->query("SELECT * FROM dashboard_foto ORDER BY updated_at DESC LIMIT 1");
$dashboard_bg = $stmt_bg->fetch();
$bg_image = '';
if ($dashboard_bg && $dashboard_bg['path_gambar']) {
    $bg_image = '../assets/img/dashboard/' . htmlspecialchars($dashboard_bg['path_gambar']);
}

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
            <div class="col-lg-6 text-center">
                <h1 class="display-4 fw-bold mb-3">Gallery</h1>
                <p class="lead">Dokumentasi kegiatan dan suasana AI Lab Polinema</p>
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

                // Only apply parallax when hero section is visible
                if (scrolled < heroHeight) {
                    // Adjust the 0.5 value to control parallax speed (lower = slower, higher = faster)
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