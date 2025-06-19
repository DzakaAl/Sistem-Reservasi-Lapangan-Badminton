<?php
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

$email = trim($data['email'] ?? '');
$no_tlp = trim($data['no_tlp'] ?? '');

if (!$email || !$no_tlp) {
    http_response_code(400);
    echo json_encode(['error' => 'Email dan No. Telepon wajib diisi']);
    exit;
}

$stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = ? AND no_tlp = ?');
$stmt->execute([$email, $no_tlp]);
$user = $stmt->fetch();

if ($user) {
    echo json_encode(['message' => 'Verifikasi berhasil', 'user_id' => $user['user_id']]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Data tidak ditemukan']);
}
