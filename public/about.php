<?php
require_once '../config/db.php';
// require_once '../helpers/background.php';
$page_title = 'About Us';

// Fetch dashboard background
$stmt_bg = $pdo->query("SELECT * FROM dashboard_foto ORDER BY updated_at DESC LIMIT 1");
$dashboard_bg = $stmt_bg->fetch();
$bg_image = '';
if ($dashboard_bg && $dashboard_bg['path_gambar']) {
    $bg_image = '../assets/img/dashboard/' . htmlspecialchars($dashboard_bg['path_gambar']);
}

// Fetch profile data
$stmt_profile = $pdo->query("SELECT * FROM profile LIMIT 1");
$profile = $stmt_profile->fetch();

// Fetch team members
$stmt_ketua = $pdo->query("SELECT * FROM anggota WHERE jabatan = 'ketua' ORDER BY nama");
$ketua = $stmt_ketua->fetchAll();

$stmt_anggota = $pdo->query("SELECT * FROM anggota WHERE jabatan = 'anggota' ORDER BY nama");
$anggota = $stmt_anggota->fetchAll();

// Fetch facilities
$stmt_fasilitas = $pdo->query("SELECT * FROM fasilitas ORDER BY nama");
$fasilitas = $stmt_fasilitas->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- Hero Section with Parallax Effect -->
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
        <div class="row align-items-center justify-content-center py-5" style="min-height: 400px;">
            <div class="col-lg-8 text-center">
                <h1 class="display-4 fw-bold mb-3" data-aos="fade-up" data-aos-duration="1000">About AI Lab</h1>
                <p class="lead" data-aos="fade-up" data-aos-duration="1000">Mengenal lebih dekat Applied Informatics Laboratory</p>
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

