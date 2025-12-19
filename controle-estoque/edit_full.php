<?php
require __DIR__ . '/config/db.php';

$id = $_POST['id'];
$nome = $_POST['nome'];
$esperada = $_POST['quantidade_esperada'];
$recebida = $_POST['quantidade_recebida'];
$defeituosa = $_POST['quantidade_defeituosa'];

$sql = "UPDATE produtos SET
        nome = ?,
        quantidade_esperada = ?,
        quantidade_recebida = ?,
        quantidade_defeituosa = ?
        WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$nome, $esperada, $recebida, $defeituosa, $id]);

header("Location: index.php");
exit;
