<?php
header('Content-Type: application/json');
require 'db.php';

// Ambil data JSON dari request
$data = json_decode(file_get_contents('php://input'), true);

$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$no_tlp = trim($data['no_tlp'] ?? '');
$password = $data['password'] ?? '';
$role = isset($data['role']) ? (bool)$data['role'] : false;  // default false

// Validasi sederhana
if (!$username || !$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Username, email, dan password wajib diisi']);
    exit;
}

// Cek apakah email sudah terdaftar
$stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Email sudah terdaftar']);
    exit;
}

// Hash password
$hashPassword = password_hash($password, PASSWORD_DEFAULT);

// Simpan data user baru
$stmt = $pdo->prepare('INSERT INTO users (username, email, no_tlp, role, password) VALUES (?, ?, ?, ?, ?)');
$success = $stmt->execute([$username, $email, $no_tlp, $role, $hashPassword]);

if ($success) {
    echo json_encode(['message' => 'Registrasi berhasil']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal menyimpan data']);
}
