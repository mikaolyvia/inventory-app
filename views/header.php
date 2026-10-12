<?php
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/escape.php';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Alat Musik</title>
    <style>
        :root {
            --bg-body: #f4f7f4;
            --primary-soft: #84a98c;
            --primary-dark: #52796f;
            --primary-light: #cad2c5;
            --text-dark: #2f3e46;
            --white: #ffffff;
            --danger: #e76f51;
            --warning: #e9c46a;
        }
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { margin: 0; background-color: var(--bg-body); color: var(--text-dark); display: flex; min-height: 100vh; }
        
        /* Sidebar Hijau Soft */
        .sidebar { width: 250px; background-color: var(--primary-dark); color: var(--white); padding: 25px 15px; display: flex; flex-direction: column; }
        .sidebar h2 { margin-top: 0; font-size: 1.25rem; border-bottom: 1px solid var(--primary-soft); padding-bottom: 12px; }
        .sidebar a { color: var(--primary-light); text-decoration: none; padding: 10px 14px; margin-bottom: 6px; border-radius: 6px; display: block; transition: 0.2s; }
        .sidebar a:hover { background-color: var(--primary-soft); color: var(--white); }
        .sidebar .logout { margin-top: auto; background-color: rgba(231,111,81,0.2); }
        
        /* Konten Utama */
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .card { background: var(--white); border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); margin-bottom: 25px; }
        .grid-dashboard { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; margin-bottom: 25px; }
        .stat-card { background: var(--white); padding: 18px; border-radius: 8px; border-left: 5px solid var(--primary-soft); box-shadow: 0 2px 4px rgba(0,0,0,0.04); }
        .stat-card h3 { margin: 0; font-size: 0.85rem; color: #7f8c8d; text-transform: uppercase; }
        .stat-card .val { font-size: 1.6rem; font-weight: bold; margin-top: 8px; color: var(--primary-dark); }
        
        /* Tabel & Tombol */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #edf2f4; }
        table th { background-color: var(--bg-body); color: var(--text-dark); }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .badge-safe { background: #d8f3dc; color: #2d6a4f; }
        .badge-low { background: #ffe5d9; color: var(--danger); }
        .btn { padding: 8px 14px; border-radius: 6px; text-decoration: none; border: none; cursor: pointer; display: inline-block; font-size: 0.9rem; }
        .btn-primary { background: var(--primary-soft); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-danger { background: var(--danger); color: white; }
        .filter-box { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px; }
        .filter-box input, .filter-box select { padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>🎵 Inventaris Musik</h2>
        <a href="/inventory-app/public/dashboard.php">📊 Dashboard</a>
        <a href="/inventory-app/public/products/index.php">🎸 Daftar Produk</a>
        <a href="/inventory-app/public/categories/index.php">📁 Kategori Musik</a>
        <a href="/inventory-app/public/suppliers/index.php">🚚 Supplier / Distributor</a>
        <a href="/inventory-app/public/product_suppliers/index.php">🔗 Produk & Supplier</a>
        <a href="/inventory-app/public/stock/index.php">🔄 Transaksi Stok</a>
        
        <div style="font-size: 0.85rem; padding: 10px 14px; color: var(--primary-light); margin-top: 15px;">
            Login: <b><?= e($user['username'] ?? '') ?></b> (<?= e($user['role'] ?? '') ?>)
        </div>
        <a href="/inventory-app/public/logout.php" class="logout">Logout</a>
    </div>
    <div class="main-content">