<?php
// register-api.php

require 'db.php';  // Mengimpor koneksi ke database MongoDB

header('Content-Type: application/json');  // Menyatakan bahwa response adalah JSON

// Fungsi untuk menghasilkan UUID
function generateUUID() {
    return strtoupper(bin2hex(random_bytes(16)));  // UUID 32 karakter
}

// Mendapatkan data JSON yang dikirimkan melalui POST
$data = json_decode(file_get_contents('php://input'), true);

// Validasi input
if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['no_tlp'])) {
    echo json_encode(['error' => 'Semua field harus diisi!']);
    exit;
}

// Mendapatkan data dari JSON
$username = $data['username'];
$email = $data['email'];
$password = $data['password'];
$no_tlp = $data['no_tlp'];  // Menambahkan nomor telepon

// Role secara otomatis akan di-set menjadi 'user' jika tidak diberikan
$role = isset($data['role']) ? $data['role'] : 'user';  // Default ke 'user' jika tidak ada role yang diberikan

// Memeriksa apakah email sudah terdaftar
$emailCheck = $collection->findOne(['email' => $email]);
if ($emailCheck) {
    echo json_encode(['error' => 'Email sudah terdaftar!']);
    exit;
}

// Memeriksa apakah nomor telepon sudah terdaftar
$phoneCheck = $collection->findOne(['no_tlp' => $no_tlp]);
if ($phoneCheck) {
    echo json_encode(['error' => 'Nomor telepon sudah terdaftar!']);
    exit;
}

// Hash password sebelum disimpan
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Generate UUID untuk user_id
$user_id = generateUUID();

// Menyimpan data pengguna baru ke dalam database MongoDB
try {
    // Menyusun data pengguna
    $user = [
        'user_id' => $user_id,
        'username' => $username,
        'email' => $email,
        'password' => $hashedPassword,
        'no_tlp' => $no_tlp,
        'role' => $role  // Menyimpan role yang otomatis di-set menjadi 'user'
    ];

    // Menyimpan data ke koleksi 'users' di MongoDB
    $result = $collection->insertOne($user);

    // Mengembalikan response sukses
    echo json_encode(['success' => 'Pendaftaran berhasil!']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
