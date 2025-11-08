<?php
require_once __DIR__ . '/../config.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    exit('Bad request');
}

$id = (int) $_GET['id'];
if ($id <= 0) {
    http_response_code(400);
    exit('Bad request');
}

$upd = $conn->prepare("UPDATE urls SET clicks = clicks + 1 WHERE id = :id");
$upd->execute([':id' => $id]);


$sel = $conn->prepare("SELECT url FROM urls WHERE id = :id");
$sel->execute([':id' => $id]);
$row = $sel->fetch(PDO::FETCH_ASSOC);

if ($row && !empty($row['url'])) {
    header('Location: ' . $row['url'], true, 302);
    exit;
}

http_response_code(404);
echo 'Not found';
