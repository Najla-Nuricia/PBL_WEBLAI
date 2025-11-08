<?php
require_once '../config/db.php';
require_once 'auth.php';

// Cek login dan session timeout
require_login();
check_session_timeout();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <style>
        :root {
            --primary-color: #1E4BA3;
            --secondary-color: #2C5AA0;
            --accent-color: #4A90E2;
            --sidebar-bg: #1a1f36;
            --sidebar-hover: #252b42;
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

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: var(--sidebar-bg);
            padding: 1.5rem 0;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }

        .sidebar-brand h4 {
            color: white;
            font-weight: 700;
            margin: 0;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.5rem;
            color: #a0aec0;
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: var(--sidebar-hover);
            color: white;
            border-left: 3px solid var(--accent-color);
        }

        .sidebar-menu li a i {
            margin-right: 0.8rem;
            font-size: 1.2rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Topbar */
        .topbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        }

        .clickable-card {
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .clickable-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .stats-card .card-footer {
            border-top: 1px solid #eee;
            padding-top: 0.75rem;
        }

        a.text-decoration-none {
            display: block;
            /* Biar seluruh area bisa diklik */
            color: inherit;
            /* Warna teks ikut warna aslinya */
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Pergantian Logo dan penambahan klik link logo -->
        <a href="../admin/dashboard.php" class="text-decoration-none text-white">
            <div class="sidebar-brand d-flex align-items-center">
                <img src="../assets/img/logo.png" alt="Logo AI Lab" class="me-2" style="width: 50px; height: 50px; object-fit: contain;">
                <div>
                    <h4 style="font-size: 1.2rem; margin-bottom: 0;">AI Lab Admin</h4>
                    <!-- Penambahan warna teks -->
                    <small style="color: #FF9F1C;">Dashboard Panel</small>
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

            <li class="mt-3">
                <div class="px-4 py-2">
                    <small class="text-white text-uppercase">Konten</small>
                </div>
            </li>

            <li>
                <a href="manage_profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_profile.php' ? 'active' : ''; ?>">
                    <i class="bi bi-building"></i>
                    <span>Profile Lab</span>
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
                <a href="manage_gallery.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_gallery.php' ? 'active' : ''; ?>">
                    <i class="bi bi-images"></i>
                    <span>Galeri</span>
                </a>
            </li>

            <li class="mt-3">
                <div class="px-4 py-2">
                    <small class="text-white text-uppercase">Tim & Mitra</small>
                </div>
            </li>

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

            <li class="mt-3">
                <div class="px-4 py-2">
                    <small class="text-white text-uppercase">Pengaturan</small>
                </div>
            </li>

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
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div>
                <button class="btn btn-link d-md-none" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h5 class="mb-0 d-inline-block"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="../public/index.php" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-globe me-1"></i>View Site
                </a>

                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Wrapper -->
        <div class="content-wrapper">