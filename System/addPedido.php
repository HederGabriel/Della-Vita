<?php
// Arquivo: addPedido.php

session_start();
include_once '../System/db.php';
require_once '../System/session.php';

header('Content-Type: application/json');

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id_cliente'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit();
}

$id_cliente = $_SESSION['id_cliente'];

// Coletando e validando os dados recebidos via POST
$id_produto = filter_input(INPUT_POST, 'id_produto', FILTER_VALIDATE_INT);
$quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
$preco_unitario = filter_input(INPUT_POST, 'preco_unitario', FILTER_VALIDATE_FLOAT);
$tipo_entrega = filter_input(INPUT_POST, 'tipo_entrega', FILTER_SANITIZE_STRING);
$tamanho = filter_input(INPUT_POST, 'tamanho', FILTER_SANITIZE_STRING);

// Validação final dos dados
if ($id_produto === false || $quantidade === false || $preco_unitario === false || !$tipo_entrega || !$tamanho) {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetros inválidos']);
    exit();
}

$total = $quantidade * $preco_unitario;

try {
    // Insere diretamente na tabela itens_pedido, sem usar ou consultar a tabela pedidos
    $stmt = $pdo->prepare("
        INSERT INTO itens_pedido (
            quantidade, preco_unitario, total, id_produto, entrega, tamanho, id_cliente
        ) VALUES (
            :quantidade, :preco_unitario, :total, :id_produto, :entrega, :tamanho, :id_cliente
        )
    ");
    $stmt->execute([
        'quantidade' => $quantidade,
        'preco_unitario' => $preco_unitario,
        'total' => $total,
        'id_produto' => $id_produto,
        'entrega' => $tipo_entrega,
        'tamanho' => $tamanho,
        'id_cliente' => $id_cliente
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Item adicionado com sucesso à tabela itens_pedido'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>
