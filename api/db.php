<?php
header('Access-Control-Allow-Origin: *'); // Ganti dengan alamat frontend Anda jika berbeda
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE'); // Metode yang diizinkan
header('Access-Control-Allow-Headers: Content-Type');

// db.php - Database connection script
// Ambil URL koneksi database dari variabel lingkungan
$databaseUrl = parse_url(getenv('mysql://root:VbdySdDnYXTCxzoaXBTIFvbwmAhXZHHo@caboose.proxy.rlwy.net:10772/railway'));

// Koneksi ke MySQL menggunakan PDO
$host = $databaseUrl['host'];
$dbname = ltrim($databaseUrl['path'], '/'); // Mengambil nama database
$username = $databaseUrl['user'];
$password = $databaseUrl['pass'];

// Create a PDO instance to connect to the database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle any connection errors
    echo "Connection failed: " . $e->getMessage();
    exit; // Stop execution if connection fails
}
?>
