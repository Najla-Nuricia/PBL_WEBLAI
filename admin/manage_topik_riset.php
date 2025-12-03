<?php
ob_start();
$page_title = 'Kelola Topik Riset';
include 'includes/auth.php';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM topik_riset WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_success'] = 'Topik riset berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal menghapus topik riset: ' . $e->getMessage();
    } finally {
        header("Location: manage_topik_riset.php");
        exit;
    }
}

// Handle Bulk Delete
if (($_POST['action'] ?? '') === 'bulk_delete' && !empty($_POST['selected'])) {
    $id = $_POST['selected'];

    try {
        // Buat placeholder dinamis sebanyak jumlah UUID
        $placeholders = implode(',', array_fill(0, count($id), '?'));
        $query = "DELETE FROM topik_riset WHERE id IN ($placeholders)";
        $stmt = $pdo->prepare($query);

        // Eksekusi semua UUID
        $stmt->execute($id);
        $_SESSION['flash_success'] = count($id) . ' topic riset berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal menghapus topik riset: ' . $e->getMessage();
    } finally {
        header("Location: manage_topik_riset.php");
        exit;
    }
}


// Handle Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['action'] ?? '') === 'save') {
    $topik = clean_input($_POST['topik'] ?? '');

    try {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update
            $id = $_POST['id'];
            $stmt = $pdo->prepare("UPDATE topik_riset SET topik = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$topik, $id]);
            $_SESSION['flash_success'] = 'Topik riset berhasil diperbarui!';
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO topik_riset (topik) VALUES (?)");
            $stmt->execute([$topik]);
            $_SESSION['flash_success'] = 'Topik riset berhasil ditambahkan!';
        }
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    } finally {
        header("Location: manage_topik_riset.php");
        exit;
    }
}

// Get all topics
$stmt = $pdo->query("SELECT * FROM topik_riset ORDER BY created_at DESC");
$topics = $stmt->fetchAll();

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM topik_riset WHERE id = ?");
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
<div class="card mb-4 shadow-sm border-0 animate__animated animate__fadeInUp">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-<?php echo $edit_data ? 'pencil' : 'plus'; ?>-circle me-2"></i>
            <?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Topik Riset
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
                    <label class="form-label">Topik Riset <span class="text-danger">*</span></label>
                    <textarea name="topik"
                        class="form-control"
                        rows="3"
                        placeholder="Contoh: Machine Learning, Computer Vision, Natural Language Processing, IoT, Cloud Computing"
                        required><?php echo $edit_data ? htmlspecialchars($edit_data['topik']) : ''; ?></textarea>
                    <small class="text-muted">Deskripsi topik penelitian yang sedang dikembangkan</small>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Simpan
                </button>
                <?php if ($edit_data): ?>
                    <a href="manage_topik_riset.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Daftar Topik Riset -->
<div class="card shadow-sm border-0 animate__animated animate__fadeInUp">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Topik Riset
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($topics)): ?>
            <div class="card shadow-sm border-0 text-center animate__animated animate__fadeInUp">
                <div class="card-body py-5">
                    <i class="bi bi-emoji-frown text-info" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">Belum ada topik riset</h5>
                    <p class="text-secondary small">Yuk tambahkan topik riset baru untuk ditampilkan di sini!</p>
                </div>
            </div>
        <?php else: ?>
            <link rel="stylesheet" href="../assets/css/swipejs.css">
            <div class="swipeable-table" id="swipeTable">
                <form method="POST" id="bulkDeleteForm" action="">
                    <input type="hidden" name="action" value="bulk_delete">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th width="50">No</th>
                                <th>Topik Riset</th>
                                <th width="150">Tanggal Dibuat</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topics as $index => $topic): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected[]" value="<?= $topic['id']; ?>" class="rowCheckbox">
                                    </td>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($topic['topik']); ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y', strtotime($topic['created_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <a href="?edit=<?php echo $topic['id']; ?>"
                                            class="btn btn-sm btn-warning"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?delete=<?php echo $topic['id']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirmDelete('Hapus topik riset ini?');"
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