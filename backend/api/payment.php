<?php
// backend/api/payment.php (Backend PHP untuk mengelola file upload dan data pembayaran)

// Untuk menerima POST, GET, DELETE request
header('Content-Type: application/json');
include_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

function uploadImage($image) {
    $targetDir = __DIR__ . "/uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $filename = uniqid('bukti_', true) . '.' . strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
    $targetFile = $targetDir . $filename;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $validExtensions = array("jpg", "jpeg", "png");

    if (!in_array($imageFileType, $validExtensions)) {
        return ["status" => false, "message" => "Hanya file gambar yang diperbolehkan"];
    }
    if ($image["size"] > 5000000) {
        return ["status" => false, "message" => "File terlalu besar"];
    }
    if (move_uploaded_file($image["tmp_name"], $targetFile)) {
        return ["status" => true, "file_path" => "uploads/" . $filename];
    } else {
        return ["status" => false, "message" => "Terjadi kesalahan saat mengupload gambar"];
    }
}

// Handle POST - Upload Bukti Pembayaran
if ($method === 'POST') {
    if (isset($_POST['reservasi_id']) && isset($_FILES['bukti_pembayaran'])) {
        $reservasi_id = $_POST['reservasi_id'];

        // Upload gambar bukti pembayaran
        $uploadResult = uploadImage($_FILES['bukti_pembayaran']);
        if (!$uploadResult['status']) {
            echo json_encode($uploadResult);
            exit;
        }

        try {
            $query = "INSERT INTO pembayaran (reservasi_id, bukti_pembayaran) VALUES (:reservasi_id, :bukti_pembayaran)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':reservasi_id', $reservasi_id, PDO::PARAM_INT);
            $stmt->bindParam(':bukti_pembayaran', $uploadResult['file_path'], PDO::PARAM_STR);
            $stmt->execute();

            echo json_encode([
                "status" => true,
                "message" => "Pembayaran berhasil diajukan",
                "file_path" => $uploadResult['file_path']
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                "status" => false,
                "message" => "Gagal menginput pembayaran: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Data tidak lengkap. Pastikan reservasi_id dan bukti_pembayaran ada"
        ]);
    }
}

// Handle GET - Ambil bukti pembayaran berdasarkan reservasi_id
elseif ($method === 'GET') {
    if (isset($_GET['reservasi_id'])) {
        $reservasi_id = $_GET['reservasi_id'];
        
        $sql = "SELECT * FROM pembayaran WHERE reservasi_id = :reservasi_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['reservasi_id' => $reservasi_id]);
        $pembayaran = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($pembayaran) {
            echo json_encode($pembayaran);
        } else {
            echo json_encode(["status" => false, "message" => "Pembayaran tidak ditemukan"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "reservasi_id tidak ditemukan"]);
    }
}

// Handle DELETE - Hapus bukti pembayaran berdasarkan ID
elseif ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['reservasi_id'])) {
        $reservasi_id = $input['reservasi_id'];

        // Cek apakah pembayaran ada
        $sql = "SELECT * FROM pembayaran WHERE reservasi_id = :reservasi_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['reservasi_id' => $reservasi_id]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$payment) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Pembayaran tidak ditemukan']);
            exit;
        }

        // Hapus file gambar bukti pembayaran
        $file_path = __DIR__ . '/' . $payment['bukti_pembayaran'];
        if (file_exists($file_path)) {
            unlink($file_path); // Menghapus file gambar
        }

        // Hapus data pembayaran dari database
        $sql = "DELETE FROM pembayaran WHERE reservasi_id = :reservasi_id";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute(['reservasi_id' => $reservasi_id])) {
            echo json_encode(['status' => true, 'message' => 'Bukti pembayaran berhasil dihapus']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Gagal menghapus bukti pembayaran']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => false, 'message' => 'ID reservasi wajib']);
    }
}
?>
