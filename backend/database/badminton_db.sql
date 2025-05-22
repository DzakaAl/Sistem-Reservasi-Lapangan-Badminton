CREATE DATABASE IF NOT EXISTS badminton;
USE badminton;

-- Table structure for table `users`
CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255),
  email VARCHAR(255),
  no_tlp VARCHAR(12),
  role BOOLEAN,
  password VARCHAR(255)
);

-- Table structure for table `lapangan`
CREATE TABLE IF NOT EXISTS lapangan (
  lapangan_id INT AUTO_INCREMENT PRIMARY KEY,
  status VARCHAR(100) DEFAULT 'Tersedia',
  jam TIME,
  tanggal DATE
);

-- Table structure for table `reservasi`
CREATE TABLE IF NOT EXISTS reservasi (
  reservasi_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  lapangan_id INT,
  tanggal DATE,
  jam_mulai TIME, 
  jam_selesai TIME,
  status VARCHAR(100) DEFAULT 'Menunggu Konfirmasi',
  CONSTRAINT fk_reservasi_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_reservasi_lapangan FOREIGN KEY (lapangan_id) REFERENCES lapangan(lapangan_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table structure for table `pembayaran`
CREATE TABLE IF NOT EXISTS pembayaran (
  pembayaran_id INT AUTO_INCREMENT PRIMARY KEY,
  reservasi_id INT,
  metode_pembayaran VARCHAR(100),
  bukti_pembayaran VARCHAR(255),
  CONSTRAINT fk_pembayaran_reservasi FOREIGN KEY (reservasi_id) REFERENCES reservasi(reservasi_id) ON DELETE CASCADE ON UPDATE CASCADE
);
