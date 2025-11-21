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


    <link rel="stylesheet" href="../assets/css/login.css">

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