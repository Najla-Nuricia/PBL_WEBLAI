<?php
echo __DIR__;

require_once __DIR__ . '/../config/db.php'; 
$page_title = 'Home';

// Fetch dashboard background
$stmt_bg = $pdo->query("SELECT * FROM dashboard_foto ORDER BY updated_at DESC LIMIT 1");
$dashboard_bg = $stmt_bg->fetch();
$bg_image = '';
if ($dashboard_bg && $dashboard_bg['path_gambar']) {
    $bg_image = '../assets/img/dashboard/' . htmlspecialchars($dashboard_bg['path_gambar']);
}

// Fetch latest news with thumbnails
$stmt_berita = $pdo->query("SELECT * FROM berita ORDER BY tanggal DESC LIMIT 3");
$latest_news = $stmt_berita->fetchAll();

// Fetch latest activities with thumbnails
$stmt_kegiatan = $pdo->query("SELECT * FROM kegiatan ORDER BY tanggal DESC LIMIT 4");
$latest_activities = $stmt_kegiatan->fetchAll();

// Fetch partnerships
$stmt_partnership = $pdo->query("SELECT * FROM partnership");
$partnerships = $stmt_partnership->fetchAll();

// Fetch profile
$stmt_profile = $pdo->query("SELECT * FROM profile LIMIT 1");
$profile = $stmt_profile->fetch();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<style>
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

.card-img-top {
    transition: transform 0.3s ease;
}

.card:hover .card-img-top {
    transform: scale(1.05);
}

.card {
    transition: all 0.3s ease;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

/* Responsive Height untuk TypeIt */
#type {
    min-height: 150px;
}

@media (max-width: 992px) {
    #type {
        min-height: 130px;
    }
}

@media (max-width: 768px) {
    #type {
        min-height: 120px;
    }
}

@media (max-width: 576px) {
    #type {
        min-height: 180px;
    }
}
</style>

<!-- Hero Section with Parallax Effect -->
<section class="hero-section position-relative py-5" id="heroSection"
    style="color: white; min-height: 500px; overflow: hidden;">
    <!-- Parallax Background Layer -->
    <?php
    $bg_style = $bg_image
        ? "background: url('$bg_image') center/cover no-repeat;"
        : 'background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%);';
    ?>
    <div class="parallax-bg position-absolute top-0 start-0 w-100 h-100"
        style="<?php echo $bg_style; ?> transform: translate3d(0, 0, 0); will-change: transform;">
    </div>

    <!-- Gradient Overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100"
        style="background: linear-gradient(135deg, rgba(30, 75, 163, 0.25) 0%, rgba(74, 144, 226, 0.25) 100%); z-index: 1;">
    </div>

    <!-- Content -->
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center min-vh-75 py-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <!-- Fixed Height untuk TypeIt -->
                <h1 class="display-4 fw-bold mb-4" id="type">
                </h1>
                <script>
                document.addEventListener("DOMContentLoaded", function() {
                    new TypeIt("#type", {
                            speed: 80,
                            startDelay: 500,
                            cursorChar: "|",
                            lifeLike: true,
                            loop: true,
                        })
                        .type("Applied ", {
                            delay: 300
                        })
                        .pause(200)
                        .type("Informatics ", {
                            delay: 250
                        })
                        .pause(150)
                        .type("Lab", {
                            delay: 300
                        })
                        .pause(500)
                        .delete(3, {
                            delay: 300
                        })
                        .type("Laboratory", {
                            delay: 300
                        })
                        .pause(7000)
                        .delete(null, {
                            delay: 500
                        })
                        .go();
                });
                </script>
                <p class="lead mb-4">
                    Laboratorium penelitian dan pengembangan teknologi informasi terapan
                    di Politeknik Negeri Malang
                </p>
                <div class="d-flex gap-3">
                    <a href="about.php" class="btn btn-light btn-lg">
                        <i class="bi bi-info-circle me-2"></i>Learn More
                    </a>
                    <a href="contact.php" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-envelope me-2"></i>Contact Us
                    </a>
                </div>
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

