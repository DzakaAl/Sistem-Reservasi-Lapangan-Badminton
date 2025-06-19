<?php
header('Access-Control-Allow-Origin: *'); // Ganti dengan alamat frontend Anda jika berbeda
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE'); // Metode yang diizinkan
header('Access-Control-Allow-Headers: Content-Type');
// db.php - Database connection script
$host = '127.0.0.1';      // Database host (use '127.0.0.1' if 'localhost' doesn't work)
$port = '4040';
$dbname = 'badminton';    // Your database name
$username = 'root';       // Your database username
$password = '';           // Your database password

// Create a PDO instance to connect to the database
try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle any connection errors
    echo "Connection failed: " . $e->getMessage();
    exit; // Stop execution if connection fails
}
?>
z
