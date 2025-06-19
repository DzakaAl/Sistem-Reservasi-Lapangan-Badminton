<?php
// db.php

require 'vendor/autoload.php';  // Mengimpor MongoDB PHP driver

// Mengonfigurasi URL MongoDB Atlas Anda
$mongoUri = "mongodb+srv://database:kingmordzz10@clusterbadminton.gph0l5s.mongodb.net/?retryWrites=true&w=majority&appName=ClusterBadminton";  // Ganti <username>, <password>, dan myDatabase dengan kredensial MongoDB Atlas Anda

try {
    // Membuat koneksi ke MongoDB
    $client = new MongoDB\Client($mongoUri);

    // Pilih database
    $db = $client->myDatabase;  // Ganti dengan nama database yang Anda inginkan

    // Menampilkan semua koleksi dalam database
    $collections = $db->listCollections();  // Mendapatkan semua koleksi dalam database

    echo "Koneksi ke MongoDB Atlas berhasil!<br>";
    echo "Daftar Koleksi:<br>";

    // Menampilkan nama-nama koleksi
    foreach ($collections as $collection) {
        echo $collection->getName() . "<br>";
    }

} catch (MongoDB\Driver\Exception\ConnectionException $e) {
    // Tangani kesalahan jika tidak dapat terhubung ke MongoDB
    echo "Gagal terhubung ke MongoDB Atlas: " . $e->getMessage();
}
?>
