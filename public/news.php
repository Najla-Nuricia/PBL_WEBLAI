<?php
require_once '../config/db.php';
$page_title = 'News & Events';

// Fetch dashboard background
$stmt_bg = $pdo->query("SELECT * FROM dashboard_foto ORDER BY updated_at DESC LIMIT 1");
$dashboard_bg = $stmt_bg->fetch();
$bg_image = '';
if ($dashboard_bg && $dashboard_bg['path_gambar']) {
    $bg_image = '../assets/img/dashboard/' . htmlspecialchars($dashboard_bg['path_gambar']);
}

// Get filter
$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Get single news if ID provided
$single_news = null;
$news_fotos = [];
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM berita WHERE uuid = ?");
    $stmt->execute([$_GET['id']]);
    $single_news = $stmt->fetch();

    // Ambil foto-foto berita
    if ($single_news) {
        $stmt_foto = $pdo->prepare("SELECT * FROM berita_foto WHERE berita_id = ? ORDER BY uploaded_at ASC");
        $stmt_foto->execute([$single_news['uuid']]);
        $news_fotos = $stmt_foto->fetchAll();
    }
} else {
    // Pagination settings for list view
    $items_per_page = 6;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($page - 1) * $items_per_page;

    // Count total items
    $count_query = "SELECT COUNT(*) FROM berita WHERE 1=1";
    if ($kategori_filter) {
        $count_query .= " AND kategori = :kategori";
    }
    $count_stmt = $pdo->prepare($count_query);
    if ($kategori_filter) {
        $count_stmt->execute(['kategori' => $kategori_filter]);
    } else {
        $count_stmt->execute();
    }
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);

    // Build query with pagination
    $query = "SELECT * FROM berita WHERE 1=1";
    if ($kategori_filter) {
        $query .= " AND kategori = :kategori";
    }
    $query .= " ORDER BY tanggal DESC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($query);
    if ($kategori_filter) {
        $stmt->bindValue(':kategori', $kategori_filter);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $news_list = $stmt->fetchAll();
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- Add CSS for line-clamp -->
<style>
    /* Untuk teks maksimal 2 baris */
    .line-clamp-2 {
        display: block;
        display: -webkit-box;
        overflow: hidden;
        text-overflow: ellipsis;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;

        /* Standar modern */
        line-clamp: 2;
        box-orient: vertical;
    }

    /* Untuk teks maksimal 3 baris */
    .line-clamp-3 {
        display: block;
        display: -webkit-box;
        overflow: hidden;
        text-overflow: ellipsis;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;

        line-clamp: 3;
        box-orient: vertical;
    }
</style>

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
            <div class="col-lg-6 text-center">
                <h1 class="display-4 fw-bold mb-3">News & Events</h1>
                <p class="lead">Berita terkini dan agenda kegiatan AI Lab Polinema</p>
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

<?php if ($single_news): ?>
    <!-- Single News Detail -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <a href="news.php" class="btn btn-outline-primary mb-4">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Berita
                    </a>

                    <div class="card border-0 shadow-sm">
                        <?php if (!empty($news_fotos)): ?>
                            <!-- Photo Gallery Carousel -->
                            <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel">
                                <?php if (count($news_fotos) > 1): ?>
                                    <div class="carousel-indicators">
                                        <?php foreach ($news_fotos as $index => $foto): ?>
                                            <button type="button" data-bs-target="#newsCarousel" data-bs-slide-to="<?php echo $index; ?>"
                                                class="<?php echo $index === 0 ? 'active' : ''; ?>"
                                                aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                                aria-label="Slide <?php echo $index + 1; ?>"></button>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="carousel-inner">
                                    <?php foreach ($news_fotos as $index => $foto):
                                        $foto_path = rtrim($_ENV['UPLOAD_DIR'], '/') . '/berita/' . htmlspecialchars($foto['file_path']);
                                    ?>
                                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                            <img src="<?php echo $foto_path; ?>"
                                                class="d-block w-100"
                                                style="max-height: 500px; object-fit: cover;"
                                                alt="<?php echo htmlspecialchars($foto['caption'] ?: 'Foto berita'); ?>"
                                                onerror="this.src='../assets/img/placeholder.jpg'">
                                            <?php if (!empty($foto['caption'])): ?>
                                                <div class="carousel-caption d-none d-md-block">
                                                    <div class="bg-dark bg-opacity-75 rounded p-2">
                                                        <p class="mb-0"><?php echo htmlspecialchars($foto['caption']); ?></p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if (count($news_fotos) > 1): ?>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="card-body p-4 p-md-5">
                            <span class="badge bg-<?php
                                                    echo $single_news['kategori'] == 'agenda' ? 'success' : ($single_news['kategori'] == 'pengumuman' ? 'warning' : 'primary');
                                                    ?> mb-3 fs-6">
                                <?php echo ucfirst($single_news['kategori']); ?>
                            </span>

                            <h1 class="fw-bold mb-3"><?php echo htmlspecialchars($single_news['judul']); ?></h1>

                            <div class="d-flex flex-wrap gap-3 text-muted mb-4">
                                <?php if (!empty($single_news['penulis'])): ?>
                                    <span>
                                        <i class="bi bi-person me-1"></i>
                                        <?php echo htmlspecialchars($single_news['penulis']); ?>
                                    </span>
                                <?php endif; ?>
                                <span>
                                    <i class="bi bi-calendar me-1"></i>
                                    <?php echo date('d F Y', strtotime($single_news['tanggal'])); ?>
                                </span>
                                <?php if ($single_news['tempat']): ?>
                                    <span>
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <?php echo htmlspecialchars($single_news['tempat']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <hr>

                            <div class="content" style="text-align: justify; line-height: 1.8; font-size: 1.05rem;">
                                <?php echo nl2br(htmlspecialchars($single_news['deskripsi'])); ?>
                            </div>

                            <?php if (!empty($news_fotos) && count($news_fotos) > 1): ?>
                                <hr class="my-4">
                                <h5 class="fw-bold mb-3">Galeri Foto</h5>
                                <div class="row g-3">
                                    <?php foreach ($news_fotos as $foto):
                                        $foto_path = rtrim($_ENV['UPLOAD_DIR'], '/') . '/berita/' . htmlspecialchars($foto['file_path']);
                                    ?>
                                        <div class="col-md-4 col-sm-6">
                                            <a href="<?php echo $foto_path; ?>" data-bs-toggle="modal" data-bs-target="#photoModal"
                                                data-photo="<?php echo $foto_path; ?>"
                                                data-caption="<?php echo htmlspecialchars($foto['caption'] ?: ''); ?>">
                                                <img src="<?php echo $foto_path; ?>"
                                                    class="img-fluid rounded shadow-sm hover-zoom"
                                                    style="height: 200px; width: 100%; object-fit: cover; cursor: pointer; transition: transform 0.3s;"
                                                    alt="<?php echo htmlspecialchars($foto['caption'] ?: 'Foto berita'); ?>"
                                                    onerror="this.src='../assets/img/placeholder.jpg'"
                                                    onmouseover="this.style.transform='scale(1.05)'"
                                                    onmouseout="this.style.transform='scale(1)'">
                                            </a>
                                            <?php if (!empty($foto['caption'])): ?>
                                                <small class="text-muted d-block mt-2"><?php echo htmlspecialchars($foto['caption']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid rounded" alt="Preview">
                    <p id="modalCaption" class="text-white mt-3"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const photoModal = document.getElementById('photoModal');
            if (photoModal) {
                photoModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const photoSrc = button.getAttribute('data-photo');
                    const caption = button.getAttribute('data-caption');

                    const modalImage = document.getElementById('modalImage');
                    const modalCaption = document.getElementById('modalCaption');

                    modalImage.src = photoSrc;
                    modalCaption.textContent = caption;
                });
            }
        });
    </script>

