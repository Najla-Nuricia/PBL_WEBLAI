<?php
$page_title = 'Kelola Partnership';
include 'includes/admin_header.php';

$success = '';
$error = '';

// ========== HANDLE DELETE ==========
if (isset($_GET['delete'])) {
    $uuid = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("SELECT logo FROM partnership WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $partner = $stmt->fetch();

        if ($partner && $partner['logo'] && file_exists('../assets/img/' . $partner['logo'])) {
            unlink('../assets/img/' . $partner['logo']);
        }

        $stmt = $pdo->prepare("DELETE FROM partnership WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $success = 'Partnership berhasil dihapus!';
    } catch (PDOException $e) {
        $error = 'Gagal menghapus partnership: ' . $e->getMessage();
    }
}

// ========== HANDLE INSERT / UPDATE ==========
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = clean_input($_POST['nama']);
    $website = clean_input($_POST['website']);
    $logo = null;

    try {
        // Upload file jika ada
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $upload_result = upload_file($_FILES['logo']);
            if ($upload_result['success']) {
                $logo = $upload_result['filename'];
            } else {
                $error = $upload_result['message'];
            }
        }

        if (!$error) {
            if (!empty($_POST['uuid'])) {
                // === UPDATE ===
                $uuid = $_POST['uuid'];

                // Hapus logo lama jika upload baru
                if ($logo) {
                    $stmt = $pdo->prepare("SELECT logo FROM partnership WHERE uuid = ?");
                    $stmt->execute([$uuid]);
                    $old = $stmt->fetch();

                    if ($old && $old['logo'] && file_exists('../assets/img/' . $old['logo'])) {
                        unlink('../assets/img/' . $old['logo']);
                    }
                }

                $query = $logo
                    ? "UPDATE partnership SET nama=?, logo=?, website=?, updated_at=CURRENT_TIMESTAMP WHERE uuid=?"
                    : "UPDATE partnership SET nama=?, website=?, updated_at=CURRENT_TIMESTAMP WHERE uuid=?";

                $params = $logo ? [$nama, $logo, $website, $uuid] : [$nama, $website, $uuid];
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);

                $success = 'Partnership berhasil diupdate!';
            } else {
                // === INSERT ===
                $stmt = $pdo->prepare("INSERT INTO partnership (nama, logo, website) VALUES (?, ?, ?)");
                $stmt->execute([$nama, $logo, $website]);
                $success = 'Partnership berhasil ditambahkan!';
            }
        }
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// ========== GET DATA ==========
$stmt = $pdo->query("SELECT * FROM partnership ORDER BY nama");
$partnerships = $stmt->fetchAll();

$edit_data = null;
if (isset($_GET['edit'])) {
    $uuid = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM partnership WHERE uuid = ?");
    $stmt->execute([$uuid]);
    $edit_data = $stmt->fetch();
}
?>

<!-- ======= ALERT SECTION ======= -->
<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i><?= $success ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>


<div class="row">
    <!-- ======= FORM TAMBAH / EDIT ======= -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-<?= $edit_data ? 'pencil' : 'plus' ?>-circle me-2"></i>
                    <?= $edit_data ? 'Edit' : 'Tambah' ?> Partnership
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="uuid" value="<?= $edit_data['uuid'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Nama Mitra <span class="text-danger">*</span></label>
                        <input type="text"
                            name="nama"
                            class="form-control"
                            value="<?= $edit_data ? htmlspecialchars($edit_data['nama']) : '' ?>"
                            placeholder="Contoh: PT Telkom Indonesia"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Website</label>
                        <input type="url"
                            name="website"
                            class="form-control"
                            value="<?= $edit_data ? htmlspecialchars($edit_data['website']) : '' ?>"
                            placeholder="https://...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Logo Mitra</label>
                        <input type="file" name="logo" class="form-control" accept="image/*"
                            onchange="previewImage(this, 'preview')">
                        <small class="text-muted">Max 2MB. Rekomendasi: PNG transparan, 300x150px</small>

                        <div class="mt-2 p-3 bg-light text-center" id="previewContainer"
                            style="<?= $edit_data && $edit_data['logo'] ? '' : 'display:none;' ?>">
                            <img id="preview"
                                src="<?= $edit_data && $edit_data['logo'] ? '../assets/img/' . htmlspecialchars($edit_data['logo']) : '' ?>"
                                class="img-thumbnail"
                                style="max-height: 100px;">
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i> Simpan
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="manage_partnerships.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i> Batal
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ======= DAFTAR PARTNERSHIP ======= -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-list-ul me-2"></i> Daftar Partnership
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($partnerships)): ?>
                    <div class="row g-3">
                        <?php foreach ($partnerships as $partner): ?>
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <?php if ($partner['logo']): ?>
                                                    <img src="../assets/img/<?= htmlspecialchars($partner['logo']) ?>"
                                                        alt="<?= htmlspecialchars($partner['nama']) ?>"
                                                        style="max-height: 60px; max-width: 100px; object-fit: contain;">
                                                <?php else: ?>
                                                    <div class="bg-light p-3 rounded text-center">
                                                        <i class="bi bi-building text-muted fs-2"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold"><?= htmlspecialchars($partner['nama']) ?></h6>
                                                <?php if ($partner['website']): ?>
                                                    <a href="<?= htmlspecialchars($partner['website']) ?>"
                                                        target="_blank"
                                                        class="small text-primary">
                                                        <i class="bi bi-link-45deg"></i> Visit Website
                                                    </a>
                                                <?php endif; ?>
                                            </div>

                                            <div class="flex-shrink-0">
                                                <div class="btn-group-vertical">
                                                    <a href="?edit=<?= $partner['uuid'] ?>"
                                                        class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="?delete=<?= $partner['uuid'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirmDelete();" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        Belum ada partnership. Tambahkan mitra pertama Anda!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const container = document.getElementById('previewContainer');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                container.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function confirmDelete() {
        return confirm("Yakin ingin menghapus partnership ini?");
    }
</script>

<?php include 'includes/admin_footer.php'; ?>