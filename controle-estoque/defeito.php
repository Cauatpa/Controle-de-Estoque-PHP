<?php
require __DIR__ . '/config/db.php';


$stmt = $pdo->prepare(
    "UPDATE produtos SET quantidade_defeituosa = ? WHERE id = ?"
);


$stmt->execute([
    $_POST['quantidade_defeituosa'],
    $_POST['id']
]);

require 'sync_google.php';
header('Location: index.php');
