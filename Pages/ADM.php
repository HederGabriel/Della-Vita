<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Della Vita - ADM</title>
    <link rel="stylesheet" href="../CSS/adm.css">
    <link rel="shortcut icon" href="../IMG/favicon.ico" type="image/x-icon">
</head>
<body>
    <section class="painel">
        <div class="painel-topo">
            <div class="upload-imagem">
                <p>Arraste e Solte para Carregar a Imagem</p>
                <p>Ou</p>
                <button class="upload-button">Escolhe o Arquivo</button>
            </div>
            <div class="add-produto">
                
                <form action="">
                    <h1>Formulário de Adição de Produto ao Cardápio</h1> 
                    <input type="text" name="nome" id="nome" placeholder="Nome do Produto" required>
                    <input type="number" name="preco" id="preco" placeholder="Preço do Produto" required>
                    <input type="text" name="descricao-r" id="descricao-r" placeholder="Descrição Resumida" required>
                    <div class="dados-page">
                        <input type="text" name="descricao_completa" id="descricao_completa" placeholder="Descrição Completa" required>
                        <div class="ingredientes">
                            <input type="text" name="ingredientes" id="ingredientes" placeholder="Ingredientes" required>
                            <input type="text" name="ingredientes" id="ingredientes" placeholder="Ingredientes" required>
                            <button id="add-ingrediente">+</button>
                        </div>
                    </div>
                    <select name="tipo" id="tipo">
                        <option value="normal">Normal</option>
                        <option value="combo">Combo</option>
                    </select>
                    <select name="categoria" id="categoroa">
                        <option value="trad">Tradicional</option>
                        <option value="doce">Doce</option>
                        <option value="esp">Especial</option>
                    </select>
                    <button id="adicionar-produto">Adicionar Produto ao Cardápio</button>
                </form>
            </div>
        </div>

        <article class="lista-produtos">
            <h2>Produtos Cadastrados</h2>
            <div class="produtos">
                <?php
                require_once '../System/db.php';

                    try {
                        $stmt = $pdo->query("SELECT * FROM produtos ORDER BY id_produto DESC");

                        if ($stmt->rowCount() > 0) {
                            while ($row = $stmt->fetch()) {
                                $nome = htmlspecialchars($row['nome']);
                                $preco = number_format($row['preco'], 2, ',', '.');
                                $imagem = htmlspecialchars($row['imagem']); // Caminho completo já vem do banco
                                $categoria = $row['sabor']; // categoria = sabor
                                $tipo = ucfirst($row['tipo']);
                    ?>
                                <div class="produto">
                                    <img src="<?= $imagem ?>" alt="<?= $nome ?>">
                                    <h3><?= $nome ?></h3>
                                    <?php if (!empty($categoria)): ?>
                                        <p>Categoria: 
                                            <?php 
                                                if ($categoria === 'trad') {
                                                    echo 'Tradicional';
                                                } elseif ($categoria === 'doce') {
                                                    echo 'Doce';
                                                } elseif ($categoria === 'esp') {
                                                    echo 'Especial';
                                                } else {
                                                    echo ucfirst($categoria); // fallback, se tiver outro valor
                                                }
                                            ?>
                                        </p>

                                    <?php endif; ?>
                                    <p>Tipo: <?= $tipo ?></p>
                                    <p>R$ <?= $preco ?></p>
                                    <button class="edit-button" data-id="<?= $row['id_produto'] ?>">Editar</button>
                                    <button class="delete-button" data-id="<?= $row['id_produto'] ?>">Excluir</button>
                                </div>
                    <?php
                            }
                        } else {
                            echo "<p>Nenhum produto cadastrado ainda.</p>";
                        }
                    } catch (PDOException $e) {
                        echo "<p>Erro ao buscar produtos: " . $e->getMessage() . "</p>";
                    }
                    ?>
            </div>

        </article>
    </section>
</body>
</html>