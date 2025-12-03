<?php
require_once '../config/db.php';
$page_title = 'Tentang Kami';

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
<link rel="stylesheet" href="../assets/css/public_about.css">
<link rel="stylesheet" href="../assets/css/quill-content.css">

<!-- Hero Section with Parallax Effect -->
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
        <div class="row align-items-center justify-content-center py-5" style="min-height: 400px;">
            <div class="col-lg-8 text-center">
                <h1 class="display-4 fw-bold mb-3" data-aos="fade-up" data-aos-duration="1000">Tentang AI Lab</h1>
                <p class="lead" data-aos="fade-up" data-aos-duration="1000">Mengenal lebih dekat Laboratorium Informatika Terapan</p>
            </div>
        </div>
    </div>
</section>

<!-- Parallax JavaScript -->
<script src="../assets/js/parallax.js"></script>

<!-- Sejarah Section -->
<?php if ($profile && !empty($profile['sejarah'])): ?>
    <section class="py-5">
        <div class="container">
            <div class="row" data-aos="fade-up" data-aos-duration="1000">
                <div class="col-lg-10 mx-auto">
                    <h2 class="section-title text-center mb-4">Sejarah Laboratorium</h2>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="quill-content" style="text-align: justify;">
                                <?php
                                // Tampilkan konten HTML dari Quill langsung
                                echo $profile['sejarah'];
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Visi & Misi Section -->
<?php if ($profile): ?>
    <section class="py-5 bg-light">
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
                            <div class="quill-content text-center">
                                <?php
                                // Tampilkan konten HTML dari Quill
                                echo $profile['visi'];
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Misi -->
                    <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-duration="1000">
                        <div class="card-body p-4" style="text-align: justify;">
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <i class="bi bi-bullseye text-primary fs-1 me-3"></i>
                                <h3 class="fw-bold mb-0">Misi</h3>
                            </div>

                            <div class="quill-content misi-content">
                                <?php
                                // Tampilkan konten HTML dari Quill
                                echo $profile['misi'];
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
<section class="py-5" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <div class="row mb-5">
            <div class="col text-center">
                <h2 class="section-title">Tim Kami</h2>
                <p class="section-subtitle">Tim peneliti dan pengembang AI Lab Polinema</p>
            </div>
        </div>

        <!-- Ketua -->
        <?php if (!empty($ketua)): ?>
            <div class="mb-5">
                <h4 class="text-center mb-4 fw-bold text-primary">Kepala Laboratorium</h4>
                <div class="row justify-content-center g-4">
                    <?php foreach ($ketua as $k): ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="team-card card border-0 shadow-sm text-center h-100"
                                data-uuid="<?php echo $k['uuid']; ?>"
                                data-keahlian="<?php echo htmlspecialchars($k['keahlian'] ?? ''); ?>">

                                <div class="card-body p-4">
                                    <div class="member-avatar mb-3 position-relative">
                                        <?php if ($k['path_gambar']): ?>
                                            <img src="../assets/img/<?php echo htmlspecialchars($k['path_gambar']); ?>"
                                                alt="<?php echo htmlspecialchars($k['nama']); ?>" class="rounded-circle member-img">
                                        <?php else: ?>
                                            <div
                                                class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center mx-auto member-placeholder">
                                                <i class="bi bi-person-fill text-white" style="font-size:3rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($k['nama']); ?></h5>
                                    <?php if ($k['nidn']): ?>
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-credit-card-2-front me-1"></i>
                                            <?php echo htmlspecialchars($k['nidn']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <span class="badge badge-custom bg-primary"><?php echo ucfirst($k['status']); ?></span>
                                    <div class="member-action mt-3">
                                        <small class="text-primary fw-semibold">
                                            <i class="bi bi-eye me-1"></i>Lihat Publikasi
                                        </small>
                                    </div>
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
                    <h4 class="text-center mb-4 fw-bold text-primary">Anggota Dosen</h4>
                    <div class="row g-4">
                        <?php foreach ($dosen as $a): ?>
                            <div class="col-md-4 col-lg-3">
                                <div class="team-card card border-0 shadow-sm text-center h-100"
                                    data-uuid="<?php echo $a['uuid']; ?>"
                                    data-keahlian="<?php echo htmlspecialchars($a['keahlian'] ?? ''); ?>">
                                    <div class="card-body p-4">
                                        <div class="member-avatar mb-3 position-relative">
                                            <?php if (!empty($a['path_gambar'])): ?>
                                                <img src="../assets/img/<?php echo htmlspecialchars($a['path_gambar']); ?>"
                                                    alt="<?php echo htmlspecialchars($a['nama']); ?>" class="rounded-circle member-img">
                                            <?php else: ?>
                                                <div
                                                    class="rounded-circle bg-gradient-secondary d-flex align-items-center justify-content-center mx-auto member-placeholder">
                                                    <i class="bi bi-person-fill text-white" style="font-size:2.5rem;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($a['nama']); ?></h6>
                                        <?php if (!empty($a['nidn'])): ?>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-credit-card-2-front me-1"></i>
                                                <?php echo htmlspecialchars($a['nidn']); ?>
                                            </p>
                                        <?php endif; ?>
                                        <span class="badge badge-custom bg-info"><?php echo ucfirst($a['status']); ?></span>
                                        <div class="member-action mt-3">
                                            <small class="text-info fw-semibold">
                                                <i class="bi bi-eye me-1"></i>Lihat Publikasi
                                            </small>
                                        </div>
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
                    <h4 class="text-center mb-4 fw-bold text-primary">Anggota Mahasiswa</h4>
                    <div class="row g-4">
                        <?php foreach ($mahasiswa as $a): ?>
                            <div class="col-md-4 col-lg-3">
                                <div class="team-card card border-0 shadow-sm text-center h-100"
                                    data-uuid="<?php echo $a['uuid']; ?>"
                                    data-keahlian="<?php echo htmlspecialchars($a['keahlian'] ?? ''); ?>">
                                    <div class="card-body p-4">
                                        <div class="member-avatar mb-3 position-relative">
                                            <?php if (!empty($a['path_gambar'])): ?>
                                                <img src="../assets/img/<?php echo htmlspecialchars($a['path_gambar']); ?>"
                                                    alt="<?php echo htmlspecialchars($a['nama']); ?>" class="rounded-circle member-img">
                                            <?php else: ?>
                                                <div
                                                    class="rounded-circle bg-gradient-success d-flex align-items-center justify-content-center mx-auto member-placeholder">
                                                    <i class="bi bi-person-fill text-white" style="font-size:2.5rem;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($a['nama']); ?></h6>
                                        <?php if (!empty($a['nidn'])): ?>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-credit-card-2-front me-1"></i>
                                                <?php echo htmlspecialchars($a['nidn']); ?>
                                            </p>
                                        <?php endif; ?>
                                        <span class="badge badge-custom bg-success"><?php echo ucfirst($a['status']); ?></span>
                                        <div class="member-action mt-3">
                                            <small class="text-success fw-semibold">
                                                <i class="bi bi-eye me-1"></i>Lihat Publikasi
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (empty($ketua) && empty($anggota)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Data anggota tim belum tersedia
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal Publikasi -->
<div class="modal fade" id="anggotaModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalTitle">
                    <i class="bi bi-journal-text me-2"></i>Publikasi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat publikasi...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.team-card[data-uuid]').forEach(card => {
            card.addEventListener('click', () => {

                const uuid = card.dataset.uuid;
                const nama = card.querySelector('h5, h6').innerText;
                const modal = new bootstrap.Modal(document.getElementById('anggotaModal'));
                const modalTitle = document.getElementById('modalTitle');
                const modalBody = document.getElementById('modalBody');

                const keahlian = card.dataset.keahlian || "";
                let badges = "";

                // split keahlian berdasarkan koma
                if (keahlian.trim() !== "") {
                    keahlian.split(",").forEach(k => {
                        badges += `<span class="badge bg-primary me-1">${k.trim()}</span>`;
                    });
                }

                // title modal
                modalTitle.innerHTML = `
                <i class="bi bi-journal-text me-2"></i>${"Publikasi"} - ${nama}
            `;

                // base layout (keahlian + research page + publikasi)
                modalBody.innerHTML = `
                <div id="keahlianSection" class="px-3 pt-2">
                    <h6 class="text-muted mb-1">Bidang Keahlian</h6>
                    <div id="keahlianBox" class="d-flex flex-wrap gap-1">${badges}</div>
                </div>

                <div id="researchSection" class="px-3 pt-3">
                    <h6 class="text-muted mb-1">Gambaran Penelitian</h6>
                    <div id="researchPageBox" class="mb-4 small text-muted">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <span class="ms-2">Memuat...</span>
                    </div>
                </div>

                <div id="publicationContainer">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted mb-0">Memuat publikasi...</p>
                    </div>
                </div>
            `;

                modal.show();

                // hide bidang keahlian kalau kosong
                if (badges.trim() === "") {
                    document.getElementById("keahlianSection").style.display = "none";
                }

                // icon research page
                function getResearchIcon(url) {
                    const u = url.toLowerCase();

                    if (u.includes("scholar.google")) return `<i class="bi bi-google"></i>`;
                    if (u.includes("researchgate")) return `<i class="bi bi-r-square"></i>`;
                    if (u.includes("orcid")) return `<i class="bi bi-person-badge"></i>`;
                    return `<i class="bi bi-globe2"></i>`; // default
                }

                // load research page dari db
                function loadResearchPage() {
                    fetch(`fetch_research_page.php?uuid=${uuid}`)
                        .then(res => res.json())
                        .then(data => {

                            const section = document.getElementById("researchSection");
                            const box = document.getElementById("researchPageBox");

                            // kalau ga ada data → hide section
                            if (!data || data.length === 0) {
                                section.style.display = "none";
                                return;
                            }

                            let html =
                                `<div class="d-flex align-items-center flex-wrap gap-3">`;

                            data.forEach(r => {
                                html += `
                                <a href="${r.link_page}" target="_blank"
                                   class="text-decoration-none"
                                   title="${r.nama_web}">
                                    <span style="font-size: 1.6rem;">
                                        ${getResearchIcon(r.link_page)}
                                    </span>
                                </a>
                            `;
                            });

                            html += `</div>`;
                            box.innerHTML = html;
                        })
                        .catch(err => {
                            console.error(err);
                            document.getElementById("researchSection").style.display = "none";
                        });
                }

                // load publikasi
                function loadPage(page = 1) {
                    fetch(`fetch_publications.php?uuid=${uuid}&page=${page}`)
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById("publicationContainer").innerHTML = html;

                            // reattach pagination click event
                            document.querySelectorAll('#publicationContainer .page-link')
                                .forEach(btn => {
                                    btn.addEventListener('click', (e) => {
                                        e.preventDefault();
                                        loadPage(btn.dataset.page);
                                    });
                                });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById("publicationContainer").innerHTML = `
                            <div class="alert alert-danger m-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Terjadi kesalahan saat memuat data publikasi.
                            </div>
                        `;
                        });
                }

                loadResearchPage(); // load research page
                loadPage(); // load publikasi
            });
        });
    });
