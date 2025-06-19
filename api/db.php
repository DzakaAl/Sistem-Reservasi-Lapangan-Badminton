<?php
require 'vendor/autoload.php';  // Pastikan Anda sudah menginstal vlucas/phpdotenv

use Dotenv\Dotenv;

// Load variabel lingkungan dari file .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// db.php - Database connection script
$host = getenv('DB_HOST');
$dbname = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

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
