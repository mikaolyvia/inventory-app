<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/validation.php';
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $type = $_POST['type'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 0);
    $note = trim($_POST['note'] ?? '');

    // Validasi input
    if ($quantity <= 0) {
        $error = 'Jumlah barang harus berupa angka positif lebih dari 0!';
    } elseif (!in_array($type, ['IN', 'OUT'])) {
        $error = 'Tipe transaksi tidak valid!';
    } else {
        try {
            // DATABASE TRANSACTION (Semua berhasil atau semua dibatalkan)
            $pdo->beginTransaction();

            // Kunci baris dengan FOR UPDATE agar konsisten
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id FOR UPDATE");
            $stmt->execute(['id' => $product_id]);
            $currentStock = $stmt->fetchColumn();

            if ($currentStock === false) {
                throw new Exception("Produk tidak ditemukan.");
            }

            // Validasi business logic: Barang keluar tidak boleh melebihi stok yang ada
            if ($type === 'OUT' && $quantity > $currentStock) {
                throw new Exception("Stok tidak mencukupi! Sisa stok saat ini hanya: " . $currentStock);
            }

            // Hitung stok baru
            $newStock = ($type === 'IN') ? $currentStock + $quantity : $currentStock - $quantity;

            // 1. Update stok di tabel products
            $updateStmt = $pdo->prepare("UPDATE products SET stock = :stock WHERE id = :id");
            $updateStmt->execute(['stock' => $newStock, 'id' => $product_id]);

            // 2. Catat histori pergerakan di tabel stock_movements
            $logStmt = $pdo->prepare("INSERT INTO stock_movements (product_id, user_id, type, quantity, note) VALUES (:p_id, :u_id, :type, :qty, :note)");
            $logStmt->execute([
                'p_id' => $product_id,
                'u_id' => currentUser()['id'],
                'type' => $type,
                'qty' => $quantity,
                'note' => $note
            ]);

            // Simpan perubahan permanen
            $pdo->commit();
            $success = "Transaksi mutasi stok berhasil disimpan!";
        } catch (Exception $e) {
            // Batalkan semua perubahan jika terjadi kesalahan
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}

// Ambil daftar produk dan 10 histori transaksi terakhir
$productList = $pdo->query("SELECT id, name, sku, stock FROM products")->fetchAll();
$history = $pdo->query("
    SELECT sm.*, p.name AS product_name, p.sku, u.username 
    FROM stock_movements sm 
    JOIN products p ON p.id = sm.product_id 
    JOIN users u ON u.id = sm.user_id 
    ORDER BY sm.id DESC LIMIT 10
")->fetchAll();

include __DIR__ . '/../../views/header.php';
?>

<h1>Pencatatan Barang Masuk & Keluar</h1>

<?php if ($error): ?>
    <div class="card" style="border-left: 5px solid var(--danger); color: var(--danger);"><?= e($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="card" style="border-left: 5px solid var(--primary-dark); color: var(--primary-dark);"><?= e($success) ?></div>
<?php endif; ?>

<div class="card">
    <h3>Input Mutasi Stok</h3>
    <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        <div>
            <label style="display:block; margin-bottom:5px;">Pilih Produk Musik:</label>
            <select name="product_id" required style="width: 100%; padding: 8px;">
                <?php foreach ($productList as $item): ?>
                    <option value="<?= e($item['id']) ?>"><?= e($item['name']) ?> (Stok: <?= e($item['stock']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="display:block; margin-bottom:5px;">Jenis Transaksi:</label>
            <select name="type" required style="width: 100%; padding: 8px;">
                <option value="IN">Barang Masuk (IN)</option>
                <option value="OUT">Barang Keluar (OUT)</option>
            </select>
        </div>
        <div>
            <label style="display:block; margin-bottom:5px;">Jumlah Kuantitas:</label>
            <input type="number" name="quantity" min="1" required style="width: 100%; padding: 8px;">
        </div>
        <div>
            <label style="display:block; margin-bottom:5px;">Catatan:</label>
            <input type="text" name="note" placeholder="Contoh: Pengadaan stok, Penjualan toko" style="width: 100%; padding: 8px;">
        </div>
        <div style="grid-column: 1 / -1;">
            <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
        </div>
    </form>
</div>

<div class="card">
    <h3>10 Riwayat Mutasi Terakhir</h3>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>SKU</th>
                <th>Produk</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Dicatat Oleh</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($history)): ?>
                <tr><td colspan="7" style="text-align: center;">Belum ada riwayat transaksi mutasi.</td></tr>
            <?php else: ?>
                <?php foreach ($history as $h): ?>
                <tr>
                    <td><?= e($h['created_at']) ?></td>
                    <td><?= e($h['sku']) ?></td>
                    <td><?= e($h['product_name']) ?></td>
                    <td>
                        <span class="badge <?= $h['type'] === 'IN' ? 'badge-safe' : 'badge-low' ?>">
                            <?= e($h['type']) ?>
                        </span>
                    </td>
                    <td><?= e($h['quantity']) ?></td>
                    <td><?= e($h['username']) ?></td>
                    <td><?= e($h['note'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../../views/footer.php'; ?>