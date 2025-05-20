<?php 
include_once '../System/session.php';
include_once '../System/db.php';

if (!isset($_SESSION['id_cliente'])) {
    header("Location: Login-Cadastro.php?redirect=Pedidos.php");
    exit();
}

$id_cliente = $_SESSION['id_cliente'];

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

$stmt = $pdo->prepare("
    SELECT ip.*, p.nome AS nome_produto, p.imagem AS imagem_produto 
    FROM itens_pedido ip
    INNER JOIN produtos p ON ip.id_produto = p.id_produto
    WHERE ip.id_cliente = :id_cliente AND ip.id_pedido IS NULL
    ORDER BY ip.entrega, ip.id_item_pedido DESC
");


$stmt->execute(['id_cliente' => $id_cliente]);
$itens = $stmt->fetchAll();

$itens_entrega = [];
$itens_local = [];

foreach ($itens as $item) {
    $entrega_tipo = isset($item['entrega']) ? $item['entrega'] : '';
    if ($entrega_tipo === 'casa') {
        $itens_entrega[] = $item;
    } elseif ($entrega_tipo === 'local') {
        $itens_local[] = $item;
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Della Vita - Acompanhar</title>
    <link rel="stylesheet" href="../CSS/nav.css" />
    <link rel="stylesheet" href="../CSS/acompanhar.css" />
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
                        <p><a onclick="window.location.href='Termo.php'">Termos de Uso</a></p>
                        <p><a onclick="window.location.href='politica.php'">Política de Privacidade</a></p>
                    </div>
                    <div class="contato">
                        <h2>Contatos</h2>
                        <h3>Obrigado pela Preferência!</h3>
                        <label for="t">Número: </label>
                        <p id="t">
                            <a href="https://wa.me/5562999772544?text=Olá%2C%20gostaria%20de%20mais%20informações" target="_blank" rel="noopener noreferrer">
                                Falar no WhatsApp
                            </a>
                        </p>
                        <br />
                        <label for="e">Gmail: </label>
                        <p>
                            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=dellavitaenterprise@gmail.com&su=Olá%20Della+Vita&body=Gostaria%20de%20mais%20informações%20sobre%20seus%20produtos!" target="_blank" rel="noopener noreferrer">
                                Dellavitaenterprise@gmail.com
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer> 
</body>

</html>