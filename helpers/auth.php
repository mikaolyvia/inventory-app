<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['user']);
}

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header("Location: /inventory-app/public/login.php");
        exit;
    }
}

function hasRole(string $role): bool {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $role;
}

function requireRole(string $role): void {
    requireLogin();
    if (!hasRole($role)) {
        http_response_code(403);
        echo "<h1 style='font-family:sans-serif; text-align:center; margin-top:50px;'>403 - Akses Ditolak</h1>";
        echo "<p style='font-family:sans-serif; text-align:center;'>Halaman ini hanya dapat diakses oleh " . htmlspecialchars($role) . ".</p>";
        exit;
    }
}