<?php
$page_title = 'Kelola Kegiatan';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $uuid = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM kegiatan WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $success = 'Kegiatan berhasil dihapus!';
    } catch (PDOException $e) {
        $error = 'Gagal menghapus kegiatan: ' . $e->getMessage();
    }
}

// Handle Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = clean_input($_POST['nama']);
    $tanggal = clean_input($_POST['tanggal']);
    $pemateri = clean_input($_POST['pemateri']);
    $kategori_kegiatan = clean_input($_POST['kategori_kegiatan']);
    $deskripsi_singkat = clean_input($_POST['deskripsi_singkat']);

    try {
        if (isset($_POST['uuid']) && !empty($_POST['uuid'])) {
            // Update
            $uuid = $_POST['uuid'];
            $stmt = $pdo->prepare("UPDATE kegiatan SET nama = ?, tanggal = ?, pemateri = ?, kategori_kegiatan = ?, deskripsi_singkat = ?, updated_at = CURRENT_TIMESTAMP WHERE uuid = ?");
            $stmt->execute([$nama, $tanggal, $pemateri, $kategori_kegiatan, $deskripsi_singkat, $uuid]);
            $success = 'Kegiatan berhasil diupdate!';
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO kegiatan (nama, tanggal, pemateri, kategori_kegiatan, deskripsi_singkat) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $tanggal, $pemateri, $kategori_kegiatan, $deskripsi_singkat]);
            $success = 'Kegiatan berhasil ditambahkan!';
        }
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
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
                    <?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Kegiatan
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="uuid" value="<?php echo $edit_data['uuid']; ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                        <input type="text"
                            name="nama"
                            class="form-control"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama']) : ''; ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori_kegiatan" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            <option value="workshop" <?php echo ($edit_data && $edit_data['kategori_kegiatan'] == 'workshop') ? 'selected' : ''; ?>>Workshop</option>
                            <option value="seminar" <?php echo ($edit_data && $edit_data['kategori_kegiatan'] == 'seminar') ? 'selected' : ''; ?>>Seminar</option>
                            <option value="pengabdian" <?php echo ($edit_data && $edit_data['kategori_kegiatan'] == 'pengabdian') ? 'selected' : ''; ?>>Pengabdian</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date"
                            name="tanggal"
                            class="form-control"
                            value="<?php echo $edit_data ? $edit_data['tanggal'] : date('Y-m-d'); ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pemateri/Pembicara</label>
                        <input type="text"
                            name="pemateri"
                            class="form-control"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['pemateri']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi Singkat <span class="text-danger">*</span></label>
                        <textarea name="deskripsi_singkat"
                            class="form-control"
                            rows="5"
                            required><?php echo $edit_data ? htmlspecialchars($edit_data['deskripsi_singkat']) : ''; ?></textarea>
                    </div>

                    <div class="d-grid gap-2">
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
    </div>

    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-list-ul me-2"></i>Daftar Kegiatan
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Kegiatan</th>
                                <th>Kategori</th>
                                <th>Pemateri</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activities as $activity): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($activity['tanggal'])); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($activity['nama']); ?></strong><br>
                                        <small class="text-muted">
                                            <?php echo substr(htmlspecialchars($activity['deskripsi_singkat']), 0, 60) . '...'; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php
                                                                echo $activity['kategori_kegiatan'] == 'workshop' ? 'primary' : ($activity['kategori_kegiatan'] == 'seminar' ? 'success' : 'info');
                                                                ?>">
                                            <?php echo ucfirst($activity['kategori_kegiatan']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($activity['pemateri']); ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $activity['uuid']; ?>"
                                            class="btn btn-sm btn-warning"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?delete=<?php echo $activity['uuid']; ?>"
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

<?php include 'includes/admin_footer.php'; ?>