<?php
require __DIR__ . '/config/db.php';

/* FILTRO */
$busca = $_GET['busca'] ?? '';

if ($busca) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE nome LIKE ?");
    $stmt->execute(["%$busca%"]);
    $produtos = $stmt->fetchAll();
} else {
    $produtos = $pdo->query("SELECT * FROM produtos")->fetchAll();
}

/* 🔢 TOTAIS */
$totalEsperado = 0;
$totalRecebido = 0;
$totalDefeituoso = 0;
$totalEstoque = 0;

foreach ($produtos as $p) {
    $totalEsperado += $p['quantidade_esperada'];
    $totalRecebido += $p['quantidade_recebida'];
    $totalDefeituoso += $p['quantidade_defeituosa'] ?? 0;
    $totalEstoque += $p['quantidade_recebida'] - ($p['quantidade_defeituosa'] ?? 0);
}

/* 🎨 CORES DOS CARTÕES DE RESUMO */
$classeEsperado   = $totalEsperado   > 0 ? 'text-bg-secondary' : 'text-bg-secondary';
$classeRecebido   = $totalRecebido   > 0 ? 'text-bg-success'   : 'text-bg-secondary';
$classeDefeituoso = $totalDefeituoso > 0 ? 'text-bg-warning'   : 'text-bg-secondary';
$classeEstoque    = $totalEstoque    > 0 ? ''                 : 'text-bg-secondary';


/* 🎨 COR TOTAL RECEBIDO */
$classeTotalRecebido = $totalRecebido < $totalEsperado
    ? 'text-bg-danger'
    : 'text-bg-success';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Controle de Estoque</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="p-4">

    <h2 class="mb-3">Controle de Estoque</h2>

    <!-- FILTRO -->
    <form method="get" class="mb-3">
        <input type="text" name="busca" class="form-control"
            placeholder="Buscar produto..."
            value="<?= htmlspecialchars($busca) ?>">
    </form>

    <!-- CADASTRO -->
    <form method="post" action="add.php" class="row g-2 mb-4">
        <div class="col-md-5">
            <input name="nome" class="form-control" placeholder="Produto" required>
        </div>
        <div class="col-md-3">
            <input name="quantidade_esperada" type="number" class="form-control"
                placeholder="Qtd Esperada" required>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Adicionar</button>
        </div>
    </form>

    <!-- TABELA -->
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Produto</th>
                <th>Esperado</th>
                <th>Recebido</th>
                <th>Defeituosos</th>
                <th>Diferença</th>
                <th>Resumo</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($produtos as $p):
                $defeito = $p['quantidade_defeituosa'] ?? 0;
                $estoque = $p['quantidade_recebida'] - $defeito;
                $dif = $p['quantidade_esperada'] - $estoque;

                /* STATUS INVERTIDO CONFORME PEDIDO */
                if ($dif == 0) $status = 'success';
                elseif ($dif > 0) $status = 'danger';
                else $status = 'warning';
            ?>
                <tr class="table-<?= $status ?>">
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= $p['quantidade_esperada'] ?></td>
                    <td><?= $p['quantidade_recebida'] ?></td>
                    <td><?= $defeito ?></td>
                    <td><?= $dif ?></td>
                    <td><strong><?= $estoque ?></strong></td>
                    <td><?= strtoupper($status) ?></td>

                    <td class="text-center">
                        <!-- EDITAR -->
                        <button class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditar<?= $p['id'] ?>">
                            ✏ Editar
                        </button>

                        <!-- EXCLUIR -->
                        <form method="post" action="delete.php"
                            class="d-inline"
                            onsubmit="return confirm('Excluir este produto?')">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">
                                🗑 Excluir
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- MODAL -->
                <div class="modal fade" id="modalEditar<?= $p['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form method="post" action="edit_full.php">
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar Produto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">

                                    <div class="mb-2">
                                        <label class="form-label">Nome</label>
                                        <input type="text" name="nome" class="form-control"
                                            value="<?= $p['nome'] ?>" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Esperado</label>
                                            <input type="number" name="quantidade_esperada"
                                                class="form-control"
                                                value="<?= $p['quantidade_esperada'] ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Recebido</label>
                                            <input type="number" name="quantidade_recebida"
                                                class="form-control"
                                                value="<?= $p['quantidade_recebida'] ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Defeituosos</label>
                                            <input type="number" name="quantidade_defeituosa"
                                                class="form-control"
                                                value="<?= $defeito ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button class="btn btn-success">💾 Salvar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- RESUMO -->
    <div class="row text-center mb-4">

        <div class="col-md-3">
            <div class="card <?= $classeEsperado ?>">
                <div class="card-body">
                    <h6>Total Esperado</h6>
                    <h4><?= $totalEsperado ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card <?= $classeRecebido ?>">
                <div class="card-body">
                    <h6>Total Recebido</h6>
                    <h4><?= $totalRecebido ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card <?= $classeDefeituoso ?>">
                <div class="card-body">
                    <h6>Defeituosos</h6>
                    <h4><?= $totalDefeituoso ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card <?= $classeEstoque ?: '' ?>"
                style="<?= $totalEstoque > 0 ? 'background-color: rgb(8 57 129)' : '' ?>">
                <div class="card-body <?= $totalEstoque > 0 ? 'text-white' : '' ?>">
                    <h6>Em Estoque</h6>
                    <h4><?= $totalEstoque ?></h4>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>