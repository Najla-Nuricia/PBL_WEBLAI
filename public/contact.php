<?php
session_start();
ob_start();

require_once '../config/db.php';
require_once '../helpers/sanitize.php';
require '../config/mail.php';

$page_title = 'Contact Us';

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

        $sent = sendEmail($subjek, $bodyHtml, $bodyPlain);

        if ($sent) {
            $_SESSION['flash_success'] = 'Terima kasih! Pesan Anda telah dikirim. Kami akan segera menghubungi Anda.';
            try {
                $stmt = $pdo->prepare("INSERT INTO email (nama, email, subjek, pesan) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nama, $email, $subjek, $pesan]);
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

include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- Page Header -->
<section class="page-header py-5" style="background: linear-gradient(135deg, #1E4BA3 0%, #4A90E2 100%); color: white;">
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
                <p class="lead">Hubungi kami untuk informasi lebih lanjut atau kerjasama</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4">Send Us a Message</h3>

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
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text"
                                        name="nama"
                                        class="form-control form-control-lg"
                                        placeholder="Masukkan nama Anda"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email"
                                        name="email"
                                        class="form-control form-control-lg"
                                        placeholder="email@example.com"
                                        required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Subjek <span class="text-danger">*</span></label>
                                    <input type="text"
                                        name="subjek"
                                        class="form-control form-control-lg"
                                        placeholder="Subjek pesan"
                                        required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Pesan <span class="text-danger">*</span></label>
                                    <textarea name="pesan"
                                        class="form-control form-control-lg"
                                        rows="6"
                                        placeholder="Tulis pesan Anda di sini..."
                                        required></textarea>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="bi bi-send me-2"></i>Kirim Pesan
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
                        <h4 class="fw-bold mb-4">Contact Information</h4>

                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-geo-alt-fill text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-1">Address</h6>
                                <p class="text-muted mb-0">
                                    Politeknik Negeri Malang<br>
                                    Jl. Soekarno Hatta No.9<br>
                                    Malang, Jawa Timur 65141
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
                                <h6 class="fw-bold mb-1">Email</h6>
                                <p class="text-muted mb-0">
                                    ailab@polinema.ac.id<br>
                                    info@ailab-polinema.ac.id
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
                                <h6 class="fw-bold mb-1">Working Hours</h6>
                                <p class="text-muted mb-0">
                                    Senin - Jumat: 08:00 - 16:00<br>
                                    Sabtu - Minggu: Tutup
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <?php if (!empty($social_media)): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-3">Follow Us</h4>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($social_media as $sosmed): ?>
                                    <a href="<?php echo htmlspecialchars($sosmed['url']); ?>"
                                        target="_blank"
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
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.4223865374843!2d112.61315931477714!3d-7.946353894280831!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e78827687d272e7%3A0x789ce9a636cd3aa2!2sPoliteknik%20Negeri%20Malang!5e0!3m2!1sen!2sid!4v1635000000000!5m2!1sen!2sid"
                    width="100%"
                    height="450"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</section>

<div id="pageLoadingOverlay" class="d-none position-fixed top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex justify-content-center align-items-center" style="z-index:1050;">
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