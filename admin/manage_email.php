<?php
ob_start();
$page_title = 'Kelola Email';
require_once '../config/db.php';
include 'includes/admin_header.php';

// POST request: insert atau update email
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['action'] ?? '') === 'save') {

    $uuid      = clean_input($_POST['uuid'] ?? null);
    $mail_to   = $_POST['mail_to'] ?? '';
    $mail_name = $_POST['mail_to_name'] ?? '';

    try {
        if ($uuid) {
            // UPDATE email
            $stmt = $pdo->prepare("
                UPDATE email_settings 
                SET mail_to = :mail_to, mail_to_name = :mail_to_name, updated_at = NOW() 
                WHERE uuid = :uuid
            ");
            $stmt->execute([
                ':mail_to'      => $mail_to,
                ':mail_to_name' => $mail_name,
                ':uuid'         => $uuid
            ]);
            $_SESSION['flash_success'] = 'Email berhasil diperbarui.';
        } else {
            // INSERT email baru
            $stmt = $pdo->prepare("
                INSERT INTO email_settings (uuid, mail_to, mail_to_name)
                VALUES (gen_random_uuid(), :mail_to, :mail_to_name)
            ");
            $stmt->execute([
                ':mail_to'      => $mail_to,
                ':mail_to_name' => $mail_name
            ]);
            $_SESSION['flash_success'] = 'Email baru berhasil ditambahkan.';
        }
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    } finally {
        header("Location: manage_email.php");
        exit;
    }
}


// DELETE email
if (isset($_GET['delete'])) {
    $uuid_delete = $_GET['delete'];

    try {
        $stmt = $pdo->prepare("DELETE FROM email_settings WHERE uuid = ?");
        $stmt->execute([$uuid_delete]);

        $_SESSION['flash_success'] = 'Email berhasil dihapus.';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal menghapus email: ' . $e->getMessage();
    } finally {
        header("Location: manage_email.php");
        exit;
    }
}


// Handle Bulk Delete untuk Email Settings
if (($_POST['action'] ?? '') === 'bulk_delete_email' && !empty($_POST['selected'])) {
    $uuids = $_POST['selected'];

    try {
        // Buat placeholder dinamis sebanyak jumlah UUID
        $placeholders = implode(',', array_fill(0, count($uuids), '?'));
        $query = "DELETE FROM email_settings WHERE uuid IN ($placeholders)";
        $stmt = $pdo->prepare($query);

        // Eksekusi semua UUID
        $stmt->execute($uuids);

        $_SESSION['flash_success'] = count($uuids) . ' email berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal menghapus email: ' . $e->getMessage();
    } finally {
        header('Location: manage_email.php');
        exit();
    }
}


// Handle Bulk Delete untuk Inbox
if (($_POST['action'] ?? '') === 'bulk_delete_inbox' && !empty($_POST['selected_inbox'])) {
    $uuids = $_POST['selected_inbox'];

    try {
        // Buat placeholder dinamis sebanyak jumlah UUID
        $placeholders = implode(',', array_fill(0, count($uuids), '?'));
        $query = "DELETE FROM email_pesan WHERE id IN ($placeholders)";
        $stmt = $pdo->prepare($query);

        // Eksekusi semua UUID
        $stmt->execute($uuids);

        $_SESSION['flash_success'] = count($uuids) . ' pesan berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal menghapus pesan: ' . $e->getMessage();
    } finally {
        header('Location: manage_email.php');
        exit();
    }
}


// SET ACTIVE – berdasarkan updated_at terbaru
if (isset($_GET['set_active'])) {
    $uuid_active = $_GET['set_active'];

    try {
        $stmt = $pdo->prepare("
            UPDATE email_settings
            SET updated_at = NOW()
            WHERE uuid = :uuid
        ");
        $stmt->execute([':uuid' => $uuid_active]);

        $_SESSION['flash_success'] = 'Email berhasil diatur sebagai aktif.';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal mengatur email aktif: ' . $e->getMessage();
    } finally {
        header("Location: manage_email.php");
        exit;
    }
}


// Ambil semua email, urutkan berdasarkan updated_at DESC
try {
    $email_settings = $pdo->query("
        SELECT * FROM email_settings
        ORDER BY updated_at DESC NULLS LAST, created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['flash_error'] = 'Gagal mengambil data email: ' . $e->getMessage();
    $email_settings = [];
}


// Inbox Pagination
$limit  = 10;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try {
    $stmt = $pdo->prepare("
    SELECT ep.*, es.mail_to, es.mail_to_name
    FROM email_pesan ep
    LEFT JOIN email_settings es 
        ON ep.email_setting_uuid = es.uuid
    ORDER BY ep.tanggal_dikirim DESC
    LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_stmt = $pdo->query("SELECT COUNT(*) FROM email_pesan");
    $total_rows = $total_stmt->fetchColumn();
    $total_pages = ceil($total_rows / $limit);
} catch (PDOException $e) {
    $_SESSION['flash_error'] = 'Gagal mengambil inbox: ' . $e->getMessage();
    $emails = [];
    $total_rows = 0;
    $total_pages = 0;
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

<div class="container mt-4">
    <!-- FORM TAMBAH EMAIL -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm animate__animated animate__fadeInUp">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-envelope-plus me-2"></i>Tambah Email Baru
                </h5>
                <small class="text-muted">Tambah email yang digunakan sebagai penerima notifikasi</small>
            </div>

            <div class="card-body">
                <form method="POST" class="row g-3 mt-1">
                    <input type="hidden" name="action" value="save">

                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="mail_to" class="form-control"
                            placeholder="contoh@email.com" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="mail_to_name" class="form-control"
                            placeholder="Nama Penerima" required>
                    </div>

                    <div class="col-12 mt-2">
                        <button class="btn btn-primary px-4">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- LIST EMAIL -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm animate__animated animate__fadeInUp">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-people me-2"></i>Daftar Email
                </h5>
                <small class="text-muted">Email aktif dan daftar email lain yang tersedia</small>
            </div>

            <div class="card-body">
                <?php if (count($email_settings) > 0): ?>
                    <link rel="stylesheet" href="../assets/css/swipejs.css">
                    <div class="swipeable-table" id="swipeTableEmail">
                        <form method="POST" id="bulkDeleteFormEmail" action="">
                            <input type="hidden" name="action" value="bulk_delete_email">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <th width="50">No</th>
                                        <th>Email</th>
                                        <th width="200">Nama Pengirim</th>
                                        <th width="110">Status</th>
                                        <th width="220">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($email_settings as $index => $email): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected[]" value="<?= $email['uuid']; ?>" class="rowCheckbox">
                                            </td>
                                            <td><?= $index + 1 ?></td>

                                            <td class="text-truncate" style="max-width: 260px;">
                                                <?= htmlspecialchars($email['mail_to']) ?>
                                            </td>

                                            <td class="text-truncate">
                                                <?= htmlspecialchars($email['mail_to_name']) ?>
                                            </td>

                                            <td>
                                                <?php if ($index === 0): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                                                    data-bs-target="#editModal<?= $email['uuid'] ?>"
                                                    title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <a href="?delete=<?= $email['uuid'] ?>" class="btn btn-sm btn-danger me-1"
                                                    onclick="return confirmDelete();"
                                                    title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </a>

                                                <?php if ($index !== 0): ?>
                                                    <a href="?set_active=<?= $email['uuid'] ?>" class="btn btn-sm btn-primary"
                                                        title="Set sebagai Active">
                                                        <i class="bi bi-check2-circle"></i> Set Active
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                        <!-- MODAL EDIT -->
                                        <div class="modal fade" id="editModal<?= $email['uuid'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <form method="POST" class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="bi bi-pencil-square me-2"></i>Edit Email
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <input type="hidden" name="uuid" value="<?= $email['uuid'] ?>">
                                                        <input type="hidden" name="action" value="save">

                                                        <div class="mb-3">
                                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                                            <input type="email" name="mail_to" class="form-control"
                                                                value="<?= htmlspecialchars($email['mail_to']) ?>" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Pengirim <span class="text-danger">*</span></label>
                                                            <input type="text" name="mail_to_name" class="form-control"
                                                                value="<?= htmlspecialchars($email['mail_to_name']) ?>" required>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="bi bi-save me-2"></i>Simpan
                                                        </button>
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!-- END MODAL -->

                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div id="bulkAction" class="mt-3 d-none">
                                <button type="button" id="bulkDeleteBtn" class="btn btn-danger">
                                    <i class="bi bi-trash3 me-2"></i>Hapus Email Terpilih
                                </button>
                            </div>
                        </form>
                    </div>

                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ddd;"></i>
                        <p class="mt-3 text-muted">
                            Belum ada email yang disimpan.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <!-- CATATAN -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-info animate__animated animate__fadeInUp">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-sticky me-2 text-info"></i>Catatan
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Pesan di bawah adalah pesan yang dikirim melalui form Contact Us.
                    Untuk membalas, gunakan email admin secara manual.
                </p>
            </div>
        </div>
    </div>

    <!-- TABEL PESAN -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm animate__animated animate__fadeInUp">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-envelope-paper me-2"></i>Inbox Contact Us
                </h5>
                <small class="text-muted">Semua pesan masuk dari form Contact Us</small>
            </div>

            <div class="card-body">
                <?php if ($emails): ?>
                    <link rel="stylesheet" href="../assets/css/swipejs.css">
                    <div class="swipeable-table" id="swipeTableInbox">
                        <form method="POST" id="bulkDeleteFormInbox" action="">
                            <input type="hidden" name="action" value="bulk_delete_inbox">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="selectAllInbox">
                                        </th>
                                        <th width="50">No</th>
                                        <th width="100">Nama</th>
                                        <th width="100">Pengirim</th>
                                        <th width="100">Penerima</th>
                                        <th width="140">Subjek</th>
                                        <th width="450">Pesan</th>
                                        <th width="150">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($emails as $index => $email): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_inbox[]" value="<?= $email['id'] ?>" class="rowCheckboxInbox">
                                            </td>
                                            <td><?= $offset + $index + 1 ?></td>

                                            <!-- Nama (dipersempit + truncate) -->
                                            <td class="fw-semibold text-truncate" style="max-width: 130px;">
                                                <?= htmlspecialchars($email['nama']) ?>
                                            </td>

                                            <!-- Email pengirim (dipersempit + truncate) -->
                                            <td class="text-truncate" style="max-width: 130px;">
                                                <a href="mailto:<?= htmlspecialchars($email['email']) ?>"
                                                    class="text-primary text-decoration-none"
                                                    title="<?= htmlspecialchars($email['email']) ?>">
                                                    <?= htmlspecialchars($email['email']) ?>
                                                </a>
                                            </td>

                                            <!-- Email lab (dipersempit + truncate) -->
                                            <td class="text-truncate" style="max-width: 130px;">
                                                <a href="mailto:<?= htmlspecialchars($email['mail_to']) ?>"
                                                    class="text-primary text-decoration-none"
                                                    title="<?= htmlspecialchars($email['mail_to'] ?? '') ?>">
                                                    <?= htmlspecialchars($email['mail_to'] ?? '-') ?>
                                                </a>
                                            </td>

                                            <td><?= htmlspecialchars($email['subjek']) ?></td>

                                            <!-- Pesan diperlebar -->
                                            <td style="max-width: 450px;">
                                                <span class="text-muted" style="white-space: normal; display: block;">
                                                    <?= nl2br(htmlspecialchars($email['pesan'])) ?>
                                                </span>
                                            </td>

                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d M Y H:i', strtotime($email['tanggal_dikirim'])) ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div id="bulkActionInbox" class="mt-3 d-none">
                                <button type="button" id="bulkDeleteBtnInbox" class="btn btn-danger">
                                    <i class="bi bi-trash3 me-2"></i>Hapus Pesan Terpilih
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav class="mt-3">
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ddd;"></i>
                        <p class="mt-3 text-muted">
                            Belum ada pesan masuk
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/admin_footer.php';
ob_end_flush();
?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const successMessage = "<?= addslashes($success ?? '') ?>";
        const errorMessage = "<?= addslashes($error ?? '') ?>";

        if (successMessage) showSuccess(successMessage);
        if (errorMessage) showError(errorMessage);

        // ===== BULK DELETE INBOX (Custom Handler) =====
        const selectAllInbox = document.getElementById('selectAllInbox');
        const rowCheckboxesInbox = document.querySelectorAll('.rowCheckboxInbox');
        const bulkActionInbox = document.getElementById('bulkActionInbox');
        const bulkDeleteBtnInbox = document.getElementById('bulkDeleteBtnInbox');
        const bulkDeleteFormInbox = document.getElementById('bulkDeleteFormInbox');

        if (selectAllInbox) {
            selectAllInbox.addEventListener('change', function() {
                rowCheckboxesInbox.forEach(cb => cb.checked = this.checked);
                toggleBulkActionInbox();
            });
        }

        rowCheckboxesInbox.forEach(cb => {
            cb.addEventListener('change', toggleBulkActionInbox);
        });

        function toggleBulkActionInbox() {
            const anyChecked = Array.from(rowCheckboxesInbox).some(cb => cb.checked);
            bulkActionInbox.classList.toggle('d-none', !anyChecked);
        }

        if (bulkDeleteBtnInbox) {
            bulkDeleteBtnInbox.addEventListener('click', async function(e) {
                e.preventDefault();

                const selected = Array.from(rowCheckboxesInbox).filter(cb => cb.checked);
                if (selected.length === 0) {
                    showError('Pilih minimal 1 pesan untuk dihapus');
                    return;
                }

                const confirmed = await confirmBulkDelete(selected.length);
                if (confirmed) {
                    bulkDeleteFormInbox.submit();
                }
            });
        }
    });
</script>