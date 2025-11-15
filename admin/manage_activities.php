<?php
ob_start();
$page_title = 'Kelola Kegiatan';
include 'includes/auth.php';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $uuid = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM kegiatan WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $_SESSION['flash_success'] = 'Kegiatan berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal menghapus kegiatan: ' . $e->getMessage();
    } finally {
        header("Location: manage_activities.php");
        exit;
    }
}

// Handle Bulk Delete
if (isset($_POST['bulk_delete']) && ($_POST['action'] ?? '') === 'bulk_delete' && !empty($_POST['selected'])) {
    $uuids = $_POST['selected'];

    try {
        // Buat placeholder dinamis sebanyak jumlah UUID
        $placeholders = implode(',', array_fill(0, count($uuids), '?'));
        $query = "DELETE FROM kegiatan WHERE uuid IN ($placeholders)";
        $stmt = $pdo->prepare($query);

        // Eksekusi semua UUID
        $stmt->execute($uuids);

        $_SESSION['flash_success'] = count($uuids) . ' kegiatan berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal menghapus beberapa kegiatan: ' . $e->getMessage();
    } finally {
        header("Location: manage_activities.php");
        exit;
    }
}

// Handle Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST'  && ($_POST['action'] ?? '') === 'save') {
    $nama = clean_input($_POST['nama'] ?? '');
    $tanggal = clean_input($_POST['tanggal'] ?? '');
    $pemateri = clean_input($_POST['pemateri'] ?? '');
    $kategori_kegiatan = clean_input($_POST['kategori_kegiatan'] ?? '');
    $deskripsi_singkat = clean_input($_POST['deskripsi_singkat'] ?? '');

    try {
        if (!empty($_POST['uuid'])) {
            $uuid = $_POST['uuid'];
            $stmt = $pdo->prepare("UPDATE kegiatan SET nama=?, tanggal=?, pemateri=?, kategori_kegiatan=?, deskripsi_singkat=?, updated_at=CURRENT_TIMESTAMP WHERE uuid=?");
            $stmt->execute([$nama, $tanggal, $pemateri, $kategori_kegiatan, $deskripsi_singkat, $uuid]);
            $_SESSION['flash_success'] = 'Kegiatan berhasil diupdate!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO kegiatan (nama, tanggal, pemateri, kategori_kegiatan, deskripsi_singkat) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $tanggal, $pemateri, $kategori_kegiatan, $deskripsi_singkat]);
            $_SESSION['flash_success'] = 'Kegiatan berhasil ditambahkan!';
        }
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    } finally {
        header("Location: manage_activities.php");
        exit;
    }
}


// Get all activities
$stmt = $pdo->query("SELECT * FROM kegiatan ORDER BY tanggal DESC");
$activities = $stmt->fetchAll();

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $uuid = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM kegiatan WHERE uuid = ?");
    $stmt->execute([$uuid]);
    $edit_data = $stmt->fetch();
}

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
            <?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Kegiatan
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <input type="hidden" name="action" value="save">
            <?php if ($edit_data): ?>
                <input type="hidden" name="uuid" value="<?php echo $edit_data['uuid']; ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                    <input type="text"
                        name="nama"
                        class="form-control"
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama']) : ''; ?>"
                        placeholder="Contoh: Workshop Machine Learning"
                        required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori_kegiatan" class="form-select select-enhanced" required>
                        <option value="">Pilih Kategori</option>
                        <option value="workshop" <?php echo ($edit_data && $edit_data['kategori_kegiatan'] == 'workshop') ? 'selected' : ''; ?>>Workshop</option>
                        <option value="seminar" <?php echo ($edit_data && $edit_data['kategori_kegiatan'] == 'seminar') ? 'selected' : ''; ?>>Seminar</option>
                        <option value="pengabdian" <?php echo ($edit_data && $edit_data['kategori_kegiatan'] == 'pengabdian') ? 'selected' : ''; ?>>Pengabdian</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date"
                        name="tanggal"
                        class="form-control"
                        value="<?php echo $edit_data ? $edit_data['tanggal'] : date('Y-m-d'); ?>"
                        required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Pemateri/Pembicara</label>
                    <input type="text"
                        name="pemateri"
                        class="form-control"
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['pemateri']) : ''; ?>"
                        placeholder="Nama pemateri">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Deskripsi Singkat <span class="text-danger">*</span></label>
                    <textarea name="deskripsi_singkat"
                        class="form-control"
                        rows="3"
                        placeholder="Deskripsi singkat kegiatan..."
                        required><?php echo $edit_data ? htmlspecialchars($edit_data['deskripsi_singkat']) : ''; ?></textarea>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Simpan
                </button>
                <?php if ($edit_data): ?>
                    <a href="manage_activities.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Daftar Kegiatan -->
<div class="card shadow-sm border-0 animate__animated animate__fadeInUp">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Kegiatan
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($activities)): ?>
            <div class="card shadow-sm border-0 text-center animate__animated animate__fadeInUp">
                <div class="card-body py-5">
                    <i class="bi bi-emoji-frown text-info" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">Belum ada kegiatan</h5>
                    <p class="text-secondary small">Yuk tambahkan kegiatan baru untuk ditampilkan di sini!</p>
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
                                <th>Tanggal</th>
                                <th>Nama Kegiatan</th>
                                <th>Kategori</th>
                                <th>Pemateri</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activities as $index => $activity): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected[]" value="<?= $activity['uuid']; ?>" class="rowCheckbox">
                                    </td>
                                    <td><?= $index + 1; ?></td>
                                    <td><?= date('d/m/Y', strtotime($activity['tanggal'])); ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($activity['nama']); ?></strong><br>
                                        <small class="text-muted">
                                            <?= substr(htmlspecialchars($activity['deskripsi_singkat']), 0, 60) . '...'; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php
                                                                echo $activity['kategori_kegiatan'] == 'workshop' ? 'primary' : ($activity['kategori_kegiatan'] == 'seminar' ? 'success' : 'info');
                                                                ?>">
                                            <?= ucfirst($activity['kategori_kegiatan']); ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($activity['pemateri']); ?></td>
                                    <td>
                                        <a href="?edit=<?= $activity['uuid']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?delete=<?= $activity['uuid']; ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete();" title="Hapus">
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