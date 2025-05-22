<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
  echo "Akses ditolak!";
  exit();
}
include 'backend/db.php';
$sql = "SELECT bookings.id, users.username, fields.name, bookings.booking_time 
        FROM bookings 
        JOIN users ON bookings.user_id = users.id 
        JOIN fields ON bookings.field_id = fields.id 
        ORDER BY bookings.booking_time DESC";
$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html>
<head><title>Admin - Daftar Reservasi</title><link rel="stylesheet" href="style.css"></head>
<body>
  <h2>Data Reservasi</h2>
  <table border="1" cellpadding="8" cellspacing="0" style="margin:auto;">
    <tr><th>ID</th><th>Nama Pengguna</th><th>Lapangan</th><th>Waktu Pemesanan</th></tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= $row['username'] ?></td>
      <td><?= $row['name'] ?></td>
      <td><?= $row['booking_time'] ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
  <br><a href="logout.php">Logout</a>
</body>
</html>
