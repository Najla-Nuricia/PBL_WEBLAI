<?php
$page_title = 'Kelola Profile Laboratorium';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Get current profile
$stmt = $pdo->query("SELECT * FROM profile LIMIT 1");
$profile = $stmt->fetch();

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $visi = clean_input($_POST['visi']);
    $misi = clean_input($_POST['misi']);
    $sejarah = clean_input($_POST['sejarah']);

    try {
        if ($profile) {
            // Update existing profile
            $stmt = $pdo->prepare("UPDATE profile SET visi = ?, misi = ?, sejarah = ?, updated_at = CURRENT_TIMESTAMP WHERE uuid = ?");
            $stmt->execute([$visi, $misi, $sejarah, $profile['uuid']]);
        } else {
            // Insert new profile
            $stmt = $pdo->prepare("INSERT INTO profile (visi, misi, sejarah) VALUES (?, ?, ?)");
            $stmt->execute([$visi, $misi, $sejarah]);
        }
        $success = 'Profile laboratorium berhasil disimpan!';

        // Refresh data
        $stmt = $pdo->query("SELECT * FROM profile LIMIT 1");
        $profile = $stmt->fetch();
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
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
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-building me-2"></i>Profile Laboratorium
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-eye me-2"></i>Visi <span class="text-danger">*</span>
                        </label>
                        <textarea name="visi"
                            class="form-control"
                            rows="5"
                            required
                            placeholder="Masukkan visi laboratorium..."><?php echo $profile ? htmlspecialchars($profile['visi']) : ''; ?></textarea>
                        <small class="text-muted">Visi laboratorium yang ingin dicapai</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-bullseye me-2"></i>Misi <span class="text-danger">*</span>
                        </label>
                        <textarea name="misi"
                            class="form-control"
                            rows="8"
                            required
                            placeholder="Masukkan misi laboratorium (pisahkan dengan enter untuk poin berbeda)..."><?php echo $profile ? htmlspecialchars($profile['misi']) : ''; ?></textarea>
                        <small class="text-muted">Misi atau langkah-langkah untuk mencapai visi (gunakan enter untuk memisahkan setiap poin)</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-clock-history me-2"></i>Sejarah Laboratorium <span class="text-danger">*</span>
                        </label>
                        <textarea name="sejarah"
                            class="form-control"
                            rows="10"
                            required
                            placeholder="Masukkan sejarah pendirian dan perkembangan laboratorium..."><?php echo $profile ? htmlspecialchars($profile['sejarah']) : ''; ?></textarea>
                        <small class="text-muted">Sejarah pendirian dan perkembangan laboratorium</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save me-2"></i>Simpan Profile
                        </button>
                        <a href="../public/about.php" target="_blank" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-eye me-2"></i>Preview
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Preview Section -->
<?php if ($profile): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-eye me-2"></i>Preview Profile
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-eye-fill me-2"></i>Visi
                                </h6>
                                <p class="text-muted">
                                    <?php echo nl2br(htmlspecialchars($profile['visi'])); ?>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-bullseye me-2"></i>Misi
                                </h6>
                                <p class="text-muted">
                                    <?php echo nl2br(htmlspecialchars($profile['misi'])); ?>
                                </p>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 border rounded">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-clock-history me-2"></i>Sejarah
                                </h6>
                                <p class="text-muted" style="text-align: justify;">
                                    <?php echo nl2br(htmlspecialchars($profile['sejarah'])); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/admin_footer.php'; ?>