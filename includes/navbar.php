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
                    <a class="nav-link dropdown-toggle <?php echo $current_page == 'research' ? 'active' : ''; ?>"
                        href="research.php"
                        id="researchDropdown"
                        role="button"
                        aria-expanded="false">
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
                    <a class="nav-link dropdown-toggle <?php echo $current_page == 'news' ? 'active' : ''; ?>"
                        href="news.php"
                        id="newsDropdown"
                        role="button"
                        aria-expanded="false">
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

<link rel="stylesheet" href="../assets/css/public_navbar.css">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manual dropdown handling without data-bs-toggle
        const dropdownToggles = document.querySelectorAll('#researchDropdown, #newsDropdown');

        dropdownToggles.forEach(function(toggle) {
            const dropdownMenu = toggle.nextElementSibling;
            const parentLi = toggle.closest('.dropdown');
            let isOpen = false;

            // Click handler for toggle
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const isMobile = window.innerWidth <= 991;

                // Close all other dropdowns first
                document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('show');
                        menu.previousElementSibling.setAttribute('aria-expanded', 'false');
                        menu.closest('.dropdown').classList.remove('show');
                    }
                });

                // Toggle current dropdown
                isOpen = dropdownMenu.classList.contains('show');

                if (isMobile) {
                    // Mobile: Always toggle dropdown, no navigation on second click
                    if (isOpen) {
                        dropdownMenu.classList.remove('show');
                        this.setAttribute('aria-expanded', 'false');
                        parentLi.classList.remove('show');
                    } else {
                        dropdownMenu.classList.add('show');
                        this.setAttribute('aria-expanded', 'true');
                        parentLi.classList.add('show');
                    }
                } else {
                    // Desktop: Second click navigates
                    if (isOpen) {
                        // If already open, navigate to the page
                        window.location.href = this.getAttribute('href');
                    } else {
                        // Open the dropdown
                        dropdownMenu.classList.add('show');
                        this.setAttribute('aria-expanded', 'true');
                        parentLi.classList.add('show');
                    }
                }
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                    menu.classList.remove('show');
                    menu.previousElementSibling.setAttribute('aria-expanded', 'false');
                    menu.closest('.dropdown').classList.remove('show');
                });
            }
        });

        // Handle dropdown item clicks - FIXED: Close dropdown when item is clicked
        document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                // Let the link navigate naturally, but close the dropdown
                const dropdown = this.closest('.dropdown');
                const dropdownMenu = this.closest('.dropdown-menu');
                const dropdownToggle = dropdown.querySelector('.dropdown-toggle');

                // Close the dropdown
                dropdownMenu.classList.remove('show');
                dropdownToggle.setAttribute('aria-expanded', 'false');
                dropdown.classList.remove('show');

                // If it's a hash link, handle smooth scroll
                const href = this.getAttribute('href');
                if (href && href.includes('#')) {
                    const parts = href.split('#');
                    const targetId = parts[1];

                    // If on the same page, smooth scroll
                    if (parts[0] === '' || parts[0] === window.location.pathname.split('/').pop()) {
                        e.preventDefault();
                        const targetElement = document.getElementById(targetId);
                        if (targetElement) {
                            targetElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                        // Update URL without page reload
                        history.pushState(null, '', href);
                    }
                }
            });
        });

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Close all dropdowns on resize
                document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                    menu.classList.remove('show');
                    menu.previousElementSibling.setAttribute('aria-expanded', 'false');
                    menu.closest('.dropdown').classList.remove('show');
                });
            }, 250);
        });
    });
</script>