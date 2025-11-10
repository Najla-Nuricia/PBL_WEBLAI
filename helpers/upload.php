<?php
function upload_file($file, $target_dir = null, $max_file_size = null)
{
    $upload_dir = $target_dir ?? $_ENV['UPLOAD_DIR'];
    $max_size = $max_file_size ?? (int)$_ENV['MAX_FILE_SIZE'];

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan.'];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'Ukuran file maksimal 2MB.'];
    }

    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_path = rtrim($upload_dir, '/') . '/' . $new_filename;

    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $new_filename, 'path' => $target_path];
    }

    return ['success' => false, 'message' => 'Gagal upload file.'];
}
