<?php
// Fetch social media links
$stmt = $pdo->query("SELECT nama, url FROM sosmed ORDER BY nama");
$social_media = $stmt->fetchAll();

// Fetch footer information
$stmt_footer = $pdo->query("SELECT * FROM footer_info LIMIT 1");
$footer = $stmt_footer->fetch();
?>

<footer class="bg-dark text-white py-5 mt-5" data-aos="fade-up"
    data-aos-anchor-placement="top-bottom">
    <div class="container">
        <div class="row">
            <!-- About Section -->
            <div class="col-lg-4 mb-4">
                <h5 class="text-uppercase mb-3 text-light">
                    <i class="bi bi-cpu-fill me-2"></i>
                    <?php echo htmlspecialchars($footer['org_name']); ?>
                </h5>
                <p class="text-light">
                    <?php echo nl2br(htmlspecialchars($footer['description'])); ?>
                </p>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-4 mb-4">
                <h5 class="text-uppercase mb-3 text-light">Tautan Langsung</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="index.php" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right"></i> Beranda
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="about.php" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right"></i> Tentang Kami
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="research.php" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right"></i> Penelitian
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="publications.php" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right"></i> Publikasi
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="contact.php" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right"></i> Kontak
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact & Social Media -->
            <div class="col-lg-4 mb-4">
                <h5 class="text-uppercase mb-3 text-light">Terhubung Dengan Kami</h5>
                <p class="text-light">
                    <i class="bi bi-geo-alt-fill me-2"></i>
                    Politeknik Negeri Malang<br>
                    <span class="ms-4">Jl. Soekarno Hatta No.9, Malang</span>
                </p>
                <p class="text-light">
                    <i class="bi bi-envelope-fill me-2"></i>
                    ailab@polinema.ac.id
                </p>
                <div class="d-flex flex-wrap mt-3">
                    <?php if (!empty($social_media)): ?>
                        <?php foreach ($social_media as $sosmed): ?>
                            <a href="<?php echo htmlspecialchars($sosmed['url']); ?>"
                                target="_blank"
                                class="btn btn-outline-light btn-sm me-2 mb-2"
                                title="<?php echo htmlspecialchars($sosmed['nama']); ?>">
                                <i class="bi bi-<?php echo strtolower($sosmed['nama']); ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <hr class="bg-light">

        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0">&copy; 2025
                    <?php echo nl2br(htmlspecialchars($footer['reserved_text'])); ?>
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0">Powered by
                    <a href="<?php echo htmlspecialchars($footer['link_powered_by']); ?>" class="text-light text-decoration-none">
                        <?php echo htmlspecialchars($footer['powered_by']); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
</footer>

<link rel="stylesheet" href="../assets/css/public_footer.css">

<!-- Bootstrap 5 JS -->
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<!-- AOS Animation JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
</script>

<!-- Custom JS -->
<script src="../assets/js/public.js"></script>

</body>

</html>