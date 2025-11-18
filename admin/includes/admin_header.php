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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Optional: Tema Bootstrap 5 untuk Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- Animated css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        :root {
            --primary-color: #1E4BA3;
            --secondary-color: #2C5AA0;
            --accent-color: #4A90E2;
            --sidebar-bg: #1a1f36;
            --sidebar-hover: #252b42;
            --text-muted: #a0aec0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #d1d2d5ff;
        }

        /* ===== IMPROVED SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: linear-gradient(180deg, #1a1f36 0%, #141829 100%);
            padding: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            overflow: hidden;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }

        /* Custom Scrollbar */
        .sidebar-scroll {
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 2rem;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            transition: background 0.3s;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Sidebar Brand - Glassmorphism */
        .sidebar-brand {
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .sidebar-brand:hover {
            background: rgba(255, 255, 255, 0.08);
            transition: all 0.3s;
        }

        .sidebar-brand h4 {
            color: white;
            font-weight: 700;
            margin: 0;
            font-size: 1.2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .sidebar-brand small {
            color: #FF9F1C;
            font-weight: 500;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        /* Menu Categories - LEBIH BESAR */
        .menu-category {
            padding: 0.75rem 1.5rem 0.5rem;
            margin-top: 1.5rem;
        }

        .menu-category small {
            color: #ffffff;
            text-transform: uppercase;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 1.2px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Sidebar Menu */
        .sidebar-menu {
            list-style: none;
            padding: 0 0.5rem;
        }

        .sidebar-menu li {
            margin-bottom: 0.25rem;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 0.9rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 10px;
            transition: background 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            will-change: padding-left;
        }

        /* Hover Effect */
        .sidebar-menu li a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--accent-color);
            transform: scaleY(0);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Hover state - hanya untuk non-active items */
        .sidebar-menu li a:not(.active):hover {
            background: rgba(74, 144, 226, 0.1);
            color: white;
            padding-left: 1.2rem;
        }

        .sidebar-menu li a:not(.active):hover::before {
            transform: scaleY(1);
        }

        /* Active State - tanpa transform untuk menghindari konflik */
        .sidebar-menu li a.active {
            background: linear-gradient(135deg, rgba(74, 144, 226, 0.2) 0%, rgba(30, 75, 163, 0.2) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.2);
        }

        .sidebar-menu li a.active::before {
            transform: scaleY(1);
            width: 4px;
            background: linear-gradient(180deg, #4A90E2 0%, #FF9F1C 100%);
        }

        .sidebar-menu li a.active i {
            color: #4A90E2;
        }

        .sidebar-menu li a i {
            margin-right: 0.8rem;
            font-size: 1.2rem;
            transition: transform 0.2s ease;
            min-width: 24px;
            text-align: center;
        }

        /* Icon scale hanya untuk non-active items */
        .sidebar-menu li a:not(.active):hover i {
            transform: scale(1.05);
        }

        /* Badge Notification */
        .menu-badge {
            margin-left: auto;
            background: #FF9F1C;
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* ===== MINIMALIST TOPBAR ===== */
        .topbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .topbar.scrolled {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 0.8rem 2rem;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topbar-left h5 {
            margin: 0;
            color: #212529;
            font-weight: 600;
        }

        /* Topbar Right */
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* View Site Button */
        .btn-view-site {
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            border: 2px solid var(--primary-color);
        }

        .btn-view-site:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 75, 163, 0.3);
        }

        /* Profile Dropdown - Minimalist */
        .profile-dropdown {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.5rem 1rem;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .profile-btn:hover {
            border-color: var(--accent-color);
            background: white;
            transform: translateY(-2px);
        }

        .profile-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .profile-name {
            font-weight: 500;
            font-size: 0.9rem;
            color: #212529;
        }

        /* Dropdown Menu Enhancement */
        .dropdown-menu {
            border: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            border-radius: 10px;
            padding: 0.5rem;
            margin-top: 0.5rem;
            min-width: 180px;
        }

        .dropdown-item {
            padding: 0.6rem 1rem;
            border-radius: 6px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background: rgba(74, 144, 226, 0.1);
            transform: translateX(3px);
        }

        .dropdown-item i {
            width: 18px;
            text-align: center;
            font-size: 1rem;
        }

        .dropdown-divider {
            margin: 0.4rem 0;
            border-top: 1px solid #e9ecef;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            transition: all 0.3s;
        }

        .content-wrapper {
            padding: 2rem;
        }

        /* Cards */
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                left: -260px;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .topbar {
                padding: 1rem;
            }

            .topbar-left h5 {
                font-size: 1rem;
            }

            .btn-view-site {
                display: none !important;
            }
        }

        .clickable-card {
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .clickable-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        /* Animasi */
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.98);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .card-body form {
            animation: fadeInScale 0.4s ease-in-out;
        }

        /* Table Styles */
        .table {
            border-collapse: separate;
            border-spacing: 0 0.5rem;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .table input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #1E4BA3;
        }

        .table input[type="checkbox"]:checked {
            transform: scale(1.1);
            transition: all 0.2s ease-in-out;
        }

        .table input[type="checkbox"]:hover {
            transform: scale(1.1);
            transition: 0.2s;
        }

        .table-responsive {
            overflow-x: hidden !important;
        }

        .table-hover tbody tr:hover {
            background-color: transparent !important;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-scroll">
            <!-- Logo -->
            <a href="../admin/dashboard.php" class="text-decoration-none text-white">
                <div class="sidebar-brand d-flex align-items-center">
                    <img src="../assets/img/logo.png" alt="Logo AI Lab" class="me-2" style="width: 50px; height: 50px; object-fit: contain;">
                    <div>
                        <h4>AI Lab Admin</h4>
                        <small>Dashboard Panel</small>
                    </div>
                </div>
            </a>

            <ul class="sidebar-menu">
                <li>
                    <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <div class="menu-category">
                    <small>Konten</small>
                </div>

                <li>
                    <a href="manage_profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_profile.php' ? 'active' : ''; ?>">
                        <i class="bi bi-building"></i>
                        <span>Profile Lab</span>
                    </a>
                </li>

                <li>
                    <a href="manage_dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_dashboard.php' ? 'active' : ''; ?>">
                        <i class="bi bi-image"></i>
                        <span>Dashboard Background</span>
                    </a>
                </li>

                <li>
                    <a href="manage_news.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_news.php' ? 'active' : ''; ?>">
                        <i class="bi bi-newspaper"></i>
                        <span>Berita & Agenda</span>
                    </a>
                </li>

                <li>
                    <a href="manage_activities.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_activities.php' ? 'active' : ''; ?>">
                        <i class="bi bi-calendar-event"></i>
                        <span>Kegiatan</span>
                    </a>
                </li>

                <li>
                    <a href="manage_publications.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_publications.php' ? 'active' : ''; ?>">
                        <i class="bi bi-journal-text"></i>
                        <span>Publikasi</span>
                    </a>
                </li>

                <li>
                    <a href="manage_products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_products.php' ? 'active' : ''; ?>">
                        <i class="bi bi-box-seam"></i>
                        <span>Produk</span>
                    </a>
                </li>

                <li>
                    <a href="manage_topik_riset.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_topik_riset.php' ? 'active' : ''; ?>">
                        <i class="bi bi-lightbulb"></i>
                        <span>Topik Riset</span>
                    </a>
                </li>

                <li>
                    <a href="manage_blueprint.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_blueprint.php' ? 'active' : ''; ?>">
                        <i class="bi bi-diagram-3"></i>
                        <span>Blueprint/Roadmap</span>
                    </a>
                </li>

                <li>
                    <a href="manage_gallery.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_gallery.php' ? 'active' : ''; ?>">
                        <i class="bi bi-images"></i>
                        <span>Galeri</span>
                    </a>
                </li>

                <div class="menu-category">
                    <small>Tim & Mitra</small>
                </div>

                <li>
                    <a href="manage_members.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_members.php' ? 'active' : ''; ?>">
                        <i class="bi bi-people"></i>
                        <span>Anggota Tim</span>
                    </a>
                </li>

                <li>
                    <a href="manage_partnerships.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_partnerships.php' ? 'active' : ''; ?>">
                        <i class="fa-regular fa-handshake"></i>
                        <span>Partnership</span>
                    </a>
                </li>

                <li>
                    <a href="manage_facilities.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_facilities.php' ? 'active' : ''; ?>">
                        <i class="bi bi-tools"></i>
                        <span>Fasilitas</span>
                    </a>
                </li>

                <div class="menu-category">
                    <small>Pengaturan</small>
                </div>

                <li>
                    <a href="manage_socmed.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_socmed.php' ? 'active' : ''; ?>">
                        <i class="bi bi-share"></i>
                        <span>Social Media</span>
                    </a>
                </li>

                <li>
                    <a href="manage_users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : ''; ?>">
                        <i class="bi bi-person-gear"></i>
                        <span>Users</span>
                    </a>
                </li>

                <div class="menu-category">
                    <small>Pesan</small>
                </div>

                <li>
                    <a href="view_email.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'view_email.php' ? 'active' : ''; ?>">
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
                <a href="../public/index.php" target="_blank" class="btn btn-outline-primary btn-sm btn-view-site d-none d-md-flex align-items-center">
                    <i class="bi bi-globe me-2"></i>
                    View Site
                </a>

                <!-- Profile Dropdown -->
                <div class="dropdown profile-dropdown">
                    <button class="profile-btn border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-avatar">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                        <span class="profile-name d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
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
            <script>
                // Topbar scroll effect
                document.addEventListener('scroll', function() {
                    const topbar = document.querySelector('.topbar');
                    if (window.scrollY > 10) {
                        topbar.classList.add('scrolled');
                    } else {
                        topbar.classList.remove('scrolled');
                    }
                });

                // Sidebar toggle for mobile
                document.getElementById('sidebarToggle')?.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('active');
                });

                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(event) {
                    const sidebar = document.querySelector('.sidebar');
                    const sidebarToggle = document.getElementById('sidebarToggle');

                    if (window.innerWidth <= 768) {
                        if (!sidebar.contains(event.target) && !sidebarToggle?.contains(event.target)) {
                            sidebar.classList.remove('active');
                        }
                    }
                });
            </script>