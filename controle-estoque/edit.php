<?php

$id = $_POST['id'];
$nome = $_POST['nome'];
$quantidade_esperada = $_POST['quantidade_esperada'];

$sql = "UPDATE produtos 
        SET nome = ?, quantidade_esperada = ?
        WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$nome, $quantidade_esperada, $id]);

require 'config/db.php';
header("Location: index.php");
exit;
