<?php
ob_start();
$page_title = 'Kelola Research Page Anggota';
include 'includes/auth.php';
include 'includes/admin_header.php';

$success = '';
$error = '';

//fetch all angoota
if (!isset($_GET['anggota'])) {
    $stmt = $pdo->query("SELECT uuid, nama FROM anggota ORDER BY nama ASC");
    $anggota_list = $stmt->fetchAll();
?>

<div class="card shadow-sm border-0 animate__animated animate__fadeInUp">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-globe me-2"></i>Pilih Anggota untuk Kelola Research Page
        </h5>
    </div>

    <div class="card-body">
        <?php if (empty($anggota_list)): ?>
        <p class="text-center text-muted">Belum ada anggota.</p>
        <?php else: ?>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Anggota</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($anggota_list as $i => $a): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($a['nama']) ?></td>
                        <td>
                            <a href="manage_research_page.php?anggota=<?= $a['uuid'] ?>" class="btn btn-info btn-sm">
                                <i class="bi bi-globe me-1"></i>Kelola
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php endif; ?>
    </div>
</div>

<?php
include 'includes/admin_footer.php';
exit;
}



// Ambil UUID anggota
$anggota_uuid = $_GET['anggota'];

// Ambil data anggota
$stmt = $pdo->prepare("SELECT * FROM anggota WHERE uuid = ?");
$stmt->execute([$anggota_uuid]);
$anggota = $stmt->fetch();

if (!$anggota) {
    echo "<div class='alert alert-danger'>Anggota tidak ditemukan.</div>";
    include 'includes/admin_footer.php';
    exit;
}

// insert
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    $nama_web = trim($_POST['nama_web'] ?? '');
    $link_page = trim($_POST['link_page'] ?? '');

    if ($nama_web && $link_page) {
        try {
            $stmt = $pdo->prepare("INSERT INTO page_penelitian_anggota (uuid, anggota_uuid, nama_web, link_page)
                VALUES (gen_random_uuid(), ?, ?, ?)");
            $stmt->execute([$anggota_uuid, $nama_web, $link_page]);
            $_SESSION['flash_success'] = 'Research Page berhasil ditambahkan!';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Gagal menambah research page: ' . $e->getMessage();
        }
    } else {
        $_SESSION['flash_error'] = 'Nama website dan link harus diisi!';
    }
    header("Location: manage_research_page.php?anggota=$anggota_uuid");
    exit;
}

// update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    $uuid = $_POST['uuid'];
    $nama_web = trim($_POST['nama_web']);
    $link_page = trim($_POST['link_page']);

    try {
        $stmt = $pdo->prepare("
            UPDATE page_penelitian_anggota 
            SET nama_web = ?, link_page = ?
            WHERE uuid = ?
        ");
        $stmt->execute([$nama_web, $link_page, $uuid]);
        $_SESSION['flash_success'] = 'Research Page berhasil diupdate!';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal update: ' . $e->getMessage();
    }

    header("Location: manage_research_page.php?anggota=$anggota_uuid");
    exit;
}

//delete
if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM page_penelitian_anggota WHERE uuid = ?");
        $stmt->execute([$_GET['delete']]);
        $_SESSION['flash_success'] = 'Research Page berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal hapus: ' . $e->getMessage();
    }
    header("Location: manage_research_page.php?anggota=$anggota_uuid");
    exit;
}

//get data web
$stmt = $pdo->prepare("SELECT * FROM page_penelitian_anggota WHERE anggota_uuid = ? ORDER BY created_at DESC");
$stmt->execute([$anggota_uuid]);
$research_list = $stmt->fetchAll();

if (isset($_SESSION['flash_success'])) {
    $success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}

?>


<!-- manage research -->
<div class="card mb-4 shadow-sm border-0 animate__animated animate__fadeInUp">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-globe me-2"></i>Research Page - <?= htmlspecialchars($anggota['nama']) ?>
        </h5>
        <a href="manage_research_page.php" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="card-body">

        <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <!-- FORM TAMBAH -->
        <form method="POST">
            <input type="hidden" name="action" value="save">

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nama Website</label>
                    <input type="text" name="nama_web" class="form-control" placeholder="Contoh: Google Scholar"
                        required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Link Page</label>
                    <input type="text" name="link_page" class="form-control" placeholder="https://..." required>
                </div>

                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-1"></i>Tambah
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<!-- LIST Research -->
<div class="card shadow-sm border-0 animate__animated animate__fadeInUp">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold"><i class="bi bi-list-ul me-2"></i>Daftar Research Page</h5>
    </div>

    <div class="card-body">

        <?php if (empty($research_list)): ?>
        <p class="text-center text-muted py-4">Belum ada research page.</p>

        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Web</th>
                        <th>Link</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($research_list as $i => $r): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>

                        <td><?= htmlspecialchars($r['nama_web']) ?></td>

                        <td>
                            <a href="<?= htmlspecialchars($r['link_page']) ?>" target="_blank" class="text-primary">
                                Visit
                            </a>
                        </td>

                        <td>
                            <!-- EDIT BUTTON -->
                            <button class="btn btn-warning btn-sm"
                                onclick="editResearch(<?= htmlspecialchars(json_encode($r)) ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <!-- DELETE -->
                            <a href="?anggota=<?= $anggota_uuid ?>&delete=<?= $r['uuid'] ?>"
                                onclick="return confirm('Hapus research page ini?')" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>

        <?php endif; ?>
    </div>
</div>


<!-- MODAL EDIT -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="uuid" id="edit_uuid">

            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Research Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Website</label>
                    <input type="text" name="nama_web" id="edit_nama_web" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Link Page</label>
                    <input type="text" name="link_page" id="edit_link_page" class="form-control" required>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editResearch(data) {
    document.getElementById('edit_uuid').value = data.uuid;
    document.getElementById('edit_nama_web').value = data.nama_web;
    document.getElementById('edit_link_page').value = data.link_page;

    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php include 'includes/admin_footer.php'; ?>