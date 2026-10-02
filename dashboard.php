<?php
require 'config.php';
if (empty($_SESSION['uid'])) {
    
    redirect('index.html?msg=signin#login');
}
$st = db()->prepare('SELECT username, email, created_at FROM users WHERE id = ?');
$st->execute([$_SESSION['uid']]);
$me = $st->fetch();
if (!$me) { session_destroy(); redirect('index.html'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard | Fieldstone</title>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<main class="wrap narrow">
  <a class="logo" href="index.html">Fieldstone</a>
  <div class="login">
    <h2>Welcome, <?= e($me['username']) ?></h2>
    <p class="sub">Email: <?= e($me['email']) ?><br>Member since <?= e(date('j F Y', strtotime($me['created_at']))) ?></p>
    <p>Your invoices and documents will appear here.</p>
    <form method="post" action="logout.php">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <button class="btn" type="submit">Sign out</button>
    </form>
  </div>
</main>
</body>
</html>
