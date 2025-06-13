<?php
require_once 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id_produto = ?");
        $stmt->execute([$id]);
        header("Location: ../Pages/ADM.php");
        exit;
    } catch (PDOException $e) {
        echo "Erro ao excluir: " . $e->getMessage();
    }
} else {
    echo "ID não informado.";
}
