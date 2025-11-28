<?php
// session_start();
ob_start();

require_once '../config/db.php';
require_once '../lang/init.php';
require_once '../helpers/sanitize.php';
require '../config/mail.php';

$page_title = 'Contact Us';

// Fetch dashboard background
$stmt_bg = $pdo->query("SELECT * FROM dashboard_foto ORDER BY updated_at DESC LIMIT 1");
$dashboard_bg = $stmt_bg->fetch();
$bg_image = '';
if ($dashboard_bg && $dashboard_bg['path_gambar']) {
    $bg_image = '../assets/img/dashboard/' . htmlspecialchars($dashboard_bg['path_gambar']);
}

$success = '';
$error = '';

if (isset($_SESSION['flash_success'])) {
    $success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = clean_input($_POST['nama'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $subjek = clean_input($_POST['subjek'] ?? '');
    $pesan = clean_input($_POST['pesan'] ?? '');

    if (!$nama || !$email || !$subjek || !$pesan) {
        $_SESSION['flash_error'] = 'Semua field harus diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_error'] = 'Format email tidak valid!';
    } else {
        $bodyHtml  = '<div style="font-family:Arial,sans-serif;color:#333;font-size:16px;line-height:1.5;">'
            . '<p style="margin:0 0 12px;"><strong>Nama:</strong> ' . htmlspecialchars($nama) . '</p>'
            . '<p style="margin:0 0 12px;"><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>'
            . '<hr style="border:none;border-top:1px solid #eee;margin:20px 0;">'
            . '<p style="margin:0 0 8px;"><strong>Pesan Anda:</strong></p>'
            . '<p style="margin:0 0 12px;padding:12px;background:#f9f9f9;border:1px solid #eee;">'
            . nl2br(htmlspecialchars($pesan))
            . '</p>'
            . '</div>';

        $bodyPlain = "Nama: {$nama}\nEmail: {$email}\nPesan:\n{$pesan}";

        $sent = sendEmail($pdo, $subjek, $bodyHtml, $bodyPlain);

        if ($sent) {
            $_SESSION['flash_success'] = 'Terima kasih! Pesan Anda telah dikirim. Kami akan segera menghubungi Anda.';
            try {
                $stmtActive = $pdo->query("
                SELECT uuid 
                FROM email_settings 
                ORDER BY updated_at DESC 
                LIMIT 1
                ");
                $activeEmail = $stmtActive->fetchColumn();
                $stmt = $pdo->prepare("INSERT INTO email_pesan (nama, email, subjek, pesan , email_setting_uuid) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nama, $email, $subjek, $pesan, $activeEmail]);
            } catch (PDOException $e) {
                $_SESSION['flash_error'] = 'data tidak tersimpan';
            }
        } else {
            $_SESSION['flash_error'] = 'Gagal mengirim email.';
        }

        header("Location: contact.php");
        exit;
    }
}

// Get social media links
$stmt = $pdo->query("SELECT * FROM sosmed");
$social_media = $stmt->fetchAll();

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

include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- Page Header -->
<section class="hero-section position-relative py-5" id="heroSection"
    style="color: white; min-height: 500px; overflow: hidden;">
    <!-- Parallax Background Layer -->
    <?php
    $bg_style = $bg_image
        ? "background: url('$bg_image') center/cover no-repeat; z-index: 0;"
        : 'background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%); z-index: 0;';
    ?>
    <div class="parallax-bg position-absolute top-0 start-0 w-100 h-100"
        style="<?php echo $bg_style; ?> transform: translate3d(0, 0, 0); will-change: transform;">
    </div>

    <!-- Gradient Overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100"
        style="background: linear-gradient(135deg, rgba(30, 75, 163, 0.25) 0%, rgba(74, 144, 226, 0.25) 100%); z-index: 1; pointer-events: none;">
    </div>

    <!-- Content -->
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center justify-content-center min-vh-75 py-5">
            <div class="col-lg-6 text-center">
                <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
                <p class="lead"><?= __('contact_hero_desc') ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Parallax JavaScript -->
<script src="../assets/js/parallax.js"></script>
<!-- Contact Section -->
<section class="py-5" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <div class="row g-4">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4"><?= __('send_message') ?></h3>

                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="bi bi-check-circle-fill me-2"></i><?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label"><?= __('form_fullname') ?> <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control form-control-lg"
                                        placeholder="<?= __('form_fullname_placeholder') ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label"><?= __('form_email') ?> <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control form-control-lg"
                                        placeholder="<?= __('form_email_placeholder') ?>" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label"><?= __('form_subject') ?> <span class="text-danger">*</span></label>
                                    <input type="text" name="subjek" class="form-control form-control-lg"
                                        placeholder="<?= __('form_subject_placeholder') ?>" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label"><?= __('form_message') ?> <span class="text-danger">*</span></label>
                                    <textarea name="pesan" class="form-control form-control-lg" rows="6"
                                        placeholder="<?= __('form_message_placeholder') ?>" required></textarea>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="bi bi-send me-2"></i><?= __('btn_send_message') ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4"><?= __('contact_information') ?></h4>

                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-geo-alt-fill text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-1"><?= __('address') ?></h6>
                                <p class="text-muted mb-0">
                                    <?= nl2br(htmlspecialchars($contact['address'] ?? '')) ?>
                                </p>
                            </div>
                        </div>

                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-envelope-fill text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-1"><?= __('form_email') ?></h6>
                                <p class="text-muted mb-0">
                                    <?= htmlspecialchars($contact['email_Email'] ?? '') ?><br>
                                    <?= htmlspecialchars($contact['email_Email2'] ?? '') ?>
                                </p>
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-clock-fill text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-1"><?= __('working_hours') ?></h6>
                                <p class="text-muted mb-0">
                                    <?php
                                    $order = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"];
                                    usort($working_hours, function ($a, $b) use ($order) {
                                        return array_search($a['day_name'], $order) - array_search($b['day_name'], $order);
                                    });

                                    function formatHours($row)
                                    {
                                        if ($row['is_closed']) {
                                            return __('closed');
                                        }
                                        if (empty($row['open_time']) || empty($row['close_time'])) {
                                            return __('closed');
                                        }
                                        return substr($row['open_time'], 0, 5) . " - " . substr($row['close_time'], 0, 5);
                                    }

                                    $groups = [];
                                    $currentGroup = [
                                        "start" => $working_hours[0]['day_name'],
                                        "end"   => $working_hours[0]['day_name'],
                                        "time"  => formatHours($working_hours[0])
                                    ];

                                    for ($i = 1; $i < count($working_hours); $i++) {
                                        $row = $working_hours[$i];
                                        $time = formatHours($row);

                                        if ($time === $currentGroup['time']) {
                                            $currentGroup['end'] = $row['day_name'];
                                        } else {
                                            $groups[] = $currentGroup;
                                            $currentGroup = [
                                                "start" => $row['day_name'],
                                                "end"   => $row['day_name'],
                                                "time"  => $time
                                            ];
                                        }
                                    }
                                    $groups[] = $currentGroup;
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
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <?php if (!empty($social_media)): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-3"><?= __('follow_us') ?></h4>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($social_media as $sosmed): ?>
                                    <a href="<?php echo htmlspecialchars($sosmed['url']); ?>" target="_blank"
                                        class="btn btn-outline-primary">
                                        <i class="bi bi-<?php echo strtolower($sosmed['nama']); ?> me-2"></i>
                                        <?php echo ucfirst($sosmed['nama']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.4223865374843!2d112.61315931477714!3d-7.946353894280831!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e78827687d272e7%3A0x789ce9a636cd3aa2!2sPoliteknik%20Negeri%20Malang!5e0!3m2!1sen!2sid!4v1635000000000!5m2!1sen!2sid"
                    width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</section>

<div id="pageLoadingOverlay"
    class="d-none position-fixed top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex justify-content-center align-items-center"
    style="z-index:1050;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading…</span>
    </div>
</div>

<script>
    const form = document.querySelector('form[method="POST"]');
    form.addEventListener('submit', function() {
        const overlay = document.getElementById('pageLoadingOverlay');
        overlay.classList.remove('d-none');
        document.body.style.overflow = 'hidden';
    });
</script>

<?php include '../includes/footer.php';
ob_end_flush();
?>