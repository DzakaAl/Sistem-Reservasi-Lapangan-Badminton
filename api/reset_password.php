<?php
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'] ?? null;
$new_password = $data['password'] ?? '';

if (!$user_id || !$new_password) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID dan Password baru wajib diisi']);
    exit;
}

$hashPassword = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('UPDATE users SET password = ? WHERE user_id = ?');
$success = $stmt->execute([$hashPassword, $user_id]);

if ($success) {
    echo json_encode(['message' => 'Password berhasil diperbarui']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal mengubah password']);
}
