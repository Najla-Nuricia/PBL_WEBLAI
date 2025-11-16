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
                <h5 class="text-uppercase mb-3">
                    <i class="bi bi-cpu-fill me-2"></i>
                    <?php echo htmlspecialchars($footer['org_name']); ?>
                </h5>
                <p class="text-light">
                    <?php echo nl2br(htmlspecialchars($footer['description'])); ?>
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
                    <?php else: ?>
                        <p class="text-light">No social media links available.</p>
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

<style>
    /* Enhanced Footer Styles */
    footer {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        position: relative;
        overflow: hidden;
    }

    footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(0, 123, 255, 0.5), transparent);
    }

    /* Section Titles */
    footer h5 {
        position: relative;
        padding-bottom: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    footer h5::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, #007bff, #00d4ff);
        border-radius: 2px;
    }

    footer h5 i {
        color: #00d4ff;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.6;
        }
    }

    /* Quick Links */
    footer .list-unstyled li {
        transition: transform 0.3s ease;
    }

    footer .list-unstyled a {
        position: relative;
        display: inline-block;
        transition: all 0.3s ease;
        padding-left: 8px;
    }

    footer .list-unstyled a::before {
        content: '';
        position: absolute;
        left: 0;
        bottom: -2px;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, #007bff, #00d4ff);
        transition: width 0.3s ease;
    }

    footer .list-unstyled a:hover {
        color: #00d4ff !important;
        transform: translateX(5px);
        padding-left: 8px;
    }

    footer .list-unstyled a:hover::before {
        width: calc(100% - 8px);
    }

    footer .list-unstyled a i {
        transition: transform 0.3s ease;
    }

    footer .list-unstyled a:hover i {
        transform: translateX(3px);
    }

    /* Contact Info */
    footer p i {
        color: #00d4ff;
        transition: transform 0.3s ease;
    }

    footer p:hover i {
        transform: scale(1.2);
    }

    /* Social Media Buttons */
    .btn-outline-light {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .btn-outline-light::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(0, 123, 255, 0.3);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.4s ease, height 0.4s ease;
    }

    .btn-outline-light:hover::before {
        width: 100px;
        height: 100px;
    }

    .btn-outline-light:hover {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border-color: #007bff;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
    }

    .btn-outline-light i {
        position: relative;
        z-index: 1;
        transition: transform 0.3s ease;
        font-size: 1.25rem;
    }

    .btn-outline-light:hover i {
        transform: rotate(360deg) scale(1.1);
    }

    /* Divider */
    footer hr {
        opacity: 0.1;
        margin: 2rem 0;
    }

    /* Bottom Section */
    footer .row:last-child p {
        transition: color 0.3s ease;
    }

    footer .row:last-child a {
        position: relative;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    footer .row:last-child a::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: #00d4ff;
        transition: width 0.3s ease;
    }

    footer .row:last-child a:hover {
        color: #00d4ff !important;
    }

    footer .row:last-child a:hover::after {
        width: 100%;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        footer h5::after {
            left: 50%;
            transform: translateX(-50%);
        }

        footer .col-lg-4 {
            text-align: center;
        }

        footer .list-unstyled {
            padding-left: 0;
        }

        footer .d-flex {
            justify-content: center;
        }
    }

    /* Mobile Responsive */
    @media (max-width: 767.98px) {
        footer {
            padding: 3rem 0 2rem !important;
        }

        footer .col-lg-4 {
            margin-bottom: 2rem !important;
        }

        footer h5 {
            font-size: 1.1rem;
            margin-bottom: 1rem !important;
        }

        footer p {
            font-size: 0.9rem;
        }

        footer .list-unstyled li {
            margin-bottom: 0.5rem !important;
        }

        footer .list-unstyled a {
            font-size: 0.9rem;
        }

        footer .btn-outline-light {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }

        footer hr {
            margin: 1.5rem 0 !important;
        }

        footer .row:last-child .col-md-6 {
            text-align: center !important;
            margin-bottom: 0.5rem;
        }

        footer .row:last-child p {
            font-size: 0.85rem;
        }
    }

    /* Small Mobile */
    @media (max-width: 575.98px) {
        footer .btn-outline-light {
            padding: 0.4rem 0.6rem;
            font-size: 0.85rem;
            margin-right: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }

        footer .d-flex {
            gap: 0.25rem !important;
        }
    }

    /* Smooth entrance animation */
    footer>.container>.row>div {
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>


<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- AOS Animation JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
</script>

<!-- Custom JS -->
<script src="../assets/js/main.js"></script>

</body>

</html>