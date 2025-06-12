<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/db.php';

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM pedidos WHERE tipo_pedido = 'casa' AND status_pedido != 'archive'");
    $stmt->execute();
    $row = $stmt->fetch();

    $temPedido = ($row['total'] > 0);

    echo json_encode(['temPedido' => $temPedido]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro na consulta SQL: ' . $e->getMessage()]);
}
exit;
