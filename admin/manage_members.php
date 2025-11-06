<?php
$page_title = 'Kelola Anggota Tim';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $uuid = $_GET['delete'];
    try {
        // Get image path to delete file
        $stmt = $pdo->prepare("SELECT path_gambar FROM anggota WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $member = $stmt->fetch();

        if ($member && $member['path_gambar'] && file_exists('../assets/img/' . $member['path_gambar'])) {
            unlink('../assets/img/' . $member['path_gambar']);
        }

        $stmt = $pdo->prepare("DELETE FROM anggota WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $success = 'Anggota berhasil dihapus!';
    } catch (PDOException $e) {
        $error = 'Gagal menghapus anggota: ' . $e->getMessage();
    }
}

// Handle Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = clean_input($_POST['nama']);
    $nidn = clean_input($_POST['nidn']);
    $jabatan = clean_input($_POST['jabatan']);
    $status = clean_input($_POST['status']);

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
                    $stmt = $pdo->prepare("SELECT path_gambar FROM anggota WHERE uuid = ?");
                    $stmt->execute([$uuid]);
                    $old = $stmt->fetch();
                    if ($old && $old['path_gambar'] && file_exists('../assets/img/' . $old['path_gambar'])) {
                        unlink('../assets/img/' . $old['path_gambar']);
                    }
                }

                if ($path_gambar) {
                    $stmt = $pdo->prepare("UPDATE anggota SET nama = ?, nidn = ?, jabatan = ?, status = ?, path_gambar = ?, updated_at = CURRENT_TIMESTAMP WHERE uuid = ?");
                    $stmt->execute([$nama, $nidn, $jabatan, $status, $path_gambar, $uuid]);
                } else {
                    $stmt = $pdo->prepare("UPDATE anggota SET nama = ?, nidn = ?, jabatan = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE uuid = ?");
                    $stmt->execute([$nama, $nidn, $jabatan, $status, $uuid]);
                }
                $success = 'Anggota berhasil diupdate!';
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO anggota (nama, nidn, jabatan, status, path_gambar) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nama, $nidn, $jabatan, $status, $path_gambar]);
                $success = 'Anggota berhasil ditambahkan!';
            }
        }
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// Get all members
$stmt = $pdo->query("SELECT * FROM anggota ORDER BY jabatan, nama");
$members = $stmt->fetchAll();

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $uuid = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM anggota WHERE uuid = ?");
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
                    <?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Anggota
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="uuid" value="<?php echo $edit_data['uuid']; ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text"
                            name="nama"
                            class="form-control"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama']) : ''; ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">NIDN</label>
                        <input type="text"
                            name="nidn"
                            class="form-control"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['nidn']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                        <select name="jabatan" class="form-select" required>
                            <option value="">Pilih Jabatan</option>
                            <option value="ketua" <?php echo ($edit_data && $edit_data['jabatan'] == 'ketua') ? 'selected' : ''; ?>>Ketua</option>
                            <option value="anggota" <?php echo ($edit_data && $edit_data['jabatan'] == 'anggota') ? 'selected' : ''; ?>>Anggota</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="">Pilih Status</option>
                            <option value="dosen" <?php echo ($edit_data && $edit_data['status'] == 'dosen') ? 'selected' : ''; ?>>Dosen</option>
                            <option value="mahasiswa" <?php echo ($edit_data && $edit_data['status'] == 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto</label>
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
                            <a href="manage_members.php" class="btn btn-secondary">
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
                    <i class="bi bi-list-ul me-2"></i>Daftar Anggota Tim
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>NIDN</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td>
                                        <?php if ($member['path_gambar']): ?>
                                            <img src="../assets/img/<?php echo htmlspecialchars($member['path_gambar']); ?>"
                                                class="rounded-circle"
                                                width="50"
                                                height="50"
                                                style="object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px;">
                                                <i class="bi bi-person text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($member['nama']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($member['nidn']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $member['jabatan'] == 'ketua' ? 'primary' : 'secondary'; ?>">
                                            <?php echo ucfirst($member['jabatan']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $member['status'] == 'dosen' ? 'success' : 'info'; ?>">
                                            <?php echo ucfirst($member['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?edit=<?php echo $member['uuid']; ?>"
                                            class="btn btn-sm btn-warning"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?delete=<?php echo $member['uuid']; ?>"
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