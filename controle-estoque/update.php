<?php
require __DIR__ . '/config/db.php';


$stmt = $pdo->prepare("UPDATE produtos SET quantidade_recebida = ? WHERE id = ?");
$stmt->execute([
    $_POST['quantidade_recebida'],
    $_POST['id']
]);

require 'config/db.php';
header('Location: index.php');
