<?php
// Fetch social media links
$stmt = $pdo->query("SELECT nama, url FROM sosmed ORDER BY nama");
$social_media = $stmt->fetchAll();
?>

<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <!-- About Section -->
            <div class="col-lg-4 mb-4">
                <h5 class="text-uppercase mb-3">
                    <i class="bi bi-cpu-fill me-2"></i>AI Lab Polinema
                </h5>
                <p class="text-light">
                    Applied Informatics Laboratory adalah laboratorium yang berfokus pada penelitian dan
                    pengembangan teknologi informasi terapan di Politeknik Negeri Malang.
                </p>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-4 mb-4">
                <h5 class="text-uppercase mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="index.php" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right"></i> Home
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="about.php" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right"></i> About Us
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="research.php" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right"></i> Research
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="publications.php" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right"></i> Publications
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="contact.php" class="text-light text-decoration-none">
                            <i class="bi bi-chevron-right"></i> Contact
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact & Social Media -->
            <div class="col-lg-4 mb-4">
                <h5 class="text-uppercase mb-3">Connect With Us</h5>
                <p class="text-light">
                    <i class="bi bi-geo-alt-fill me-2"></i>
                    Politeknik Negeri Malang<br>
                    <span class="ms-4">Jl. Soekarno Hatta No.9, Malang</span>
                </p>
                <p class="text-light">
                    <i class="bi bi-envelope-fill me-2"></i>
                    ailab@polinema.ac.id
                </p>
                <div class="mt-3">
                    <?php if (!empty($social_media)): ?>
                        <?php foreach ($social_media as $sosmed): ?>
                            <a href="<?php echo htmlspecialchars($sosmed['url']); ?>"
                                target="_blank"
                                class="btn btn-outline-light btn-sm me-2 mb-2">
                                <i class="bi bi-<?php echo strtolower($sosmed['nama']); ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-youtube"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-linkedin"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <hr class="bg-light">

        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> AI Lab Polinema. All Rights Reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0">Powered by <a href="https://polinema.ac.id" class="text-light">Polinema</a></p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="assets/js/main.js"></script>

</body>

</html>