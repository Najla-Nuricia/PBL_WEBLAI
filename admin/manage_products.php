<?php
ob_start();
$page_title = 'Kelola Produk';
include 'includes/auth.php';
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
        $_SESSION['flash_success'] = 'Produk berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal menghapus produk: ' . $e->getMessage();
    } finally {
        header("Location: manage_products.php");
        exit;
    }
}

// Handle Bulk Delete
if (isset($_POST['bulk_delete']) && ($_POST['action'] ?? '') === 'bulk_delete' && !empty($_POST['selected'])) {
    $uuids = $_POST['selected'];

    try {
        // Buat placeholder dinamis sebanyak jumlah UUID
        $placeholders = implode(',', array_fill(0, count($uuids), '?'));
        $query = "DELETE FROM produk WHERE uuid IN ($placeholders)";
        $stmt = $pdo->prepare($query);

        // Eksekusi semua UUID
        $stmt->execute($uuids);

        $_SESSION['flash_success'] = count($uuids) . ' produk berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal menghapus beberapa produk: ' . $e->getMessage();
    } finally {
        header("Location: manage_products.php");
        exit;
    }
}

// Handle Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['action'] ?? '') === 'save') {
    $nama = clean_input($_POST['nama'] ?? '');
    $tahun = clean_input($_POST['tahun'] ?? '');
    $pembuat_id = !empty($_POST['pembuat_id'] ?? '') ? $_POST['pembuat_id'] : null;
    $deskripsi = clean_input($_POST['deskripsi'] ?? '');
    $link_demo = clean_input($_POST['link_demo'] ?? '');

    try {
        $path_gambar = null;

        // Handle file upload
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $upload_result = upload_file($_FILES['gambar']);
            if ($upload_result['success']) {
                $path_gambar = $upload_result['filename'];
            } else {
                $_SESSION['flash_error'] = $upload_result['error'];
            }
        }
        // Proceed only if no upload error
        if (!isset($_SESSION['flash_error'])) {
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
                $_SESSION['flash_success'] = 'Produk berhasil diperbarui!';
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO produk (nama, tahun, pembuat_id, deskripsi, link_demo, path_gambar) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nama, $tahun, $pembuat_id, $deskripsi, $link_demo, $path_gambar]);
                $_SESSION['flash_success'] = 'Produk berhasil ditambahkan!';
            }
        }
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    } finally {
        header("Location: manage_products.php");
        exit;
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

// Ambil flash message jika ada
if (isset($_SESSION['flash_success'])) {
    $success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}
?>

<!-- Form Tambah/Edit -->
<div class="card mb-4 shadow-sm border-0 animate__animated animate__fadeInUp">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-<?php echo $edit_data ? 'pencil' : 'plus'; ?>-circle me-2"></i>
            <?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Produk
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save">
            <?php if ($edit_data): ?>
                <input type="hidden" name="uuid" value="<?php echo $edit_data['uuid']; ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control"
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama']) : ''; ?>"
                        placeholder="Nama aplikasi/sistem" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                    <input type="number" name="tahun" class="form-control"
                        min="2000" max="<?php echo date('Y'); ?>"
                        value="<?php echo $edit_data ? $edit_data['tahun'] : date('Y'); ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Pembuat</label>
                    <select name="pembuat_id" class="form-select select-enhanced">
                        <option value="">Pilih Pembuat</option>
                        <?php foreach ($members as $member): ?>
                            <option value="<?php echo $member['uuid']; ?>"
                                <?php echo ($edit_data && $edit_data['pembuat_id'] == $member['uuid']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($member['nama']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Link Demo</label>
                    <input type="url" name="link_demo" class="form-control"
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['link_demo']) : ''; ?>"
                        placeholder="https://...">
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                    <textarea name="deskripsi" class="form-control" rows="3" required><?php echo $edit_data ? htmlspecialchars($edit_data['deskripsi']) : ''; ?></textarea>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Screenshot/Gambar</label>
                    <input type="file" name="gambar" class="form-control" accept="image/*" onchange="previewImage(this, 'preview')">
                    <small class="text-muted">Max 2MB, rekomendasi: 800x600px</small>

                    <?php if ($edit_data && $edit_data['path_gambar']): ?>
                        <div class="mt-2">
                            <img src="../assets/img/<?php echo htmlspecialchars($edit_data['path_gambar']); ?>"
                                id="preview" class="img-thumbnail" style="max-width: 300px;">
                        </div>
                    <?php else: ?>
                        <img id="preview" class="img-thumbnail mt-2" style="max-width: 300px; display: none;">
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex gap-2">
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

<!-- Daftar Produk (Tetap gunakan card grid atau ubah ke table) -->
<div class="card shadow-sm border-0 animate__animated animate__fadeInUp">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Produk
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($products)): ?>
            <div class="card shadow-sm border-0 text-center animate__animated animate__fadeInUp">
                <div class="card-body py-5">
                    <i class="bi bi-emoji-frown text-info" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">Belum ada produk</h5>
                    <p class="text-secondary small">Yuk tambahkan produk baru untuk ditampilkan di sini!</p>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <form method="POST" id="bulkDeleteForm" action="">
                    <input type="hidden" name="action" value="bulk_delete">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th width="50">No</th>
                                <th width="100">Gambar</th>
                                <th>Nama Produk</th>
                                <th width="80">Tahun</th>
                                <th width="150">Pembuat</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $index => $product): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected[]" value="<?= $product['uuid']; ?>" class="rowCheckbox">
                                    </td>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <?php if ($product['path_gambar']): ?>
                                            <img src="../assets/img/<?php echo htmlspecialchars($product['path_gambar']); ?>"
                                                style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                style="width: 80px; height: 60px; border-radius: 5px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($product['nama']); ?></strong><br>
                                        <small class="text-muted"><?php echo substr(htmlspecialchars($product['deskripsi']), 0, 60) . '...'; ?></small>
                                    </td>
                                    <td><?php echo $product['tahun']; ?></td>
                                    <td>
                                        <?php if ($product['pembuat_nama']): ?>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($product['pembuat_nama']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['link_demo']): ?>
                                            <a href="<?php echo htmlspecialchars($product['link_demo']); ?>"
                                                target="_blank" class="btn btn-sm btn-info" title="Demo">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="?edit=<?php echo $product['uuid']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?delete=<?php echo $product['uuid']; ?>" class="btn btn-sm btn-danger"
                                            onclick="return confirmDelete();" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div id="bulkAction" class="mt-3 d-none">
                        <button type="button" id="bulkDeleteBtn" class="btn btn-danger">
                            <i class="bi bi-trash3 me-2"></i>Hapus Terpilih
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const successMessage = "<?= addslashes($success ?? '') ?>";
        const errorMessage = "<?= addslashes($error ?? '') ?>";

        if (successMessage) showSuccess(successMessage);
        if (errorMessage) showError(errorMessage);
    });
</script>