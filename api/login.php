<?php
// login.php
include 'db.php';  // Menghubungkan ke database

session_start();  // Mulai sesi PHP

header('Content-Type: application/json');  // Menyatakan bahwa respons adalah JSON

// Mendapatkan data JSON yang dikirimkan melalui POST
$data = json_decode(file_get_contents('php://input'), true);

// Validasi input
if (empty($data['email']) || empty($data['password'])) {
    echo json_encode(['error' => 'Email dan password harus diisi!']);
    exit;
}

// Mendapatkan data dari JSON
$email = $data['email'];
$password = $data['password'];

// Memeriksa email dan password di database
$sql = "SELECT user_id, username, password, role FROM users WHERE email = :email";
$stmt = $conn->prepare($sql);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Cek apakah user ditemukan
if (!$user) {
    echo json_encode(['error' => 'Email atau password salah!']);
    exit;
}

// Verifikasi password
if (!password_verify($password, $user['password'])) {
    echo json_encode(['error' => 'Email atau password salah!']);
    exit;
}

// Menyimpan user_id dan role dalam sesi
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['role'] = $user['role'];

// Mengembalikan response sukses dan data user
echo json_encode([
    'message' => 'Login berhasil!',
    'user' => [
        'user_id' => $user['user_id'],
        'username' => $user['username'],
        'role' => $user['role'],
    ]
]);
?>
