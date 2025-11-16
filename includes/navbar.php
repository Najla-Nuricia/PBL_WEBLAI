<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="../assets/img/logo.png" alt="Logo AI Lab" class="navbar-logo">
            <span class="navbar-logo-text">AI Lab Polinema</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'index' ? 'active' : ''; ?>" href="index.php">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'about' ? 'active' : ''; ?>" href="about.php">
                        <i class="bi bi-info-circle me-1"></i>About
                    </a>
                </li>

                <!-- Research Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo $current_page == 'research' ? 'active' : ''; ?>" href="research.php" id="researchDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-lightbulb me-1"></i>Research
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="researchDropdown">
                        <li><a class="dropdown-item" href="research.php"><i class="bi bi-grid me-2"></i>Research Overview</a></li>
                        <li><a class="dropdown-item" href="research.php#products"><i class="bi bi-box-seam me-2"></i>Products</a></li>
                        <li><a class="dropdown-item" href="research.php#blueprint"><i class="bi bi-diagram-3 me-2"></i>Blueprint</a></li>
                        <li><a class="dropdown-item" href="research.php#topics"><i class="bi bi-bookmark me-2"></i>Research Topics</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'publications' ? 'active' : ''; ?>" href="publications.php">
                        <i class="bi bi-journal-text me-1"></i>Publications
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'gallery' ? 'active' : ''; ?>" href="gallery.php">
                        <i class="bi bi-images me-1"></i>Gallery
                    </a>
                </li>

                <!-- News Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo $current_page == 'news' ? 'active' : ''; ?>" href="news.php" id="newsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-newspaper me-1"></i>News
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="newsDropdown">
                        <li><a class="dropdown-item" href="news.php#news-list"><i class="bi bi-list-ul me-2"></i>All News</a></li>
                        <li><a class="dropdown-item" href="news.php?kategori=berita#news-list"><i class="bi bi-megaphone me-2"></i>Berita</a></li>
                        <li><a class="dropdown-item" href="news.php?kategori=agenda#news-list"><i class="bi bi-calendar-event me-2"></i>Agenda</a></li>
                        <li><a class="dropdown-item" href="news.php?kategori=pengumuman#news-list"><i class="bi bi-bell me-2"></i>Pengumuman</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'contact' ? 'active' : ''; ?>" href="contact.php">
                        <i class="bi bi-envelope me-1"></i>Contact
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    /* Enhanced Navbar Styles */
    .navbar {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        border: none;
        border-bottom: none;
        z-index: 1030;
    }

    .navbar-brand {
        transition: transform 0.3s ease;
    }

    .navbar-brand:hover {
        transform: translateY(-2px);
    }

    .navbar-logo {
        transition: transform 0.4s ease, filter 0.3s ease;
    }

    .navbar-brand:hover .navbar-logo {
        transform: rotate(5deg) scale(1.1);
        filter: drop-shadow(0 4px 8px rgba(0, 123, 255, 0.3));
    }

    .navbar-logo-text {
        transition: color 0.3s ease, letter-spacing 0.3s ease;
    }

    .navbar-brand:hover .navbar-logo-text {
        color: #007bff;
        letter-spacing: 0.5px;
    }

    /* Nav Links Enhanced */
    .nav-link {
        position: relative;
        transition: all 0.3s ease;
        padding: 8px 16px !important;
        border-radius: 8px;
        font-weight: 500;
    }

    .nav-link i {
        transition: transform 0.3s ease;
    }

    .nav-link:hover i {
        transform: translateY(-2px) scale(1.1);
    }

    /* Underline Effect */
    .nav-link::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, #007bff, #00d4ff);
        transform: translateX(-50%);
        transition: width 0.3s ease;
    }

    .nav-link:hover::before {
        width: 80%;
    }

    /* Hover Background Effect */
    .nav-link:hover {
        background: linear-gradient(135deg, rgba(0, 123, 255, 0.08), rgba(0, 212, 255, 0.08));
        color: #007bff !important;
        transform: translateY(-2px);
    }

    /* Active Link Style */
    .nav-link.active {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white !important;
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    }

    .nav-link.active::before {
        display: none;
    }

    .nav-link.active:hover {
        background: linear-gradient(135deg, #0056b3, #004085);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    }

    /* Dropdown Enhanced */
    .dropdown-menu {
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        border-radius: 12px;
        padding: 8px;
        margin-top: 8px;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        animation: dropdownSlide 0.3s ease;
    }

    @keyframes dropdownSlide {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-item {
        border-radius: 8px;
        padding: 10px 16px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        margin-bottom: 4px;
    }

    /* Dropdown Item Hover Effect */
    .dropdown-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 3px;
        height: 100%;
        background: linear-gradient(180deg, #007bff, #00d4ff);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .dropdown-item:hover::before {
        transform: scaleY(1);
    }

    .dropdown-item:hover {
        background: linear-gradient(90deg, rgba(0, 123, 255, 0.1), rgba(0, 212, 255, 0.05));
        color: #007bff;
        transform: translateX(5px);
        padding-left: 20px;
    }

    .dropdown-item:active {
        background: linear-gradient(90deg, rgba(0, 123, 255, 0.2), rgba(0, 212, 255, 0.1));
    }

    /* Dropdown Toggle Arrow Animation */
    .dropdown-toggle::after {
        transition: transform 0.3s ease;
    }

    .dropdown.show .dropdown-toggle::after {
        transform: rotate(180deg);
    }

    /* Navbar Toggler Enhanced */
    .navbar-toggler {
        border: 2px solid #007bff;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .navbar-toggler:hover {
        background: rgba(0, 123, 255, 0.1);
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.3);
    }

    /* Mobile Responsive */
    @media (max-width: 991.98px) {
        .dropdown-menu {
            background: rgba(248, 249, 250, 0.98);
            border-left: 3px solid #007bff;
            margin-left: 15px;
        }

        .nav-link {
            margin: 4px 0;
        }
    }

    /* Subtle Pulse Animation for Active Dropdown */
    .nav-item.dropdown.show>.nav-link {
        background: linear-gradient(135deg, rgba(0, 123, 255, 0.12), rgba(0, 212, 255, 0.12));
        color: #007bff !important;
    }

    /* Icon Animation on Dropdown Open */
    .dropdown.show .nav-link i {
        animation: iconBounce 0.5s ease;
    }

    @keyframes iconBounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-4px);
        }
    }
</style>