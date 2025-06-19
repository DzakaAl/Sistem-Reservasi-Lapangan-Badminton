<?php
session_start();

// Cek apakah user_id ada di sesi
if (isset($_SESSION['user_id'])) {
    echo json_encode(['loggedIn' => true]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>
