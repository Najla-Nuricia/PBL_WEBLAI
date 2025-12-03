<?php
require_once '../config/db.php';

// Get parameters
$uuid = $_GET['uuid'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10; // Jumlah publikasi per halaman
$offset = ($page - 1) * $limit;

if (empty($uuid)) {
    echo '<div class="alert alert-warning m-3">UUID anggota tidak valid.</div>';
    exit;
}

try {
    // Get total count
    $stmt_count = $pdo->prepare("
        SELECT COUNT(DISTINCT p.uuid) as total
        FROM publikasi p
        JOIN anggota_publikasi ap ON p.uuid = ap.publikasi_uuid
        WHERE ap.anggota_uuid = ?
    ");
    $stmt_count->execute([$uuid]);
    $total_result = $stmt_count->fetch();
    $total_publications = $total_result['total'];
    $total_pages = ceil($total_publications / $limit);

    // Get publications with pagination
    $stmt = $pdo->prepare("
        SELECT DISTINCT
            p.uuid,
            p.judul,
            p.tahun,
            p.tautan,
            p.kategori,
            p.created_at
        FROM publikasi p
        JOIN anggota_publikasi ap ON p.uuid = ap.publikasi_uuid
        WHERE ap.anggota_uuid = ?
        ORDER BY p.tahun DESC, p.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$uuid, $limit, $offset]);
    $publications = $stmt->fetchAll();

    if (empty($publications)) {
        echo '<div class="alert alert-info m-3">
                <i class="bi bi-info-circle me-2"></i>
                Belum ada publikasi untuk anggota ini.
              </div>';
        exit;
    }

    // Display publications
    echo '<div class="px-3">';
    echo '<h6 class="text-muted mb-3">Daftar Publikasi (' . $total_publications . ' total)</h6>';
    echo '<div class="list-group list-group-flush">';

    foreach ($publications as $pub) {
        // Get authors for this publication
        $stmt_authors = $pdo->prepare("
            SELECT a.nama
            FROM anggota a
            JOIN anggota_publikasi ap ON a.uuid = ap.anggota_uuid
            WHERE ap.publikasi_uuid = ?
        ");
        $stmt_authors->execute([$pub['uuid']]);
        $authors = $stmt_authors->fetchAll(PDO::FETCH_COLUMN);
        $authors_text = implode(', ', $authors);

        // Badge color based on publication category
        $badge_colors = [
            'Internasional' => 'primary',
            'Scopus' => 'success',
            'Google Scholar' => 'info',
            'Springer' => 'warning',
            'Nasional' => 'secondary',
            'lainnya' => 'secondary'
        ];
        $badge_color = $badge_colors[$pub['kategori']] ?? 'primary';

        echo '<div class="list-group-item border-0 border-bottom py-3">';
        echo '<div class="d-flex justify-content-between align-items-start mb-2">';
        echo '<span class="badge bg-' . $badge_color . '">' . htmlspecialchars($pub['kategori']) . '</span>';
        echo '<span class="badge bg-light text-dark">' . ($pub['tahun'] ?: 'N/A') . '</span>';
        echo '</div>';

        echo '<h6 class="mb-2 fw-bold">' . htmlspecialchars($pub['judul']) . '</h6>';

        if (!empty($authors_text)) {
            echo '<p class="mb-2 small text-muted">';
            echo '<i class="bi bi-people-fill me-1"></i>';
            echo htmlspecialchars($authors_text);
            echo '</p>';
        }

        if (!empty($pub['tautan'])) {
            echo '<a href="' . htmlspecialchars($pub['tautan']) . '" target="_blank" class="btn btn-sm btn-outline-primary">';
            echo '<i class="bi bi-link-45deg me-1"></i>Lihat Publikasi';
            echo '</a>';
        }

        echo '</div>';
    }

    echo '</div>'; // end list-group

    // Pagination
    if ($total_pages > 1) {
        echo '<nav aria-label="Pagination publikasi" class="mt-4">';
        echo '<ul class="pagination justify-content-center">';

        // Previous button
        echo '<li class="page-item ' . (($page <= 1) ? 'disabled' : '') . '">';
        echo '<a class="page-link pagination-btn" data-page="' . ($page - 1) . '" href="#">&laquo; Sebelumnya</a>';
        echo '</li>';

        // Page numbers - show limited range
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);

        // First page
        if ($start_page > 1) {
            echo '<li class="page-item">';
            echo '<a class="page-link pagination-btn" data-page="1" href="#">1</a>';
            echo '</li>';
            if ($start_page > 2) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Page numbers in range
        for ($i = $start_page; $i <= $end_page; $i++) {
            echo '<li class="page-item ' . (($i == $page) ? 'active' : '') . '">';
            echo '<a class="page-link pagination-btn" data-page="' . $i . '" href="#">' . $i . '</a>';
            echo '</li>';
        }

        // Last page
        if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item">';
            echo '<a class="page-link pagination-btn" data-page="' . $total_pages . '" href="#">' . $total_pages . '</a>';
            echo '</li>';
        }

        // Next button
        echo '<li class="page-item ' . (($page >= $total_pages) ? 'disabled' : '') . '">';
        echo '<a class="page-link pagination-btn" data-page="' . ($page + 1) . '" href="#">Selanjutnya &raquo;</a>';
        echo '</li>';

        echo '</ul>';
        echo '</nav>';
    }

    echo '</div>'; // end px-3 container

} catch (PDOException $e) {
    echo '<div class="alert alert-danger m-3">';
    echo '<i class="bi bi-exclamation-triangle me-2"></i>';
    echo 'Terjadi kesalahan: ' . htmlspecialchars($e->getMessage());
    echo '</div>';
}
