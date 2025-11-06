<?php
$page_title = 'Kelola Fasilitas';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $uuid = $_GET['delete'];
    try {
        // Get image path to delete file
        $stmt = $pdo->prepare("SELECT path_gambar FROM fasilitas WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $facility = $stmt->fetch();

        if ($facility && $facility['path_gambar'] && file_exists('../assets/img/' . $facility['path_gambar'])) {
            unlink('../assets/img/' . $facility['path_gambar']);
        }

        $stmt = $pdo->prepare("DELETE FROM fasilitas WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $success = 'Fasilitas berhasil dihapus!';
    } catch (PDOException $e) {
        $error = 'Gagal menghapus fasilitas: ' . $e->getMessage();
    }
}

// Handle Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = clean_input($_POST['nama']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $kuantitas = clean_input($_POST['kuantitas']);

    try {
        $path_gambar = null;

        // Handle file upload
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $upload_result = upload_file($_FILES['gambar']);
            if ($upload_result['success']) {
                $path_gambar = $upload_result['filename'];
            } else {
                $error = $upload_result['message'];
            }
        }

        if (!$error) {
            if (isset($_POST['uuid']) && !empty($_POST['uuid'])) {
                // Update
                $uuid = $_POST['uuid'];

                // Delete old image if new one uploaded
                if ($path_gambar) {
                    $stmt = $pdo->prepare("SELECT path_gambar FROM fasilitas WHERE uuid = ?");
                    $stmt->execute([$uuid]);
                    $old = $stmt->fetch();
                    if ($old && $old['path_gambar'] && file_exists('../assets/img/' . $old['path_gambar'])) {
                        unlink('../assets/img/' . $old['path_gambar']);
                    }
                }

                if ($path_gambar) {
                    $stmt = $pdo->prepare("UPDATE fasilitas SET nama = ?, deskripsi = ?, kuantitas = ?, path_gambar = ?, updated_at = CURRENT_TIMESTAMP WHERE uuid = ?");
                    $stmt->execute([$nama, $deskripsi, $kuantitas, $path_gambar, $uuid]);
                } else {
                    $stmt = $pdo->prepare("UPDATE fasilitas SET nama = ?, deskripsi = ?, kuantitas = ?, updated_at = CURRENT_TIMESTAMP WHERE uuid = ?");
                    $stmt->execute([$nama, $deskripsi, $kuantitas, $uuid]);
                }
                $success = 'Fasilitas berhasil diupdate!';
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO fasilitas (nama, deskripsi, kuantitas, path_gambar) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nama, $deskripsi, $kuantitas, $path_gambar]);
                $success = 'Fasilitas berhasil ditambahkan!';
            }
        }
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// Get all facilities
$stmt = $pdo->query("SELECT * FROM fasilitas ORDER BY nama");
$facilities = $stmt->fetchAll();

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $uuid = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM fasilitas WHERE uuid = ?");
    $stmt->execute([$uuid]);
    $edit_data = $stmt->fetch();
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

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-<?php echo $edit_data ? 'pencil' : 'plus'; ?>-circle me-2"></i>
                    <?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Fasilitas
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="uuid" value="<?php echo $edit_data['uuid']; ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Nama Fasilitas <span class="text-danger">*</span></label>
                        <input type="text"
                            name="nama"
                            class="form-control"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama']) : ''; ?>"
                            placeholder="Contoh: Komputer High-End"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="deskripsi"
                            class="form-control"
                            rows="5"
                            placeholder="Spesifikasi atau detail fasilitas..."
                            required><?php echo $edit_data ? htmlspecialchars($edit_data['deskripsi']) : ''; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Unit</label>
                        <input type="number"
                            name="kuantitas"
                            class="form-control"
                            min="0"
                            value="<?php echo $edit_data ? $edit_data['kuantitas'] : '1'; ?>">
                        <small class="text-muted">Kosongkan jika tidak ada jumlah tertentu</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Fasilitas</label>
                        <input type="file"
                            name="gambar"
                            class="form-control"
                            accept="image/*"
                            onchange="previewImage(this, 'preview')">
                        <small class="text-muted">Max 2MB, format: JPG, PNG, GIF</small>

                        <?php if ($edit_data && $edit_data['path_gambar']): ?>
                            <div class="mt-2">
                                <img src="../assets/img/<?php echo htmlspecialchars($edit_data['path_gambar']); ?>"
                                    id="preview"
                                    class="img-thumbnail"
                                    style="max-width: 200px;">
                            </div>
                        <?php else: ?>
                            <img id="preview" class="img-thumbnail mt-2" style="max-width: 200px; display: none;">
                        <?php endif; ?>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="manage_facilities.php" class="btn btn-secondary">
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
                    <i class="bi bi-list-ul me-2"></i>Daftar Fasilitas
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($facilities)): ?>
                    <div class="row g-3">
                        <?php foreach ($facilities as $facility): ?>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <?php if ($facility['path_gambar']): ?>
                                        <img src="../assets/img/<?php echo htmlspecialchars($facility['path_gambar']); ?>"
                                            class="card-img-top"
                                            alt="<?php echo htmlspecialchars($facility['nama']); ?>"
                                            style="height: 150px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold"><?php echo htmlspecialchars($facility['nama']); ?></h6>
                                        <?php if ($facility['kuantitas']): ?>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-box"></i> Jumlah: <?php echo $facility['kuantitas']; ?> unit
                                            </p>
                                        <?php endif; ?>
                                        <p class="card-text text-muted small">
                                            <?php echo htmlspecialchars($facility['deskripsi']); ?>
                                        </p>
                                        <div class="d-flex gap-2">
                                            <a href="?edit=<?php echo $facility['uuid']; ?>"
                                                class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="?delete=<?php echo $facility['uuid']; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirmDelete();">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        Belum ada fasilitas. Tambahkan fasilitas pertama Anda!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>