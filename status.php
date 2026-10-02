<?php
require 'config.php';
header('Content-Type: application/json');
header('Cache-Control: no-store');
echo json_encode([
    'loggedIn' => !empty($_SESSION['uid']),
    'username' => $_SESSION['username'] ?? null,
    'csrf'     => csrf_token(),
]);
