<?php
session_start();  // Memulai session

// Hapus semua data dalam sesi
session_unset();  

// Hancurkan sesi
session_destroy();  

// Redirect ke halaman login setelah logout
header('Location: index.html');
exit();
?>
