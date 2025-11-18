<?php
ob_start();
$page_title = 'Kelola Profile Laboratorium';
include 'includes/auth.php';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Get current profile
$stmt = $pdo->query("SELECT * FROM profile LIMIT 1");
$profile = $stmt->fetch();

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
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
        $_SESSION['flash_success'] = 'Profile laboratorium berhasil disimpan!';

        // Refresh data
        $stmt = $pdo->query("SELECT * FROM profile LIMIT 1");
        $profile = $stmt->fetch();
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    } finally {
        header("Location: manage_profile.php");
        exit;
    }
}

// Get current footer profile
$stmt_footer = $pdo->query("SELECT * FROM footer_info LIMIT 1");
$footer = $stmt_footer->fetch();

// Handle Footer Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_footer') {
    $org_name = clean_input($_POST['judul_footer'] ?? '');
    $description = clean_input($_POST['description_text'] ?? '');
    $reserved_text = clean_input($_POST['reserved_text'] ?? '');
    $powered_by = clean_input($_POST['powered_by'] ?? '');
    $link_powered_by = clean_input($_POST['link_powered_by'] ?? '');

    try {
        if ($footer) {
            $stmt_footer = $pdo->prepare("UPDATE footer_info SET org_name = ?, description = ?, reserved_text = ?, powered_by = ?, link_powered_by = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            $stmt_footer->execute([$org_name, $description, $reserved_text, $powered_by, $link_powered_by, $footer['id']]);
        } else {
            $stmt_footer = $pdo->prepare("INSERT INTO footer_info 
                (org_name, description, reserved_text, powered_by, link_powered_by) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt_footer->execute([$org_name, $description, $reserved_text, $powered_by, $link_powered_by]);
        }
        $_SESSION['flash_success'] = 'Footer laboratorium berhasil disimpan!';

        // Refresh data
        $stmt_footer = $pdo->query("SELECT * FROM footer_info LIMIT 1");
        $footer = $stmt_footer->fetch();
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    } finally {
        header("Location: manage_profile.php");
        exit;
    }
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

<!-- Content Profile -->
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
                    <input type="hidden" name="action" value="update_profile">
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

<!-- Content Footer -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-2"></i>Footer Laboratorium
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="update_footer">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-building me-2"></i>Judul Footer <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                            name="judul_footer"
                            class="form-control"
                            required
                            value="<?= htmlspecialchars($footer['org_name'] ?? '') ?>"
                            placeholder="Contoh: AI LAB POLINEMA">
                        <small class="text-muted">Nama organisasi/lab yang akan ditampilkan di footer</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-file-text me-2"></i>Deskripsi Footer <span class="text-danger">*</span>
                        </label>
                        <textarea name="description_text"
                            class="form-control"
                            rows="5"
                            required
                            placeholder="Masukkan deskripsi singkat tentang laboratorium..."><?php echo $footer ? htmlspecialchars($footer['description']) : ''; ?></textarea>
                        <small class="text-muted">Deskripsi singkat tentang laboratorium (max 200 karakter direkomendasikan)</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-c-circle me-2"></i>Reserved Text <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                            name="reserved_text"
                            class="form-control"
                            required
                            value="<?php echo $footer ? htmlspecialchars($footer['reserved_text']) : ''; ?>"
                            placeholder="Contoh: AI LAB POLINEMA - All rights reserved.">
                        <small class="text-muted">Teks copyright/reserved di bagian bawah footer</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-lightning-charge me-2"></i>Powered By <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                            name="powered_by"
                            required
                            class="form-control"
                            value="<?= htmlspecialchars($footer['powered_by'] ?? '') ?>"
                            placeholder="Contoh: Polinema">
                        <small class="text-muted">Nama institusi/organisasi yang membuat website</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-link-45deg me-2"></i>Link Powered By
                        </label>
                        <input type="url" 
                            name="link_powered_by"
                            class="form-control"
                            value="<?= htmlspecialchars($footer['link_powered_by'] ?? '') ?>"
                            placeholder="https://www.polinema.ac.id">
                        <small class="text-muted">URL website institusi (opsional, kosongkan jika tidak ada)</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save me-2"></i>Simpan Footer
                        </button>
                        <a href="../public/index.php" target="_blank" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-eye me-2"></i>Preview Footer
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Preview Section Profile -->
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
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="p-3 border rounded">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-eye-fill me-2"></i>Visi
                                </h6>
                                <p class="text-muted text-center">
                                    <?php echo nl2br(htmlspecialchars($profile['visi'])); ?>
                                </p>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 border rounded">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-bullseye me-2"></i>Misi
                                </h6>
                                <?php 
                                $misi_items = preg_split('/\r\n|\r|\n/', trim($profile['misi']));
                                foreach ($misi_items as $item) {
                                    if (trim($item) !== '') {
                                    echo '
                                    <li class="d-flex align-items-start">
                                                <i class="bi bi-check-circle-fill text-primary me-2 mt-1"></i>
                                        <p class="text-muted">' . nl2br(htmlspecialchars($item)) . '</p>
                                    </li>';
                                    }
                            }?>
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

<!-- Preview Section Footer -->
<?php if ($footer): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-eye me-2"></i>Preview Footer
                    </h5>
                </div>
                <div class="card-body p-0">
                    <!-- Footer Preview with Full Styling -->
                    <footer class="bg-dark text-white py-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
                        <div class="container">
                            <div class="row">
                                <!-- About Section -->
                                <div class="col-lg-4 mb-3">
                                    <h6 class="text-uppercase mb-3" style="position: relative; padding-bottom: 12px; font-weight: 600;">
                                        <i class="bi bi-cpu-fill me-2" style="color: #00d4ff;"></i>
                                        <?php echo htmlspecialchars($footer['org_name']); ?>
                                        <span style="content: ''; position: absolute; bottom: 0; left: 0; width: 50px; height: 3px; background: linear-gradient(90deg, #007bff, #00d4ff); border-radius: 2px; display: block;"></span>
                                    </h6>
                                    <p class="text-light small">
                                        <?php echo nl2br(htmlspecialchars($footer['description'])); ?>
                                    </p>
                                </div>

                                <!-- Quick Links -->
                                <div class="col-lg-4 mb-3">
                                    <h6 class="text-uppercase mb-3" style="position: relative; padding-bottom: 12px; font-weight: 600;">
                                        Quick Links
                                        <span style="content: ''; position: absolute; bottom: 0; left: 0; width: 50px; height: 3px; background: linear-gradient(90deg, #007bff, #00d4ff); border-radius: 2px; display: block;"></span>
                                    </h6>
                                    <ul class="list-unstyled small">
                                        <li class="mb-2">
                                            <a href="#" class="text-light text-decoration-none">
                                                <i class="bi bi-chevron-right"></i> Home
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a href="#" class="text-light text-decoration-none">
                                                <i class="bi bi-chevron-right"></i> About Us
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a href="#" class="text-light text-decoration-none">
                                                <i class="bi bi-chevron-right"></i> Research
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a href="#" class="text-light text-decoration-none">
                                                <i class="bi bi-chevron-right"></i> Publications
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a href="#" class="text-light text-decoration-none">
                                                <i class="bi bi-chevron-right"></i> Contact
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Contact & Social Media -->
                                <div class="col-lg-4 mb-3">
                                    <h6 class="text-uppercase mb-3" style="position: relative; padding-bottom: 12px; font-weight: 600;">
                                        Connect With Us
                                        <span style="content: ''; position: absolute; bottom: 0; left: 0; width: 50px; height: 3px; background: linear-gradient(90deg, #007bff, #00d4ff); border-radius: 2px; display: block;"></span>
                                    </h6>
                                    <p class="text-light small">
                                        <i class="bi bi-geo-alt-fill me-2" style="color: #00d4ff;"></i>
                                        Politeknik Negeri Malang<br>
                                        <span class="ms-4">Jl. Soekarno Hatta No.9, Malang</span>
                                    </p>
                                    <p class="text-light small">
                                        <i class="bi bi-envelope-fill me-2" style="color: #00d4ff;"></i>
                                        ailab@polinema.ac.id
                                    </p>
                                    <div class="d-flex flex-wrap mt-3">
                                        <a href="#" class="btn btn-outline-light btn-sm me-2 mb-2" style="border-radius: 8px;">
                                            <i class="bi bi-facebook"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-light btn-sm me-2 mb-2" style="border-radius: 8px;">
                                            <i class="bi bi-twitter"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-light btn-sm me-2 mb-2" style="border-radius: 8px;">
                                            <i class="bi bi-instagram"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <hr class="bg-light" style="opacity: 0.1; margin: 1.5rem 0;">

                            <div class="row">
                                <div class="col-md-6 text-center text-md-start">
                                    <p class="mb-0 small">&copy; 2025
                                        <?php echo htmlspecialchars($footer['reserved_text']); ?>
                                    </p>
                                </div>
                                <div class="col-md-6 text-center text-md-end">
                                    <p class="mb-0 small">Powered by
                                        <?php if (!empty($footer['link_powered_by'])): ?>
                                            <a href="<?php echo htmlspecialchars($footer['link_powered_by']); ?>" 
                                                class="text-light text-decoration-none" 
                                                target="_blank"
                                                style="font-weight: 500;">
                                                    <?php echo htmlspecialchars($footer['powered_by']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span style="font-weight: 500;">
                                                <?php echo htmlspecialchars($footer['powered_by']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/admin_footer.php'; ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const successMessage = "<?= addslashes($success ?? '') ?>";
    const errorMessage = "<?= addslashes($error ?? '') ?>";

    if (successMessage) showSuccess(successMessage);
    if (errorMessage) showError(errorMessage);
});
</script>