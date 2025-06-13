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
        <button onclick="window.location.href='../Pages/adm-cozinha.php' ">Sair</button>
        <div class="painel-topo">

            <div class="upload-imagem" id="upload-imagem">
                <p>Arraste e Solte para Carregar a Imagem</p>
                <p>Ou</p>
                <button type="button" class="upload-button" id="btnEscolherImagem">Escolher Arquivo</button>
            </div>

            <div class="add-produto">
                
                <form action="..\System\inserirProduto.php" method="POST" enctype="multipart/form-data">
                    <input type="file" id="inputImagem" name="imagem" accept="image/*" required style="opacity: 0; position: absolute; pointer-events: none; width: 0; height: 0;">


                    <h1>Formulário de Adição de Produto ao Cardápio</h1>

                    <input type="text" name="nome" id="nome" placeholder="Nome do Produto" required>

                    <input type="number" name="preco" id="preco" step="0.01" placeholder="Preço do Produto" required>

                    <div class="campo-com-contador">
                        <input type="text" name="descricao-r" id="descricao-r" maxlength="30" placeholder="Descrição Resumida" required>
                        <span class="contador" id="contador-resumo">0/30</span>
                    </div>

                    <div class="dados-page">
                        <div class="campo-com-contador">
                            <textarea name="descricao_completa" id="descricao_completa" maxlength="185" placeholder="Descrição Completa" required></textarea>
                            <span class="contador" id="contador-completa">0/185</span>
                        </div>

                        <div class="ingredientes" id="ingredientes-container">
                            <input type="text" name="ingredientes[]" placeholder="Ingrediente">
                            <input type="text" name="ingredientes[]" placeholder="Ingrediente">
                            <input type="text" name="ingredientes[]" placeholder="Ingrediente">
                            <input type="text" name="ingredientes[]" placeholder="Ingrediente">
                        </div>
                        <button type="button" id="add-ingrediente">+</button>
                    </div>

                    <select name="tipo" id="tipo">
                        <option value="normal">Normal</option>
                        <option value="combo">Combo</option>
                    </select>

                    <select name="categoria" id="categoria">
                        <option value="trad">Tradicional</option>
                        <option value="doce">Doce</option>
                        <option value="esp">Especial</option>
                    </select>

                    <input type="hidden" name="id_produto" id="id_produto">
                    <button type="submit" id="adicionar-produto">Adicionar Produto ao Cardápio</button>
                </form>
                <script src="../JS/admForm.js"></script>

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