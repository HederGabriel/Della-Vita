<?php 
include_once '../System/session.php';
include_once '../System/db.php';

if (isset($_SESSION['id_cliente'])) {
    $id_cliente = $_SESSION['id_cliente'];
    $stmt = $pdo->prepare("SELECT nome, avatar FROM clientes WHERE id_cliente = :id_cliente");
    $stmt->execute(['id_cliente' => $id_cliente]);
    $cliente = $stmt->fetch();
    if ($cliente) {
        $_SESSION['nome'] = $cliente['nome'];
        $_SESSION['avatar'] = $cliente['avatar'] ?? '../IMG/Profile/Default.png';
    }

    // Buscar itens do pedido com informações do produto
    $stmt = $pdo->prepare("
        SELECT ip.*, p.nome AS nome_produto, p.imagem AS imagem_produto 
        FROM itens_pedido ip
        INNER JOIN produtos p ON ip.id_produto = p.id_produto
        WHERE ip.id_cliente = :id_cliente 
        ORDER BY ip.entrega, ip.id_item_pedido DESC
    ");
    $stmt->execute(['id_cliente' => $id_cliente]);
    $itens = $stmt->fetchAll();

    $itens_entrega = [];
    $itens_local = [];

    foreach ($itens as $item) {
        if ($item['entrega'] === 'casa') {
            $itens_entrega[] = $item;
        } elseif ($item['entrega'] === 'local') {
            $itens_local[] = $item;
        }
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    $redirect_url = $_POST['redirect'] ?? 'index.php';
    header("Location: " . $redirect_url);
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Della Vita - Pedidos</title>
    <link rel="stylesheet" href="../CSS/nav.css">
    <link rel="stylesheet" href="../CSS/pedido.css">
    <link rel="stylesheet" href="/CSS/font.css">
    <link rel="stylesheet" href="/CSS/footer.css">
</head>
<body>
    <nav>
        <img src="..\IMG\Logo2.jpg" alt="Logo" class="logo" onclick="window.location.href='index.php'">
        <div class="nav-links">
            <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">Início</a>
            <a href="Cardapio.php" class="<?= $current_page === 'Cardapio.php' ? 'active' : '' ?>">Cardápio</a>
            <a href="Destaque.php" class="<?= $current_page === 'Destaque.php' ? 'active' : '' ?>">Destaque</a>
        </div>
        <div class="nav-search">
            <input type="text" placeholder="Buscar...">
        </div>
        <?php if (isset($_SESSION['id_cliente'])): ?>
            <div class="user-profile" onclick="toggleMenu(event)">
                <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Foto de Perfil">
            </div>
        <?php else: ?>
            <button class="login-btn" onclick="window.location.href='Login-Cadastro.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search)">Entrar</button>
        <?php endif; ?>
    </nav>

    <div id="user-menu">
        <ul>
            <li><a href="Perfil.php" class="<?= $current_page === 'Perfil.php' ? 'active' : '' ?>">Perfil</a></li>
            <li><a href="Pedidos.php" class="<?= $current_page === 'Pedidos.php' ? 'active' : '' ?>">Pedidos</a></li>
            <li><a href="#" onclick="showLogoutModal()">Sair</a></li>
        </ul>
    </div>

    <div id="overlay" onclick="hideLogoutModal()"></div>
    <div id="logout-modal">
        <p>Tem certeza que deseja sair?</p>
        <button class="confirm-btn" onclick="document.getElementById('logout-form').submit()">Confirmar</button>
        <button class="cancel-btn" onclick="hideLogoutModal()">Cancelar</button>
    </div>
    <script src="../JS/userMenu.js"></script>

    <?php if (isset($cliente)): ?>
        <form id="logout-form" method="POST" style="display: none;">
            <input type="hidden" name="logout" value="1">
        </form>
    <?php endif; ?>

    <main>
        <section class="pedidos">
            <div class="titulo-pedidos">
                <div class="titulo-alinhado">
                    <label class="custom-checkbox">
                        <input type="checkbox" id="casa" onchange="toggleCheckbox(this)">
                        <span class="checkmark"></span>
                        <h2 class="h2-casa">Pedidos para Entrega em Casa</h2>
                    </label>
                </div>
            </div>
            <?php if (!empty($itens_entrega)): ?>
                <?php foreach ($itens_entrega as $item): ?>
                    <div class="pedido-item">
                        <img src="<?= htmlspecialchars($item['imagem_produto']) ?>" alt="Imagem do Produto" class="produto-img">
                        <p><strong>Produto:</strong> <?= htmlspecialchars($item['nome_produto']) ?></p>
                        <p><strong>Quantidade:</strong> <?= $item['quantidade'] ?></p>
                        <p><strong>Tamanho:</strong> <?= strtoupper($item['tamanho']) ?></p>
                        <p><strong>Preço Unitário:</strong> R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Não há pedidos com entrega em casa.</p>
            <?php endif; ?>

            <div class="titulo-pedidos">
                <div class="titulo-alinhado">
                    <label class="custom-checkbox">
                        <input class="baixo" type="checkbox" id="local" onchange="toggleCheckbox(this)">
                        <span class="checkmark"></span>
                        <h2 class="h2-local">Pedidos para Retirada no Local</h2>
                    </label>
                </div>
            </div>
            <?php if (!empty($itens_local)): ?>
                <?php foreach ($itens_local as $item): ?>
                    <div class="pedido-item">
                        <img src="<?= htmlspecialchars($item['imagem_produto']) ?>" alt="Imagem do Produto" class="produto-img">
                        <p><strong>Produto:</strong> <?= htmlspecialchars($item['nome_produto']) ?></p>
                        <p><strong>Quantidade:</strong> <?= $item['quantidade'] ?></p>
                        <p><strong>Tamanho:</strong> <?= strtoupper($item['tamanho']) ?></p>
                        <p><strong>Preço Unitário:</strong> R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Não há pedidos para consumo no local.</p>
            <?php endif; ?>
            <div class="finalizar-pedido-container">
                <button class="finalizarPedido" onclick="window.location.href='..\System\finalizarPedido.php'">
                    Finalizar Pedido
                </button>
            </div>
        </section>
        <script src="..\JS\Pedidos.js"></script>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-logo">
                <img src="..\IMG\Logo1.jpg" alt="Logo Della Vita">
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
                        <p><a onclick="window.location.href='Termo.php'">Termos de Uso</a></p>
                        <p><a onclick="window.location.href='politica.php'">Política de Privacidade</a></p>
                    </div>
                    <div class="contato">
                        <h2>Contatos</h2>
                        <h3>Obrigado pela Preferência!</h3>
                        <label for="t">Número: </label>
                        <p id="t">
                            <a href="https://wa.me/5562999772544?text=Olá%2C%20gostaria%20de%20mais%20informações" target="_blank">
                            Falar no WhatsApp
                            </a>
                        </p>
                        <br>
                        <label for="e">Gmail: </label>
                        <p>
                            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=dellavitaenterprise@gmail.com&su=Olá%20Della+Vita&body=Gostaria%20de%20mais%20informações%20sobre%20seus%20produtos." target="_blank">
                            Enviar mensagem via Gmail
                            </a>
                        </p>
                    </div>
                    <div class="social">
                        <h2>Redes Sociais</h2>
                        <h3>Siga-Nós</h3>
                        <a href="https://www.instagram.com/della.vita.enterprise/profilecard/?igsh=aTk2Y2t4cHlwNHN4" target="_blank"><img src="..\IMG\Icons\instagram.svg" alt="Instagram"></a>
                        <a href="https://wa.me/5562999772544?text=Olá%2C%20gostaria%20de%20mais%20informações" target="_blank"><img src="..\IMG\Icons\whatsapp.svg" alt="Whatsapp"></a>
                        <a href="https://www.facebook.com/share/1APRM1n7BA/" target="_blank"><img src="..\IMG\Icons\facebook.svg" alt="Facebook"></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
