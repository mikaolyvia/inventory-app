<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/escape.php';
session_start();

// Jika sudah login, langsung lempar ke dashboard
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Prepared statement untuk mencegah SQL Injection
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Verifikasi password hash
    if ($user && password_verify($password, $user['password_hash'])) {
        // Regenerasi session ID untuk mencegah session fixation
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = 'Username atau kata sandi tidak valid!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Inventaris Toko Musik</title>
    <style>
        body { background: #cad2c5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; font-family: sans-serif; }
        .login-box { background: white; padding: 30px; border-radius: 10px; width: 330px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .login-box h2 { text-align: center; color: #52796f; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #2f3e46; font-size: 0.9rem; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn-submit { width: 100%; padding: 10px; background: #84a98c; border: none; color: white; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .btn-submit:hover { background: #52796f; }
        .error { color: #e76f51; font-size: 0.85rem; margin-bottom: 12px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Masuk Sistem</h2>
        <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autocomplete="off" placeholder="Contoh: admin / staff">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Ketik kata sandi...">
            </div>
            <button type="submit" class="btn-submit">Masuk</button>
        </form>
    </div>
</body>
</html>