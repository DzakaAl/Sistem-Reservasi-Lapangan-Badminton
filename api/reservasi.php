<?php
session_start(); // Memulai session

// Cek apakah user_id ada di sesi
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Anda harus login terlebih dahulu']);
    exit;
}

include 'db.php'; // Menghubungkan ke database

header('Content-Type: application/json'); // Mengatur response menjadi JSON

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Jika ada ID yang dikirimkan dalam query string, ambil reservasi berdasarkan ID
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $sql = "SELECT * FROM reservasi WHERE reservasi_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($data ?: []);
    } else {
        // Ambil semua reservasi
        $sql = "SELECT * FROM reservasi ORDER BY tanggal DESC, jam DESC";
        $stmt = $conn->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }
} elseif ($method === 'POST') {
    // Proses POST untuk memasukkan reservasi baru
    $input = json_decode(file_get_contents('php://input'), true);

    // Validasi input
    if (empty($input['name']) || empty($input['tanggal']) || empty($input['jam']) || empty($input['lapangan'])) {
        echo json_encode(['error' => 'Semua field harus diisi!']);
        exit;
    }

    // Mendapatkan data dari input JSON
    $name = $input['name'];
    $tanggal = $input['tanggal'];
    $lapangan = $input['lapangan'];

    // Ubah angka lapangan menjadi string sesuai ENUM di DB
    if (in_array($lapangan, ['1','2','3','4'])) {
        $lapangan = 'Lapangan ' . $lapangan;
    }

    // Ubah jam menjadi array jika dikirim dalam bentuk JSON string
    $jamList = $input['jam'];
    if (is_string($jamList)) {
        $jamList = json_decode($jamList, true);
    }
    if (!is_array($jamList)) {
        $jamList = [$jamList];
    }

    $reservasiIds = [];
    foreach ($jamList as $jamText) {
        // Ambil jam awal dari string, misal "10:00 - 11:00" -> "10:00"
        $jam = trim(explode('-', $jamText)[0]);
        // Cek double booking
        $sql = "SELECT * FROM reservasi WHERE lapangan = :lapangan AND tanggal = :tanggal AND jam = :jam";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['lapangan' => $lapangan, 'tanggal' => $tanggal, 'jam' => $jam]);
        $existingReservation = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingReservation) {
            echo json_encode(['error' => "Lapangan sudah tereservasi pada $jamText."]);
            exit;
        }

        // Insert reservasi menggunakan prepared statement tanpa real_escape_string()
        $sql = "INSERT INTO reservasi (user_id, lapangan, tanggal, jam, nama_reservasi) 
                VALUES (:user_id, :lapangan, :tanggal, :jam, :nama_reservasi)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'lapangan' => $lapangan,
            'tanggal' => $tanggal,
            'jam' => $jam,
            'nama_reservasi' => $name
        ]);
        $reservasiIds[] = $conn->lastInsertId();
    }

    echo json_encode([
        'success' => 'Reservasi berhasil!',
        'reservasi_id' => $reservasiIds // array of id
    ]);
} elseif ($method === 'PUT') {
    // Proses PUT untuk memperbarui data reservasi
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['reservasi_id'])) {
        $id = intval($input['reservasi_id']);
        $fields = [];
        
        // Periksa data yang ingin diperbarui dan buat array untuk update
        if (isset($input['lapangan'])) $fields[] = "lapangan = :lapangan";
        if (isset($input['tanggal'])) $fields[] = "tanggal = :tanggal";
        if (isset($input['jam'])) $fields[] = "jam = :jam";
        if (isset($input['status'])) $fields[] = "status = :status";
        
        // Jika tidak ada data yang diubah, kembalikan pesan error
        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Tidak ada data yang diubah']);
            exit;
        }

        // Bangun query UPDATE
        $sql = "UPDATE reservasi SET " . implode(', ', $fields) . " WHERE reservasi_id = :id";
        
        // Persiapkan dan eksekusi query dengan parameter binding
        $stmt = $conn->prepare($sql);
        
        // Binding parameter
        $params = ['id' => $id];
        if (isset($input['lapangan'])) $params['lapangan'] = $input['lapangan'];
        if (isset($input['tanggal'])) $params['tanggal'] = $input['tanggal'];
        if (isset($input['jam'])) $params['jam'] = $input['jam'];
        if (isset($input['status'])) $params['status'] = $input['status'];

        // Eksekusi query
        if ($stmt->execute($params)) {
            echo json_encode(['status' => true, 'message' => 'Reservasi diperbarui']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Gagal update']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => false, 'message' => 'ID reservasi wajib']);
    }
} elseif ($method === 'DELETE') {
    // Proses DELETE untuk menghapus data reservasi
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['reservasi_id'])) {
        $id = intval($input['reservasi_id']);
        
        // Cek apakah reservasi ada
        $sql = "SELECT * FROM reservasi WHERE reservasi_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reservation) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Reservasi tidak ditemukan']);
            exit;
        }

        // Hapus reservasi
        $sql = "DELETE FROM reservasi WHERE reservasi_id = :id";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute(['id' => $id])) {
            echo json_encode(['status' => true, 'message' => 'Reservasi berhasil dihapus']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Gagal menghapus reservasi']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => false, 'message' => 'ID reservasi wajib']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Metode tidak didukung']);
}
?>
