<?php
ob_start();
$page_title = 'Kelola Email';
require_once '../config/db.php';
include 'includes/admin_header.php';

// POST request: insert atau update email
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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
?>

<div class="container mt-4">

    <!-- Flash Message -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['flash_success'];
                                            unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['flash_error'];
                                        unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <!-- FORM TAMBAH EMAIL -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
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
                        <label class="form-label">Email</label>
                        <input type="email" name="mail_to" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="mail_to_name" class="form-control">
                    </div>

                    <div class="col-12 mt-2">
                        <button class="btn btn-primary px-4">Tambah</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <!-- LIST EMAIL -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-people me-2"></i>Daftar Email
                </h5>
                <small class="text-muted">Email aktif dan daftar email lain yang tersedia</small>
            </div>

            <div class="card-body">

                <?php if (count($email_settings) > 0): ?>
                    <div class="table-responsive mt-2">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
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
                                                data-bs-target="#editModal<?= $email['uuid'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <a href="?delete=<?= $email['uuid'] ?>" class="btn btn-sm btn-danger me-1"
                                                onclick="return confirm('Hapus email ini?');">
                                                <i class="bi bi-trash"></i>
                                            </a>

                                            <?php if ($index !== 0): ?>
                                                <a href="?set_active=<?= $email['uuid'] ?>" class="btn btn-sm btn-primary">
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
                                                    <h5 class="modal-title">Edit Email</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">

                                                    <input type="hidden" name="uuid" value="<?= $email['uuid'] ?>">
                                                    <input type="hidden" name="action" value="save">

                                                    <div class="mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="mail_to" class="form-control"
                                                            value="<?= htmlspecialchars($email['mail_to']) ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Pengirim</label>
                                                        <input type="text" name="mail_to_name" class="form-control"
                                                            value="<?= htmlspecialchars($email['mail_to_name']) ?>">
                                                    </div>

                                                </div>

                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Simpan</button>
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
                    </div>

                <?php else: ?>
                    <p class="mt-3 text-muted">
                        <i class="bi bi-inbox me-1"></i>Belum ada email yang disimpan.
                    </p>
                <?php endif; ?>

            </div>
        </div>
    </div>


    <!-- CATATAN -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-sticky me-2"></i>Catatan
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-0">
                    Pesan di bawah adalah pesan yang dikirim melalui form Contact Us.
                    Untuk membalas, gunakan email admin secara manual.
                </p>
            </div>
        </div>
    </div>

    <!-- TABEL PESAN -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-envelope-paper me-2"></i>Inbox Contact Us
                </h5>
                <small class="text-muted">Semua pesan masuk</small>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
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
                            <?php if ($emails): ?>
                                <?php foreach ($emails as $index => $email): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>

                                        <!-- Nama (dipersempit + truncate) -->
                                        <td class="fw-semibold text-truncate" style="max-width: 130px;">
                                            <?= htmlspecialchars($email['nama']) ?>
                                        </td>

                                        <!-- Email pengirim (dipersempit + truncate) -->
                                        <td class="text-truncate" style="max-width: 130px;">
                                            <a href="mailto:<?= htmlspecialchars($email['email']) ?>" class="text-primary">
                                                <?= htmlspecialchars($email['email']) ?>
                                            </a>
                                        </td>



                                        <!-- Email lab (dipersempit + truncate) -->
                                        <td class="text-truncate" style="max-width: 130px;">
                                            <a href="mailto:<?= htmlspecialchars($email['mail_to']) ?>" class="text-primary">
                                                <?= htmlspecialchars($email['mail_to'] ?? '') ?>
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
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox me-1"></i>Belum ada pesan masuk
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- INFORMASI INBOX -->
    <div class="col-12 mb-4">
        <div class="card border-primary shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-2 text-primary"></i>Informasi Inbox
                </h5>
                <small class="text-muted">Ringkasan pesan masuk</small>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Total Pesan:</strong> <?= count($emails) ?></p>
                <p class="text-muted small">Jumlah seluruh pesan yang diterima dari form Contact Us.</p>
                <hr>
                <ul class="small text-muted mb-0">
                    <li>Pesan terbaru berada di urutan paling atas</li>
                    <li>Pesan tidak dapat dihapus pada versi ini</li>
                    <li>Gunakan informasi email untuk follow-up</li>
                </ul>
            </div>
        </div>
    </div>

    <?php
    include 'includes/admin_footer.php';
    ?>