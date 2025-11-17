<?php
require_once '../config/db.php';
$page_title = 'About Us';

$stmt_profile = $pdo->query("SELECT * FROM profile LIMIT 1");
$profile = $stmt_profile->fetch();

$stmt_ketua = $pdo->query("SELECT * FROM anggota WHERE jabatan = 'ketua' ORDER BY nama");
$ketua = $stmt_ketua->fetchAll();

$stmt_anggota = $pdo->query("SELECT * FROM anggota WHERE jabatan = 'anggota' ORDER BY nama");
$anggota = $stmt_anggota->fetchAll();

// Konfigurasi Pagination
$limit_fasilitas = 3; // Jumlah fasilitas per halaman
$page_fasilitas = isset($_GET['fasilitas_page']) ? (int)$_GET['fasilitas_page'] : 1;
if ($page_fasilitas < 1) $page_fasilitas = 1;
$offset_fasilitas = ($page_fasilitas - 1) * $limit_fasilitas;

// Hitung total fasilitas
$stmt_total_fasilitas = $pdo->query("SELECT COUNT(*) FROM fasilitas");
$total_fasilitas = $stmt_total_fasilitas->fetchColumn();
$total_pages_fasilitas = ceil($total_fasilitas / $limit_fasilitas);

if ($page_fasilitas > $total_pages_fasilitas && $total_pages_fasilitas > 0) {
    $page_fasilitas = $total_pages_fasilitas;
    $offset_fasilitas = ($page_fasilitas - 1) * $limit_fasilitas;
}

// Ambil data fasilitas sesuai halaman
$stmt_fasilitas = $pdo->prepare("SELECT * FROM fasilitas ORDER BY nama LIMIT :limit OFFSET :offset");
$stmt_fasilitas->bindParam(':limit', $limit_fasilitas, PDO::PARAM_INT);
$stmt_fasilitas->bindParam(':offset', $offset_fasilitas, PDO::PARAM_INT);
$stmt_fasilitas->execute();
$fasilitas = $stmt_fasilitas->fetchAll();


include '../includes/header.php';
include '../includes/navbar.php';
?>

<section class="page-header py-5" style="background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%); color: white;">
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h1 class="display-4 fw-bold mb-3">About AI Lab</h1>
                <p class="lead">Mengenal lebih dekat Applied Informatics Laboratory</p>
            </div>
        </div>
    </div>
</section>

<?php if ($profile): ?>
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm mb-4">
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

                <div class="card border-0 shadow-sm">
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

    <?php if ($profile['sejarah']): ?>
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row">
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

<section class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col text-center">
                <h2 class="section-title">Our Team</h2>
                <p class="section-subtitle">Tim peneliti dan pengembang AI Lab Polinema</p>
            </div>
        </div>

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
                            <span class="badge bg-secondary"><?php echo ucfirst($a['status']); ?></span>
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
                            <span class="badge bg-secondary"><?php echo ucfirst($a['status']); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <div class="modal fade" id="anggotaModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody"><p>Loading...</p></div>
            </div>
        </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        document.querySelectorAll('.card[data-uuid]').forEach(card => {
        card.addEventListener('click', () => {
            const uuid = card.dataset.uuid
            const modal = new bootstrap.Modal(document.getElementById('anggotaModal'))
            const modalTitle = document.getElementById('modalTitle')
            const modalBody = document.getElementById('modalBody')
            modalTitle.innerText = card.querySelector('h5, h6').innerText
            modalBody.innerHTML = '<p>Loading...</p>'
            modal.show()
            function loadPage(page = 1) {
            fetch(`get_penelitian.php?uuid=${uuid}&page=${page}`)
                .then(res => res.text())
                .then(html => {
                modalBody.innerHTML = html
                modalBody.querySelectorAll('.page-link').forEach(btn => {
                    btn.addEventListener('click', () => {
                    loadPage(btn.dataset.page)
                    })
                })
                })
            }
            loadPage()
        })
        })
        </script>

        <!-- pop up -->
        <div class="modal fade" id="anggotaModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody"><p>Loading...</p></div>
            </div>
        </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        document.querySelectorAll('.card[data-uuid]').forEach(card => {
        card.addEventListener('click', () => {
            const uuid = card.dataset.uuid
            const modal = new bootstrap.Modal(document.getElementById('anggotaModal'))
            const modalTitle = document.getElementById('modalTitle')
            const modalBody = document.getElementById('modalBody')
            modalTitle.innerText = card.querySelector('h5, h6').innerText
            modalBody.innerHTML = '<p>Loading...</p>'
            modal.show()
            function loadPage(page = 1) {
            fetch(`get_penelitian.php?uuid=${uuid}&page=${page}`)
                .then(res => res.text())
                .then(html => {
                modalBody.innerHTML = html
                modalBody.querySelectorAll('.page-link').forEach(btn => {
                    btn.addEventListener('click', () => {
                    loadPage(btn.dataset.page)
                    })
                })
                })
            }
            loadPage()
        })
        })
        </script>

        <?php if (empty($ketua) && empty($anggota)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Data anggota tim belum tersedia
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($fasilitas)): // Jika ada fasilitas, tampilkan section ?>
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

            <?php if ($total_pages_fasilitas > 1): ?>
            <div class="row mt-5">
                <div class="col">
                    <nav>
                        <ul class="pagination justify-content-center">
                            
                            <li class="page-item <?php echo ($page_fasilitas <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?fasilitas_page=<?php echo $page_fasilitas - 1; ?>">
                                    Sebelumnya
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $total_pages_fasilitas; $i++): ?>
                                <li class="page-item <?php echo ($i == $page_fasilitas) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?fasilitas_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo ($page_fasilitas >= $total_pages_fasilitas) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?fasilitas_page=<?php echo $page_fasilitas + 1; ?>">
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
<?php elseif ($total_fasilitas == 0): // Jika tidak ada fasilitas sama sekali (total 0) ?>
    <section class="py-5 bg-light">
        <div class="container">
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Data fasilitas belum tersedia
            </div>
        </div>
    </section>
<?php endif; ?>
<?php include '../includes/footer.php'; ?>