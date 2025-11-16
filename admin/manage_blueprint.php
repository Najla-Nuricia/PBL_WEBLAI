<?php
ob_start();
$page_title = 'Kelola Blueprint';
include 'includes/auth.php';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM blueprint WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_success'] = 'Bluerint berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal menghapus bluerint: ' . $e->getMessage();
    } finally {
        header("Location: manage_blueprint.php");
        exit;
    }
}

// Handle Bulk Delete
if (isset($_POST['bulk_delete']) && ($_POST['action'] ?? '') === 'bulk_delete' && !empty($_POST['selected'])) {
    $id = $_POST['selected'];

    try {
        // Buat placeholder dinamis sebanyak jumlah UUID
        $placeholders = implode(',', array_fill(0, count($id), '?'));
        $query = "DELETE FROM blueprint WHERE id IN ($placeholders)";
        $stmt = $pdo->prepare($query);

        // Eksekusi semua UUID
        $stmt->execute($id);

        $_SESSION['flash_success'] = count($id) . ' Blueprint berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal menghapus beberapa blueprint: ' . $e->getMessage();
    } finally {
        header("Location: manage_blueprint.php");
        exit;
    }
}

// Handle Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['action'] ?? '') === 'save') {
    $judul = clean_input($_POST['judul'] ?? '');
    $deskripsi = clean_input($_POST['deskripsi'] ?? '');

    try {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update
            $id = $_POST['id'];
            $stmt = $pdo->prepare("UPDATE blueprint SET judul = ?, deskripsi = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$judul, $deskripsi, $id]);
            $_SESSION['flash_success'] = 'Bluerint berhasil diudate!';
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO blueprint (judul, deskripsi) VALUES (?, ?)");
            $stmt->execute([$judul, $deskripsi]);
            $_SESSION['flash_success'] = 'Blueprint berhasil ditambahkan!';
        }
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    } finally {
        header("Location: manage_blueprint.php");
        exit;
    }
}

// Get all blueprints
$stmt = $pdo->query("SELECT * FROM blueprint ORDER BY created_at DESC");
$blueprints = $stmt->fetchAll();

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM blueprint WHERE id = ?");
    $stmt->execute([$id]);
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
<div class="card mb-4 border-0 animate__animated animate__fadeInUp">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-<?php echo $edit_data ? 'pencil' : 'plus'; ?>-circle me-2"></i>
            <?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Blueprint
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <input type="hidden" name="action" value="save">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label">Judul Blueprint <span class="text-danger">*</span></label>
                    <input type="text"
                        name="judul"
                        class="form-control"
                        placeholder="Contoh: Pengembangan Sistem AI untuk Deteksi Penyakit"
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['judul']) : ''; ?>"
                        maxlength="100"
                        required>
                    <small class="text-muted">Maksimal 100 karakter</small>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Deskripsi Blueprint</label>
                    <textarea name="deskripsi"
                        class="form-control"
                        rows="5"
                        placeholder="Deskripsi lengkap mengenai blueprint penelitian, tujuan, metodologi, dan hasil yang diharapkan..."><?php echo $edit_data ? htmlspecialchars($edit_data['deskripsi']) : ''; ?></textarea>
                    <small class="text-muted">Jelaskan secara detail mengenai blueprint penelitian ini</small>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Simpan
                </button>
                <?php if ($edit_data): ?>
                    <a href="manage_blueprint.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Daftar Blueprint -->
<div class="card border-0 animate__animated animate__fadeInUp">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Blueprint
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($blueprints)): ?>
            <div class="card shadow-sm border-0 text-center animate__animated animate__fadeInUp">
                <div class="card-body py-5">
                    <i class="bi bi-emoji-frown text-info" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">Belum ada blueprint</h5>
                    <p class="text-secondary small">Yuk tambahkan blueprint baru untuk ditampilkan di sini!</p>
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
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th width="150">Tanggal Dibuat</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($blueprints as $index => $blueprint): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected[]" value="<?= $blueprint['id']; ?>" class="rowCheckbox">
                                    </td>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                        <strong><?php echo htmlspecialchars($blueprint['judul']); ?></strong>
                                    </td>
                                    <td>
                                        <?php
                                        $desc = htmlspecialchars($blueprint['deskripsi']);
                                        echo mb_strlen($desc) > 100 ? mb_substr($desc, 0, 100) . '...' : $desc;
                                        ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($blueprint['created_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <a href="?edit=<?php echo $blueprint['id']; ?>"
                                            class="btn btn-sm btn-warning"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?delete=<?php echo $blueprint['id']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirmDelete('Hapus blueprint ini?');"
                                            title="Hapus">
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