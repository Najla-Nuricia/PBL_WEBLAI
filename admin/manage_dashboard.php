<?php
$page_title = 'Kelola Dashboard';
include 'includes/admin_header.php';

$success = '';
$error = '';
$upload_dir = '../assets/img/dashboard/';

// Create directory if not exists
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        // Get file path before delete
        $stmt = $pdo->prepare("SELECT path_gambar FROM dashboard_foto WHERE id = ?");
        $stmt->execute([$id]);
        $foto = $stmt->fetch();

        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM dashboard_foto WHERE id = ?");
        $stmt->execute([$id]);

        // Delete file
        if ($foto && $foto['path_gambar'] && file_exists($upload_dir . $foto['path_gambar'])) {
            unlink($upload_dir . $foto['path_gambar']);
        }

        $success = 'Foto dashboard berhasil dihapus!';
    } catch (PDOException $e) {
        $error = 'Gagal menghapus foto: ' . $e->getMessage();
    }
}

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['gambar'])) {
    $file = $_FILES['gambar'];

    // Validate file
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if ($file['error'] === 0) {
        if (in_array($file['type'], $allowed_types)) {
            if ($file['size'] <= $max_size) {
                $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $new_filename = 'dashboard_' . uniqid() . '.' . $file_ext;
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO dashboard_foto (path_gambar) VALUES (?)");
                        $stmt->execute([$new_filename]);
                        $success = 'Foto dashboard berhasil diupload!';
                    } catch (PDOException $e) {
                        unlink($upload_path);
                        $error = 'Gagal menyimpan ke database: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Gagal mengupload file!';
                }
            } else {
                $error = 'Ukuran file terlalu besar! Maksimal 5MB.';
            }
        } else {
            $error = 'Tipe file tidak diizinkan! Hanya JPG, PNG, GIF, WEBP.';
        }
    } else {
        $error = 'Error saat upload file!';
    }
}

