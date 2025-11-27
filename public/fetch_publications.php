<?php
require_once '../config/db.php';

$uuid = $_GET['uuid'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

if ($uuid) {

    // Ambil publikasi dengan semua authors
    $stmt = $pdo->prepare("
    SELECT *
    FROM view_publikasi_author v
    JOIN anggota_publikasi ap ON v.uuid = ap.publikasi_uuid
    WHERE ap.anggota_uuid = ?
    ORDER BY v.tahun DESC, v.judul ASC
    LIMIT $limit OFFSET $offset
");

$stmt->execute([$uuid]);
$publikasis = $stmt->fetchAll();


    if ($publikasis && count($publikasis) > 0) {
        echo '<div class="list-group mb-3">';
        foreach ($publikasis as $p) {
            echo '<div class="list-group-item">';

            // Judul dengan link
            echo '<h6 class="fw-bold mb-2">';
            if (!empty($p['tautan'])) {
                echo '<a href="' . htmlspecialchars($p['tautan']) . '" target="_blank" class="text-decoration-none item-link">';
                echo htmlspecialchars($p['judul']);
                echo ' <i class="bi bi-box-arrow-up-right ms-1 small"></i>';
                echo '</a>';
            } else {
                echo htmlspecialchars($p['judul']);
            }
            echo '</h6>';

            // Authors (jika ada)
            if (!empty($p['all_authors'])) {
                echo '<p class="small text-muted mb-1">';
                echo '<i class="bi bi-people-fill me-1"></i>';
                echo '<strong>' . htmlspecialchars($p['all_authors']) . '</strong>';
                echo '</p>';
            }

            // Kategori dan Tahun
            echo '<div class="d-flex gap-2 align-items-center">';
            if (!empty($p['kategori'])) {
                echo '<span class="badge bg-info">';
                echo htmlspecialchars($p['kategori']);
                echo '</span>';
            }
            if (!empty($p['tahun'])) {
                echo '<small class="text-muted">';
                echo '<i class="bi bi-calendar3 me-1"></i>' . htmlspecialchars($p['tahun']);
                echo '</small>';
            }
            echo '</div>';

            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="alert alert-info">';
        echo '<i class="bi bi-info-circle me-2"></i>';
        echo 'Belum ada publikasi untuk anggota ini.';
        echo '</div>';
    }

}
?>

<style>
.item-link {
    color: #313131 !important;
    transition: color 0.15s ease, text-decoration 0.15s ease;
}

.item-link:hover {
    color: #0d6efd !important;
    text-decoration: underline !important;
}

.list-group-item {
    transition: background-color 0.15s ease;
    border-left: 3px solid transparent;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    border-left-color: #0d6efd;
}

.badge {
    font-weight: 500;
    font-size: 0.75rem;
}

.pagination-sm .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    cursor: pointer;
}

.pagination-sm .page-link:hover {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.pagination-sm .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
</style>