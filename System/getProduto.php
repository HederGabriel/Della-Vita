<?php
require_once 'db.php'; // Ajuste o caminho conforme necessário

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID não informado']);
    exit;
}

$id = (int) $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id_produto = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        echo json_encode(['error' => 'Produto não encontrado']);
        exit;
    }

    // Aqui você pode querer decodificar ingredientes se estiver salvo em JSON, etc.
    // Exemplo: $produto['ingredientes'] = json_decode($produto['ingredientes_json']);

    echo json_encode($produto);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro no banco: ' . $e->getMessage()]);
}
