CREATE DATABASE IF NOT EXISTS badminton;
USE badminton;

-- Tabel users
CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL UNIQUE,
  email VARCHAR(255) NOT NULL UNIQUE,
  no_tlp VARCHAR(15) NOT NULL,
  role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  password VARCHAR(255) NOT NULL
);

-- Tabel reservasi (langsung simpan info lapangan)
CREATE TABLE IF NOT EXISTS reservasi (
  reservasi_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  lapangan ENUM('Lapangan 1', 'Lapangan 2', 'Lapangan 3', 'Lapangan 4') NOT NULL,
  tanggal DATE NOT NULL,
  jam TIME NOT NULL,
  nama_reservasi VARCHAR(50) NOT NULL,
  status ENUM('Menunggu Konfirmasi', 'Dikonfirmasi', 'Dibatalkan') DEFAULT 'Menunggu Konfirmasi',
  CONSTRAINT fk_reservasi_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE (lapangan, tanggal, jam) -- Cegah double booking
);

-- Tabel pembayaran
CREATE TABLE IF NOT EXISTS pembayaran (
  pembayaran_id INT AUTO_INCREMENT PRIMARY KEY,
  reservasi_id INT NOT NULL,
  bukti_pembayaran VARCHAR(255),
  total_bayar DECIMAL(10, 2) NOT NULL,
  CONSTRAINT fk_pembayaran_reservasi FOREIGN KEY (reservasi_id) REFERENCES reservasi(reservasi_id) ON DELETE CASCADE ON UPDATE CASCADE
);
