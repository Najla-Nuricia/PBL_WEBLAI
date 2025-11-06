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

    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">

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
        }

        .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: color 0.3s;
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
    </style>
</head>

<body>
    <script src="https://unpkg.com/typeit@8.8.3/dist/index.umd.js"></script>

</body>