<?php
include_once '../System/session.php';
require_once '../System/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pedido = $_POST['id_pedido'] ?? null;

    if ($id_pedido && isset($_SESSION['id_cliente'])) {
        try {
            $pdo->beginTransaction();

            // Apagar endereço
            $stmt = $pdo->prepare("DELETE FROM enderecos WHERE id_pedido = :id_pedido");
            $stmt->execute(['id_pedido' => $id_pedido]);

            // Apagar itens do pedido
            $stmt = $pdo->prepare("DELETE FROM itens_pedido WHERE id_pedido = :id_pedido");
            $stmt->execute(['id_pedido' => $id_pedido]);

            // Apagar pedido
            $stmt = $pdo->prepare("DELETE FROM pedidos WHERE id_pedido = :id_pedido AND id_cliente = :id_cliente");
            $stmt->execute(['id_pedido' => $id_pedido, 'id_cliente' => $_SESSION['id_cliente']]);

            $pdo->commit();

            echo json_encode(['success' => true, 'message' => 'Pedido cancelado com sucesso.']);
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao cancelar pedido: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos ou não autorizado.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
}
?>
