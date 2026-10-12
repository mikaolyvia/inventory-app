<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/escape.php';
requireLogin();

// Pengaturan Pencarian, Filter Kategori, Pengurutan, dan Pagination
$search = trim($_GET['search'] ?? '');
$category_id = trim($_GET['category_id'] ?? '');
$sort = $_GET['sort'] ?? 'name_asc';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Susun Query Dasar
$where = [];
$params = [];

if ($search !== '') {
    $where[] = '(p.name LIKE :search1 OR p.sku LIKE :search2)';
    $params['search1'] = "%$search%";
    $params['search2'] = "%$search%";
}

if ($category_id !== '') {
    $where[] = 'p.category_id = :category_id';
    $params['category_id'] = $category_id;
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Pengurutan
$orderSql = match($sort) {
    'name_desc' => 'ORDER BY p.name DESC',
    'price_asc' => 'ORDER BY p.price ASC',
    'price_desc' => 'ORDER BY p.price DESC',
    'stock_asc' => 'ORDER BY p.stock ASC',
    'stock_desc' => 'ORDER BY p.stock DESC',
    default => 'ORDER BY p.name ASC',
};

// Hitung Total Data
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products p $whereSql");
$countStmt->execute($params);
$totalProducts = (int)$countStmt->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

// Ambil Data Produk
$dataStmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    $whereSql 
    $orderSql 
    LIMIT :limit OFFSET :offset
");

foreach ($params as $key => $val) {
    $dataStmt->bindValue(":$key", $val);
}
$dataStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$dataStmt->execute();
$products = $dataStmt->fetchAll();

// Ambil Seluruh Kategori untuk Pilihan Filter
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

require_once __DIR__ . '/../../views/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h2>Katalog Instrumen & Alat Musik</h2>
    <?php if (hasRole('admin')): ?>
        <a href="/inventory-app/public/products/create.php" style="background:#52796f; color:white; padding:10px 16px; border-radius:6px; text-decoration:none; font-weight:bold;">+ Tambah Produk Musik</a>
    <?php endif; ?>
</div>

<!-- Form Pencarian & Filter -->
<div style="background:white; padding:20px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.05); margin-bottom:20px;">
    <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap;">
        <input type="text" name="search" placeholder="Cari nama alat musik / SKU..." value="<?= e($search) ?>" style="flex:1; min-width:200px; padding:10px; border:1px solid #ccc; border-radius:6px;">
        
        <select name="category_id" style="padding:10px; border:1px solid #ccc; border-radius:6px;">
            <option value="">Semua Kategori</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="sort" style="padding:10px; border:1px solid #ccc; border-radius:6px;">
            <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Nama (A - Z)</option>
            <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Nama (Z - A)</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Harga Terendah</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Harga Tertinggi</option>
            <option value="stock_asc" <?= $sort === 'stock_asc' ? 'selected' : '' ?>>Stok Paling Sedikit</option>
            <option value="stock_desc" <?= $sort === 'stock_desc' ? 'selected' : '' ?>>Stok Terbanyak</option>
        </select>

        <button type="submit" style="background:#354f52; color:white; padding:10px 20px; border:none; border-radius:6px; cursor:pointer; font-weight:bold;">Terapkan</button>
        <a href="/inventory-app/public/products/index.php" style="background:#cad2c5; color:#2f3e46; padding:10px 16px; border-radius:6px; text-decoration:none; font-weight:bold;">Reset</a>
    </form>
</div>

<!-- Tabel Data Produk -->
<div style="background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 4px rgba(0,0,0,0.05);">
    <table style="width:100%; border-collapse:collapse; text-align:left;">
        <thead>
            <tr style="background:#cad2c5; color:#2f3e46;">
                <th style="padding:12px;">SKU</th>
                <th style="padding:12px;">Nama Produk</th>
                <th style="padding:12px;">Kategori</th>
                <th style="padding:12px;">Harga Satuan</th>
                <th style="padding:12px;">Sisa Stok</th>
                <?php if (hasRole('admin')): ?>
                    <th style="padding:12px; text-align:center;">Aksi</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="6" style="padding:20px; text-align:center; color:#888;">Tidak ada produk musik yang sesuai.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:12px; font-weight:bold; color:#52796f;"><?= e($p['sku']) ?></td>
                        <td style="padding:12px;"><?= e($p['name']) ?></td>
                        <td style="padding:12px;"><?= e($p['category_name'] ?? 'Umum') ?></td>
                        <td style="padding:12px;">Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
                        <td style="padding:12px;">
                            <?php 
                            $minStock = $p['min_stock'] ?? 5;
                            if ($p['stock'] <= $minStock): 
                            ?>
                                <span style="background:#e76f51; color:white; padding:4px 8px; border-radius:4px; font-weight:bold; font-size:0.85rem;"><?= e($p['stock']) ?> (Menipis)</span>
                            <?php else: ?>
                                <span style="background:#84a98c; color:white; padding:4px 8px; border-radius:4px; font-weight:bold; font-size:0.85rem;"><?= e($p['stock']) ?> unit</span>
                            <?php endif; ?>
                        </td>
                        <?php if (hasRole('admin')): ?>
                            <td style="padding:12px; text-align:center;">
                                <a href="delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus instrumen ini?')" style="color:#e76f51; font-weight:bold; text-decoration:none; padding:4px 8px; border:1px solid #e76f51; border-radius:4px;">Hapus</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Navigasi Pagination -->
<?php if ($totalPages > 1): ?>
    <div style="display:flex; justify-content:center; gap:8px; margin-top:20px;">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category_id=<?= urlencode($category_id) ?>&sort=<?= urlencode($sort) ?>" 
               style="padding:8px 14px; border-radius:4px; text-decoration:none; font-weight:bold; <?= $i === $page ? 'background:#52796f; color:white;' : 'background:white; color:#52796f; border:1px solid #52796f;' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>