<?php
ob_start();
$page_title = 'Kelola Informasi Kontak';
include 'includes/auth.php';
include 'includes/admin_header.php';

$success = '';
$error = '';

// Get current contact info
$stmt = $pdo->query("SELECT uuid, type, label, value FROM contact_address_email");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


$contact = [];
foreach ($rows as $row) {
    if ($row['type'] === 'email') {
        $contact['email_' . $row['label'] . '_uuid'] = $row['uuid'];
        $contact['email_' . $row['label']] = $row['value'];
    } else {
        $contact[$row['type'] . '_uuid'] = $row['uuid'];
        $contact[$row['type']] = $row['value'];
    }
}

// Get working hours
$stmt = $pdo->query("SELECT * FROM contact_working_hours ORDER BY ordering ASC");
$working_hours = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_contact') {

    $address = trim($_POST['address'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $email2  = trim($_POST['email2'] ?? '');

    try {
        // Prepare statement for UPDATE
        $update = $pdo->prepare("
            UPDATE contact_address_email 
            SET value = ?, updated_at = NOW()
            WHERE uuid = ?
        ");

        // Update address
        if (isset($contact['address_uuid'])) {
            $update->execute([$address, $contact['address_uuid']]);
        }

        // Update first email 
        if (isset($contact['email_Email_uuid'])) {
            $update->execute([$email, $contact['email_Email_uuid']]);
        }

        // Update second email
        if (isset($contact['email_Email2_uuid'])) {
            // KASUS 1: Email2 sudah ada di database 
            if (!empty($email2)) {
                $update->execute([$email2, $contact['email_Email2_uuid']]);
            } else {
                $delete = $pdo->prepare("DELETE FROM contact_address_email WHERE uuid = ?");
                $delete->execute([$contact['email_Email2_uuid']]);
            }
        } else {
            if (!empty($email2)) {
                $insert = $pdo->prepare("INSERT INTO contact_address_email (type, label, value) VALUES ('email', 'Email2', ?)");
                $insert->execute([$email2]);
            }
        }

        $_SESSION['flash_success'] = "Informasi kontak berhasil diperbarui!";
        header("Location: manage_contactInfo.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['flash_error'] = "Gagal menyimpan data: " . $e->getMessage();
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_hours') {

    try {
        // Prepare statement for UPDATE
        $update = $pdo->prepare("
            UPDATE contact_working_hours
            SET is_closed = ?, open_time = ?, close_time = ?, updated_at = NOW()
            WHERE uuid = ?
        ");

        foreach ($_POST['hours'] as $uuid => $data) {
            // Checkbox: jika 1 = true (tutup), if 0 = false (buka)
            $is_closed  = isset($data['is_closed']) ? 1 : 0;
            
            // open_time and close_time
            $open_time  = !empty($data['open_time']) ? $data['open_time'] : null;
            $close_time = !empty($data['close_time']) ? $data['close_time'] : null;

            // Execute update for each day
            $update->execute([$is_closed, $open_time, $close_time, $uuid]);
        }

        $_SESSION['flash_success'] = "Jam operasional berhasil diperbarui!";
        header("Location: manage_contactInfo.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['flash_error'] = "Gagal menyimpan jam operasional: " . $e->getMessage();
    }
}

if (isset($_SESSION['flash_success'])) {
    $success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}
?>

<!-- Content Contact -->
<div class="row">
    <div class="col-lg-7">
        <!-- Contact Address & Emails -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-envelope-at me-2 text-primary"></i>Alamat & Email
                </h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update_contact">

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="bi bi-geo-alt me-2"></i>Alamat Laboratorium
                            <span class="text-danger">*</span>
                        </label>
                        <textarea name="address"
                            class="form-control"
                            rows="4" 
                            required
                            placeholder="Masukkan alamat lengkap laboratorium..."><?= htmlspecialchars($contact['address'] ?? '') ?></textarea>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Gunakan Enter untuk memisahkan baris alamat
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="bi bi-envelope me-2"></i>Email Utama
                            <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                            name="email"
                            class="form-control"
                            value="<?= htmlspecialchars($contact['email_Email'] ?? '') ?>" 
                            required 
                            placeholder="contoh: ailab@polinema.ac.id">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Email kontak utama yang akan ditampilkan
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="bi bi-envelope-plus me-2"></i>Email Kedua
                            <span class="text-muted">(Opsional)</span>
                        </label>
                        <input type="email" 
                            name="email2"
                            class="form-control"
                            value="<?= htmlspecialchars($contact['email_Email2'] ?? '') ?>" 
                            placeholder="contoh: info@ailab-polinema.ac.id">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Email alternatif (kosongkan jika tidak ada)
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <!-- Working Hours -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-clock-history me-2 text-primary"></i>Jam Operasional
                </h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update_hours">
                    
                    <?php foreach ($working_hours as $wh): ?>
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <strong class="text-primary">
                                        <i class="bi bi-calendar-check me-2"></i>
                                        <?= htmlspecialchars($wh['day_name']) ?>
                                    </strong>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox"
                                            class="form-check-input"
                                            id="closed_<?= $wh['uuid'] ?>"
                                            name="hours[<?= $wh['uuid'] ?>][is_closed]"
                                            <?= $wh['is_closed'] ? 'checked' : '' ?>
                                            onchange="toggleTime(this, '<?= $wh['uuid'] ?>')">
                                        <label class="form-check-label" for="closed_<?= $wh['uuid'] ?>">
                                            <span class="badge bg-danger">Tutup</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row g-2" id="time_<?= $wh['uuid'] ?>">
                                        <div class="col-6">
                                            <label class="form-label small mb-1">Buka</label>
                                            <input type="time"
                                                name="hours[<?= $wh['uuid'] ?>][open_time]"
                                                class="form-control form-control-sm"
                                                value="<?= $wh['open_time'] ?>"
                                                <?= $wh['is_closed'] ? 'disabled' : '' ?>>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small mb-1">Tutup</label>
                                            <input type="time"
                                                name="hours[<?= $wh['uuid'] ?>][close_time]"
                                                class="form-control form-control-sm"
                                                value="<?= $wh['close_time'] ?>"
                                                <?= $wh['is_closed'] ? 'disabled' : '' ?>>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Simpan Jam Operasional
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Contact -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4 pb-3 border-bottom">
                    <i class="bi bi-eye me-2 text-primary"></i>Preview Kontak
                </h5>

                <!-- Address Preview -->
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-geo-alt-fill text-primary fs-3"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fw-bold mb-2 text-dark">Address</h6>
                        <p class="text-muted mb-0 small">
                            <?= nl2br(htmlspecialchars($contact['address'] ?? 'Alamat belum diisi')) ?>
                        </p>
                    </div>
                </div>

                <!-- Email Preview -->
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-envelope-fill text-primary fs-3"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fw-bold mb-2 text-dark">Email</h6>
                        <p class="text-muted mb-0 small">
                            <?= htmlspecialchars($contact['email_Email'] ?? 'Email belum diisi') ?>
                            <?php if (isset($contact['email_Email2']) && !empty($contact['email_Email2'])): ?>
                                <br><?= htmlspecialchars($contact['email_Email2']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- Working Hours Preview -->
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-clock-fill text-primary fs-3"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fw-bold mb-2 text-dark">Working Hours</h6>
                        <div class="small">
                            <?php
                            // Sort days according to calendar order
                            $order = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"];
                            usort($working_hours, function($a, $b) use ($order) {
                                return array_search($a['day_name'], $order) - array_search($b['day_name'], $order);
                            });

                            // Function to format hours
                            function formatHours($row) {
                                if ($row['is_closed']) {
                                    return "Tutup";
                                }
                                if (empty($row['open_time']) || empty($row['close_time'])) {
                                    return "Tutup";
                                }
                                return substr($row['open_time'], 0, 5) . " - " . substr($row['close_time'], 0, 5);
                            }

                            // Group days with the same hours
                            // Example: Monday-Friday: 08:00 - 16:00
                            $groups = [];
                            $currentGroup = [
                                "start" => $working_hours[0]['day_name'],
                                "end"   => $working_hours[0]['day_name'],
                                "time"  => formatHours($working_hours[0])
                            ];

                            // Loop from the second day to the last
                            for ($i = 1; $i < count($working_hours); $i++) {
                                $row = $working_hours[$i];
                                $time = formatHours($row);

                                // If hours are the same as the previous group, merge them
                                if ($time === $currentGroup['time']) {
                                    $currentGroup['end'] = $row['day_name'];
                                } else {
                                    // If different, save the old group and start a new group
                                    $groups[] = $currentGroup;
                                    $currentGroup = [
                                        "start" => $row['day_name'],
                                        "end"   => $row['day_name'],
                                        "time"  => $time
                                    ];
                                }
                            }
                            // save the last group
                            $groups[] = $currentGroup;

                            // Display grouped results
                            foreach ($groups as $g) {
                                if ($g['start'] === $g['end']) {
                                    echo "<p class='text-muted mb-1'>
                                            <strong>{$g['start']}:</strong> {$g['time']}</p>";
                                } else {
                                    echo "<p class='text-muted mb-1'>
                                            <strong>{$g['start']} - {$g['end']}:</strong> {$g['time']}</p>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// disable/enable inputs when the checkbox is checked
function toggleTime(checkbox, uuid) {
    const timeInputs = document.querySelectorAll(`#time_${uuid} input`);
    timeInputs.forEach(input => {
        input.disabled = checkbox.checked;
        if (checkbox.checked) {
            input.value = '';
        }
    });
}
</script>

<?php include 'includes/admin_footer.php'; ?>