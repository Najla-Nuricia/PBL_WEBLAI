<?php
require_once '../config/db.php';

$page_title = 'News & Events';


$stmt_bg = $pdo->query("SELECT * FROM dashboard_foto ORDER BY updated_at DESC LIMIT 1");
$dashboard_bg = $stmt_bg->fetch();
$bg_image = '';
if ($dashboard_bg && $dashboard_bg['path_gambar']) {
    $bg_image = '../assets/img/dashboard/' . htmlspecialchars($dashboard_bg['path_gambar']);
}

$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';

//Konfigurasi Pagination
$limit_berita = 3; // Jumlah berita per halaman (bisa diubah)
$page_berita = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page_berita < 1) $page_berita = 1;
$offset_berita = ($page_berita - 1) * $limit_berita;

// Hitung total berita (dengan filter)
$count_query = "SELECT COUNT(*) FROM berita WHERE 1=1";
$params_count = [];
if ($kategori_filter) {
    $count_query .= " AND kategori = :kategori";
    $params_count['kategori'] = $kategori_filter;
}
$stmt_total = $pdo->prepare($count_query);
$stmt_total->execute($params_count);
$total_berita = $stmt_total->fetchColumn();
$total_pages_berita = ceil($total_berita / $limit_berita);

// filter dan pagination
$query = "SELECT * FROM berita WHERE 1=1";
if ($kategori_filter) {
    $query .= " AND kategori = :kategori";
}
$query .= " ORDER BY tanggal DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);

// Bind parameters
if ($kategori_filter) {
    $stmt->bindParam(':kategori', $kategori_filter, PDO::PARAM_STR);
}
$stmt->bindParam(':limit', $limit_berita, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset_berita, PDO::PARAM_INT);

$stmt->execute();
$news_list = $stmt->fetchAll();

$single_news = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM berita WHERE uuid = ?");
    $stmt->execute([$_GET['id']]);
    $single_news = $stmt->fetch();
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<section class="hero-section position-relative py-5" id="heroSection" style="color: white; min-height: 500px; overflow: hidden;">
    <?php
    $bg_style = $bg_image
        ? "background: url('$bg_image') center/cover no-repeat; z-index: 0;"
        : 'background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%); z-index: 0;';
    ?>
    <div class="parallax-bg position-absolute top-0 start-0 w-100 h-100"
        style="<?php echo $bg_style; ?> transform: translate3d(0, 0, 0); will-change: transform;">
    </div>

    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(30, 75, 163, 0.25) 0%, rgba(74, 144, 226, 0.25) 100%); z-index: 1; pointer-events: none;"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center justify-content-center min-vh-75 py-5">
            <div class="col-lg-6 text-center">
                <h1 class="display-4 fw-bold mb-3">News & Events</h1>
                <p class="lead">Berita terkini dan agenda kegiatan AI Lab Polinema</p>
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
            window.addEventListener('scroll', requestTick, { passive: true });
        }
    });
</script>

<?php if ($single_news): ?>
    <section class="py-5 ">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <a href="news.php" class="btn btn-outline-primary mb-4">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Berita
                    </a>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <span class="badge bg-primary mb-3 fs-6">
                                <?php echo ucfirst($single_news['kategori']); ?>
                            </span>

                            <h1 class="fw-bold mb-3"><?php echo htmlspecialchars($single_news['judul']); ?></h1>

                            <div class="d-flex flex-wrap gap-3 text-muted mb-4">
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

                            <div class="content" style="text-align: justify; line-height: 1.8;">
                                <?php echo nl2br(htmlspecialchars($single_news['deskripsi'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php else: ?>
    <section id="news-list" class="py-5" style="scroll-margin-top:180px;">
        <div class="container">
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

            <div class="row g-4">
                <?php if (!empty($news_list)): ?>
                    <?php foreach ($news_list as $news): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body">
                                    <span class="badge bg-<?php
                                                        echo $news['kategori'] == 'agenda' ? 'success' : ($news['kategori'] == 'pengumuman' ? 'warning' : 'primary');
                                                        ?> mb-2">
                                        <?php echo ucfirst($news['kategori']); ?>
                                    </span>

                                    <h5 class="card-title fw-bold">
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

                                    <p class="card-text text-muted">
                                        <?php echo substr(htmlspecialchars($news['deskripsi']), 0, 150) . '...'; ?>
                                    </p>

                                    <a href="news.php?id=<?php echo $news['uuid']; ?>" class="btn btn-sm btn-outline-primary">
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

            <?php if ($total_pages_berita > 1): ?>
            <div class="row mt-5">
                <div class="col">
                    <nav>
                        <ul class="pagination justify-content-center">
                            
                            <?php
                            $prev_page = $page_berita - 1;
                            $prev_link_params = ['page' => $prev_page];
                            if ($kategori_filter) $prev_link_params['kategori'] = $kategori_filter;
                            $prev_href = 'news.php?' . http_build_query($prev_link_params);
                            ?>
                            <li class="page-item <?php echo ($page_berita <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo $prev_href; ?>">
                                    Sebelumnya
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $total_pages_berita; $i++): ?>
                                <?php
                                $page_link_params = ['page' => $i];
                                if ($kategori_filter) $page_link_params['kategori'] = $kategori_filter;
                                $page_href = 'news.php?' . http_build_query($page_link_params);
                                ?>
                                <li class="page-item <?php echo ($i == $page_berita) ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo $page_href; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php
                            $next_page = $page_berita + 1;
                            $next_link_params = ['page' => $next_page];
                            if ($kategori_filter) $next_link_params['kategori'] = $kategori_filter;
                            $next_href = 'news.php?' . http_build_query($next_link_params);
                            ?>
                            <li class="page-item <?php echo ($page_berita >= $total_pages_berita) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo $next_href; ?>">
                                    Selanjutnya
                                </a>
                            </li>

                        </ul>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
            </div>
    </section>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>