<div class="container">
    <h2 class="titulo-grid">Pizzas Populares</h2>
    
    <?php foreach ($produtos as $produto): ?>
        <div class="produto-card">
            <img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
            <h3><?= htmlspecialchars($produto['nome']) ?></h3>
            <p><?= htmlspecialchars($produto['descricao_resumida']) ?></p>
            <strong>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></strong>
            <button>Escolher</button>
        </div>
    <?php endforeach; ?>
</div>