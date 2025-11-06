<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <i class="bi bi-cpu-fill me-2 fs-3"></i>
            <span>AI Lab Polinema</span>
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
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'research' ? 'active' : ''; ?>" href="research.php">
                        <i class="bi bi-lightbulb me-1"></i>Research
                    </a>
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
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'news' ? 'active' : ''; ?>" href="news.php">
                        <i class="bi bi-newspaper me-1"></i>News
                    </a>
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