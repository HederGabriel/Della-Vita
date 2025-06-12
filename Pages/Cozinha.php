<?php
include_once '../System/db.php';
date_default_timezone_set('America/Sao_Paulo');

$sql = "
    SELECT 
        p.id_pedido, p.nome_cliente, p.status_pedido, p.data_pedido, c.avatar,
        e.rua, e.numero, e.setor, e.cidade, e.cep, e.complemento,
        GROUP_CONCAT(prod.nome SEPARATOR ', ') AS itens
    FROM pedidos p
    INNER JOIN clientes c ON c.id_cliente = p.id_cliente
    LEFT JOIN enderecos e ON e.id_pedido = p.id_pedido
    INNER JOIN itens_pedido i ON i.id_pedido = p.id_pedido
    INNER JOIN produtos prod ON prod.id_produto = i.id_produto
    WHERE p.status_pedido != 'Cancelado'
    GROUP BY p.id_pedido
    ORDER BY p.data_pedido DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Della Vita - Cozinha</title>
    <link rel="stylesheet" href="../CSS/cozinha.css" />
</head>
<body>
<section id="lista-pedido">
    <button id="sair" onclick="window.location.href='adm-cozinha.php'">Sair</button>
    <h1>Lista de Pedidos</h1>
    <button id="reload" onclick="location.reload()">RELOAD</button>

    <div class="container-pedidos">
        <?php foreach ($pedidos as $pedido): ?>
            <?php 
                $temEndereco = !empty($pedido['rua']);
                $statusAtual = $pedido['status_pedido'];

                // Mapeamento visual do status para exibir no destaque
                $mapaStatusExibicao = [
                    'Recebido' => 'Recebido',
                    'Em Preparo' => 'Em Preparo',
                    'Enviado' => $temEndereco ? 'Enviado' : 'Aguardando Retirada',
                    'Entregue' => $temEndereco ? 'Entregue' : 'Retirado',
                ];

                $statusExibicao = $mapaStatusExibicao[$statusAtual] ?? $statusAtual;
            ?>
            <div class="pedido">
                <div class="avatar">
                    <img src="<?= htmlspecialchars($pedido['avatar'] ?? '../IMG/Profile/Default.png') ?>" alt="Avatar" />
                </div>
                <div class="pedido-info">
                    <p>Cliente: <?= htmlspecialchars($pedido['nome_cliente']) ?></p>
                    <p>Itens: <?= htmlspecialchars($pedido['itens']) ?></p>
                    <p class="select-status">
                        Status:
                        <span class="status-destaque"><?= htmlspecialchars($statusExibicao) ?></span>
                        <select class="select" onchange="atualizarStatus(this, <?= (int)$pedido['id_pedido'] ?>)">
                            <option value="Recebido" <?= $statusAtual === 'Recebido' ? 'selected' : '' ?>>Recebido</option>
                            <option value="Em Preparo" <?= $statusAtual === 'Em Preparo' ? 'selected' : '' ?>>Em Preparo</option>

                            <?php if (!$temEndereco): // Retirada no Local ?>
                                <option value="Enviado" <?= $statusAtual === 'Enviado' ? 'selected' : '' ?>>Aguardando Retirada</option>
                                <option value="Entregue" <?= $statusAtual === 'Entregue' ? 'selected' : '' ?>>Retirado</option>
                            <?php else: // Pedido com endereço (entrega) ?>
                                <option value="Enviado" <?= $statusAtual === 'Enviado' ? 'selected' : '' ?>>Enviado</option>
                                <!-- Não mostrar Entregue para pedidos com endereço -->
                            <?php endif; ?>
                        </select>
                    </p>
                    <p>
                        Endereço: 
                        <?= $temEndereco
                            ? htmlspecialchars("{$pedido['rua']}, {$pedido['numero']} - {$pedido['setor']}, {$pedido['cidade']} - CEP: {$pedido['cep']}")
                            : 'Retirada no Local'
                        ?>
                    </p>
                    <p>Horário: <?= date('H:i', strtotime($pedido['data_pedido'])) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script src="../JS/cozinha-status.js"></script>
</body>
</html>
