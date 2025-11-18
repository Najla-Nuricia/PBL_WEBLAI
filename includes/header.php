<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Applied Informatics Laboratory - Politeknik Negeri Malang">
    <meta name="keywords" content="AI Lab, Polinema, Laboratory, Research, Informatics">
    <meta name="author" content="AI Lab Polinema">

    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>AI Lab Polinema</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #1E4BA3;
            --secondary-color: #2C5AA0;
            --accent-color: #4A90E2;
            --text-dark: #2C3E50;
            --text-light: #6C757D;
            --bg-light: #F8F9FA;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            line-height: 1.6;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            gap: 10px;
        }

        .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: color 0.3s;
        }

        /* Setting layout logo */
        .navbar-logo {
            width: auto;
            height: 50px;
            object-fit: contain;
            display: block;
        }

        .navbar-logo-text {
            font-weight: 700;
            color: var(--primary-color);
            line-height: 1;
            position: relative;
            top: 2px;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .nav-link.active {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .section-title {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .section-subtitle {
            color: var(--text-light);
            margin-bottom: 3rem;
        }

        .page-header h1 {
            color: #fff !important;
        }

        /* Efek page-vaigation */
        .pagination .page-link {
            border-radius: 8px;
            margin: 0 4px;
            color: #1E4BA3;
            border: 1px solid #1E4BA3;
            background-color: #fff;
            transition: all 0.25s ease-in-out;
            font-weight: 500;
            box-shadow: 0 0 0 rgba(0, 0, 0, 0);
        }

        .pagination .page-link:hover {
            background-color: #1E4BA3;
            color: #fff;
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 4px 8px rgba(30, 75, 163, 0.2);
        }

        .pagination .page-item.active .page-link {
            background-color: #1E4BA3;
            border-color: #1E4BA3;
            color: #fff;
            transform: none;
            box-shadow: 0 3px 6px rgba(30, 75, 163, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.6;
            pointer-events: none;
            transform: none;
            box-shadow: none;
        }

        /* Efek List-group Hover  */
        .list-group-item:hover:not(.active) {
            font-weight: bold;
            color: var(--bs-list-group-active-bg);
        }

        .list-group-item:hover.active {
            font-weight: bold;
        }

        /* Sosial media button smooth hover */
        footer .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.9);
            background-color: transparent;
            color: white;
            transition: all 0.3s ease;
            border-radius: 10px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0;
        }
    </style>
</head>

<body>
    <script src="https://unpkg.com/typeit@8.8.3/dist/index.umd.js"></script>

</body>