<?php
// Mulai session jika belum berjalan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user']);
}

// Membatasi akses: wajib login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit;
    }
}

// Membatasi akses berdasarkan role (admin/staff)
function requireRole($role) {
    requireLogin();
    if ($_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        echo "<h1 style='color:#e76f51; text-align:center; margin-top:50px;'>403 Forbidden: Akses Ditolak</h1>";
        echo "<p style='text-align:center;'>Hanya role <b>" . htmlspecialchars($role) . "</b> yang dapat mengakses halaman ini.</p>";
        echo "<p style='text-align:center;'><a href='../dashboard.php'>Kembali ke Dashboard</a></p>";
        exit;
    }
}

// Ambil data user yang sedang login saat ini
function currentUser() {
    return $_SESSION['user'] ?? null;
}