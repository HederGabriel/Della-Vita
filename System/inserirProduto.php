<?php
require_once '../System/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $preco = number_format((float)$_POST['preco'], 2, '.', '');
    $descricao_r = mb_substr($_POST['descricao-r'], 0, 30);
    $descricao_completa = mb_substr($_POST['descricao_completa'], 0, 185);
    $ingredientes = array_filter($_POST['ingredientes']);
    $tipo = $_POST['tipo'];
    $categoria = $_POST['categoria'];

    // Verifica imagem enviada
    if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        echo "Erro ao carregar imagem.";
        exit;
    }

    // Pasta destino da imagem
    $pastaImagem = '../IMG/Produtos/';
    if (!file_exists($pastaImagem)) {
        mkdir($pastaImagem, 0777, true);
    }

    $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $nomeImagem = uniqid('produto_', true) . '.' . $ext;
    $caminhoImagem = $pastaImagem . $nomeImagem;

    if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoImagem)) {
        echo "Erro ao salvar a imagem.";
        exit;
    }

    $caminhoImagemBanco = $caminhoImagem;

    // Criação do JSON
    $dadosJson = [
        'descricao_completa' => $descricao_completa,
        'ingredientes' => array_values($ingredientes)
    ];

    $pastaJson = '../Json/';
    if (!file_exists($pastaJson)) {
        mkdir($pastaJson, 0777, true);
    }

    $nomeJson = uniqid('produto_', true) . '.json';
    $caminhoJson = $pastaJson . $nomeJson;
    file_put_contents($caminhoJson, json_encode($dadosJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    try {
        $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, imagem, descricao_resumida, dadosPagina, tipo, sabor) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $nome, $preco, $caminhoImagemBanco, $descricao_r, $caminhoJson, $tipo, $categoria
        ]);

        header("Location: ../Pages/ADM.php");
        exit;
    } catch (PDOException $e) {
        echo "Erro ao inserir produto: " . $e->getMessage();
    }
} else {
    echo "Requisição inválida.";
}
?>
