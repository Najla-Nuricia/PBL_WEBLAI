<?php

require_once '../config/db.php';
require_once '../helpers/sanitize.php';
require_once '../helpers/upload.php';
require_once 'includes/auth.php';

// header anti-cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect jika sudah login
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        $result = login_user($username, $password, $pdo);

        if ($result['success']) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AI Lab Polinema</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #1E4BA3;
            --secondary-color: #2C5AA0;
            --accent-color: #4A90E2;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background Elements */
        body::before,
        body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite;
        }

        body::before {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        body::after {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            animation-delay: 5s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(50px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-30px, 30px) scale(0.9);
            }
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 950px;
            width: 100%;
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-left {
            background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 30s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .login-left>div {
            position: relative;
            z-index: 1;
        }

        .login-left img {
            animation: pulse 3s ease-in-out infinite;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .login-right {
            padding: 3rem;
        }

        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(30, 75, 163, 0.15);
            transform: translateY(-2px);
        }

        .form-control:hover {
            border-color: var(--accent-color);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.5s ease, height 0.5s ease;
        }

        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 75, 163, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary span {
            position: relative;
            z-index: 1;
        }

        .alert {
            border-radius: 10px;
            border: none;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .back-link {
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            font-weight: 500;
        }

        .back-link:hover {
            color: var(--primary-color);
            transform: translateX(-5px);
        }

        .back-link i {
            transition: transform 0.3s ease;
        }

        .back-link:hover i {
            transform: translateX(-3px);
        }

        /* Password Toggle Eye */
        .password-toggle {
            position: relative;
        }

        .password-toggle-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
        }

        .password-toggle-icon:hover {
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .login-left {
                padding: 2rem;
            }

            .login-left h2 {
                font-size: 1.5rem;
            }

            .login-left p {
                font-size: 0.9rem;
            }

            .login-right {
                padding: 2rem;
            }
        }

        @media (max-width: 767.98px) {
            body {
                padding: 10px;
            }

            .login-card {
                border-radius: 15px;
            }

            .login-left {
                padding: 2rem 1.5rem;
            }

            .login-left img {
                width: 80px !important;
                height: 80px !important;
            }

            .login-left h2 {
                font-size: 1.3rem;
                margin-bottom: 1rem !important;
            }

            .login-left .lead {
                font-size: 0.9rem;
            }

            .login-left p:last-child {
                font-size: 0.85rem;
            }

            .login-right {
                padding: 1.5rem;
            }

            .login-right h3 {
                font-size: 1.3rem;
            }

            .form-control-lg {
                font-size: 1rem;
                padding: 0.6rem 1rem;
            }

            .btn-lg {
                font-size: 1rem;
                padding: 0.6rem;
            }
        }

        @media (max-width: 575.98px) {
            .login-left img {
                width: 70px !important;
                height: 70px !important;
            }

            .login-left h2 {
                font-size: 1.2rem;
            }

            .login-right h3 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="login-card">
                    <div class="row g-0">
                        <!-- Left Side -->
                        <div class="col-lg-5 login-left">
                            <div>
                                <img src="../assets/img/logo.png" alt="Logo AI Lab" class="mb-3" style="width: 100px; height: 100px; object-fit: contain;">
                                <h2 class="fw-bold mb-3">AI Lab Polinema</h2>
                                <p class="lead mb-2">Applied Informatics Laboratory</p>
                                <p class="mb-0">Dashboard Admin untuk mengelola konten website laboratorium</p>
                            </div>
                        </div>

                        <!-- Right Side - Login Form -->
                        <div class="col-lg-7 login-right">
                            <div class="mb-4">
                                <h3 class="fw-bold" style="color: var(--primary-color);">
                                    <i class="bi bi-shield-lock me-2"></i>Login Admin
                                </h3>
                                <p class="text-muted">Masuk untuk mengakses dashboard admin</p>
                            </div>

                            <?php if ($error): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <?php echo $error; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if ($success): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <?php echo $success; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="username" class="form-label">
                                        <i class="bi bi-person-fill me-1"></i>Username
                                    </label>
                                    <input type="text"
                                        class="form-control form-control-lg"
                                        id="username"
                                        name="username"
                                        placeholder="Masukkan username"
                                        required
                                        autofocus>
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label">
                                        <i class="bi bi-lock-fill me-1"></i>Password
                                    </label>
                                    <div class="password-toggle">
                                        <input type="password"
                                            class="form-control form-control-lg"
                                            id="password"
                                            name="password"
                                            placeholder="Masukkan password"
                                            required>
                                        <i class="bi bi-eye-slash password-toggle-icon" id="togglePassword"></i>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <span>
                                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                        </span>
                                    </button>
                                </div>
                            </form>

                            <hr class="my-4">

                            <div class="text-center">
                                <a href="../public/index.php" class="back-link">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Homepage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Password Toggle Script -->
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
        }
    </script>
</body>

</html>