// Handle Set Active
if (isset($_GET['set_active'])) {
    $id = $_GET['set_active'];
    try {
        // Deactivate all first (optional: you can modify this to allow multiple active images)
        $pdo->query("UPDATE dashboard_foto SET updated_at = CURRENT_TIMESTAMP");

        // Then activate selected one (mark as most recent)
        $stmt = $pdo->prepare("UPDATE dashboard_foto SET updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Foto berhasil diatur sebagai aktif!';
    } catch (PDOException $e) {
        $error = 'Gagal mengatur foto: ' . $e->getMessage();
    }
}

// Get all dashboard photos
$stmt = $pdo->query("SELECT * FROM dashboard_foto ORDER BY updated_at DESC");
$photos = $stmt->fetchAll();

// Get active photo (most recent)
$active_photo = !empty($photos) ? $photos[0] : null;
?>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i><?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Info Section -->
<div class="alert alert-info mb-4">
    <i class="bi bi-info-circle-fill me-2"></i>
    <strong>Info:</strong> Upload gambar untuk background hero section di halaman dashboard.
    Gambar yang terakhir diupdate akan menjadi background aktif.
    Ukuran maksimal 5MB. Format: JPG, PNG, GIF, WEBP.
</div>

<!-- Current Active Background -->
<?php if ($active_photo): ?>
    <div class="card mb-4 border-success">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-check-circle me-2"></i>Background Aktif Saat Ini
            </h5>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <img src="<?php echo $upload_dir . htmlspecialchars($active_photo['path_gambar']); ?>"
                        alt="Active Background"
                        class="img-fluid rounded shadow-sm"
                        style="max-height: 200px; width: 100%; object-fit: cover;">
                </div>
                <div class="col-md-8">
                    <h6 class="fw-bold mb-2">
                        <i class="bi bi-image me-2"></i>
                        <?php echo htmlspecialchars($active_photo['path_gambar']); ?>
                    </h6>
                    <p class="text-muted mb-2">
                        <i class="bi bi-calendar me-2"></i>
                        Diupload: <?php echo date('d/m/Y H:i', strtotime($active_photo['created_at'])); ?>
                    </p>
                    <p class="text-muted mb-0">
                        <i class="bi bi-clock me-2"></i>
                        Terakhir Update: <?php echo date('d/m/Y H:i', strtotime($active_photo['updated_at'])); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Upload Form -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-cloud-upload me-2"></i>Upload Foto Background Baru
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label">Pilih Gambar <span class="text-danger">*</span></label>
                    <input type="file"
                        name="gambar"
                        class="form-control"
                        accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                        required>
                    <small class="text-muted">
                        Format: JPG, PNG, GIF, WEBP | Maksimal: 5MB | Rekomendasi ukuran: 1920x800px
                    </small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-cloud-upload me-2"></i>Upload Gambar
            </button>
        </form>
    </div>
</div>

<!-- Gallery of All Backgrounds -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-images me-2"></i>Galeri Background Dashboard
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($photos)): ?>
            <div class="row g-4">
                <?php foreach ($photos as $index => $photo): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 <?php echo $index === 0 ? 'border-success' : ''; ?>">
                            <?php if ($index === 0): ?>
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Aktif
                                    </span>
                                </div>
                            <?php endif; ?>

                            <img src="<?php echo $upload_dir . htmlspecialchars($photo['path_gambar']); ?>"
                                class="card-img-top"
                                alt="Dashboard Background"
                                style="height: 200px; object-fit: cover;">

                            <div class="card-body">
                                <h6 class="card-title text-truncate" title="<?php echo htmlspecialchars($photo['path_gambar']); ?>">
                                    <i class="bi bi-image me-1"></i>
                                    <?php echo htmlspecialchars($photo['path_gambar']); ?>
                                </h6>

                                <p class="card-text small text-muted mb-2">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($photo['created_at'])); ?>
                                </p>

                                <p class="card-text small text-muted mb-3">
                                    <i class="bi bi-clock me-1"></i>
                                    Update: <?php echo date('d/m/Y H:i', strtotime($photo['updated_at'])); ?>
                                </p>

                                <div class="d-flex gap-2">
                                    <?php if ($index !== 0): ?>
                                        <a href="?set_active=<?php echo $photo['id']; ?>"
                                            class="btn btn-sm btn-success"
                                            title="Set sebagai aktif">
                                            <i class="bi bi-check-circle"></i> Set Aktif
                                        </a>
                                    <?php endif; ?>

                                    <a href="<?php echo $upload_dir . htmlspecialchars($photo['path_gambar']); ?>"
                                        target="_blank"
                                        class="btn btn-sm btn-info"
                                        title="Lihat gambar">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="?delete=<?php echo $photo['id']; ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirmDelete('Hapus foto ini?');"
                                        title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center mb-0">
                <i class="bi bi-info-circle me-2"></i>
                Belum ada foto background. Upload foto pertama Anda!
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Statistics -->
<?php if (!empty($photos)): ?>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h2 class="display-4 fw-bold text-primary mb-2">
                        <?php echo count($photos); ?>
                    </h2>
                    <p class="text-muted mb-0">Total Background</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h2 class="display-4 fw-bold text-success mb-2">
                        <i class="bi bi-image"></i>
                    </h2>
                    <p class="text-muted mb-0">Background Aktif</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Preview Section -->
<?php if ($active_photo): ?>
    <div class="card mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-eye me-2"></i>Preview Hero Section
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="position-relative" style="background: url('<?php echo $upload_dir . htmlspecialchars($active_photo['path_gambar']); ?>') center/cover; min-height: 400px;">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(30, 75, 163, 0.8) 0%, rgba(74, 144, 226, 0.8) 100%);"></div>
                <div class="position-relative text-white p-5">
                    <h1 class="display-4 fw-bold mb-3">Applied Informatics Laboratory</h1>
                    <p class="lead mb-4">Laboratorium penelitian dan pengembangan teknologi informasi terapan di Politeknik Negeri Malang</p>
                    <div class="d-flex gap-3">
                        <button class="btn btn-light btn-lg">
                            <i class="bi bi-info-circle me-2"></i>Learn More
                        </button>
                        <button class="btn btn-outline-light btn-lg">
                            <i class="bi bi-envelope me-2"></i>Contact Us
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Usage Instructions -->
<div class="card mt-4 border-info">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-question-circle me-2"></i>Cara Menggunakan
        </h5>
    </div>
    <div class="card-body">
        <ol class="mb-0">
            <li class="mb-2">Upload gambar background baru menggunakan form di atas</li>
            <li class="mb-2">Gambar yang terakhir diupdate otomatis menjadi background aktif</li>
            <li class="mb-2">Untuk mengubah background, klik tombol "Set Aktif" pada gambar yang diinginkan</li>
            <li class="mb-2">Preview akan menampilkan bagaimana background terlihat di halaman utama</li>
            <li>Background akan otomatis muncul di halaman index.php dengan gradient overlay biru</li>
        </ol>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>