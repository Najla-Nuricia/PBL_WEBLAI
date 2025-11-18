<?php
require_once '../config/db.php';
$uuid = $_GET['uuid'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

if ($uuid) {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM publikasi WHERE penulis_id = ?");
    $countStmt->execute([$uuid]);
    $total = $countStmt->fetchColumn();
    $totalPages = ceil($total / $limit);

    $stmt = $pdo->prepare("SELECT * FROM publikasi WHERE penulis_id = ? ORDER BY tahun DESC LIMIT $limit OFFSET $offset");
    $stmt->execute([$uuid]);
    $publikasis = $stmt->fetchAll();

    if ($publikasis) {
        echo '<div class="list-group mb-3">';
        foreach ($publikasis as $p) {
            echo '<div class="list-group-item">';
            echo '<h6 class="fw-bold mb-1">
                    <a href="'.htmlspecialchars($p['tautan']).'" target="_blank" class="text-decoration-none item-link">
                        '.htmlspecialchars($p['judul']).'
                    </a>
                  </h6>';
            echo '<p class="small text-muted mb-1">Tahun: '.htmlspecialchars($p['tahun']).'</p>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p class="text-muted">Belum ada penelitian.</p>';
    }

    // PAGINATION
    if ($totalPages > 1) {
        echo '<nav><ul class="pagination justify-content-center">';
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i == $page) ? 'active' : '';
            echo "<li class='page-item $active'>
                    <button class='page-link' data-page='$i'>$i</button>
                  </li>";
        }
        echo '</ul></nav>';
    }

}
?>

<style>
.item-link {
    color: #313131ff !important;          /* warna hitam */
    transition: 0.15s ease;          /* smooth */
}

.item-link:hover {
    color: #0d6efd !important;       /* hover biru bootstrap */
    text-decoration: underline;
}
</style>

