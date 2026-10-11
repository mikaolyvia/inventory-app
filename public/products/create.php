<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/validation.php';

// Membatasi akses: Hanya role admin yang boleh menambah produk
requireRole('admin');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int)($_POST['category_id'] ?? 0);
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $price = $_POST['price'] ?? 0;
    $stock = (int)($_POST['stock'] ?? 0);
    $minStock = (int)($_POST['minimum_stock'] ?? 5);

    $priceError = validatePrice($price);

    if (empty($sku) || empty($name)) {
        $error = 'SKU dan Nama Produk wajib diisi!';
    } elseif ($priceError) {
        $error = $priceError;
    } else {
        try {
            // Prepared statement insert produk
            $stmt = $pdo->prepare("INSERT INTO products (category_id, sku, name, price, stock, minimum_stock) VALUES (:cat, :sku, :name, :price, :stock, :min)");
            $stmt->execute([
                'cat' => $category_id,
                'sku' => $sku,
                'name' => $name,
                'price' => $price,
                'stock' => $stock,
                'min' => $minStock
            ]);
            header("Location: index.php");
            exit;
        } catch (\PDOException $e) {
            // Menangkap jika ada duplikasi SKU unik
            if ($e->getCode() == 23000) {
                $error = 'SKU tersebut sudah terdaftar! Gunakan kode SKU lain.';
            } else {
                $error = 'Terjadi kesalahan: ' . $e->getMessage();
            }
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
include __DIR__ . '/../../views/header.php';
?>

<h1>Tambah Produk Baru</h1>

<?php if ($error): ?>
    <div class="card" style="border-left: 5px solid var(--danger); color: var(--danger);"><?= e($error) ?></div>
<?php endif; ?>

<div class="card" style="max-width: 600px;">
    <form method="POST">
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px;">Kategori:</label>
            <select name="category_id" required style="width:100%; padding:8px;">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat['id']) ?>"><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px;">Kode SKU (Harus Unik):</label>
            <input type="text" name="sku" required placeholder="Contoh: GTR-001" style="width:100%; padding:8px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px;">Nama Produk:</label>
            <input type="text" name="name" required placeholder="Contoh: Gitar Akustik Yamaha" style="width:100%; padding:8px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px;">Harga (Rp):</label>
            <input type="number" step="1000" name="price" required min="0" style="width:100%; padding:8px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px;">Stok Awal:</label>
            <input type="number" name="stock" value="0" min="0" style="width:100%; padding:8px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px;">Batas Minimum Stok:</label>
            <input type="number" name="minimum_stock" value="5" min="1" style="width:100%; padding:8px;">
        </div>
        <button type="submit" class="btn btn-primary">Simpan Produk</button>
        <a href="index.php" class="btn" style="background:#ccc; color:#333;">Batal</a>
    </form>
</div>

<?php include __DIR__ . '/../../views/footer.php'; ?>