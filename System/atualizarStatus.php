<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pedido'], $_POST['status'])) {
    $id_pedido = $_POST['id_pedido'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE pedidos SET status_pedido = :status WHERE id_pedido = :id_pedido");
    $success = $stmt->execute([
        'status' => $status,
        'id_pedido' => $id_pedido
    ]);

    echo json_encode(['success' => $success]);
    exit();
}
echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
