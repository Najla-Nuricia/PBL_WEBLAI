<?php
$page_title = 'Kelola Produk';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $uuid = $_GET['delete'];
    try {
        // Get image path to delete file
        $stmt = $pdo->prepare("SELECT path_gambar FROM produk WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $product = $stmt->fetch();

        if ($product && $product['path_gambar'] && file_exists('../assets/img/' . $product['path_gambar'])) {
            unlink('../assets/img/' . $product['path_gambar']);
        }

        $stmt = $pdo->prepare("DELETE FROM produk WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $success = 'Produk berhasil dihapus!';
    } catch (PDOException $e) {
        $error = 'Gagal menghapus produk: ' . $e->getMessage();
    }
}

// Handle Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = clean_input($_POST['nama']);
    $tahun = clean_input($_POST['tahun']);
    $pembuat_id = !empty($_POST['pembuat_id']) ? $_POST['pembuat_id'] : null;
    $deskripsi = clean_input($_POST['deskripsi']);
    $link_demo = clean_input($_POST['link_demo']);

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
                    $stmt = $pdo->prepare("SELECT path_gambar FROM produk WHERE uuid = ?");
                    $stmt->execute([$uuid]);
                    $old = $stmt->fetch();
                    if ($old && $old['path_gambar'] && file_exists('../assets/img/' . $old['path_gambar'])) {
                        unlink('../assets/img/' . $old['path_gambar']);
                    }
                }

                if ($path_gambar) {
                    $stmt = $pdo->prepare("UPDATE produk SET nama = ?, tahun = ?, pembuat_id = ?, deskripsi = ?, link_demo = ?, path_gambar = ?, updated_at = CURRENT_TIMESTAMP WHERE uuid = ?");
                    $stmt->execute([$nama, $tahun, $pembuat_id, $deskripsi, $link_demo, $path_gambar, $uuid]);
                } else {
                    $stmt = $pdo->prepare("UPDATE produk SET nama = ?, tahun = ?, pembuat_id = ?, deskripsi = ?, link_demo = ?, updated_at = CURRENT_TIMESTAMP WHERE uuid = ?");
                    $stmt->execute([$nama, $tahun, $pembuat_id, $deskripsi, $link_demo, $uuid]);
                }
                $success = 'Produk berhasil diupdate!';
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO produk (nama, tahun, pembuat_id, deskripsi, link_demo, path_gambar) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nama, $tahun, $pembuat_id, $deskripsi, $link_demo, $path_gambar]);
                $success = 'Produk berhasil ditambahkan!';
            }
        }
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// Get all products with author info
$stmt = $pdo->query("
    SELECT p.*, a.nama as pembuat_nama 
    FROM produk p 
    LEFT JOIN anggota a ON p.pembuat_id = a.uuid 
    ORDER BY p.tahun DESC, p.nama ASC
");
$products = $stmt->fetchAll();

// Get all members for dropdown
$stmt_members = $pdo->query("SELECT uuid, nama FROM anggota ORDER BY nama");
$members = $stmt_members->fetchAll();

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $uuid = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE uuid = ?");
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
                    <?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Produk
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="uuid" value="<?php echo $edit_data['uuid']; ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text"
                            name="nama"
                            class="form-control"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama']) : ''; ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tahun <span class="text-danger">*</span></label>
                        <input type="number"
                            name="tahun"
                            class="form-control"
                            min="2000"
                            max="<?php echo date('Y'); ?>"
                            value="<?php echo $edit_data ? $edit_data['tahun'] : date('Y'); ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pembuat</label>
                        <select name="pembuat_id" class="form-select">
                            <option value="">Pilih Pembuat</option>
                            <?php foreach ($members as $member): ?>
                                <option value="<?php echo $member['uuid']; ?>"
                                    <?php echo ($edit_data && $edit_data['pembuat_id'] == $member['uuid']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($member['nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="deskripsi"
                            class="form-control"
                            rows="5"
                            required><?php echo $edit_data ? htmlspecialchars($edit_data['deskripsi']) : ''; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Link Demo</label>
                        <input type="url"
                            name="link_demo"
                            class="form-control"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['link_demo']) : ''; ?>"
                            placeholder="https://...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Screenshot/Gambar</label>
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
                            <a href="manage_products.php" class="btn btn-secondary">
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
                    <i class="bi bi-list-ul me-2"></i>Daftar Produk
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <?php if ($product['path_gambar']): ?>
                                        <img src="../assets/img/<?php echo htmlspecialchars($product['path_gambar']); ?>"
                                            class="card-img-top"
                                            alt="<?php echo htmlspecialchars($product['nama']); ?>"
                                            style="height: 150px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <span class="badge bg-primary mb-2"><?php echo $product['tahun']; ?></span>
                                        <h6 class="card-title fw-bold"><?php echo htmlspecialchars($product['nama']); ?></h6>
                                        <?php if ($product['pembuat_nama']): ?>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($product['pembuat_nama']); ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="card-text text-muted small">
                                            <?php echo substr(htmlspecialchars($product['deskripsi']), 0, 100) . '...'; ?>
                                        </p>
                                        <div class="d-flex gap-2">
                                            <a href="?edit=<?php echo $product['uuid']; ?>"
                                                class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?delete=<?php echo $product['uuid']; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirmDelete();">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <?php if ($product['link_demo']): ?>
                                                <a href="<?php echo htmlspecialchars($product['link_demo']); ?>"
                                                    target="_blank"
                                                    class="btn btn-sm btn-info ms-auto">
                                                    <i class="bi bi-eye"></i> Demo
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle me-2"></i>
                                Belum ada produk. Tambahkan produk pertama Anda!
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>