</script>

<!-- Facilities Section -->
<?php if (!empty($fasilitas)): ?>
    <?php
    // Pagination setup untuk fasilitas
    $facilities_per_page = 6; // 6 cards per page (2 rows × 3 columns)
    $page_facility = isset($_GET['facility_page']) ? max(1, intval($_GET['facility_page'])) : 1;
    $offset_facility = ($page_facility - 1) * $facilities_per_page;
    $total_facilities = count($fasilitas);
    $pages_facility = ceil($total_facilities / $facilities_per_page);

    // Slice array untuk pagination
    $fasilitas_paginated = array_slice($fasilitas, $offset_facility, $facilities_per_page);
    ?>

    <section class="py-5 bg-light" id="facilities">
        <div class="container">
            <div class="row mb-4">
                <div class="col text-center">
                    <h2 class="section-title">Fasilitas Laboratorium</h2>
                    <p class="section-subtitle">Fasilitas penunjang penelitian dan pengembangan</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($fasilitas_paginated as $fas): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <?php if ($fas['path_gambar']): ?>
                                <img src="../assets/img/<?php echo htmlspecialchars($fas['path_gambar']); ?>" class="card-img-top"
                                    alt="<?php echo htmlspecialchars($fas['nama']); ?>" style="height: 200px; object-fit: cover;">
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

            <!-- Pagination -->
            <?php if ($pages_facility > 1): ?>
                <nav aria-label="Facilities pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page_facility <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?facility_page=<?= $page_facility - 1 ?>#facilities">&laquo; Sebelumnya</a>
                        </li>
                        <?php for ($i = 1; $i <= $pages_facility; $i++): ?>
                            <li class="page-item <?= ($page_facility == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?facility_page=<?= $i ?>#facilities"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page_facility >= $pages_facility) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?facility_page=<?= $page_facility + 1 ?>#facilities">Selanjutnya &raquo;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>