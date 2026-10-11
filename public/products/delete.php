<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';

// Membatasi akses: Hanya role admin yang boleh menghapus produk
requireRole('admin');

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
    } catch (\PDOException $e) {
        // Melindungi integritas data jika produk sudah ada riwayat transaksi
        die("<h3 style='color:red;'>Gagal menghapus: Produk ini sudah memiliki histori transaksi stok dan tidak boleh dihapus demi menjaga integritas data.</h3><p><a href='index.php'>Kembali ke Daftar Produk</a></p>");
    }
}

header("Location: index.php");
exit;