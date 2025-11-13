<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function sendEmail(string $subject, string $bodyHtml, string $bodyPlain = ''): bool {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'];
        $mail->Password   = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = intval($_ENV['SMTP_PORT']);
        $mail->SMTPDebug = 2; 
        $mail->Debugoutput = 'error_log';

        $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
        $mail->addAddress($_ENV['MAIL_TO'], $_ENV['MAIL_TO_NAME']);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyHtml;
        $mail->AltBody = $bodyPlain !== '' ? $bodyPlain : strip_tags($bodyHtml);

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}

