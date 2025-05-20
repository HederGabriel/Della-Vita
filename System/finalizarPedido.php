<?php
// finalizarPedido.php

include_once 'session.php'; // session_start() garantido aqui
include_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

// Verifica se o cliente está autenticado
if (!isset($_SESSION['id_cliente'], $_SESSION['nome_cliente'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado.']);
    exit;
}

// Validação rígida do id_cliente e nome_cliente
$id_cliente = filter_var($_SESSION['id_cliente'], FILTER_VALIDATE_INT);
$nome_cliente = trim($_SESSION['nome_cliente']);

if (!$id_cliente) {
    http_response_code(401);
    echo json_encode(['error' => 'ID de cliente inválido.']);
    exit;
}

// Recebe e valida o tipo de pedido
$tipo_pedido = filter_input(INPUT_POST, 'tipo_pedido', FILTER_SANITIZE_STRING);
if (!$tipo_pedido) {
    http_response_code(400);
    echo json_encode(['error' => 'Tipo de pedido não informado.']);
    exit;
}

$tipo_pedido = strtolower(trim($tipo_pedido));
$tipos_validos = ['casa', 'local'];

if (!in_array($tipo_pedido, $tipos_validos, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Tipo de pedido inválido.']);
    exit;
}

try {
    // Inicia transação
    $pdo->beginTransaction();

    $status_pedido = 'Recebido';

    // Insere o pedido na tabela pedidos
    $stmt = $pdo->prepare("
        INSERT INTO pedidos (id_cliente, nome_cliente, data_pedido, tipo_pedido, status_pedido)
        VALUES (:id_cliente, :nome_cliente, NOW(), :tipo_pedido, :status_pedido)
    ");
    $stmt->execute([
        ':id_cliente' => $id_cliente,
        ':nome_cliente' => $nome_cliente,
        ':tipo_pedido' => $tipo_pedido,
        ':status_pedido' => $status_pedido
    ]);

    // Pega o id do pedido recém-inserido
    $id_pedido = $pdo->lastInsertId();

    // Atualiza os itens do cliente que ainda não tem id_pedido associado
    $stmt = $pdo->prepare("
        UPDATE itens_pedido
        SET id_pedido = :id_pedido
        WHERE id_cliente = :id_cliente AND id_pedido IS NULL
    ");
    $stmt->execute([
        ':id_pedido' => $id_pedido,
        ':id_cliente' => $id_cliente
    ]);

    // Verifica se algum item foi atualizado
    if ($stmt->rowCount() === 0) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['error' => 'Nenhum item encontrado para associar ao pedido.']);
        exit;
    }

    // Finaliza a transação
    $pdo->commit();

    // Retorna sucesso com o id do pedido criado
    echo json_encode([
        'success' => true,
        'message' => 'Pedido finalizado com sucesso!',
        'id_pedido' => $id_pedido
    ]);
    exit;

} catch (Exception $e) {
    // Em caso de erro, desfaz a transação
    $pdo->rollBack();

    error_log('Erro ao finalizar pedido: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode(['error' => 'Erro ao finalizar pedido. Tente novamente mais tarde.']);
    exit;
}
