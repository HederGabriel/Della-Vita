<?php
// adicionar_item.php

include_once 'System/db.php'; // ajuste o caminho se necessário

// Verifica se os dados foram enviados
if (!isset($_POST['id_produto'], $_POST['quantidade'], $_POST['preco_unitario'], $_POST['total'])) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
    exit;
}

$id_produto = (int)$_POST['id_produto'];
$quantidade = (int)$_POST['quantidade'];
$preco_unitario = (float)$_POST['preco_unitario'];
$total = (float)$_POST['total'];

// Preparar a inserção usando PDO
$stmt = $pdo->prepare("INSERT INTO carrinho (id_produto, quantidade, preco_unitario, total) VALUES (?, ?, ?, ?)");
if ($stmt->execute([$id_produto, $quantidade, $preco_unitario, $total])) {
    echo json_encode(['success' => true, 'message' => 'Item adicionado com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao adicionar o item.']);
}
?>
