<?php 
// Arquivo: Pedidos.php

include_once '../System/session.php';
include_once '../System/db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['id_cliente'])) {
    header("Location: Login-Cadastro.php?redirect=Pedidos.php");
    exit();
}

$id_cliente = $_SESSION['id_cliente'];

// Busca dados do cliente para exibir nome e avatar
$stmt = $pdo->prepare("SELECT nome, avatar FROM clientes WHERE id_cliente = :id_cliente");
$stmt->execute(['id_cliente' => $id_cliente]);
$cliente = $stmt->fetch();

if ($cliente) {
    $_SESSION['nome'] = $cliente['nome'];
    $_SESSION['avatar'] = !empty($cliente['avatar']) ? $cliente['avatar'] : '../IMG/Profile/Default.png';
} else {
    session_destroy();
    header("Location: Login-Cadastro.php");
    exit();
}

// Busca os itens do pedido do cliente ainda não finalizados (id_pedido IS NULL)
$stmt = $pdo->prepare("
    SELECT ip.*, p.nome AS nome_produto, p.imagem AS imagem_produto 
    FROM itens_pedido ip
    INNER JOIN produtos p ON ip.id_produto = p.id_produto
    WHERE ip.id_cliente = :id_cliente AND ip.id_pedido IS NULL
    ORDER BY ip.entrega, ip.id_item_pedido DESC
");
$stmt->execute(['id_cliente' => $id_cliente]);
$itens = $stmt->fetchAll();

// Separa os itens por tipo de entrega
$itens_entrega = [];
$itens_local = [];

foreach ($itens as $item) {
    $entrega_tipo = $item['entrega'] ?? '';
    if ($entrega_tipo === 'casa') {
        $itens_entrega[] = $item;
    } elseif ($entrega_tipo === 'local') {
        $itens_local[] = $item;
    }
}

// Processa logout
if (isset($_POST['logout'])) {
    session_destroy();
    $redirect_url = $_POST['redirect'] ?? 'index.php';
    header("Location: " . $redirect_url);
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);

// Processa o envio do pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo_pedido'])) {
    $tipo_pedido = $_POST['tipo_pedido'];
    $endereco = ($tipo_pedido === 'casa') ? trim($_POST['endereco'] ?? '') : null;

    // Validação para pedidos de entrega
    if ($tipo_pedido === 'casa' && empty($endereco)) {
        die("Endereço é obrigatório para pedidos de entrega.");
    }

    // Insere novo pedido
    $stmt = $pdo->prepare("INSERT INTO pedidos (id_cliente, tipo_pedido, endereco, status, data_pedido) VALUES (:id_cliente, :tipo_pedido, :endereco, 'pendente', NOW())");
    $stmt->execute([
        'id_cliente' => $id_cliente,
        'tipo_pedido' => $tipo_pedido,
        'endereco' => $endereco
    ]);

    $id_pedido = $pdo->lastInsertId();

    // Atualiza itens do pedido para associar ao novo pedido
    $stmt = $pdo->prepare("UPDATE itens_pedido SET id_pedido = :id_pedido WHERE id_cliente = :id_cliente AND id_pedido IS NULL AND entrega = :tipo_pedido");
    $stmt->execute([
        'id_pedido' => $id_pedido,
        'id_cliente' => $id_cliente,
        'tipo_pedido' => $tipo_pedido
    ]);

    header("Location: acompanharPedido.php?pedido=" . $id_pedido);
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Della Vita - Pedidos</title>
    <link rel="stylesheet" href="../CSS/nav.css" />
    <link rel="stylesheet" href="../CSS/pedido.css" />
    <link rel="stylesheet" href="/CSS/font.css" />
    <link rel="stylesheet" href="/CSS/footer.css" />
</head>
<body>
    <nav>
        <img src="../IMG/Logo2.jpg" alt="Logo" class="logo" onclick="window.location.href='index.php'" />
        <div class="nav-links">
            <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">Início</a>
            <a href="Cardapio.php" class="<?= $current_page === 'Cardapio.php' ? 'active' : '' ?>">Cardápio</a>
            <a href="Destaque.php" class="<?= $current_page === 'Destaque.php' ? 'active' : '' ?>">Destaque</a>
        </div>
        <div class="nav-search">
            <input type="text" placeholder="Buscar..." />
        </div>
        <?php if (isset($_SESSION['id_cliente'])): ?>
            <div class="user-profile" onclick="toggleMenu(event)">
                <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Foto de Perfil" />
            </div>
        <?php else: ?>
            <button class="login-btn" onclick="window.location.href='Login-Cadastro.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search)">Entrar</button>
        <?php endif; ?>
    </nav>

    <div id="user-menu" style="display:none;">
        <ul>
            <li><a href="Perfil.php" class="<?= $current_page === 'Perfil.php' ? 'active' : '' ?>">Perfil</a></li>
            <li><a href="Pedidos.php" class="<?= $current_page === 'Pedidos.php' ? 'active' : '' ?>">Pedidos</a></li>
            <li><a href="#" onclick="showLogoutModal()">Sair</a></li>
        </ul>
    </div>

    <div id="overlay" onclick="hideLogoutModal()" style="display:none;"></div>
    <div id="logout-modal" style="display:none;">
        <p>Tem certeza que deseja sair?</p>
        <button class="confirm-btn" onclick="document.getElementById('logout-form').submit()">Confirmar</button>
        <button class="cancel-btn" onclick="hideLogoutModal()">Cancelar</button>
    </div>
    <script src="../JS/userMenu.js"></script>

    <form id="logout-form" method="POST" style="display: none;">
        <input type="hidden" name="logout" value="1" />
    </form>

    <main>
        <section class="pedidos">
            <div class="acompanhar-container">
                <button class="btn-acompanhar" onclick="window.location.href='acompanharPedido.php'">📦 Acompanhar Pedido</button>
            </div>

            <!-- Checkbox Pedidos para Entrega em Casa -->
            <div class="titulo-pedidos">
                <label class="custom-checkbox" for="casa">
                    <input type="checkbox" id="casa" name="tipo_pedido" value="casa" onchange="toggleCheckbox(this)" />
                    <span class="checkmark"></span>
                    <h2 class="h2-casa">Pedidos para Entrega em Casa</h2>
                </label>
            </div>

            <?php if (!empty($itens_entrega)): ?>
                <?php foreach ($itens_entrega as $item): ?>
                    <div class="pedido-item">
                        <img src="<?= htmlspecialchars($item['imagem_produto']) ?>" class="produto-img" alt="Imagem Produto" />
                        <p><strong>Produto:</strong> <?= htmlspecialchars($item['nome_produto']) ?></p>
                        <p><strong>Quantidade:</strong> <?= (int)$item['quantidade'] ?></p>
                        <p><strong>Tamanho:</strong> <?= strtoupper(htmlspecialchars($item['tamanho'])) ?></p>
                        <p><strong>Preço Unitário:</strong> R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Não há pedidos com entrega em casa.</p>
            <?php endif; ?>

            <!-- Checkbox Pedidos para Retirada no Local -->
            <div class="titulo-pedidos">
                <label class="custom-checkbox" for="local">
                    <input type="checkbox" id="local" name="tipo_pedido" value="local" onchange="toggleCheckbox(this)" />
                    <span class="checkmark"></span>
                    <h2 class="h2-local">Pedidos para Retirada no Local</h2>
                </label>
            </div>

            <?php if (!empty($itens_local)): ?>
                <?php foreach ($itens_local as $item): ?>
                    <div class="pedido-item">
                        <img src="<?= htmlspecialchars($item['imagem_produto']) ?>" class="produto-img" alt="Imagem Produto" />
                        <p><strong>Produto:</strong> <?= htmlspecialchars($item['nome_produto']) ?></p>
                        <p><strong>Quantidade:</strong> <?= (int)$item['quantidade'] ?></p>
                        <p><strong>Tamanho:</strong> <?= strtoupper(htmlspecialchars($item['tamanho'])) ?></p>
                        <p><strong>Preço Unitário:</strong> R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Não há pedidos para consumo no local.</p>
            <?php endif; ?>

            <!-- Formulário finalização -->
            <form id="form-finalizar" method="POST" onsubmit="return validarFormulario()">
                <input type="hidden" name="tipo_pedido" id="input-entrega" required />
                <input type="hidden" name="endereco" id="hidden-endereco" />
                <button type="submit" class="finalizarPedido" id="btnFinalizar" disabled>Finalizar Pedido</button>
            </form>

            <!-- Modal para endereço, só aparece para entrega em casa -->
            <div id="modal-endereco" class="modal-endereco" style="display: none;">
                <div class="modal-content">
                    <h2>Informe o Endereço de Entrega</h2>
                    <input type="text" id="input-modal-endereco" placeholder="Digite seu endereço" />
                    <div class="modal-actions">
                        <button id="confirmar-endereco" type="button" class="btn-salvar">Confirmar</button>
                        <button type="button" onclick="fecharModalEndereco()" class="btn-cancelar">Cancelar</button>
                    </div>
                </div>
            </div>

            <!-- Google Places API para autocomplete -->
            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBGn0vqgAS22ZHzFENXQtDj1AqjgPUVjTo&libraries=places" async defer></script>
        </section>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-logo">
                <img src="../IMG/Logo1.jpg" alt="Logo Della Vita" />
            </div>
            <div class="footer-conteudo">
                <div class="footer-topo">
                    <h1>Explore mais</h1>
                </div>
                <div class="footer-boxes">
                    <div class="links">
                        <h2>Principais Links</h2>
                        <p onclick="window.location.href='index.php'">Início</p>
                        <p onclick="window.location.href='Login-Cadastro.php'">Entrar/Cadastro</p>
                        <p onclick="window.location.href='Perfil.php'">Meu Perfil</p>
                        <p onclick="window.location.href='Cardapio.php'">Cardápio</p>
                        <p><a href="Termo.php">Termos de Uso</a></p>
                        <p><a href="politica.php">Política de Privacidade</a></p>
                    </div>
                    <div class="contato">
                        <h2>Contatos</h2>
                        <h3>Obrigado pela Preferência!</h3>
                        <label for="t">Número: </label>
                        <p id="t">
                            <a href="https://wa.me/5562999772544?text=Olá%2C%20gostaria%20de%20mais%20informações" target="_blank">Falar no WhatsApp</a>
                        </p>
                        <br />
                        <label for="e">Gmail: </label>
                        <p>
                            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=dellavitaenterprise@gmail.com&su=Olá%20Della+Vita&body=Gostaria%20de%20mais%20informações%20sobre%20seus%20produtos." target="_blank">Enviar mensagem via Gmail</a>
                        </p>
                    </div>
                    <div class="social">
                        <h2>Redes Sociais</h2>
                        <h3>Siga-Nós</h3>
                        <a href="https://www.instagram.com/della.vita.enterprise/profilecard/?igsh=aTk2Y2t4cHlwNHN4" target="_blank"><img src="../IMG/Icons/instagram.svg" alt="Instagram" /></a>
                        <a href="https://wa.me/5562999772544?text=Olá%2C%20gostaria%20de%20mais%20informações" target="_blank"><img src="../IMG/Icons/whatsapp.svg" alt="Whatsapp" /></a>
                        <a href="https://www.facebook.com/share/1APRM1n7BA/" target="_blank"><img src="../IMG/Icons/facebook.svg" alt="Facebook" /></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="../JS/userMenu.js"></script>
    <script src="../JS/Pedidos.js"></script>

</body>
</html>
