<?php
// index.php sebagai bridge untuk API endpoint

// Mendapatkan path dari URL, misal: /backend/index.php/api/jadwal.php
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

// Hapus slash depan
$path = ltrim($path, '/');

// Jika path kosong, tampilkan info API
if ($path === '' || $path === 'index.php') {
    echo "Sistem Reservasi Badminton API is running.";
    exit;
}

// Cegah directory traversal
if (strpos($path, '..') !== false) {
    http_response_code(400);
    echo "Invalid path.";
    exit;
}

// Cek apakah file API ada
$apiFile = __DIR__ . '/' . $path;
if (file_exists($apiFile) && is_file($apiFile)) {
    require $apiFile;
    exit;
} else {
    http_response_code(404);
    echo "API endpoint not found.";
    exit;
}
?>
