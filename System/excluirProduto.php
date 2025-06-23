<?php
require_once 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        // 1. Buscar os caminhos da imagem e JSON do produto
        $stmt = $pdo->prepare("SELECT imagem, dadosPagina FROM produtos WHERE id_produto = ?");
        $stmt->execute([$id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            echo "Produto não encontrado.";
            exit;
        }

        // 2. Apagar arquivos imagem e JSON, se existirem
        if (!empty($produto['imagem']) && file_exists($produto['imagem'])) {
            unlink($produto['imagem']);
        }
        if (!empty($produto['dadosPagina']) && file_exists($produto['dadosPagina'])) {
            unlink($produto['dadosPagina']);
        }

        // 3. Verificar se existem itens_pedido com este produto
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM itens_pedido WHERE id_produto = ?");
        $stmt->execute([$id]);
        $countItens = $stmt->fetchColumn();

        if ($countItens > 0) {
            // Apagar os itens do pedido relacionados ao produto
            $stmt = $pdo->prepare("DELETE FROM itens_pedido WHERE id_produto = ?");
            $stmt->execute([$id]);
        }

        // 4. Apagar o produto
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
?>
