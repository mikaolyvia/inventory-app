<?php
// Pengaturan koneksi database MySQL menggunakan PDO
$host = 'localhost';
$db   = 'inventory_musik'; // Sesuai nama database di phpMyAdmin Anda
$user = 'root';
$pass = ''; // Default XAMPP biasanya kosong
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lempar Exception jika terjadi error query
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Ambil data sebagai array asosiatif
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Gunakan prepared statement native MySQL
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}