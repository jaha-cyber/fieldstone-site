<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.html');
csrf_check();

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$st = db()->prepare('SELECT id, username, password_hash FROM users WHERE username = ?');
$st->execute([$username]);
$user = $st->fetch();

// Always run password_verify so response time doesn't reveal whether the username exists.
$hash = $user['password_hash'] ?? password_hash('dummy-value', PASSWORD_DEFAULT);
$ok = password_verify($password, $hash) && $user;

if ($ok) {
    session_regenerate_id(true);
    $_SESSION['uid'] = (int)$user['id'];
    $_SESSION['username'] = $user['username'];
    if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
    }
    redirect('dashboard.php');
}

usleep(500000); // slow down guessing

redirect('index.html?msg=badlogin#login');
