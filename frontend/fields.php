<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
include 'backend/db.php';

$result = $koneksi->query("SELECT * FROM fields WHERE status = 'available'");
?>

<!DOCTYPE html>
<html>
<head><title>Daftar Lapangan</title></head>
<body>
  <h2>Lapangan Tersedia</h2>
  <ul>
    <?php while($row = $result->fetch_assoc()): ?>
      <li>
        <?= $row['name'] ?> - 
        <form action="backend/booking.php" method="POST" style="display:inline;">
          <input type="hidden" name="field_id" value="<?= $row['id'] ?>">
          <input type="datetime-local" name="booking_time" required>
          <button type="submit">Pesan</button>
        </form>
      </li>
    <?php endwhile; ?>
  </ul>
  <br><a href="logout.php">Logout</a>
</body>
</html>
