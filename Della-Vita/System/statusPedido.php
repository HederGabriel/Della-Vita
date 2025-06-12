<?php
require_once '../System/db.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id_pedido']) || empty($_GET['id_pedido'])) {
    echo json_encode(['status' => null, 'error' => 'Parâmetro id_pedido ausente']);
    exit;
}

$id_pedido = $_GET['id_pedido'];

try {
    $stmt = $pdo->prepare("SELECT status_pedido FROM pedidos WHERE id_pedido = :id");
    $stmt->execute(['id' => $id_pedido]);
    $status = $stmt->fetchColumn();

    if ($status === false) {
        echo json_encode(['status' => null, 'error' => 'Pedido não encontrado']);
    } else {
        echo json_encode(['status' => $status]);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => null, 'error' => 'Erro no banco de dados']);
}

exit;
