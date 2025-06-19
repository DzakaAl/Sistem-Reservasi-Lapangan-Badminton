<?php
// register-api.php
include 'db.php';
header('Content-Type: application/json');  // Menyatakan bahwa response adalah JSON

// Fungsi untuk menghasilkan UUID
function generateUUID() {
    // Menghasilkan UUID berbasis timestamp dan karakter acak
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
$role = isset($data['role']) ? $data['role'] : 'user';  // Default ke 'user' jika tidak ada role yang diberikan

// Memeriksa apakah email sudah terdaftar
$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $conn->prepare($sql);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode(['error' => 'Email sudah terdaftar!']);
    exit;
}

// Memeriksa apakah nomor telepon sudah terdaftar
$sql = "SELECT * FROM users WHERE no_tlp = :no_tlp";
$stmt = $conn->prepare($sql);
$stmt->execute(['no_tlp' => $no_tlp]);
$phoneCheck = $stmt->fetch(PDO::FETCH_ASSOC);

if ($phoneCheck) {
    echo json_encode(['error' => 'Nomor telepon sudah terdaftar!']);
    exit;
}

// Hash password sebelum disimpan
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Generate UUID untuk user_id
$user_id = generateUUID();

// Menyimpan data pengguna baru ke dalam database
try {
    $sql = "INSERT INTO users (user_id, username, email, password, no_tlp, role) 
            VALUES (:user_id, :username, :email, :password, :no_tlp, :role)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'user_id' => $user_id,  // Menyimpan UUID sebagai user_id
        'username' => $username,
        'email' => $email,
        'password' => $hashedPassword,
        'no_tlp' => $no_tlp,
        'role' => $role
    ]);

    // Mengembalikan response sukses
    echo json_encode(['success' => 'Pendaftaran berhasil!']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
