<?php
require_once '../config/db.php';
$page_title = 'Home';

// Fetch latest news
$stmt_berita = $pdo->query("SELECT * FROM berita ORDER BY tanggal DESC LIMIT 3");
$latest_news = $stmt_berita->fetchAll();

// Fetch latest activities
$stmt_kegiatan = $pdo->query("SELECT * FROM kegiatan ORDER BY tanggal DESC LIMIT 4");
$latest_activities = $stmt_kegiatan->fetchAll();

// Fetch partnerships
$stmt_partnership = $pdo->query("SELECT * FROM partnership LIMIT 6");
$partnerships = $stmt_partnership->fetchAll();

// Fetch profile
$stmt_profile = $pdo->query("SELECT * FROM profile LIMIT 1");
$profile = $stmt_profile->fetch();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- Hero Section -->
<section class="hero-section py-5" style="background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%); color: white;">
    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="display-4 fw-bold mb-4 animate__animated animate__fadeInLeft" id="type">
                    Applied Informatics Laboratory
                </h1>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        new TypeIt("#type", {
                            strings: [""],
                        }).go();
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
            <div class="col-lg-6">
                <img src="assets/img/hero-illustration.svg" alt="AI Lab" class="img-fluid"
                    onerror="this.src='https://via.placeholder.com/600x400/1E4BA3/ffffff?text=AI+Lab+Polinema'">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col">
                <h2 class="section-title">Why Choose AI Lab?</h2>
                <p class="section-subtitle">Keunggulan laboratorium kami</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-lightbulb-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">Innovation</h5>
                    <p class="text-muted">
                        Fokus pada inovasi teknologi terkini dengan pendekatan riset yang komprehensif
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">Collaboration</h5>
                    <p class="text-muted">
                        Kerjasama dengan industri dan institusi untuk hasil riset yang aplikatif
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-trophy-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">Excellence</h5>
                    <p class="text-muted">
                        Komitmen terhadap kualitas dan standar penelitian internasional
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest News Section -->
<section class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="section-title">Latest News & Events</h2>
                <p class="section-subtitle">Berita dan kegiatan terbaru dari laboratorium kami</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($latest_news)): ?>
                <?php foreach ($latest_news as $news): ?>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <span class="badge bg-primary mb-2">
                                    <?php echo ucfirst($news['kategori']); ?>
                                </span>
                                <h5 class="card-title fw-bold">
                                    <?php echo htmlspecialchars($news['judul']); ?>
                                </h5>
                                <p class="text-muted small mb-3">
                                    <i class="bi bi-calendar me-2"></i>
                                    <?php echo date('d M Y', strtotime($news['tanggal'])); ?>
                                    <?php if ($news['tempat']): ?>
                                        <i class="bi bi-geo-alt ms-2 me-1"></i>
                                        <?php echo htmlspecialchars($news['tempat']); ?>
                                    <?php endif; ?>
                                </p>
                                <p class="card-text text-muted">
                                    <?php echo substr(htmlspecialchars($news['deskripsi']), 0, 120) . '...'; ?>
                                </p>
                                <a href="news.php?id=<?php echo $news['uuid']; ?>" class="btn btn-sm btn-outline-primary">
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
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="section-title">Recent Activities</h2>
                <p class="section-subtitle">Kegiatan penelitian dan pengabdian masyarakat</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($latest_activities)): ?>
                <?php foreach ($latest_activities as $activity): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <span class="badge bg-success mb-2">
                                    <?php echo ucfirst($activity['kategori_kegiatan']); ?>
                                </span>
                                <h6 class="card-title fw-bold">
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
                                <p class="card-text text-muted small">
                                    <?php echo substr(htmlspecialchars($activity['deskripsi_singkat']), 0, 80) . '...'; ?>
                                </p>
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
    </div>
</section>

<!-- Partnerships Section -->
<?php if (!empty($partnerships)): ?>
    <section class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col text-center">
                    <h2 class="section-title">Our Partners</h2>
                    <p class="section-subtitle">Mitra kerja sama AI Lab Polinema</p>
                </div>
            </div>

            <div class="row g-4 align-items-center">
                <?php foreach ($partnerships as $partner): ?>
                    <div class="col-6 col-md-4 col-lg-2 text-center">
                        <a href="<?php echo htmlspecialchars($partner['website']); ?>"
                            target="_blank"
                            class="text-decoration-none"
                            title="<?php echo htmlspecialchars($partner['nama']); ?>">
                            <?php if ($partner['logo']): ?>
                                <img src="../assets/img/<?php echo htmlspecialchars($partner['logo']); ?>"
                                    alt="<?php echo htmlspecialchars($partner['nama']); ?>"
                                    class="img-fluid grayscale-hover"
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
<section class="py-5" style="background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%); color: white;">
    <div class="container text-center">
        <h2 class="mb-3">Ready to Collaborate?</h2>
        <p class="lead mb-4">
            Mari berkolaborasi dengan kami untuk mengembangkan teknologi informasi terapan
        </p>
        <a href="contact.php" class="btn btn-light btn-lg">
            <i class="bi bi-envelope me-2"></i>Get in Touch
        </a>
    </div>
</section>

<?php include '../includes/footer.php'; ?>