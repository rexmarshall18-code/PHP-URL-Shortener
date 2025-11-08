<?php

$env = __DIR__.'/.env';
if (is_readable($env)) {
  foreach (file($env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if ($line[0] === '#') continue;
    [$k,$v] = array_map('trim', explode('=', $line, 2));
    $_ENV[$k] = $v;
  }
}

$driver  = $_ENV['DB_DRIVER'] ?? 'mysql';
$host    = $_ENV['DB_HOST'] ?? 'localhost';
$db      = $_ENV['DB_NAME'] ?? 'short-urls';
$user    = $_ENV['DB_USER'] ?? 'root';
$pass    = $_ENV['DB_PASS'] ?? '';
$baseUrl = rtrim($_ENV['BASE_URL'] ?? 'http://localhost/short-urls', '/');

try {
  if ($driver === 'pgsql') {
    $conn = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
  } else {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
  }
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("DB error: ".$e->getMessage()); }

function short_link($id) {
  global $baseUrl;
  return $baseUrl . "/u/index.php?id=".(int)$id;
}
