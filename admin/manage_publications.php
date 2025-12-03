<?php
ob_start();
$page_title = 'Kelola Publikasi';
include 'includes/auth.php';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $uuid = $_GET['delete'];
    try {
        // Delete akan cascade ke anggota_publikasi
        $stmt = $pdo->prepare("DELETE FROM publikasi WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $_SESSION['flash_success'] = 'Publikasi berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal menghapus publikasi: ' . $e->getMessage();
    } finally {
        header("Location: manage_publications.php");
        exit;
    }
}

// Handle Bulk Delete
if (($_POST['action'] ?? '') === 'bulk_delete' && !empty($_POST['selected'])) {
    $uuids = $_POST['selected'];

    try {
        $placeholders = implode(',', array_fill(0, count($uuids), '?'));
        $query = "DELETE FROM publikasi WHERE uuid IN ($placeholders)";
        $stmt = $pdo->prepare($query);
        $stmt->execute($uuids);

        $_SESSION['flash_success'] = count($uuids) . ' Publikasi berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal menghapus publikasi: ' . $e->getMessage();
    } finally {
        header("Location: manage_publications.php");
        exit;
    }
}

// Handle Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['action'] ?? '') === 'save') {
    $judul = clean_input($_POST['judul'] ?? '');
    $tahun = clean_input($_POST['tahun'] ?? '');
    $penulis_ids = $_POST['penulis_id'] ?? []; // Array of UUIDs
    $tautan = clean_input($_POST['tautan'] ?? '');
    $kategori = clean_input($_POST['kategori'] ?? '');

    try {
        $pdo->beginTransaction();

        if (isset($_POST['uuid']) && !empty($_POST['uuid'])) {
            // Update
            $uuid = $_POST['uuid'];
            $stmt = $pdo->prepare("UPDATE publikasi SET judul = ?, tahun = ?, tautan = ?, kategori = ?, updated_at = CURRENT_TIMESTAMP WHERE uuid = ?");
            $stmt->execute([$judul, $tahun, $tautan, $kategori, $uuid]);

            // Delete existing penulis relations
            $stmt = $pdo->prepare("DELETE FROM anggota_publikasi WHERE publikasi_uuid = ?");
            $stmt->execute([$uuid]);
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO publikasi (judul, tahun, tautan, kategori) VALUES (?, ?, ?, ?) RETURNING uuid");
            $stmt->execute([$judul, $tahun, $tautan, $kategori]);
            $uuid = $stmt->fetchColumn();
        }

        // Insert penulis relations
        if (!empty($penulis_ids)) {
            $stmt = $pdo->prepare("INSERT INTO anggota_publikasi (anggota_uuid, publikasi_uuid) VALUES (?, ?)");
            foreach ($penulis_ids as $anggota_uuid) {
                if (!empty($anggota_uuid)) {
                    $stmt->execute([$anggota_uuid, $uuid]);
                }
            }
        }

        $pdo->commit();
        $_SESSION['flash_success'] = isset($_POST['uuid']) ? 'Publikasi berhasil diperbarui!' : 'Publikasi berhasil ditambahkan!';
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_error'] = 'Gagal menyimpan publikasi: ' . $e->getMessage();
    } finally {
        header("Location: manage_publications.php");
        exit;
    }
}

