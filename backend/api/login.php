<?php
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Email dan password wajib diisi']);
    exit;
}

$stmt = $pdo->prepare('SELECT user_id, username, email, password, role FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Email atau password salah']);
    exit;
}

// Set session user
$_SESSION['user'] = [
    'user_id' => $user['user_id'],
    'username' => $user['username'],
    'email' => $user['email'],
    'role' => (bool)$user['role']
];

echo json_encode(['message' => 'Login berhasil', 'user' => $_SESSION['user']]);
?>
