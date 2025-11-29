<?php
ob_start();
$page_title = 'Kelola Users';
include 'includes/auth.php';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $uuid = $_GET['delete'];

    // Prevent deleting current user
    if ($uuid == $_SESSION['user_id']) {
        $_SESSION['flash_error'] = 'Anda tidak dapat menghapus user yang sedang digunakan!';
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE uuid = ?");
            $stmt->execute([$uuid]);
            $_SESSION['flash_success'] = 'User berhasil dihapus!';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Gagal menghapus user: ' . $e->getMessage();
        } finally {
            header("Location: manage_users.php");
            exit;
        }
    }
}

// Handle Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (strlen($username) < 4) {
        $_SESSION['flash_error'] = 'Username minimal 4 karakter!';
    } elseif (isset($_POST['uuid']) && empty($_POST['uuid']) && strlen($password) < 6) {
        $_SESSION['flash_error'] = 'Password minimal 6 karakter!';
    } elseif (!empty($password) && $password !== $confirm_password) {
        $_SESSION['flash_error'] = 'Konfirmasi password tidak sesuai!';
    } else {
        try {
            if (isset($_POST['uuid']) && !empty($_POST['uuid'])) {
                // Update
                $uuid = $_POST['uuid'];

                if (!empty($password)) {
                    // Update with new password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ?, updated_at = CURRENT_TIMESTAMP WHERE uuid = ?");
                    $stmt->execute([$username, $hashed_password, $uuid]);
                } else {
                    // Update without changing password
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, updated_at = CURRENT_TIMESTAMP WHERE uuid = ?");
                    $stmt->execute([$username, $uuid]);
                }
                $_SESSION['flash_success'] = 'User berhasil diperbarui!';
            } else {
                // Insert - check if username exists
                $stmt = $pdo->prepare("SELECT uuid FROM users WHERE username = ?");
                $stmt->execute([$username]);
                if ($stmt->fetch()) {
                    $_SESSION['flash_error'] = 'Username sudah digunakan!';
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                    $stmt->execute([$username, $hashed_password]);
                    $_SESSION['flash_success'] = 'User berhasil ditambahkan!';
                }
            }
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        } finally {
            header("Location: manage_users.php");
            exit;
        }
    }
}

// Get all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $uuid = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE uuid = ?");
    $stmt->execute([$uuid]);
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
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-<?php echo $edit_data ? 'pencil' : 'plus'; ?>-circle me-2"></i>
                    <?php echo $edit_data ? 'Edit' : 'Tambah'; ?> User
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="uuid" value="<?php echo $edit_data['uuid']; ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text"
                            name="username"
                            class="form-control"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['username']) : ''; ?>"
                            minlength="4"
                            required>
                        <small class="text-muted">Minimal 4 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Password
                            <?php if (!$edit_data): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>
                        <input type="password"
                            name="password"
                            class="form-control"
                            minlength="6"
                            <?php echo !$edit_data ? 'required' : ''; ?>>
                        <small class="text-muted">
                            <?php if ($edit_data): ?>
                                Kosongkan jika tidak ingin mengubah password
                            <?php else: ?>
                                Minimal 6 karakter
                            <?php endif; ?>
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Konfirmasi Password
                            <?php if (!$edit_data): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>
                        <input type="password"
                            name="confirm_password"
                            class="form-control"
                            minlength="6"
                            <?php echo !$edit_data ? 'required' : ''; ?>>
                    </div>

                    <?php if ($edit_data && $edit_data['uuid'] == $_SESSION['user_id']): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Anda sedang mengedit akun yang sedang digunakan
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="manage_users.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="card mt-3 border-warning">
            <div class="card-header bg-warning bg-opacity-10">
                <h6 class="mb-0 fw-bold text-warning">
                    <i class="bi bi-shield-exclamation me-2"></i>Keamanan
                </h6>
            </div>
            <div class="card-body">
                <ul class="small mb-0">
                    <li>Gunakan password yang kuat dan unik</li>
                    <li>Jangan gunakan password yang sama dengan website lain</li>
                    <li>Ganti password secara berkala (3-6 bulan)</li>
                    <li>Jangan share password ke orang lain</li>
                    <li>Logout setelah selesai menggunakan dashboard</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-list-ul me-2"></i>Daftar Users
                </h5>
            </div>
            <div class="card-body">
                <link rel="stylesheet" href="../assets/css/swipejs.css">
                <div class="swipeable-table" id="swipeTable">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Dibuat</th>
                                <th>Terakhir Update</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                        <?php if ($user['uuid'] == $_SESSION['user_id']): ?>
                                            <span class="badge bg-success ms-2">Anda</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($user['updated_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <a href="?edit=<?php echo $user['uuid']; ?>"
                                            class="btn btn-sm btn-warning"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($user['uuid'] != $_SESSION['user_id']): ?>
                                            <a href="?delete=<?php echo $user['uuid']; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirmDelete('Hapus user ini?');"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled title="Tidak dapat menghapus diri sendiri">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-2"></i>Informasi
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Total Users:</strong> <?php echo count($users); ?></p>
                <p class="mb-0 small text-muted">
                    User yang memiliki akses ke dashboard admin untuk mengelola konten website.
                </p>
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
    });
</script>