<?php
require_once '../config/db.php';

$uuid = $_GET['uuid'] ?? '';

$stmt = $pdo->prepare("
    SELECT uuid, nama_web, link_page 
    FROM page_penelitian_anggota
    WHERE anggota_uuid = :uuid
    ORDER BY created_at DESC
");

$stmt->execute(['uuid' => $uuid]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));