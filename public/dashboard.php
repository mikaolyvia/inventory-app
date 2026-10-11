<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';
requireLogin();

// Fungsi Agregat (COUNT, SUM)
$totalProducts = $pdo->query("SELECT COUNT(id) FROM products")->fetchColumn();
$totalValue = $pdo->query("SELECT COALESCE(SUM(stock * price), 0) FROM products")->fetchColumn();
$lowStock = $pdo->query("SELECT COUNT(id) FROM products WHERE stock <= minimum_stock")->fetchColumn();

// Total Mutasi In & Out
$movements = $pdo->query("SELECT 
    COALESCE(SUM(CASE WHEN type = 'IN' THEN quantity ELSE 0 END), 0) AS total_in,
    COALESCE(SUM(CASE WHEN type = 'OUT' THEN quantity ELSE 0 END), 0) AS total_out
FROM stock_movements")->fetch();

include __DIR__ . '/../views/header.php';
?>

<h1>Dashboard Inventaris</h1>

<div class="grid-dashboard">
    <div class="stat-card">
        <h3>Total Produk</h3>
        <div class="val"><?= e($totalProducts) ?></div>
    </div>
    <div class="stat-card">
        <h3>Nilai Inventaris</h3>
        <div class="val">Rp <?= number_format($totalValue, 0, ',', '.') ?></div>
    </div>
    <div class="stat-card" style="border-left-color: var(--danger);">
        <h3>Stok Rendah</h3>
        <div class="val" style="color: var(--danger);"><?= e($lowStock) ?></div>
    </div>
    <div class="stat-card">
        <h3>Mutasi (In / Out)</h3>
        <div class="val"><?= e($movements['total_in']) ?> / <?= e($movements['total_out']) ?></div>
    </div>
</div>

<div class="card">
    <h3>Peringatan: Stok Menipis</h3>
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th>Sisa Stok</th>
                <th>Batas Minimum</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT sku, name, stock, minimum_stock FROM products WHERE stock <= minimum_stock");
            $lowList = $stmt->fetchAll();
            if (empty($lowList)): ?>
                <tr><td colspan="4" style="text-align:center;">Semua stok barang berada pada level aman.</td></tr>
            <?php else:
                foreach ($lowList as $row): ?>
                <tr>
                    <td><?= e($row['sku']) ?></td>
                    <td><?= e($row['name']) ?></td>
                    <td><span class="badge badge-low"><?= e($row['stock']) ?></span></td>
                    <td><?= e($row['minimum_stock']) ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../views/footer.php'; ?>