<!-- Visi & Misi Section -->
<?php if ($profile): ?>
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Kartu Visi -->
                    <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-duration="1000">
                        <div class="card-body p-4 text-center">
                            <div class="d-flex justify-content-center align-items-center mb-3">
                                <i class="bi bi-eye-fill text-primary fs-1 me-3"></i>
                                <h3 class="fw-bold mb-0">Visi</h3>
                            </div>
                            <p class="text-muted mb-0 text-center">
                                <?php echo nl2br(htmlspecialchars($profile['visi'])); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Misi -->
                    <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-duration="1000">
                        <div class="card-body p-4 text-baseline" style="text-align: justify;">
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <i class="bi bi-bullseye text-primary fs-1 me-3"></i>
                                <h3 class="fw-bold mb-0">Misi</h3>
                            </div>

                            <?php
                            // Mengubah setiap baris menjadi poin list
                            $misi_items = preg_split('/\r\n|\r|\n/', trim($profile['misi']));
                            if (!empty($misi_items)) {
                                echo '<ul class="list-unstyled mb-0 text-muted">';
                                foreach ($misi_items as $item) {
                                    if (trim($item) !== '') {
                                        echo '
                                    <li class="d-flex align-items-start mb-2">
                                        <i class="bi bi-check-circle-fill text-primary me-2 mt-1"></i>
                                        <span>' . htmlspecialchars($item) . '</span>
                                    </li>';
                                    }
                                }
                                echo '</ul>';
                            } else {
                                echo '<p class="text-muted">Belum ada misi yang terdaftar.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sejarah Section -->
    <?php if ($profile['sejarah']): ?>
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row" data-aos="fade-up" data-aos-duration="1000">
                    <div class="col-lg-10 mx-auto">
                        <h2 class="section-title text-center mb-4">Sejarah Laboratorium</h2>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <p class="text-muted" style="text-align: justify;">
                                    <?php echo nl2br(htmlspecialchars($profile['sejarah'])); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
<?php else: ?>
    <section class="py-5">
        <div class="container">
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Informasi profil laboratorium belum tersedia
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Team Section -->
<section class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col text-center">
                <h2 class="section-title">Our Team</h2>
                <p class="section-subtitle">Tim peneliti dan pengembang AI Lab Polinema</p>
            </div>
        </div>

        <!-- Ketua -->
        <?php if (!empty($ketua)): ?>
            <div class="mb-5">
                <h4 class="text-center mb-4 fw-bold">Kepala Laboratorium</h4>
                <div class="row justify-content-center g-4">
                    <?php foreach ($ketua as $k): ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="card border-0 shadow-sm text-center h-100" data-uuid="<?php echo $k['uuid']; ?>">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <?php if ($k['path_gambar']): ?>
                                            <img src="../assets/img/<?php echo htmlspecialchars($k['path_gambar']); ?>" alt="<?php echo htmlspecialchars($k['nama']); ?>" class="rounded-circle" style="width:120px;height:120px;object-fit:cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto" style="width:120px;height:120px;">
                                                <i class="bi bi-person-fill text-white" style="font-size:3rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($k['nama']); ?></h5>
                                    <?php if ($k['nidn']): ?>
                                        <p class="text-muted small mb-2">NIDN: <?php echo htmlspecialchars($k['nidn']); ?></p>
                                    <?php endif; ?>
                                    <span class="badge bg-primary"><?php echo ucfirst($k['status']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Anggota Dosen  -->
        <?php if (!empty($anggota)): ?>
            <?php
            $dosen = array_filter($anggota, fn($a) => strtolower($a['status']) === 'dosen');
            $mahasiswa = array_filter($anggota, fn($a) => strtolower($a['status']) === 'mahasiswa');
            ?>

            <?php if (!empty($dosen)): ?>
                <div class="mb-5">
                    <h4 class="text-center mb-4 fw-bold">Anggota Dosen</h4>
                    <div class="row g-4">
                        <?php foreach ($dosen as $a): ?>
                            <div class="col-md-4 col-lg-3">
                                <div class="card border-0 shadow-sm text-center h-100" data-uuid="<?php echo $a['uuid']; ?>">
                                    <div class="card-body p-4">
                                        <div class="mb-3">
                                            <?php if (!empty($a['path_gambar'])): ?>
                                                <img src="../assets/img/<?php echo htmlspecialchars($a['path_gambar']); ?>" alt="<?php echo htmlspecialchars($a['nama']); ?>" class="rounded-circle" style="width:100px;height:100px;object-fit:cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto" style="width:100px;height:100px;">
                                                    <i class="bi bi-person-fill text-white" style="font-size:2.5rem;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($a['nama']); ?></h6>
                                        <?php if (!empty($a['nidn'])): ?>
                                            <p class="text-muted small mb-2">NIDN: <?php echo htmlspecialchars($a['nidn']); ?></p>
                                        <?php endif; ?>
                                        <span class="badge bg-warning"><?php echo ucfirst($a['status']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Anggota Mahasiswa -->
            <?php if (!empty($mahasiswa)): ?>
                <div>
                    <h4 class="text-center mb-4 fw-bold">Anggota Mahasiswa</h4>
                    <div class="row g-4">
                        <?php foreach ($mahasiswa as $a): ?>
                            <div class="col-md-4 col-lg-3">
                                <div class="card border-0 shadow-sm text-center h-100" data-uuid="<?php echo $a['uuid']; ?>">
                                    <div class="card-body p-4">
                                        <div class="mb-3">
                                            <?php if (!empty($a['path_gambar'])): ?>
                                                <img src="../assets/img/<?php echo htmlspecialchars($a['path_gambar']); ?>" alt="<?php echo htmlspecialchars($a['nama']); ?>" class="rounded-circle" style="width:100px;height:100px;object-fit:cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto" style="width:100px;height:100px;">
                                                    <i class="bi bi-person-fill text-white" style="font-size:2.5rem;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($a['nama']); ?></h6>
                                        <?php if (!empty($a['nidn'])): ?>
                                            <p class="text-muted small mb-2">NIDN: <?php echo htmlspecialchars($a['nidn']); ?></p>
                                        <?php endif; ?>
                                        <span class="badge bg-success"><?php echo ucfirst($a['status']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- pop up -->
        <div class="modal fade" id="anggotaModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.querySelectorAll('.card[data-uuid]').forEach(card => {
                card.style.cursor = 'pointer';

                card.addEventListener('click', () => {
                    const uuid = card.dataset.uuid;
                    const nama = card.querySelector('h5, h6').innerText;
                    const modal = new bootstrap.Modal(document.getElementById('anggotaModal'));
                    const modalTitle = document.getElementById('modalTitle');
                    const modalBody = document.getElementById('modalBody');

                    // Set title modal
                    modalTitle.innerHTML = `<i class="bi bi-journal-text me-2"></i>Publikasi - ${nama}`;

                    // Show loading
                    modalBody.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Memuat publikasi...</p>
            </div>
        `;

                    modal.show();

                    // Function to load publications
                    function loadPage(page = 1) {
                        fetch(`fetch_publications.php?uuid=${uuid}&page=${page}`)
                            .then(res => {
                                if (!res.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return res.text();
                            })
                            .then(html => {
                                modalBody.innerHTML = html;

                                // Re-attach event listeners to pagination buttons
                                modalBody.querySelectorAll('.page-link').forEach(btn => {
                                    btn.addEventListener('click', (e) => {
                                        e.preventDefault();
                                        loadPage(btn.dataset.page);
                                    });
                                });
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                modalBody.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Terjadi kesalahan saat memuat data publikasi.
                        </div>
                    `;
                            });
                    }

                    // Load first page
                    loadPage();
                });
            });
        </script>

        <?php if (empty($ketua) && empty($anggota)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Data anggota tim belum tersedia
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Facilities Section -->
<?php if (!empty($fasilitas)): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col text-center">
                    <h2 class="section-title">Laboratory Facilities</h2>
                    <p class="section-subtitle">Fasilitas penunjang penelitian dan pengembangan</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($fasilitas as $fas): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <?php if ($fas['path_gambar']): ?>
                                <img src="../assets/img/<?php echo htmlspecialchars($fas['path_gambar']); ?>"
                                    class="card-img-top"
                                    alt="<?php echo htmlspecialchars($fas['nama']); ?>"
                                    style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($fas['nama']); ?></h5>
                                <?php if ($fas['kuantitas']): ?>
                                    <p class="text-muted small">
                                        <i class="bi bi-box me-1"></i>
                                        Jumlah: <?php echo $fas['kuantitas']; ?> unit
                                    </p>
                                <?php endif; ?>
                                <p class="card-text text-muted">
                                    <?php echo htmlspecialchars($fas['deskripsi']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>