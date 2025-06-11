<?php
include_once '../System/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pedido = $_POST['id_pedido'] ?? null;
    $status = $_POST['status_pedido'] ?? null;

    $statusPermitidos = ['Recebido', 'Em Preparo', 'Enviado', 'Entregue'];

    if ($id_pedido && $status && in_array($status, $statusPermitidos, true)) {
        $stmt = $pdo->prepare("UPDATE pedidos SET status_pedido = :status WHERE id_pedido = :id");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id_pedido, PDO::PARAM_INT);
        if ($stmt->execute()) {
            echo 'OK';
        } else {
            echo 'Erro ao atualizar';
        }
    } else {
        echo 'Dados inválidos';
    }
}
