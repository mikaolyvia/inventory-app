<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/escape.php';
requireLogin();

$error = '';
$success = '';

// Tambah Relasi Produk & Supplier (Khusus Admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'link') {
    requireRole('admin');
    $product_id = (int)($_POST['product_id'] ?? 0);
    $supplier_id = (int)($_POST['supplier_id'] ?? 0);
    $purchase_price = (float)($_POST['purchase_price'] ?? 0);

    if ($product_id <= 0 || $supplier_id <= 0 || $purchase_price < 0) {
        $error = 'Pilih produk, supplier, dan isi harga beli dengan benar (tidak boleh negatif).';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO product_suppliers (product_id, supplier_id, purchase_price) VALUES (:p, :s, :price)');
            $stmt->execute([
                'p' => $product_id,
                's' => $supplier_id,
                'price' => $purchase_price
            ]);
            $success = 'Hubungan produk dan supplier berhasil disimpan!';
        } catch (PDOException $e) {
            $error = 'Produk sudah terhubung dengan supplier ini sebelumnya.';
        }
    }
}

// Hapus Relasi
if (isset($_GET['delete_id'])) {
    requireRole('admin');
    $delId = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare('DELETE FROM product_suppliers WHERE id = :id');
    $stmt->execute(['id' => $delId]);
    header("Location: index.php");
    exit;
}

// Ambil Seluruh Relasi Many-to-Many
$relations = $pdo->query('
    SELECT ps.id, p.name AS product_name, p.sku, s.name AS supplier_name, ps.purchase_price
    FROM product_suppliers ps
    JOIN products p ON ps.product_id = p.id
    JOIN suppliers s ON ps.supplier_id = s.id
    ORDER BY ps.id DESC
')->fetchAll();

$productsList = $pdo->query('SELECT id, name, sku FROM products ORDER BY name ASC')->fetchAll();
$suppliersList = $pdo->query('SELECT id, name FROM suppliers ORDER BY name ASC')->fetchAll();

require_once __DIR__ . '/../../views/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h2>Hubungan Produk & Supplier (Many-to-Many)</h2>
</div>

<?php if ($error): ?><div style="background:#f8d7da;color:#721c24;padding:12px;border-radius:6px;margin-bottom:15px;"><?= e($error) ?></div><?php endif; ?>
<?php if ($success): ?><div style="background:#d4edda;color:#155724;padding:12px;border-radius:6px;margin-bottom:15px;"><?= e($success) ?></div><?php endif; ?>

<?php if (hasRole('admin')): ?>
<div style="background:white; padding:20px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.05); margin-bottom:20px;">
    <h3 style="margin-bottom:12px;">Tetapkan Supplier untuk Produk</h3>
    <form method="POST" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 120px; gap:12px; align-items:end;">
        <input type="hidden" name="action" value="link">
        <div>
            <label style="font-size:0.85rem; font-weight:bold;">Pilih Alat Musik</label>
            <select name="product_id" required style="width:100%; padding:9px; border:1px solid #ccc; border-radius:6px;">
                <option value="">-- Pilih Produk --</option>
                <?php foreach ($productsList as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= e($p['name']) ?> (<?= e($p['sku']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="font-size:0.85rem; font-weight:bold;">Pilih Supplier</label>
            <select name="supplier_id" required style="width:100%; padding:9px; border:1px solid #ccc; border-radius:6px;">
                <option value="">-- Pilih Supplier --</option>
                <?php foreach ($suppliersList as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="font-size:0.85rem; font-weight:bold;">Harga Beli / Pasokan (Rp)</label>
            <input type="number" name="purchase_price" min="0" required placeholder="0" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
        </div>
        <button type="submit" style="background:#52796f; color:white; padding:10px; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">Hubungkan</button>
    </form>
</div>
<?php endif; ?>

<div style="background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 4px rgba(0,0,0,0.05);">
    <table style="width:100%; border-collapse:collapse; text-align:left;">
        <thead>
            <tr style="background:#cad2c5; color:#2f3e46;">
                <th style="padding:12px;">SKU</th>
                <th style="padding:12px;">Nama Produk</th>
                <th style="padding:12px;">Nama Supplier</th>
                <th style="padding:12px;">Harga Beli</th>
                <?php if (hasRole('admin')): ?>
                    <th style="padding:12px; text-align:center;">Aksi</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($relations)): ?>
                <tr><td colspan="5" style="padding:20px; text-align:center; color:#888;">Belum ada pemetaan produk dengan supplier.</td></tr>
            <?php else: ?>
                <?php foreach ($relations as $r): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:12px; font-weight:bold; color:#52796f;"><?= e($r['sku']) ?></td>
                        <td style="padding:12px;"><?= e($r['product_name']) ?></td>
                        <td style="padding:12px; font-weight:bold; color:#354f52;"><?= e($r['supplier_name']) ?></td>
                        <td style="padding:12px;">Rp <?= number_format($r['purchase_price'], 0, ',', '.') ?></td>
                        <?php if (hasRole('admin')): ?>
                            <td style="padding:12px; text-align:center;">
                                <a href="index.php?delete_id=<?= $r['id'] ?>" onclick="return confirm('Apakah yakin ingin memutus relasi ini?')" style="color:#e76f51; text-decoration:none; font-weight:bold; border:1px solid #e76f51; padding:4px 8px; border-radius:4px;">Hapus</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>