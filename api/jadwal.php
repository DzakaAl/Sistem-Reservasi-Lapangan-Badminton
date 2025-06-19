<?php
// Jadwal.php
include 'db.php'; // Menghubungkan ke database

header('Content-Type: application/json'); // Menyatakan bahwa response adalah JSON

// Memastikan hanya menerima metode GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Ambil parameter tanggal dari query string
    $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

    // Query untuk mengambil data reservasi berdasarkan tanggal
    $sql = "SELECT lapangan, jam, nama_reservasi FROM reservasi WHERE tanggal = :date";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['date' => $date]);
    $reservasi = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Helper: konversi "Lapangan 1" -> 1 dst
    function lapanganToCourt($lapangan) {
        if (preg_match('/(\d+)/', $lapangan, $m)) {
            return (int)$m[1]; // Mengembalikan angka lapangan
        }
        return null;
    }

    // Susun array hasil sesuai kebutuhan frontend
    $result = [];
    for ($hour = 10; $hour <= 23; $hour++) {
        $jam = sprintf('%02d:00:00', $hour);
        for ($court = 1; $court <= 4; $court++) {
            // Cari reservasi pada jam dan lapangan ini
            $found = null;
            foreach ($reservasi as $r) {
                // Periksa apakah jam dan lapangan cocok
                if ($r['jam'] == $jam && lapanganToCourt($r['lapangan']) == $court) {
                    $found = $r['nama_reservasi']; // Ambil nama reservasi
                    break;
                }
            }
            // Simpan hasilnya
            $result[] = [
                'time' => $jam,
                'court' => $court,
                'reserved' => $found  // Jika reserved ada, simpan nama pemesan
            ];
        }
    }

    // Mengembalikan data dalam format JSON
    echo json_encode($result);

} else {
    // Jika bukan metode GET, kirimkan response error
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method not allowed, only GET is allowed']);
}
?>
