<?php
include_once '../System/db.php';
date_default_timezone_set('America/Sao_Paulo');

// Consulta para buscar os pedidos finalizados (ajuste o status conforme seu critério de finalização)
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Della Vita - Cozinha</title>
    <link rel="stylesheet" href="../CSS/cozinha.css">
</head>
<body>
    
    <section id="lista-pedido">
        <button id="sair">Sair</button>
        <h1>Lista de Pedidos</h1>
        <button id="reload" onclick="location.reload()">RELOAD</button>

        <?php foreach ($pedidos as $pedido): ?>
            <div class="pedido">
                <div class="avatar">
                    <img src="<?= htmlspecialchars($pedido['avatar'] ?? '../IMG/Profile/Default.png') ?>" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%;">
                </div>
                <p>Cliente: <?= htmlspecialchars($pedido['nome_cliente']) ?></p>
                <p>Itens: <?= htmlspecialchars($pedido['itens']) ?></p>
                <p>Status: <?= htmlspecialchars($pedido['status_pedido']) ?></p>
                <p>
                    Endereço: 
                    <?= empty($pedido['rua']) 
                        ? 'Retirada no Local' 
                        : htmlspecialchars("{$pedido['rua']}, {$pedido['numero']} - {$pedido['setor']}, {$pedido['cidade']} - CEP: {$pedido['cep']}") ?>
                </p>
                <p>Horário: <?= date('H:i', strtotime($pedido['data_pedido'])) ?></p>
            </div>
        <?php endforeach; ?>

    </section>

</body>
</html>
