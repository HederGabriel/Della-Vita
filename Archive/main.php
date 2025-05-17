    <main class="produto-detalhe">
        <img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
        <h1><?= htmlspecialchars($produto['nome']) ?></h1>
        <p><?= htmlspecialchars($descricaoCompleta) ?></p>
        <strong>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></strong>
        <br>
        <button>Adicionar ao carrinho</button>
    </main>