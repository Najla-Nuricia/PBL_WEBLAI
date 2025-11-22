    <?php
    require_once '../config/db.php';
    include '../includes/header.php';
    include '../includes/navbar.php';

    $page_title = 'Research & Activities';

    // Get background image
    $stmt_bg = $pdo->query('SELECT * FROM dashboard_foto ORDER BY updated_at DESC LIMIT 1');
    $dashboard_bg = $stmt_bg->fetch();
    $bg_image = '';
    if ($dashboard_bg && $dashboard_bg['path_gambar']) {
        $bg_image = '../assets/img/dashboard/' . htmlspecialchars($dashboard_bg['path_gambar']);
    }

    // Get filter
    $kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';

    // Get single activity if ID provided
    $single_kegiatan = null;
    $kegiatan_fotos = [];
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM kegiatan WHERE uuid = ?");
        $stmt->execute([$_GET['id']]);
        $single_kegiatan = $stmt->fetch();

        // Ambil foto-foto kegiatan
        if ($single_kegiatan) {
            $stmt_foto = $pdo->prepare("SELECT * FROM kegiatan_foto WHERE kegiatan_uuid = ? ORDER BY created_at ASC");
            $stmt_foto->execute([$single_kegiatan['uuid']]);
            $kegiatan_fotos = $stmt_foto->fetchAll();
        }
    } else {
        // Pagination setup
        $limit = 6; // 9 card per page
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        // Count total rows for pagination
        $count_query = "SELECT COUNT(*) FROM kegiatan WHERE 1=1";
        if ($kategori_filter) {
            $count_query .= " AND kategori_kegiatan = :kategori";
        }
        $count_stmt = $pdo->prepare($count_query);
        if ($kategori_filter) {
            $count_stmt->execute(['kategori' => $kategori_filter]);
        } else {
            $count_stmt->execute();
        }
        $total_rows = $count_stmt->fetchColumn();
        $total_pages = ceil($total_rows / $limit);

        // Build query with limit and offset
        $query = "SELECT * FROM kegiatan WHERE 1=1";
        if ($kategori_filter) {
            $query .= " AND kategori_kegiatan = :kategori";
        }
        $query .= " ORDER BY tanggal DESC LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($query);
        if ($kategori_filter) {
            $stmt->bindValue(':kategori', $kategori_filter);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $kegiatan_list = $stmt->fetchAll();
    }
    ?>

    <!-- Add CSS for line-clamp -->
    <style>
        .line-clamp-2 {
            display: block;
            display: -webkit-box;
            overflow: hidden;
            text-overflow: ellipsis;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
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

        .hover-zoom {
            transition: transform 0.3s ease;
        }

        .hover-zoom:hover {
            transform: scale(1.05);
        }
    </style>

    <!-- Page Header -->
    <section class="hero-section position-relative py-5" id="heroSection"
        style="color: white; min-height: 500px; overflow: hidden;">
        <?php $bg_style = $bg_image
            ? "background: url('$bg_image') center/cover no-repeat; z-index: 0;"
            : 'background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%); z-index: 0;'; ?>
        <div class="parallax-bg position-absolute top-0 start-0 w-100 h-100"
            style="<?php echo $bg_style; ?> transform: translate3d(0, 0, 0); will-change: transform;">
        </div>

        <div class="position-absolute top-0 start-0 w-100 h-100"
            style="background: linear-gradient(135deg, rgba(30, 75, 163, 0.25) 0%, rgba(74, 144, 226, 0.25) 100%); z-index: 1; pointer-events: none;">
        </div>

        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center justify-content-center min-vh-75 py-5">
                <div class="col-lg-6 text-center">
                    <h1 class="display-4 fw-bold mb-3">Research & Activities</h1>
                    <p class="lead">Kegiatan seminar, workshop, dan pengabdian masyarakat AI Lab Polinema</p>
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

    <?php if ($single_kegiatan): ?>
        <!-- Single Activity Detail -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        <a href="activity.php" class="btn btn-outline-primary mb-4">
                            <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Kegiatan
                        </a>

                        <div class="card border-0 shadow-sm">
                            <?php if (!empty($kegiatan_fotos)): ?>
                                <!-- Photo Gallery Carousel -->
                                <div id="activityCarousel" class="carousel slide" data-bs-ride="carousel">
                                    <?php if (count($kegiatan_fotos) > 1): ?>
                                        <div class="carousel-indicators">
                                            <?php foreach ($kegiatan_fotos as $index => $foto): ?>
                                                <button type="button" data-bs-target="#activityCarousel"
                                                    data-bs-slide-to="<?php echo $index; ?>"
                                                    class="<?php echo $index === 0 ? 'active' : ''; ?>"
                                                    aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                                    aria-label="Slide <?php echo $index + 1; ?>"></button>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="carousel-inner">
                                        <?php foreach ($kegiatan_fotos as $index => $foto):
                                            $foto_path = rtrim($_ENV['UPLOAD_DIR'], '/') . '/kegiatan/' . htmlspecialchars($foto['path_gambar']); ?>
                                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                <img src="<?php echo $foto_path; ?>" class="d-block w-100"
                                                    style="max-height: 500px; object-fit: cover;"
                                                    alt="<?php echo htmlspecialchars($foto['keterangan'] ?: 'Foto kegiatan'); ?>"
                                                    onerror="this.src='../assets/img/placeholder.jpg'">
                                                <?php if (!empty($foto['keterangan'])): ?>
                                                    <div class="carousel-caption d-none d-md-block">
                                                        <div class="bg-dark bg-opacity-75 rounded p-2">
                                                            <p class="mb-0"><?php echo htmlspecialchars($foto['keterangan']); ?></p>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <?php if (count($kegiatan_fotos) > 1): ?>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#activityCarousel"
                                            data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#activityCarousel"
                                            data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="card-body p-4 p-md-5">
                                <span class="badge bg-<?php
                                                        echo $single_kegiatan['kategori_kegiatan'] == 'workshop' ? 'primary' : ($single_kegiatan['kategori_kegiatan'] == 'seminar' ? 'success' : 'info');
                                                        ?> mb-3 fs-6">
                                    <?php echo ucfirst($single_kegiatan['kategori_kegiatan']); ?>
                                </span>

                                <h1 class="fw-bold mb-3"><?php echo htmlspecialchars($single_kegiatan['nama']); ?></h1>

                                <div class="d-flex flex-wrap gap-3 text-muted mb-4">
                                    <span>
                                        <i class="bi bi-calendar me-1"></i>
                                        <?php echo date('d F Y', strtotime($single_kegiatan['tanggal'])); ?>
                                    </span>
                                    <?php if (!empty($single_kegiatan['pemateri'])): ?>
                                        <span>
                                            <i class="bi bi-person me-1"></i>
                                            <?php echo htmlspecialchars($single_kegiatan['pemateri']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <hr>

                                <div class="content" style="text-align: justify; line-height: 1.8; font-size: 1.05rem;">
                                    <?php echo nl2br(htmlspecialchars($single_kegiatan['deskripsi_singkat'])); ?>
                                </div>

                                <?php if (!empty($kegiatan_fotos) && count($kegiatan_fotos) > 1): ?>
                                    <hr class="my-4">
                                    <h5 class="fw-bold mb-3">Galeri Foto</h5>
                                    <div class="row g-3">
                                        <?php foreach ($kegiatan_fotos as $foto):
                                            $foto_path = rtrim($_ENV['UPLOAD_DIR'], '/') . '/kegiatan/' . htmlspecialchars($foto['path_gambar']); ?>
                                            <div class="col-md-4 col-sm-6">
                                                <a href="<?php echo $foto_path; ?>" data-bs-toggle="modal" data-bs-target="#photoModal"
                                                    data-photo="<?php echo $foto_path; ?>"
                                                    data-caption="<?php echo htmlspecialchars($foto['keterangan'] ?: ''); ?>">
                                                    <img src="<?php echo $foto_path; ?>"
                                                        class="img-fluid rounded shadow-sm hover-zoom"
                                                        style="height: 200px; width: 100%; object-fit: cover; cursor: pointer;"
                                                        alt="<?php echo htmlspecialchars($foto['keterangan'] ?: 'Foto kegiatan'); ?>"
                                                        onerror="this.src='../assets/img/placeholder.jpg'">
                                                </a>
                                                <?php if (!empty($foto['keterangan'])): ?>
                                                    <small class="text-muted d-block mt-2"><?php echo htmlspecialchars($foto['keterangan']); ?></small>
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
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
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
        <!-- Activities List -->
        <section id="activity-list" class="py-5" style="scroll-margin-top:180px;">
            <div class="container">
                <!-- Filter -->
                <div class="row mb-4">
                    <div class="col">
                        <div class="btn-group" role="group">
                            <a href="activity.php" class="btn btn-<?php echo !$kategori_filter ? 'primary' : 'outline-primary'; ?>">
                                Semua
                            </a>
                            <a href="activity.php?kategori=workshop" class="btn btn-<?php echo $kategori_filter == 'workshop' ? 'primary' : 'outline-primary'; ?>">
                                Workshop
                            </a>
                            <a href="activity.php?kategori=seminar" class="btn btn-<?php echo $kategori_filter == 'seminar' ? 'primary' : 'outline-primary'; ?>">
                                Seminar
                            </a>
                            <a href="activity.php?kategori=pengabdian" class="btn btn-<?php echo $kategori_filter == 'pengabdian' ? 'primary' : 'outline-primary'; ?>">
                                Pengabdian
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Activities Grid -->
                <div class="row g-4">
                    <?php if (!empty($kegiatan_list)): ?>
                        <?php
                        // Ambil semua kegiatan jadi array indexed
                        $kegiatan_array = array_values($kegiatan_list);
                        $perRow = 3;
                        $total = count($kegiatan_array);

                        for ($i = 0; $i < $total; $i += $perRow):
                            // Ambil 3 item per baris
                            $rowItems = array_slice($kegiatan_array, $i, $perRow);

                            // Cek apakah baris ini punya thumbnail
                            $rowHasThumbnail = false;
                            foreach ($rowItems as $k):
                                $stmt_thumb = $pdo->prepare("SELECT path_gambar FROM kegiatan_foto WHERE kegiatan_uuid = ? ORDER BY created_at ASC LIMIT 1");
                                $stmt_thumb->execute([$k['uuid']]);
                                if ($stmt_thumb->fetch()) {
                                    $rowHasThumbnail = true;
                                    break;
                                }
                            endforeach;
                        ?>

                            <div class="row g-4 mb-3">
                                <?php foreach ($rowItems as $kegiatan): ?>
                                    <?php
                                    // Ambil thumbnail per kegiatan
                                    $stmt_thumb = $pdo->prepare("SELECT path_gambar FROM kegiatan_foto WHERE kegiatan_uuid = ? ORDER BY created_at ASC LIMIT 1");
                                    $stmt_thumb->execute([$kegiatan['uuid']]);
                                    $thumbnail = $stmt_thumb->fetch();

                                    $thumb_path = "";
                                    if ($thumbnail) {
                                        $thumb_path = rtrim($_ENV['UPLOAD_DIR'], "/") . "/kegiatan/" . htmlspecialchars($thumbnail['path_gambar']);
                                    }
                                    ?>

                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm border-0 overflow-hidden">
                                            <?php if ($thumbnail): ?>
                                                <img src="<?= $thumb_path ?>" class="card-img-top"
                                                    style="height:200px; object-fit:cover;"
                                                    alt="<?= htmlspecialchars($kegiatan['nama']) ?>"
                                                    onerror="this.src='../assets/img/placeholder.jpg'">
                                            <?php else: ?>
                                                <?php if ($rowHasThumbnail): ?>
                                                    <!-- Baris ini punya thumbnail → kasih placeholder -->
                                                    <div style="height:200px; background: #f0f0f0;"></div>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <div class="card-body d-flex flex-column">
                                                <span class="badge bg-<?php
                                                                        echo $kegiatan['kategori_kegiatan'] == 'workshop' ? 'primary' : ($kegiatan['kategori_kegiatan'] == 'seminar' ? 'success' : 'info');
                                                                        ?> mb-2 align-self-start">
                                                    <?= ucfirst($kegiatan['kategori_kegiatan']); ?>
                                                </span>

                                                <h5 class="card-title fw-bold line-clamp-2" style="min-height:3em;">
                                                    <?= htmlspecialchars($kegiatan['nama']); ?>
                                                </h5>

                                                <p class="text-muted small mb-3">
                                                    <i class="bi bi-calendar me-2"></i>
                                                    <?= date("d M Y", strtotime($kegiatan['tanggal'])); ?>

                                                    <?php if ($kegiatan['pemateri']): ?>
                                                        <br>
                                                        <i class="bi bi-person me-2"></i>
                                                        <?= htmlspecialchars($kegiatan['pemateri']); ?>
                                                    <?php endif; ?>
                                                </p>

                                                <p class="card-text text-muted line-clamp-3" style="line-height:1.5; min-height:4.5em;">
                                                    <?= htmlspecialchars($kegiatan['deskripsi_singkat']); ?>
                                                </p>

                                                <a href="activity.php?id=<?= $kegiatan['uuid']; ?>" class="btn btn-sm btn-outline-primary mt-auto">
                                                    Lihat Detail <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php endfor; ?>

                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle me-2"></i>
                                Belum ada kegiatan untuk kategori ini
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <!-- Previous button -->
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?<?= $kategori_filter ? 'kategori=' . $kategori_filter . '&' : '' ?>page=<?= $page - 1 ?>">&laquo; Sebelumnya</a>
                            </li>

                            <?php
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
                                <li class="page-item <?= $page == $i ? 'active' : '' ?>">
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

                            <!-- Next button -->
                            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?<?= $kategori_filter ? 'kategori=' . $kategori_filter . '&' : '' ?>page=<?= $page + 1 ?>">Selanjutnya &raquo;</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php include '../includes/footer.php'; ?>