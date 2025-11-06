<?php
$page_title = 'Kelola Publikasi';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $uuid = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM publikasi WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $success = 'Publikasi berhasil dihapus!';
    } catch (PDOException $e) {
        $error = 'Gagal menghapus publikasi: ' . $e->getMessage();
    }
}

// Handle Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = clean_input($_POST['judul']);
    $tahun = clean_input($_POST['tahun']);
    $penulis_id = !empty($_POST['penulis_id']) ? $_POST['penulis_id'] : null;
    $tautan = clean_input($_POST['tautan']);
    $kategori = clean_input($_POST['kategori']);

    try {
        if (isset($_POST['uuid']) && !empty($_POST['uuid'])) {
            // Update
            $uuid = $_POST['uuid'];
            $stmt = $pdo->prepare("UPDATE publikasi SET judul = ?, tahun = ?, penulis_id = ?, tautan = ?, kategori = ?, updated_at = CURRENT_TIMESTAMP WHERE uuid = ?");
            $stmt->execute([$judul, $tahun, $penulis_id, $tautan, $kategori, $uuid]);
            $success = 'Publikasi berhasil diupdate!';
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO publikasi (judul, tahun, penulis_id, tautan, kategori) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$judul, $tahun, $penulis_id, $tautan, $kategori]);
            $success = 'Publikasi berhasil ditambahkan!';
        }
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// Get all publications with author info
$stmt = $pdo->query("
    SELECT p.*, a.nama as penulis_nama 
    FROM publikasi p 
    LEFT JOIN anggota a ON p.penulis_id = a.uuid 
    ORDER BY p.tahun DESC, p.judul ASC
");
$publications = $stmt->fetchAll();

// Get all members for dropdown
$stmt_members = $pdo->query("SELECT uuid, nama FROM anggota ORDER BY nama");
$members = $stmt_members->fetchAll();

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $uuid = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM publikasi WHERE uuid = ?");
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
                    <?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Publikasi
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="uuid" value="<?php echo $edit_data['uuid']; ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Judul Publikasi <span class="text-danger">*</span></label>
                        <input type="text"
                            name="judul"
                            class="form-control"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['judul']) : ''; ?>"
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
                        <label class="form-label">Penulis</label>
                        <select name="penulis_id" class="form-select">
                            <option value="">Pilih Penulis</option>
                            <?php foreach ($members as $member): ?>
                                <option value="<?php echo $member['uuid']; ?>"
                                    <?php echo ($edit_data && $edit_data['penulis_id'] == $member['uuid']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($member['nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Opsional - Pilih dari daftar anggota</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <input type="text"
                            name="kategori"
                            class="form-control"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['kategori']) : ''; ?>"
                            placeholder="Contoh: Journal Paper, Conference Paper"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Link/URL Publikasi</label>
                        <input type="url"
                            name="tautan"
                            class="form-control"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['tautan']) : ''; ?>"
                            placeholder="https://...">
                        <small class="text-muted">Link ke paper/journal online (DOI, IEEE, dll)</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="manage_publications.php" class="btn btn-secondary">
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
                    <i class="bi bi-list-ul me-2"></i>Daftar Publikasi
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>Tahun</th>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Kategori</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($publications as $pub): ?>
                                <tr>
                                    <td><strong><?php echo $pub['tahun']; ?></strong></td>
                                    <td>
                                        <?php echo htmlspecialchars($pub['judul']); ?>
                                        <?php if ($pub['tautan']): ?>
                                            <br>
                                            <a href="<?php echo htmlspecialchars($pub['tautan']); ?>"
                                                target="_blank"
                                                class="small text-primary">
                                                <i class="bi bi-link-45deg"></i>View Paper
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($pub['penulis_nama']): ?>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($pub['penulis_nama']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($pub['kategori']); ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $pub['uuid']; ?>"
                                            class="btn btn-sm btn-warning"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?delete=<?php echo $pub['uuid']; ?>"
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