<?php
require_once '../System/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id_produto']);
    $nome = $_POST['nome'];
    $preco = number_format((float)$_POST['preco'], 2, '.', '');
    $descricao_resumida = mb_substr($_POST['descricao-r'], 0, 30);
    $descricao_completa = mb_substr($_POST['descricao_completa'], 0, 185);
    $ingredientes = array_filter($_POST['ingredientes']);
    $tipo = $_POST['tipo'];
    $categoria = $_POST['categoria'];

    // Atualiza ou cria o arquivo JSON com descrição completa e ingredientes
    $dadosJson = [
        'descricao_completa' => $descricao_completa,
        'ingredientes' => array_values($ingredientes)
    ];

    $pastaJson = '../Json/';
    if (!file_exists($pastaJson)) {
        mkdir($pastaJson, 0777, true);
    }

    // Verifica se o produto já tem um dadosPagina
    $stmt = $pdo->prepare("SELECT dadosPagina FROM produtos WHERE id_produto = ?");
    $stmt->execute([$id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado && !empty($resultado['dadosPagina']) && file_exists($resultado['dadosPagina'])) {
        $caminhoJson = $resultado['dadosPagina']; // sobrescreve JSON existente
    } else {
        $nomeJson = uniqid('produto_', true) . '.json';
        $caminhoJson = $pastaJson . $nomeJson;
    }

    file_put_contents($caminhoJson, json_encode($dadosJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    // Atualiza no banco os outros campos e o novo caminho do JSON
    $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ?, descricao_resumida = ?, dadosPagina = ?, tipo = ?, sabor = ? WHERE id_produto = ?");
    $success = $stmt->execute([
        $nome, $preco, $descricao_resumida, $caminhoJson, $tipo, $categoria, $id
    ]);

    if ($success) {
        header("Location: ../Pages/ADM.php");
        exit;
    } else {
        echo "Erro ao editar produto.";
    }
}
?>
