<?php
include 'db.php';
session_start();

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT tanggal, jam, lapangan, nama_reservasi, status FROM reservasi WHERE user_id = :user_id ORDER BY tanggal DESC, jam DESC";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($riwayat);
?>
