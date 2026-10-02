<?php
require 'config.php';
$errors = [];
$username = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!preg_match('/^[A-Za-z0-9_]{3,40}$/', $username)) $errors[] = 'Username must be 3 to 40 letters, numbers or underscores.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))       $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8)                            $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm)                           $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $st = db()->prepare('SELECT 1 FROM users WHERE username = ? OR email = ?');
        $st->execute([$username, $email]);
        if ($st->fetch()) {
            $errors[] = 'That username or email is already registered.';
        } else {
            db()->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)')
                ->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
            
            redirect('index.html?msg=registered#login');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Create account | Fieldstone</title>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<main class="wrap narrow">
  <a class="logo" href="index.html">Fieldstone</a>
  <div class="login">
    <h2>Create account</h2>
    <?php foreach ($errors as $err): ?><p class="msg err"><?= e($err) ?></p><?php endforeach; ?>
    <form method="post" novalidate>
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="field"><label for="u">Username</label><input id="u" name="username" type="text" value="<?= e($username) ?>" autocomplete="username" required></div>
      <div class="field"><label for="e">Email</label><input id="e" name="email" type="email" value="<?= e($email) ?>" autocomplete="email" required></div>
      <div class="field"><label for="p">Password</label><input id="p" name="password" type="password" autocomplete="new-password" required></div>
      <div class="field"><label for="c">Confirm password</label><input id="c" name="confirm" type="password" autocomplete="new-password" required></div>
      <button class="btn" type="submit">Create account</button>
    </form>
    <p class="note">Already registered? <a href="index.html#login">Sign in</a></p>
  </div>
</main>
</body>
</html>
