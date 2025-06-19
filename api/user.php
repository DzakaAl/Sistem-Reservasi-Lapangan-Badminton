<?php
header('Content-Type: application/json');
require_once 'db.php';

function getInput() {
    return json_decode(file_get_contents('php://input'), true);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Ambil semua user atau user tertentu jika ada ?id=
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT user_id, username, email, no_tlp, role FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($data ?: []);
    } else {
        $stmt = $conn->query("SELECT user_id, username, email, no_tlp, role FROM users ORDER BY user_id DESC");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }
} elseif ($method === 'PUT') {
    // Update data user
    $input = getInput();
    if (isset($input['user_id'])) {
        $id = intval($input['user_id']);
        $fields = [];
        $params = [];
        if (isset($input['username'])) {
            $fields[] = "username = ?";
            $params[] = $input['username'];
        }
        if (isset($input['email'])) {
            $fields[] = "email = ?";
            $params[] = $input['email'];
        }
        if (isset($input['no_tlp'])) {
            $fields[] = "no_tlp = ?";
            $params[] = $input['no_tlp'];
        }
        if (isset($input['role'])) {
            $fields[] = "role = ?";
            $params[] = $input['role'];
        }
        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Tidak ada data diubah']);
            exit;
        }
        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute($params)) {
            echo json_encode(['status' => true, 'message' => 'User diperbarui']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Gagal update']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => false, 'message' => 'ID user wajib']);
    }
} elseif ($method === 'DELETE') {
    // Hapus user
    $input = getInput();
    if (isset($input['user_id'])) {
        $id = intval($input['user_id']);
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        if ($stmt->execute([$id])) {
            echo json_encode(['status' => true, 'message' => 'User dihapus']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Gagal menghapus user']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => false, 'message' => 'ID user wajib']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Metode tidak diizinkan']);
}
?>