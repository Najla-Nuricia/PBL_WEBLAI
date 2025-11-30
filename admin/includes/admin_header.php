<?php
require_once '../config/db.php';
require_once '../helpers/sanitize.php';
require_once '../helpers/upload.php';
require_once 'auth.php';

// Cek login dan session timeout
require_login();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Admin Dashboard - AI Lab</title>

    <!-- Bootstrap 5 CSS -->
    <link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Optional: Tema Bootstrap 5 untuk Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <!-- Animated css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <link rel="stylesheet" href="../assets/css/admin_header.css">

    <link rel="icon" type="image/x-icon" href="../assets/icons/logo.ico">
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-scroll">
            <!-- Logo -->
            <a href="../admin/dashboard.php" class="text-decoration-none text-white">
                <div class="sidebar-brand d-flex align-items-center">
                    <img src="../assets/icons/logo.png" alt="Logo AI Lab" class="me-2"
                        style="width: 50px; height: 50px; object-fit: contain;">
                    <div>
                        <h4>AI Lab Admin</h4>
                        <small>Dashboard Panel</small>
                    </div>
                </div>
            </a>

            <ul class="sidebar-menu">
                <li>
                    <a href="dashboard.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <div class="menu-category">
                    <small>Konten</small>
                </div>

                <li>
                    <a href="manage_profile.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_profile.php' ? 'active' : ''; ?>">
                        <i class="bi bi-building"></i>
                        <span>Profile Lab</span>
                    </a>
                </li>

                <li>
                    <a href="manage_dashboard.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_dashboard.php' ? 'active' : ''; ?>">
                        <i class="bi bi-image"></i>
                        <span>Dashboard Background</span>
                    </a>
                </li>

                <li>
                    <a href="manage_news.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_news.php' ? 'active' : ''; ?>">
                        <i class="bi bi-newspaper"></i>
                        <span>Berita & Agenda</span>
                    </a>
                </li>

                <li>
                    <a href="manage_activities.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_activities.php' ? 'active' : ''; ?>">
                        <i class="bi bi-calendar-event"></i>
                        <span>Kegiatan</span>
                    </a>
                </li>

                <li>
                    <a href="manage_publications.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_publications.php' ? 'active' : ''; ?>">
                        <i class="bi bi-journal-text"></i>
                        <span>Publikasi</span>
                    </a>
                </li>

                <li>
                    <a href="manage_products.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_products.php' ? 'active' : ''; ?>">
                        <i class="bi bi-box-seam"></i>
                        <span>Produk</span>
                    </a>
                </li>

                <li>
                    <a href="manage_topik_riset.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_topik_riset.php' ? 'active' : ''; ?>">
                        <i class="bi bi-lightbulb"></i>
                        <span>Topik Riset</span>
                    </a>
                </li>

                <li>
                    <a href="manage_blueprint.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_blueprint.php' ? 'active' : ''; ?>">
                        <i class="bi bi-diagram-3"></i>
                        <span>Blueprint/Roadmap</span>
                    </a>
                </li>

                <li>
                    <a href="manage_gallery.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_gallery.php' ? 'active' : ''; ?>">
                        <i class="bi bi-images"></i>
                        <span>Galeri</span>
                    </a>
                </li>

                <li>
                    <a href="manage_contactInfo.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_contactInfo.php' ? 'active' : ''; ?>">
                        <i class="bi bi-person-vcard"></i>
                        <span>Contact Information</span>
                    </a>
                </li>

                <div class="menu-category">
                    <small>Tim & Mitra</small>
                </div>

                <li>
                    <a href="manage_members.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_members.php' ? 'active' : ''; ?>">
                        <i class="bi bi-people"></i>
                        <span>Anggota Tim</span>
                    </a>
                </li>

                <li>
                    <a href="manage_research_page.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_members.php' ? 'active' : ''; ?>">
                        <i class="bi bi-link-45deg"></i>
                        <span>Research Web</span>
                    </a>
                </li>

                <li>
                    <a href=" manage_partnerships.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_partnerships.php' ? 'active' : ''; ?>">
                        <i class="fa-regular fa-handshake"></i>
                        <span>Partnership</span>
                    </a>
                </li>

                <li>
                    <a href="manage_facilities.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_facilities.php' ? 'active' : ''; ?>">
                        <i class="bi bi-tools"></i>
                        <span>Fasilitas</span>
                    </a>
                </li>

                <div class="menu-category">
                    <small>Pengaturan</small>
                </div>

                <li>
                    <a href="manage_socmed.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_socmed.php' ? 'active' : ''; ?>">
                        <i class="bi bi-share"></i>
                        <span>Social Media</span>
                    </a>
                </li>

                <li>
                    <a href="manage_users.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : ''; ?>">
                        <i class="bi bi-person-gear"></i>
                        <span>Users</span>
                    </a>
                </li>

                <div class="menu-category">
                    <small>Pesan</small>
                </div>

                <li>
                    <a href="manage_email.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_email.php' ? 'active' : ''; ?>">
                        <i class="bi bi-envelope"></i>
                        <span>Email</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar - Minimalist -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="btn btn-link d-md-none p-0 text-dark" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <h5><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h5>
            </div>

            <div class="topbar-right">
                <!-- View Site Button -->
                <a href="../public/index.php" target="_blank"
                    class="btn btn-outline-primary btn-sm btn-view-site d-none d-md-flex align-items-center">
                    <i class="bi bi-globe me-2"></i>
                    View Site
                </a>

                <!-- Profile Dropdown -->
                <div class="dropdown profile-dropdown">
                    <button class="profile-btn border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-avatar">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                        <span
                            class="profile-name d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <i class="bi bi-chevron-down d-none d-md-inline"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Wrapper -->
        <div class="content-wrapper">