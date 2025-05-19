<?php
// Arquivo: addPedido.php

session_start();
include_once '../System/db.php';

header('Content-Type: application/json');

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id_cliente'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit();
}

$id_cliente = $_SESSION['id_cliente'];
$id_produto = filter_input(INPUT_POST, 'id_produto', FILTER_VALIDATE_INT);
$quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
$preco_unitario = filter_input(INPUT_POST, 'preco_unitario', FILTER_VALIDATE_FLOAT);
$tipo_entrega = filter_input(INPUT_POST, 'tipo_entrega', FILTER_SANITIZE_STRING);
$tamanho = filter_input(INPUT_POST, 'tamanho', FILTER_SANITIZE_STRING); // <- Novo campo

// Verificação dos parâmetros
if (!$id_produto || !$quantidade || !$preco_unitario || !$tipo_entrega || !$tamanho) {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetros inválidos']);
    exit();
}

$total = $quantidade * $preco_unitario;

try {
    // Verifica se há pedido em aberto
    $stmt = $pdo->prepare("SELECT id_pedido, valor_total FROM pedidos WHERE id_cliente = :id_cliente AND status_pedido = 'em aberto' LIMIT 1");
    $stmt->execute(['id_cliente' => $id_cliente]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        // Cria novo pedido
        $stmtNome = $pdo->prepare("SELECT nome FROM clientes WHERE id_cliente = :id_cliente");
        $stmtNome->execute(['id_cliente' => $id_cliente]);
        $clienteDados = $stmtNome->fetch(PDO::FETCH_ASSOC);
        $nome_cliente = $clienteDados['nome'] ?? 'Cliente';

        $stmt = $pdo->prepare("INSERT INTO pedidos (nome_cliente, tipo_pedido, status_pedido, data_pedido, valor_total, id_cliente) VALUES (:nome_cliente, 'carrinho', 'em aberto', NOW(), 0, :id_cliente)");
        $stmt->execute([
            'nome_cliente' => $nome_cliente,
            'id_cliente' => $id_cliente
        ]);
        $id_pedido = $pdo->lastInsertId();
        $valor_total_pedido = 0;
    } else {
        $id_pedido = $pedido['id_pedido'];
        $valor_total_pedido = (float)$pedido['valor_total'];
    }

    // Insere item no pedido incluindo o tamanho
    $stmt = $pdo->prepare("
        INSERT INTO itens_pedido (
            quantidade, preco_unitario, total, id_pedido, id_produto, entrega, tamanho, id_cliente
        ) VALUES (
            :quantidade, :preco_unitario, :total, :id_pedido, :id_produto, :entrega, :tamanho, :id_cliente
        )
    ");
    $stmt->execute([
        'quantidade' => $quantidade,
        'preco_unitario' => $preco_unitario,
        'total' => $total,
        'id_pedido' => $id_pedido,
        'id_produto' => $id_produto,
        'entrega' => $tipo_entrega,
        'tamanho' => $tamanho, // <- Incluído aqui
        'id_cliente' => $id_cliente
    ]);

    // Atualiza o valor total do pedido
    $novo_valor_total = $valor_total_pedido + $total;
    $stmt = $pdo->prepare("UPDATE pedidos SET valor_total = :valor_total WHERE id_pedido = :id_pedido");
    $stmt->execute([
        'valor_total' => $novo_valor_total,
        'id_pedido' => $id_pedido
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Item adicionado ao carrinho',
        'id_pedido' => $id_pedido
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>
