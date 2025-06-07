<?php
require_once 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

file_put_contents("log_debug.txt", date('Y-m-d H:i:s') . " POST: " . print_r($_POST, true) . "\n", FILE_APPEND);

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
?>