<?php
session_start();
require_once '../System/db.php';

header('Content-Type: application/json');

if (!isset($_POST['id_item'])) {
    echo json_encode(['success' => false, 'error' => 'ID do item ausente.']);
    exit;
}

$idItem = (int)$_POST['id_item'];

try {
    $stmt = $pdo->prepare("DELETE FROM itens_pedido WHERE id_item_pedido = ?");
    $stmt->execute([$idItem]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erro no banco: ' . $e->getMessage()]);
}
