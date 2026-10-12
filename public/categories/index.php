<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/escape.php';
requireLogin();

$error = '';
$success = '';

// Tambah Kategori (Khusus Admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    requireRole('admin');
    $name = trim($_POST['name'] ?? '');
    if (empty($name)) {
        $error = 'Nama kategori musik wajib diisi.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (:name)');
        $stmt->execute(['name' => $name]);
        $success = 'Kategori instrumen berhasil ditambahkan!';
    }
}

// Ambil Kategori Beserta Jumlah Produk
$categories = $pdo->query('
    SELECT c.*, COUNT(p.id) AS total_items 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id 
    GROUP BY c.id 
    ORDER BY c.name ASC
')->fetchAll();

require_once __DIR__ . '/../../views/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h2>Kategori Alat Musik</h2>
</div>

<?php if ($error): ?><div style="background:#f8d7da;color:#721c24;padding:12px;border-radius:6px;margin-bottom:15px;"><?= e($error) ?></div><?php endif; ?>
<?php if ($success): ?><div style="background:#d4edda;color:#155724;padding:12px;border-radius:6px;margin-bottom:15px;"><?= e($success) ?></div><?php endif; ?>

<?php if (hasRole('admin')): ?>
<div style="background:white; padding:20px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.05); margin-bottom:20px;">
    <h3>Tambah Kategori Baru</h3>
    <form method="POST" style="display:flex; gap:12px; margin-top:10px;">
        <input type="hidden" name="action" value="add">
        <input type="text" name="name" placeholder="Misal: Gitar Elektrik, Keyboard, Perkusi" required style="flex:1; padding:10px; border:1px solid #ccc; border-radius:6px;">
        <button type="submit" style="background:#52796f; color:white; padding:10px 20px; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">Simpan Kategori</button>
    </form>
</div>
<?php endif; ?>

<div style="background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 4px rgba(0,0,0,0.05);">
    <table style="width:100%; border-collapse:collapse; text-align:left;">
        <thead>
            <tr style="background:#cad2c5; color:#2f3e46;">
                <th style="padding:12px;">ID</th>
                <th style="padding:12px;">Nama Kategori Instrumen</th>
                <th style="padding:12px;">Jumlah Produk Terdaftar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:12px;"><?= e($cat['id']) ?></td>
                    <td style="padding:12px; font-weight:bold; color:#354f52;"><?= e($cat['name']) ?></td>
                    <td style="padding:12px;"><span style="background:#84a98c; color:white; padding:3px 8px; border-radius:12px; font-size:0.85rem; font-weight:bold;"><?= e($cat['total_items']) ?> produk</span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>ssss