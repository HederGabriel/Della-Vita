<?php
session_start();

// Cabeçalhos para JSON e desabilitar cache
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Conexão com o banco de dados
require_once '../System/db.php';

// Verificar se os dados foram enviados via POST
if (!isset($_POST['id_item'], $_POST['quantidade'])) {
    echo json_encode(['success' => false, 'error' => 'Parâmetros ausentes.']);
    exit;
}

$idItem = (int)$_POST['id_item'];
$quantidade = (int)$_POST['quantidade'];

// Validação da quantidade
if ($quantidade < 1) {
    echo json_encode(['success' => false, 'error' => 'Quantidade inválida.']);
    exit;
}

try {
    // Usar a variável correta da conexão: $pdo (vinda do db.php)
    $stmt = $pdo->prepare("UPDATE itens_pedido SET quantidade = ? WHERE id_item_pedido = ?");
    $success = $stmt->execute([$quantidade, $idItem]);

    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao atualizar no banco.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erro no banco: ' . $e->getMessage()]);
}
exit;
