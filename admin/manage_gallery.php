<?php
$page_title = 'Kelola Galeri';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Handle Delete Gallery
if (isset($_GET['delete_gallery'])) {
    $uuid = $_GET['delete_gallery'];
    try {
        // Delete all photos first
        $stmt = $pdo->prepare("SELECT path_gambar FROM foto WHERE id_galeri = ?");
        $stmt->execute([$uuid]);
        $photos = $stmt->fetchAll();

        foreach ($photos as $photo) {
            if ($photo['path_gambar'] && file_exists('../assets/img/' . $photo['path_gambar'])) {
                unlink('../assets/img/' . $photo['path_gambar']);
            }
        }

        // Delete gallery (cascade will delete photos)
        $stmt = $pdo->prepare("DELETE FROM galeri WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $success = 'Galeri berhasil dihapus!';
    } catch (PDOException $e) {
        $error = 'Gagal menghapus galeri: ' . $e->getMessage();
    }
}

// Handle Delete Single Photo
if (isset($_GET['delete_photo'])) {
    $uuid = $_GET['delete_photo'];
    try {
        $stmt = $pdo->prepare("SELECT path_gambar FROM foto WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $photo = $stmt->fetch();

        if ($photo && $photo['path_gambar'] && file_exists('../assets/img/' . $photo['path_gambar'])) {
            unlink('../assets/img/' . $photo['path_gambar']);
        }

        $stmt = $pdo->prepare("DELETE FROM foto WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $success = 'Foto berhasil dihapus!';
    } catch (PDOException $e) {
        $error = 'Gagal menghapus foto: ' . $e->getMessage();
    }
}

// Handle Insert/Update Gallery
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'save_gallery') {
        $judul = clean_input($_POST['judul']);
        $deskripsi = clean_input($_POST['deskripsi']);

        try {
            if (isset($_POST['uuid']) && !empty($_POST['uuid'])) {
                // Update
                $uuid = $_POST['uuid'];
                $stmt = $pdo->prepare("UPDATE galeri SET judul = ?, deskripsi = ?, updated_at = CURRENT_TIMESTAMP WHERE uuid = ?");
                $stmt->execute([$judul, $deskripsi, $uuid]);
                $success = 'Galeri berhasil diupdate!';
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO galeri (judul, deskripsi) VALUES (?, ?) RETURNING uuid");
                $stmt->execute([$judul, $deskripsi]);
                $success = 'Galeri berhasil ditambahkan!';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }

    // Handle Upload Photos to Gallery
    if ($_POST['action'] == 'upload_photos' && isset($_POST['gallery_id'])) {
        $gallery_id = $_POST['gallery_id'];

        if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
            $upload_count = 0;
            $total_files = count($_FILES['photos']['name']);

            for ($i = 0; $i < $total_files; $i++) {
                if ($_FILES['photos']['error'][$i] == 0) {
                    $file = [
                        'name' => $_FILES['photos']['name'][$i],
                        'type' => $_FILES['photos']['type'][$i],
                        'tmp_name' => $_FILES['photos']['tmp_name'][$i],
                        'error' => $_FILES['photos']['error'][$i],
                        'size' => $_FILES['photos']['size'][$i]
                    ];

                    $upload_result = upload_file($file);

                    if ($upload_result['success']) {
                        try {
                            $stmt = $pdo->prepare("INSERT INTO foto (path_gambar, id_galeri) VALUES (?, ?)");
                            $stmt->execute([$upload_result['filename'], $gallery_id]);
                            $upload_count++;
                        } catch (PDOException $e) {
                            $error = 'Error saving to database: ' . $e->getMessage();
                        }
                    }
                }
            }

            if ($upload_count > 0) {
                $success = "$upload_count foto berhasil diupload!";
            }
        } else {
            $error = 'Pilih minimal 1 foto untuk diupload!';
        }
    }
}

// Get all galleries with photo count
$stmt = $pdo->query("
    SELECT g.*, COUNT(f.uuid) as foto_count 
    FROM galeri g 
    LEFT JOIN foto f ON g.uuid = f.id_galeri 
    GROUP BY g.uuid 
    ORDER BY g.created_at DESC
");
$galleries = $stmt->fetchAll();

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $uuid = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM galeri WHERE uuid = ?");
    $stmt->execute([$uuid]);
    $edit_data = $stmt->fetch();
}

// Get photos for view
$view_gallery = null;
$view_photos = [];
if (isset($_GET['view'])) {
    $uuid = $_GET['view'];
    $stmt = $pdo->prepare("SELECT * FROM galeri WHERE uuid = ?");
    $stmt->execute([$uuid]);
    $view_gallery = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT * FROM foto WHERE id_galeri = ? ORDER BY created_at DESC");
    $stmt->execute([$uuid]);
    $view_photos = $stmt->fetchAll();
}
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

<?php if ($view_gallery): ?>
    <!-- View Gallery Photos -->
    <div class="mb-4">
        <a href="manage_gallery.php" class="btn btn-secondary mb-3">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Galeri
        </a>

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-images me-2"></i><?php echo htmlspecialchars($view_gallery['judul']); ?>
                </h5>
                <p class="text-muted mb-0 small"><?php echo htmlspecialchars($view_gallery['deskripsi']); ?></p>
            </div>
            <div class="card-body">
                <!-- Upload Form -->
                <form method="POST" enctype="multipart/form-data" class="mb-4">
                    <input type="hidden" name="action" value="upload_photos">
                    <input type="hidden" name="gallery_id" value="<?php echo $view_gallery['uuid']; ?>">

                    <div class="row">
                        <div class="col-md-10">
                            <input type="file"
                                name="photos[]"
                                class="form-control"
                                multiple
                                accept="image/*"
                                required>
                            <small class="text-muted">Pilih satu atau lebih foto (Max 2MB per file)</small>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-upload me-2"></i>Upload
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Photos Grid -->
                <?php if (!empty($view_photos)): ?>
                    <div class="row g-3">
                        <?php foreach ($view_photos as $photo): ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card">
                                    <img src="../assets/img/<?php echo htmlspecialchars($photo['path_gambar']); ?>"
                                        class="card-img-top"
                                        style="height: 200px; object-fit: cover;">
                                    <div class="card-body p-2 text-center">
                                        <a href="?delete_photo=<?php echo $photo['uuid']; ?>&view=<?php echo $view_gallery['uuid']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirmDelete('Hapus foto ini?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Belum ada foto. Upload foto menggunakan form di atas.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Gallery List -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-<?php echo $edit_data ? 'pencil' : 'plus'; ?>-circle me-2"></i>
                        <?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Galeri
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="save_gallery">
                        <?php if ($edit_data): ?>
                            <input type="hidden" name="uuid" value="<?php echo $edit_data['uuid']; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">Judul Galeri <span class="text-danger">*</span></label>
                            <input type="text"
                                name="judul"
                                class="form-control"
                                value="<?php echo $edit_data ? htmlspecialchars($edit_data['judul']) : ''; ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi"
                                class="form-control"
                                rows="4"><?php echo $edit_data ? htmlspecialchars($edit_data['deskripsi']) : ''; ?></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan
                            </button>
                            <?php if ($edit_data): ?>
                                <a href="manage_gallery.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-list-ul me-2"></i>Daftar Galeri
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover datatable">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah Foto</th>
                                    <th width="180">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($galleries as $gallery): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($gallery['judul']); ?></strong></td>
                                        <td><?php echo substr(htmlspecialchars($gallery['deskripsi']), 0, 50) . '...'; ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo $gallery['foto_count']; ?> foto
                                            </span>
                                        </td>
                                        <td>
                                            <a href="?view=<?php echo $gallery['uuid']; ?>"
                                                class="btn btn-sm btn-info"
                                                title="Lihat Foto">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="?edit=<?php echo $gallery['uuid']; ?>"
                                                class="btn btn-sm btn-warning"
                                                title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?delete_gallery=<?php echo $gallery['uuid']; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirmDelete();"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/admin_footer.php'; ?>