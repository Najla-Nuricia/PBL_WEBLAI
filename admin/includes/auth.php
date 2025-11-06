<?php
session_start();

// Cek apakah user sudah login
function is_logged_in()
{
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

// Redirect ke login jika belum login
function require_login()
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

// Login user
function login_user($username, $password, $pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT uuid, username, password FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['uuid'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_time'] = time();
            return ['success' => true, 'message' => 'Login berhasil!'];
        }

        return ['success' => false, 'message' => 'Username atau password salah!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Logout user
function logout_user()
{
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

// Cek session timeout (30 menit)
function check_session_timeout()
{
    $timeout_duration = 1800; // 30 menit

    if (isset($_SESSION['login_time'])) {
        $elapsed_time = time() - $_SESSION['login_time'];

        if ($elapsed_time > $timeout_duration) {
            logout_user();
        }
    }

    $_SESSION['login_time'] = time(); // Reset timer
}
