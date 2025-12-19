<?php
require __DIR__ . '/config/db.php';

if ($_POST) {
    $stmt = $pdo->prepare("INSERT INTO produtos (nome, quantidade_esperada) VALUES (?, ?)");
    $stmt->execute([
        $_POST['nome'],
        $_POST['quantidade_esperada']
    ]);
    require 'sync_google.php';
    header('Location: index.php');
}