<?php else: ?>
    <!-- News List -->
    <section id="news-list" class="py-5" style="scroll-margin-top:180px;">
        <div class="container">
            <!-- Filter -->
            <div class="row mb-4">
                <div class="col">
                    <div class="btn-group" role="group">
                        <a href="news.php" class="btn btn-<?php echo !$kategori_filter ? 'primary' : 'outline-primary'; ?>">
                            Semua
                        </a>
                        <a href="news.php?kategori=berita" class="btn btn-<?php echo $kategori_filter == 'berita' ? 'primary' : 'outline-primary'; ?>">
                            Berita
                        </a>
                        <a href="news.php?kategori=agenda" class="btn btn-<?php echo $kategori_filter == 'agenda' ? 'primary' : 'outline-primary'; ?>">
                            Agenda
                        </a>
                        <a href="news.php?kategori=pengumuman" class="btn btn-<?php echo $kategori_filter == 'pengumuman' ? 'primary' : 'outline-primary'; ?>">
                            Pengumuman
                        </a>
                    </div>
                </div>
            </div>

            <!-- News Grid -->
            <div class="row g-4">
                <?php if (!empty($news_list)): ?>
                    <?php foreach ($news_list as $news):
                        // Ambil foto pertama untuk thumbnail
                        $stmt_thumb = $pdo->prepare("SELECT file_path FROM berita_foto WHERE berita_id = ? ORDER BY uploaded_at ASC LIMIT 1");
                        $stmt_thumb->execute([$news['uuid']]);
                        $thumbnail = $stmt_thumb->fetch();
                    ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0 overflow-hidden">
                                <?php if ($thumbnail):
                                    $thumb_path = rtrim($_ENV['UPLOAD_DIR'], '/') . '/berita/' . htmlspecialchars($thumbnail['file_path']);
                                ?>
                                    <img src="<?php echo $thumb_path; ?>"
                                        class="card-img-top"
                                        style="height: 200px; object-fit: cover;"
                                        alt="<?php echo htmlspecialchars($news['judul']); ?>"
                                        onerror="this.src='../assets/img/placeholder.jpg'">
                                <?php else: ?>
                                    <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center"
                                        style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bi bi-<?php
                                                        echo $news['kategori'] == 'agenda' ? 'calendar-event' : ($news['kategori'] == 'pengumuman' ? 'megaphone' : 'newspaper');
                                                        ?> text-white" style="font-size: 3rem; opacity: 0.5;"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="card-body d-flex flex-column">
                                    <span class="badge bg-<?php
                                                            echo $news['kategori'] == 'agenda' ? 'success' : ($news['kategori'] == 'pengumuman' ? 'warning' : 'primary');
                                                            ?> mb-2 align-self-start">
                                        <?php echo ucfirst($news['kategori']); ?>
                                    </span>

                                    <h5 class="card-title fw-bold line-clamp-2" style="min-height: 3em;">
                                        <?php echo htmlspecialchars($news['judul']); ?>
                                    </h5>

                                    <p class="text-muted small mb-3">
                                        <i class="bi bi-calendar me-2"></i>
                                        <?php echo date('d M Y', strtotime($news['tanggal'])); ?>
                                        <?php if ($news['tempat']): ?>
                                            <br>
                                            <i class="bi bi-geo-alt me-2"></i>
                                            <?php echo htmlspecialchars($news['tempat']); ?>
                                        <?php endif; ?>
                                    </p>

                                    <!-- Deskripsi dengan batasan 3 baris -->
                                    <p class="card-text text-muted line-clamp-3" style="line-height: 1.5; min-height: 4.5em;">
                                        <?php echo htmlspecialchars($news['deskripsi']); ?>
                                    </p>

                                    <a href="news.php?id=<?php echo $news['uuid']; ?>" class="btn btn-sm btn-outline-primary mt-auto">
                                        Baca Selengkapnya <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i>
                            Belum ada berita untuk kategori ini
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?<?= $kategori_filter ? 'kategori=' . $kategori_filter . '&' : '' ?>page=<?= $page - 1 ?>">&laquo; Sebelumnya</a>
                        </li>

                        <?php
                        // Smart pagination - show limited page numbers
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);

                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= $kategori_filter ? 'kategori=' . $kategori_filter . '&' : '' ?>page=1">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= $kategori_filter ? 'kategori=' . $kategori_filter . '&' : '' ?>page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= $kategori_filter ? 'kategori=' . $kategori_filter . '&' : '' ?>page=<?= $total_pages ?>"><?= $total_pages ?></a>
                            </li>
                        <?php endif; ?>

                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?<?= $kategori_filter ? 'kategori=' . $kategori_filter . '&' : '' ?>page=<?= $page + 1 ?>">Selanjutnya &raquo;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>