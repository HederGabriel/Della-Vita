<?php
include_once 'session.php';
require_once 'db.php';

if (!isset($_SESSION['id_cliente'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit();
}

$id_cliente = $_SESSION['id_cliente'];

// Atualiza todos pedidos do cliente com status 'Entregue' para 'archive'
$stmt = $pdo->prepare("UPDATE pedidos SET status_pedido = 'archive' WHERE id_cliente = :id_cliente AND status_pedido = 'Entregue'");
$success = $stmt->execute(['id_cliente' => $id_cliente]);

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Pedidos arquivados com sucesso']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao arquivar pedidos']);
}
?>