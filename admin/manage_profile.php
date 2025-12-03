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
    $visi = $_POST['visi']; // Quill mengirim HTML, jadi tidak perlu clean_input yang strip tags
    $misi = $_POST['misi'];
    $sejarah = $_POST['sejarah'];

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
    $description = $_POST['description_text'] ?? ''; // Quill HTML
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

<style>
    /* Styling untuk Quill Editor */
    .ql-container {
        font-family: inherit;
        font-size: 1rem;
    }

    .ql-editor {
        min-height: 150px;
        max-height: 400px;
        overflow-y: auto;
    }

    .ql-editor.ql-blank::before {
        font-style: normal;
        color: #6c757d;
    }

    /* Ukuran berbeda untuk setiap editor */
    #editor-visi .ql-editor {
        min-height: 120px;
    }

    #editor-misi .ql-editor {
        min-height: 200px;
    }

    #editor-sejarah .ql-editor {
        min-height: 250px;
    }

    #editor-description .ql-editor {
        min-height: 150px;
    }
</style>

<!-- Content Profile -->
<div class="row animate__animated animate__fadeInUp">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-building me-2"></i>Profile Laboratorium
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="profileForm">
                    <input type="hidden" name="action" value="update_profile">

                    <!-- Hidden inputs untuk menyimpan konten Quill -->
                    <input type="hidden" name="visi" id="visi-input">
                    <input type="hidden" name="misi" id="misi-input">
                    <input type="hidden" name="sejarah" id="sejarah-input">

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-eye me-2"></i>Visi <span class="text-danger">*</span>
                        </label>
                        <div id="editor-visi"></div>
                        <small class="text-muted">Visi laboratorium yang ingin dicapai</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-bullseye me-2"></i>Misi <span class="text-danger">*</span>
                        </label>
                        <div id="editor-misi"></div>
                        <small class="text-muted">Misi atau langkah-langkah untuk mencapai visi</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-clock-history me-2"></i>Sejarah Laboratorium <span class="text-danger">*</span>
                        </label>
                        <div id="editor-sejarah"></div>
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
<div class="row mt-4 animate__animated animate__fadeInUp">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-2"></i>Footer Laboratorium
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="footerForm">
                    <input type="hidden" name="action" value="update_footer">
                    <input type="hidden" name="description_text" id="description-input">

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
                        <div id="editor-description"></div>
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

<?php include 'includes/admin_footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const successMessage = "<?= addslashes($success ?? '') ?>";
        const errorMessage = "<?= addslashes($error ?? '') ?>";

        if (successMessage) showSuccess(successMessage);
        if (errorMessage) showError(errorMessage);

        // Konfigurasi toolbar Quill
        const toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{
                'header': 1
            }, {
                'header': 2
            }],
            [{
                'list': 'ordered'
            }, {
                'list': 'bullet'
            }],
            [{
                'script': 'sub'
            }, {
                'script': 'super'
            }],
            [{
                'indent': '-1'
            }, {
                'indent': '+1'
            }],
            [{
                'direction': 'rtl'
            }],
            [{
                'size': ['small', false, 'large', 'huge']
            }],
            [{
                'header': [1, 2, 3, 4, 5, 6, false]
            }],
            [{
                'color': []
            }, {
                'background': []
            }],
            [{
                'font': []
            }],
            [{
                'align': []
            }],
            ['link', 'image'],
            ['clean']
        ];

        // Inisialisasi Quill untuk Visi
        const quillVisi = new Quill('#editor-visi', {
            theme: 'snow',
            modules: {
                toolbar: toolbarOptions
            },
            placeholder: 'Masukkan visi laboratorium...'
        });

        // Inisialisasi Quill untuk Misi
        const quillMisi = new Quill('#editor-misi', {
            theme: 'snow',
            modules: {
                toolbar: toolbarOptions
            },
            placeholder: 'Masukkan misi laboratorium...'
        });

        // Inisialisasi Quill untuk Sejarah
        const quillSejarah = new Quill('#editor-sejarah', {
            theme: 'snow',
            modules: {
                toolbar: toolbarOptions
            },
            placeholder: 'Masukkan sejarah laboratorium...'
        });

        // Inisialisasi Quill untuk Deskripsi Footer
        const quillDescription = new Quill('#editor-description', {
            theme: 'snow',
            modules: {
                toolbar: toolbarOptions
            },
            placeholder: 'Masukkan deskripsi singkat tentang laboratorium...'
        });

        // Load konten dari database
        <?php if ($profile): ?>
            quillVisi.root.innerHTML = <?= json_encode($profile['visi']) ?>;
            quillMisi.root.innerHTML = <?= json_encode($profile['misi']) ?>;
            quillSejarah.root.innerHTML = <?= json_encode($profile['sejarah']) ?>;
        <?php endif; ?>

        <?php if ($footer): ?>
            quillDescription.root.innerHTML = <?= json_encode($footer['description']) ?>;
        <?php endif; ?>

        // Handle submit Profile Form
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            // Set nilai hidden input dengan konten HTML dari Quill
            document.getElementById('visi-input').value = quillVisi.root.innerHTML;
            document.getElementById('misi-input').value = quillMisi.root.innerHTML;
            document.getElementById('sejarah-input').value = quillSejarah.root.innerHTML;

            // Validasi tidak boleh kosong
            if (quillVisi.getText().trim().length === 0) {
                e.preventDefault();
                showError('Visi tidak boleh kosong!');
                return false;
            }
            if (quillMisi.getText().trim().length === 0) {
                e.preventDefault();
                showError('Misi tidak boleh kosong!');
                return false;
            }
            if (quillSejarah.getText().trim().length === 0) {
                e.preventDefault();
                showError('Sejarah tidak boleh kosong!');
                return false;
            }
        });

        // Handle submit Footer Form
        document.getElementById('footerForm').addEventListener('submit', function(e) {
            document.getElementById('description-input').value = quillDescription.root.innerHTML;

            if (quillDescription.getText().trim().length === 0) {
                e.preventDefault();
                showError('Deskripsi footer tidak boleh kosong!');
                return false;
            }
        });
    });
</script>