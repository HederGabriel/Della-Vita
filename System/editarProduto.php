<?php
require_once '../System/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id_produto']);
    $nome = $_POST['nome'];

    // Garante nome_formatado mesmo se não vier via formulário
    if (isset($_POST['nome_formatado']) && !empty($_POST['nome_formatado'])) {
        $nomeFormatado = $_POST['nome_formatado'];
    } else {
        $nomeFormatado = iconv('UTF-8', 'ASCII//TRANSLIT', $nome);
        $nomeFormatado = preg_replace('/[^a-zA-Z0-9]/', '_', $nomeFormatado);
        $nomeFormatado = strtolower($nomeFormatado);
    }

    $preco = number_format((float)$_POST['preco'], 2, '.', '');
    $descricao_resumida = mb_substr($_POST['descricao-r'], 0, 30);
    $descricao_completa = mb_substr($_POST['descricao_completa'], 0, 185);
    $ingredientes = array_filter($_POST['ingredientes']);
    $tipo = $_POST['tipo'];
    $categoria = $_POST['categoria'];

    // --- Busca os caminhos antigos de imagem e JSON ---
    $stmt = $pdo->prepare("SELECT imagem, dadosPagina FROM produtos WHERE id_produto = ?");
    $stmt->execute([$id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $imagemAntiga = $resultado['imagem'] ?? '';
    $jsonAntigo = $resultado['dadosPagina'] ?? '';

    // --- Pasta JSON ---
    $pastaJson = '../Json/';
    if (!file_exists($pastaJson)) {
        mkdir($pastaJson, 0777, true);
    }

    // --- Apaga JSON antigo se existir ---
    if (!empty($jsonAntigo) && file_exists($jsonAntigo)) {
        unlink($jsonAntigo);
    }

    // --- Cria JSON novo ---
    $dadosJson = [
        'descricao_completa' => $descricao_completa,
        'ingredientes' => array_values($ingredientes)
    ];
    $nomeJson = $nomeFormatado . '.json';
    $caminhoJson = $pastaJson . $nomeJson;
    file_put_contents($caminhoJson, json_encode($dadosJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    // --- Pasta Imagem ---
    $pastaImagem = '../IMG/Produtos/';
    if (!file_exists($pastaImagem)) {
        mkdir($pastaImagem, 0777, true);
    }

    $caminhoImagem = null;

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        // Apaga imagem antiga
        if (!empty($imagemAntiga) && file_exists($imagemAntiga)) {
            unlink($imagemAntiga);
        }

        // Salva nova imagem
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $nomeImagem = $nomeFormatado . '.' . $ext;
        $caminhoImagem = $pastaImagem . $nomeImagem;

        if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoImagem)) {
            echo "Erro ao salvar a nova imagem.";
            exit;
        }
    } else {
        // Mantém imagem antiga
        $caminhoImagem = $imagemAntiga;
    }

    // --- Atualiza banco ---
    $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ?, imagem = ?, descricao_resumida = ?, dadosPagina = ?, tipo = ?, sabor = ? WHERE id_produto = ?");
    $success = $stmt->execute([
        $nome, $preco, $caminhoImagem, $descricao_resumida, $caminhoJson, $tipo, $categoria, $id
    ]);

    if ($success) {
        header("Location: ../Pages/ADM.php");
        exit;
    } else {
        echo "Erro ao editar produto.";
    }
}
?>
