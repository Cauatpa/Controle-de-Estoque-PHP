<?php
$host = '127.0.0.1'; // ou 'localhost'
$db   = 'controle_estoque';
$user = 'root';
$pass = ''; // no XAMPP normalmente é vazio

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro: ' . $e->getMessage());
}
