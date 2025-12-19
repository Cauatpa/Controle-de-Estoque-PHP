<?php
require 'config/db.php';


if (!isset($_POST['id'])) {
    die('ID não recebido');
}

$id = $_POST['id'];

$sql = "DELETE FROM produtos WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

require 'sync_google.php';
header("Location: index.php");
exit;
