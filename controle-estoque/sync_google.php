<?php
require 'config/db.php';


$stmt = $pdo->query("SELECT * FROM produtos");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$headers = [
    'id',
    'nome',
    'esperado',
    'recebido',
    'defeituoso',
    'diferenca',
    'status',
    'atualizado_em'
];

$rows = [];

foreach ($produtos as $p) {
    $dif = $p['quantidade_esperada']
        - $p['quantidade_recebida']
        - ($p['quantidade_defeituosa'] ?? 0);

    if ($dif == 0) $status = 'OK';
    elseif ($dif > 0) $status = 'FALTA';
    else $status = 'EXCESSO';

    $rows[] = [
        $p['id'],
        $p['nome'],
        $p['quantidade_esperada'],
        $p['quantidade_recebida'],
        $p['quantidade_defeituosa'] ?? 0,
        $dif,
        $status,
        date('Y-m-d H:i:s')
    ];
}

$data = json_encode([
    'headers' => $headers,
    'rows' => $rows
]);

$url = '';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close($ch);

