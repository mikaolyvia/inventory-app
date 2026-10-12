# Sistem Inventaris Alat Musik (Inventory Management App)

Aplikasi inventaris berbasis web untuk toko alat musik, dibangun menggunakan PHP Native (PDO) dan MySQL dengan arsitektur modular yang rapi.

## Fitur Utama

- **Autentikasi & Otorisasi:** Sistem login multi-role (Admin dan Staff).
- **Katalog Produk:** Pencarian (Search), Filter Kategori, Pengurutan (Sorting), dan Pembagian Halaman (Pagination).
- **Manajemen Kategori & Supplier:** Kelola data master kategori alat musik dan distributor/supplier.
- **Relasi Many-to-Many:** Pemetaan produk dan supplier beserta harga beli modal.
- **Mutasi Stok (Stock Movement):** Pencatatan barang masuk (IN) dan keluar (OUT) dengan riwayat mutasi otomatis.
- **Business Rules:**
  - Alert stok menipis (stok <= 5 unit).
  - Produk yang sudah memiliki riwayat mutasi stok tidak dapat dihapus sembarangan.
  - Akses penambahan dan penghapusan data master dibatasi hanya untuk Admin.

## Akun Demo

| Role  | Username | Password |
| ----- | -------- | -------- |
| Admin | admin    | admin123 |
| Staff | staff    | staff123 |

## Cara Instalasi

1. Clone repositori ini atau ekstrak folder ke `xampp/htdocs/inventory-app`.
2. Impor basis data MySQL `inventory_musik.sql` melalui phpMyAdmin.
3. Pastikan konfigurasi basis data di `config/database.php` sesuai dengan port dan kredensial lokal XAMPP.
4. Akses aplikasi melalui browser di `http://localhost/inventory-app/public/login.php`.
