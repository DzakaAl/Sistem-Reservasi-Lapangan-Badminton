<?php
// Mengizinkan akses CORS dari frontend
header('Access-Control-Allow-Origin: *'); // Ganti dengan alamat frontend Anda jika berbeda
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE'); // Metode yang diizinkan
header('Access-Control-Allow-Headers: Content-Type');

// db.php - Database connection script

// Ambil data dari environment variables
$host = getenv('DB_HOST');      // Database host
$dbname = getenv('DB_NAME');    // Nama database
$username = getenv('DB_USERNAME'); // Username untuk database
$password = getenv('DB_PASSWORD'); // Password untuk database

// Cek apakah variabel lingkungan sudah terisi
if (!$host || !$dbname || !$username || !$password) {
    echo json_encode(['error' => 'Database credentials are missing.']);
    exit;  // Jika tidak ada variabel yang ditemukan, hentikan eksekusi
}

// Membuat koneksi PDO ke database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set atribut error mode untuk pengecualian
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Tangani kesalahan koneksi
    echo "Connection failed: " . $e->getMessage();
    exit; // Hentikan eksekusi jika koneksi gagal
}
?>
