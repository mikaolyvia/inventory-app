<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/escape.php';
requireLogin();

$error = '';
$success = '';

// Tambah Supplier (Khusus Admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    requireRole('admin');
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($name)) {
        $error = 'Nama supplier distributor musik wajib diisi.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO suppliers (name, phone, address) VALUES (:name, :phone, :address)');
        $stmt->execute(['name' => $name, 'phone' => $phone, 'address' => $address]);
        $success = 'Data supplier berhasil ditambahkan!';
    }
}

// Ambil Seluruh Data Supplier
$suppliers = $pdo->query('SELECT * FROM suppliers ORDER BY id DESC')->fetchAll();

require_once __DIR__ . '/../../views/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h2>Kelola Distributor & Supplier Alat Musik</h2>
</div>

<?php if ($error): ?><div style="background:#f8d7da;color:#721c24;padding:12px;border-radius:6px;margin-bottom:15px;"><?= e($error) ?></div><?php endif; ?>
<?php if ($success): ?><div style="background:#d4edda;color:#155724;padding:12px;border-radius:6px;margin-bottom:15px;"><?= e($success) ?></div><?php endif; ?>

<?php if (hasRole('admin')): ?>
<div style="background:white; padding:20px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.05); margin-bottom:20px;">
    <h3 style="margin-bottom:12px;">Tambah Supplier Baru</h3>
    <form method="POST" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 120px; gap:12px; align-items:end;">
        <input type="hidden" name="action" value="add">
        <div>
            <label style="font-size:0.85rem; font-weight:bold; color:#2f3e46;">Nama Distributor</label>
            <input type="text" name="name" required placeholder="Misal: PT Yamaha Music Indo" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
        </div>
        <div>
            <label style="font-size:0.85rem; font-weight:bold; color:#2f3e46;">Nomor Telepon</label>
            <input type="text" name="phone" placeholder="0812xxxxxxx" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
        </div>
        <div>
            <label style="font-size:0.85rem; font-weight:bold; color:#2f3e46;">Alamat</label>
            <input type="text" name="address" placeholder="Kota / Alamat gudang" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
        </div>
        <button type="submit" style="background:#52796f; color:white; padding:10px; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">Simpan</button>
    </form>
</div>
<?php endif; ?>

<div style="background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 4px rgba(0,0,0,0.05);">
    <table style="width:100%; border-collapse:collapse; text-align:left;">
        <thead>
            <tr style="background:#cad2c5; color:#2f3e46;">
                <th style="padding:12px;">ID</th>
                <th style="padding:12px;">Nama Supplier</th>
                <th style="padding:12px;">Kontak / Telepon</th>
                <th style="padding:12px;">Alamat</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($suppliers)): ?>
                <tr><td colspan="4" style="padding:20px; text-align:center; color:#888;">Belum ada supplier yang terdaftar.</td></tr>
            <?php else: ?>
                <?php foreach ($suppliers as $s): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:12px;"><?= e($s['id']) ?></td>
                        <td style="padding:12px; font-weight:bold; color:#354f52;"><?= e($s['name']) ?></td>
                        <td style="padding:12px;"><?= e($s['phone'] ?? '-') ?></td>
                        <td style="padding:12px;"><?= e($s['address'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>