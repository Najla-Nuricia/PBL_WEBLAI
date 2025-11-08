<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'db_lai');
define('DB_USER', 'postgres');
define('DB_PASS', '12345678'); // Ganti dengan password PostgreSQL Anda

try {
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper function untuk sanitasi input
function clean_input($data)
{
    if ($data === null) {
        return '';
    }
    $data = trim((string)$data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Helper function untuk upload file
function upload_file($file, $target_dir = '../assets/img/')
{
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB

    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan. Hanya JPG, PNG, GIF.'];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'Ukuran file maksimal 2MB.'];
    }

    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_path = $target_dir . $new_filename;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $new_filename, 'path' => $target_path];
    }

    return ['success' => false, 'message' => 'Gagal upload file.'];
}