<!-- Latest News Section -->
<section class="py-5" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="section-title">Latest News & Events</h2>
                <p class="section-subtitle">Berita dan kegiatan terbaru dari laboratorium kami</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($latest_news)): ?>
            <?php
                // Cek apakah ada berita yang punya thumbnail
                $hasAnyNewsThumbnail = false;
                foreach ($latest_news as $n) {
                    $stmt_check = $pdo->prepare("SELECT file_path FROM berita_foto WHERE berita_id = ? LIMIT 1");
                    $stmt_check->execute([$n['uuid']]);
                    if ($stmt_check->fetch()) {
                        $hasAnyNewsThumbnail = true;
                        break;
                    }
                }
                ?>
            <?php foreach ($latest_news as $news): ?>
            <?php
                    // Ambil thumbnail berita
                    $stmt_thumb = $pdo->prepare("SELECT file_path FROM berita_foto WHERE berita_id = ? ORDER BY uploaded_at ASC LIMIT 1");
                    $stmt_thumb->execute([$news['uuid']]);
                    $thumbnail = $stmt_thumb->fetch();

                    $thumb_path = "";
                    if ($thumbnail && !empty($thumbnail['file_path'])) {
                        // Tambahkan path prefix jika diperlukan
                        $file_path = $thumbnail['file_path'];
                        // Jika path tidak dimulai dengan ../ atau /, tambahkan prefix
                        if (strpos($file_path, '../') !== 0 && strpos($file_path, '/') !== 0) {
                            $thumb_path = '../assets/img/berita/' . $file_path;
                        } else {
                            $thumb_path = $file_path;
                        }
                    }
                    ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <?php if ($thumbnail): ?>
                    <img src="<?php echo htmlspecialchars($thumb_path); ?>" class="card-img-top"
                        style="height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($news['judul']); ?>"
                        onerror="this.src='../assets/img/placeholder.jpg'">
                    <?php elseif ($hasAnyNewsThumbnail): ?>
                    <!-- Placeholder jika row ini punya thumbnail tapi item ini tidak -->
                    <div style="height: 200px; background: #f0f0f0;"></div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-<?php
                                                        echo $news['kategori'] == 'berita' ? 'primary' : ($news['kategori'] == 'agenda' ? 'success' : 'info');
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
                        <p class="card-text text-muted line-clamp-3 mb-3" style="min-height: 4.5em;">
                            <?php echo htmlspecialchars($news['deskripsi']); ?>
                        </p>
                        <a href="news.php?id=<?php echo $news['uuid']; ?>"
                            class="btn btn-sm btn-outline-primary mt-auto">
                            Read More <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle me-2"></i>
                    Belum ada berita terbaru
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($latest_news)): ?>
        <div class="text-center mt-4">
            <a href="news.php" class="btn btn-primary">
                View All News <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Research Activities Section -->
<section class="py-5 bg-light" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="section-title">Recent Activities</h2>
                <p class="section-subtitle">Kegiatan penelitian dan pengabdian masyarakat</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($latest_activities)): ?>
            <?php
                // Cek apakah ada kegiatan yang punya thumbnail
                $hasAnyThumbnail = false;
                foreach ($latest_activities as $act) {
                    $stmt_check = $pdo->prepare("SELECT path_gambar FROM kegiatan_foto WHERE kegiatan_uuid = ? LIMIT 1");
                    $stmt_check->execute([$act['uuid']]);
                    if ($stmt_check->fetch()) {
                        $hasAnyThumbnail = true;
                        break;
                    }
                }
                ?>
            <?php foreach ($latest_activities as $activity): ?>
            <?php
                    // Ambil thumbnail kegiatan
                    $stmt_thumb = $pdo->prepare("SELECT path_gambar FROM kegiatan_foto WHERE kegiatan_uuid = ? ORDER BY created_at ASC LIMIT 1");
                    $stmt_thumb->execute([$activity['uuid']]);
                    $thumbnail = $stmt_thumb->fetch();

                    $thumb_path = "";
                    if ($thumbnail && !empty($thumbnail['path_gambar'])) {
                        // Tambahkan path prefix jika diperlukan
                        $file_path = $thumbnail['path_gambar'];
                        // Jika path tidak dimulai dengan ../ atau /, tambahkan prefix
                        if (strpos($file_path, '../') !== 0 && strpos($file_path, '/') !== 0) {
                            $thumb_path = '../assets/img/kegiatan/' . $file_path;
                        } else {
                            $thumb_path = $file_path;
                        }
                    }
                    ?>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0">
                    <?php if ($thumbnail): ?>
                    <img src="<?php echo htmlspecialchars($thumb_path); ?>" class="card-img-top"
                        style="height: 180px; object-fit: cover;"
                        alt="<?php echo htmlspecialchars($activity['nama']); ?>"
                        onerror="this.src='../assets/img/placeholder.jpg'">
                    <?php elseif ($hasAnyThumbnail): ?>
                    <!-- Placeholder jika row ini punya thumbnail tapi item ini tidak -->
                    <div style="height: 180px; background: #f0f0f0;"></div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-<?php
                                                        echo $activity['kategori_kegiatan'] == 'workshop' ? 'primary' : ($activity['kategori_kegiatan'] == 'seminar' ? 'success' : 'info');
                                                        ?> mb-2 align-self-start">
                            <?php echo ucfirst($activity['kategori_kegiatan']); ?>
                        </span>
                        <h6 class="card-title fw-bold line-clamp-2" style="min-height: 2.5em;">
                            <?php echo htmlspecialchars($activity['nama']); ?>
                        </h6>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-calendar me-1"></i>
                            <?php echo date('d M Y', strtotime($activity['tanggal'])); ?>
                        </p>
                        <?php if ($activity['pemateri']): ?>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-person me-1"></i>
                            <?php echo htmlspecialchars($activity['pemateri']); ?>
                        </p>
                        <?php endif; ?>
                        <p class="card-text text-muted small line-clamp-2 mb-3" style="min-height: 3em;">
                            <?php echo htmlspecialchars($activity['deskripsi_singkat']); ?>
                        </p>
                        <a href="activity.php?id=<?php echo $activity['uuid']; ?>"
                            class="btn btn-sm btn-outline-primary mt-auto">
                            View Details <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle me-2"></i>
                    Belum ada kegiatan terbaru
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($latest_activities)): ?>
        <div class="text-center mt-4">
            <a href="activity.php" class="btn btn-primary">
                View All Activities <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Partnerships Section -->
