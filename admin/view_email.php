<?php
require_once '../config/db.php';
include 'includes/admin_header.php';

// Ambil semua email
$stmt = $pdo->query("SELECT * FROM email ORDER BY tanggal_dikirim DESC");
$emails = $stmt->fetchAll();
?>

<div class="row">

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
                                <th width="140">Nama</th>
                                <th width="180">Email</th>
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

                                        <!-- Email (dipersempit + truncate) -->
                                        <td class="text-truncate" style="max-width: 160px;">
                                            <a href="mailto:<?= htmlspecialchars($email['email']) ?>" class="text-primary">
                                                <?= htmlspecialchars($email['email']) ?>
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

</div>
