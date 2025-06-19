<?php
header('Content-Type: application/json'); // Mengatur response menjadi JSON
require_once 'db.php'; // Menghubungkan ke database

// Memastikan hanya metode GET yang diterima
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Total reservasi
    $q1 = $conn->query("SELECT COUNT(*) as total FROM reservasi");
    $total_reservasi = $q1->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;  // Ganti fetch_assoc() dengan fetch(PDO::FETCH_ASSOC)

    // Total pembayaran masuk
    $q2 = $conn->query("SELECT SUM(total_bayar) as total FROM pembayaran");
    $total_pembayaran = $q2->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;  // Ganti fetch_assoc() dengan fetch(PDO::FETCH_ASSOC)

    // Total pengguna terdaftar
    $q3 = $conn->query("SELECT COUNT(*) as total FROM users");
    $total_user = $q3->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;  // Ganti fetch_assoc() dengan fetch(PDO::FETCH_ASSOC)

    // Lapangan digunakan hari ini
    $tgl = date('Y-m-d');
    $q4 = $conn->query("SELECT COUNT(DISTINCT lapangan) as total FROM reservasi WHERE tanggal='$tgl'");
    $lapangan_hari_ini = $q4->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;  // Ganti fetch_assoc() dengan fetch(PDO::FETCH_ASSOC)

    // Mengembalikan hasil dalam format JSON
    echo json_encode([
        'total_reservasi' => (int)$total_reservasi,
        'total_pembayaran' => (float)$total_pembayaran,
        'total_user' => (int)$total_user,
        'lapangan_hari_ini' => (int)$lapangan_hari_ini
    ]);
} else {
    // Jika bukan metode GET, kirimkan response error
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method not allowed, only GET is allowed']);
}
?>