// Get all publications with authors
$stmt = $pdo->query("
    SELECT *
    FROM view_publikasi_penulis
    ORDER BY tahun DESC, judul ASC
");
$publications = $stmt->fetchAll();

// Get all members for dropdown
$stmt_members = $pdo->query("SELECT uuid, nama FROM anggota ORDER BY nama");
$members = $stmt_members->fetchAll();

// Get data for edit
$edit_data = null;
$edit_penulis_ids = [];
if (isset($_GET['edit'])) {
    $uuid = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM publikasi WHERE uuid = ?");
    $stmt->execute([$uuid]);
    $edit_data = $stmt->fetch();

    // Get penulis IDs
    $stmt = $pdo->prepare("SELECT anggota_uuid FROM anggota_publikasi WHERE publikasi_uuid = ?");
    $stmt->execute([$uuid]);
    $edit_penulis_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
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
<div class="card mb-4 shadow-sm border-0 animate__animated animate__fadeInUp" style="animation-delay: 0s;">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-<?php echo $edit_data ? 'pencil' : 'plus'; ?>-circle me-2"></i>
            <?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Publikasi
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <input type="hidden" name="action" value="save">
            <?php if ($edit_data): ?>
            <input type="hidden" name="uuid" value="<?php echo $edit_data['uuid']; ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Judul Publikasi <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control"
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['judul']) : ''; ?>"
                        placeholder="Judul paper/publikasi" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                    <input type="number" name="tahun" class="form-control" min="2000" max="<?php echo date('Y'); ?>"
                        value="<?php echo $edit_data ? $edit_data['tahun'] : date('Y'); ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Penulis/Author (dapat pilih lebih dari 1)</label>
                    <select name="penulis_id[]" class="form-select select-enhanced" multiple>
                        <?php foreach ($members as $member): ?>
                        <option value="<?php echo $member['uuid']; ?>"
                            <?php echo in_array($member['uuid'], $edit_penulis_ids) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($member['nama']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Tekan Ctrl/Cmd untuk pilih lebih dari satu author</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori Publikasi <span class="text-danger">*</span></label>
                    <input type="text" name="kategori" class="form-control"
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['kategori']) : ''; ?>"
                        placeholder="Contoh: Scopus, Sinta 1, IEEE, Conference, dll" required>
                    <small class="text-muted">Ketik kategori publikasi, misalnya: Scopus, Sinta 1-6, IEEE, Springer, Web
                        of Science, Conference, dll</small>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Link/URL Publikasi</label>
                    <input type="url" name="tautan" class="form-control"
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['tautan']) : ''; ?>"
                        placeholder="https://doi.org/... atau https://ieeexplore.ieee.org/...">
                    <small class="text-muted">Link ke paper/journal online (DOI, IEEE, ResearchGate, dll)</small>
                </div>
            </div>

            <div class="d-flex gap-2">
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

<!-- Daftar Publikasi -->
<div class="card shadow-sm border-0 animate__animated animate__fadeInUp" style="animation-delay: 0.04s;">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Publikasi
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($publications)): ?>
        <div class="card shadow-sm border-0 text-center animate__animated animate__fadeInUp">
            <div class="card-body py-5">
                <i class="bi bi-emoji-frown text-info" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">Belum ada Publikasi</h5>
                <p class="text-secondary small">Yuk tambahkan Publikasi baru untuk ditampilkan di sini!</p>
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
                            <th width="80">Tahun</th>
                            <th>Judul</th>
                            <th width="200">Penulis</th>
                            <th width="150">Kategori</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($publications as $index => $pub): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="selected[]" value="<?= $pub['uuid']; ?>"
                                    class="rowCheckbox">
                            </td>
                            <td><?php echo $index + 1; ?></td>
                            <td><strong><?php echo $pub['tahun']; ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($pub['judul']); ?>
                                <?php if ($pub['tautan']): ?>
                                <br>
                                <a href="<?php echo htmlspecialchars($pub['tautan']); ?>" target="_blank"
                                    class="small text-primary">
                                    <i class="bi bi-link-45deg"></i>View Paper
                                </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($pub['penulis_nama']): ?>
                                <?php
                                            $penulis = explode(', ', $pub['penulis_nama']);
                                            foreach ($penulis as $author):
                                            ?>
                                <span class="badge bg-secondary mb-1"><?php echo htmlspecialchars($author); ?></span>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($pub['kategori']); ?></span>
                            </td>
                            <td>
                                <a href="?edit=<?php echo $pub['uuid']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="?delete=<?php echo $pub['uuid']; ?>" class="btn btn-sm btn-danger"
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