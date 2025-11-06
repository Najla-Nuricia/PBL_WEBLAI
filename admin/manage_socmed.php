<?php
$page_title = 'Kelola Social Media';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $uuid = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM sosmed WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $success = 'Social media berhasil dihapus!';
    } catch (PDOException $e) {
        $error = 'Gagal menghapus social media: ' . $e->getMessage();
    }
}

// Handle Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = strtolower(clean_input($_POST['nama']));
    $url = clean_input($_POST['url']);

    try {
        if (isset($_POST['uuid']) && !empty($_POST['uuid'])) {
            // Update
            $uuid = $_POST['uuid'];
            $stmt = $pdo->prepare("UPDATE sosmed SET nama = ?, url = ? WHERE uuid = ?");
            $stmt->execute([$nama, $url, $uuid]);
            $success = 'Social media berhasil diupdate!';
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO sosmed (nama, url) VALUES (?, ?)");
            $stmt->execute([$nama, $url]);
            $success = 'Social media berhasil ditambahkan!';
        }
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// Get all social media
$stmt = $pdo->query("SELECT * FROM sosmed ORDER BY nama");
$social_media = $stmt->fetchAll();

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $uuid = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM sosmed WHERE uuid = ?");
    $stmt->execute([$uuid]);
    $edit_data = $stmt->fetch();
}

// Social media platforms with icons
$platforms = [
    'facebook' => ['icon' => 'facebook', 'color' => 'primary'],
    'instagram' => ['icon' => 'instagram', 'color' => 'danger'],
    'twitter' => ['icon' => 'twitter', 'color' => 'info'],
    'linkedin' => ['icon' => 'linkedin', 'color' => 'primary'],
    'youtube' => ['icon' => 'youtube', 'color' => 'danger'],
    'github' => ['icon' => 'github', 'color' => 'dark'],
    'tiktok' => ['icon' => 'tiktok', 'color' => 'dark'],
    'whatsapp' => ['icon' => 'whatsapp', 'color' => 'success'],
];
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
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-<?php echo $edit_data ? 'pencil' : 'plus'; ?>-circle me-2"></i>
                    <?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Social Media
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="uuid" value="<?php echo $edit_data['uuid']; ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Platform <span class="text-danger">*</span></label>
                        <select name="nama" class="form-select" required>
                            <option value="">Pilih Platform</option>
                            <?php foreach ($platforms as $key => $platform): ?>
                                <option value="<?php echo $key; ?>"
                                    <?php echo ($edit_data && $edit_data['nama'] == $key) ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($key); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="url"
                            name="url"
                            class="form-control"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['url']) : ''; ?>"
                            placeholder="https://..."
                            required>
                        <small class="text-muted">Link lengkap ke profile/halaman social media</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="manage_socmed.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Platform Guide -->
        <div class="card mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-2"></i>Format URL
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2">
                        <strong>Facebook:</strong><br>
                        <code>https://facebook.com/username</code>
                    </li>
                    <li class="mb-2">
                        <strong>Instagram:</strong><br>
                        <code>https://instagram.com/username</code>
                    </li>
                    <li class="mb-2">
                        <strong>LinkedIn:</strong><br>
                        <code>https://linkedin.com/company/name</code>
                    </li>
                    <li class="mb-2">
                        <strong>YouTube:</strong><br>
                        <code>https://youtube.com/@channel</code>
                    </li>
                    <li>
                        <strong>GitHub:</strong><br>
                        <code>https://github.com/username</code>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-list-ul me-2"></i>Daftar Social Media
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($social_media)): ?>
                    <div class="list-group">
                        <?php foreach ($social_media as $sosmed): ?>
                            <?php
                            $platform = $platforms[$sosmed['nama']] ?? ['icon' => 'link', 'color' => 'secondary'];
                            ?>
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-<?php echo $platform['color']; ?> bg-opacity-10 p-3 rounded">
                                            <i class="bi bi-<?php echo $platform['icon']; ?> text-<?php echo $platform['color']; ?>"
                                                style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold"><?php echo ucfirst($sosmed['nama']); ?></h6>
                                        <a href="<?php echo htmlspecialchars($sosmed['url']); ?>"
                                            target="_blank"
                                            class="small text-muted text-decoration-none">
                                            <i class="bi bi-link-45deg"></i>
                                            <?php echo htmlspecialchars($sosmed['url']); ?>
                                        </a>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="btn-group">
                                            <a href="?edit=<?php echo $sosmed['uuid']; ?>"
                                                class="btn btn-sm btn-warning"
                                                title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?delete=<?php echo $sosmed['uuid']; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirmDelete();"
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
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        Belum ada social media. Tambahkan link social media pertama Anda!
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Preview -->
        <?php if (!empty($social_media)): ?>
            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-eye me-2"></i>Preview di Website
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Ini adalah tampilan social media di footer website:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($social_media as $sosmed): ?>
                            <?php $platform = $platforms[$sosmed['nama']] ?? ['icon' => 'link', 'color' => 'secondary']; ?>
                            <a href="<?php echo htmlspecialchars($sosmed['url']); ?>"
                                target="_blank"
                                class="btn btn-outline-<?php echo $platform['color']; ?> btn-sm">
                                <i class="bi bi-<?php echo $platform['icon']; ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>