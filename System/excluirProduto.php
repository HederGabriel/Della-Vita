<?php
require_once '../System/db.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Excluir imagem associada
    $stmt = $pdo->prepare("SELECT imagem FROM produtos WHERE id_produto = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch();
    if ($produto && file_exists($produto['imagem'])) {
        unlink($produto['imagem']);
    }

    // Excluir produto
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id_produto = ?");
    if ($stmt->execute([$id])) {
        echo 'sucesso';
    } else {
        echo 'erro';
    }
}