<?php if (!empty($partnerships)): ?>
<section class="py-5" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <div class="row mb-4">
            <div class="col text-center">
                <h2 class="section-title">Our Partners</h2>
                <p class="section-subtitle">Mitra kerja sama AI Lab Polinema</p>
            </div>
        </div>

        <div class="row g-4 justify-content-center align-items-center text-center">
            <?php foreach ($partnerships as $partner): ?>
            <div class="col-6 col-md-4 col-lg-2 text-center">
                <a href="<?php echo htmlspecialchars($partner['website']); ?>" target="_blank"
                    class="text-decoration-none" title="<?php echo htmlspecialchars($partner['nama']); ?>">
                    <?php if ($partner['logo']): ?>
                    <img src="../assets/img/<?php echo htmlspecialchars($partner['logo']); ?>"
                        alt="<?php echo htmlspecialchars($partner['nama']); ?>" class="img-fluid grayscale-hover"
                        style="max-height: 80px; filter: grayscale(100%); transition: 0.3s;"
                        onmouseover="this.style.filter='grayscale(0%)'"
                        onmouseout="this.style.filter='grayscale(100%)'">
                    <?php else: ?>
                    <div class="p-3 bg-light rounded">
                        <strong><?php echo htmlspecialchars($partner['nama']); ?></strong>
                    </div>
                    <?php endif; ?>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="py-5 position-relative overflow-hidden"
    style="background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%);" data-aos="fade-up" data-aos-duration="1000">
    <!-- Decorative Elements -->
    <div
        style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%; filter: blur(40px);">
    </div>
    <div
        style="position: absolute; bottom: -80px; right: -80px; width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%; filter: blur(60px);">
    </div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-8 text-center">
                <!-- Icon Badge -->
                <div class="mb-4">
                    <span
                        style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border-radius: 50%; border: 2px solid rgba(255,255,255,0.3);">
                        <i class="bi bi-chat-dots" style="font-size: 2rem; color: white;"></i>
                    </span>
                </div>

                <!-- Heading -->
                <h2 class="mb-3 text-white" style="font-size: 2.5rem; font-weight: 700;">
                    Ready to Collaborate?
                </h2>

                <!-- Description -->
                <p class="lead mb-4"
                    style="color: rgba(255,255,255,0.95); font-size: 1.2rem; max-width: 600px; margin: 0 auto 2rem;">
                    Mari berkolaborasi dengan kami untuk mengembangkan teknologi informasi terapan
                </p>

                <!-- CTA Button Group -->
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="contact.php" class="btn btn-light btn-lg px-4 py-3"
                        style="border-radius: 50px; font-weight: 600; box-shadow: 0 8px 25px rgba(0,0,0,0.2); transition: all 0.3s ease;"
                        onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 35px rgba(0,0,0,0.3)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.2)';">
                        <i class="bi bi-envelope me-2"></i>Get in Touch
                    </a>
                    <a href="about.php" class="btn btn-outline-light btn-lg px-4 py-3"
                        style="border-radius: 50px; font-weight: 600; border-width: 2px; transition: all 0.3s ease;"
                        onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'; this.style.transform='translateY(-3px)';"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.transform='translateY(0)';">
                        <i class="bi bi-info-circle me-2"></i>Learn More
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>