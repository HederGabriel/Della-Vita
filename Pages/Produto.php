<?php 
// Arquivo: Produto.php

include_once '../System/session.php';
include_once '../System/db.php';

$id_cliente = $_SESSION['id_cliente'] ?? null;
$cliente = null;

if ($id_cliente) {
    $stmt = $pdo->prepare("SELECT nome, avatar FROM clientes WHERE id_cliente = :id_cliente");
    $stmt->execute(['id_cliente' => $id_cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($cliente) {
        $_SESSION['nome'] = $cliente['nome'];
        $_SESSION['avatar'] = !empty($cliente['avatar']) ? $cliente['avatar'] : '../IMG/Profile/Default.png';
    }
}

if (isset($_POST['logout'])) {
    session_destroy(); // Destrói a sessão

    // Redireciona de volta para a mesma página
    $redirect_url = $_POST['redirect'] ?? 'index.php';
    header("Location: " . $redirect_url);
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);

// Validar o ID do produto
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    echo "Produto não encontrado.";
    exit();
}

$id_produto = (int)$_GET['id'];

$comando = $pdo->prepare("SELECT nome, preco, imagem, dadosPagina FROM produtos WHERE id_produto = :id_produto");
$comando->execute(['id_produto' => $id_produto]);
$produto = $comando->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    echo "Produto não encontrado.";
    exit();
}

$nome = $produto['nome'];
$preco = (float)$produto['preco'];
$imagem = $produto['imagem'];
$dadosJsonPath = $produto['dadosPagina'];

// Carregar JSON com verificação de segurança e robustez
$descricaoCompleta = 'Descrição não disponível';
$ingredientes = [];

if ($dadosJsonPath && preg_match('/^[\w\-]+\.json$/', basename($dadosJsonPath))) {
    $jsonPath = __DIR__ . '/../Json/' . basename($dadosJsonPath);
    if (is_file($jsonPath) && is_readable($jsonPath)) {
        $jsonContent = file_get_contents($jsonPath);
        $dadosExtra = json_decode($jsonContent, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $descricaoCompleta = $dadosExtra['descricao_completa'] ?? 'Descrição não disponível';
            $ingredientes = $dadosExtra['ingredientes'] ?? [];
        } else {
            $descricaoCompleta = 'Erro ao ler os dados JSON.';
        }
    } else {
        $descricaoCompleta = 'Arquivo de descrição não encontrado.';
    }
} else {
    $descricaoCompleta = 'Caminho do arquivo de descrição inválido.';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($nome, ENT_QUOTES | ENT_HTML5) ?> - Della Vita</title>
    <link rel="stylesheet" href="../CSS/nav.css" /> 
    <link rel="stylesheet" href="../CSS/produto.css" /> 
    <link rel="stylesheet" href="../CSS/font.css" />
    <link rel="stylesheet" href="../CSS/footer.css" />
    <link rel="shortcut icon" href="../IMG/favicon.ico" type="image/x-icon">
</head>
<body>
    <nav>
        <img src="../IMG/Logo2.jpg" alt="Logo" class="logo" onclick="window.location.href='index.php'">

        <div class="nav-links">
            <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">Início</a>
            <a href="Cardapio.php" class="<?= $current_page === 'Cardapio.php' ? 'active' : '' ?>">Cardápio</a>
        </div>

        <div class="nav-search">
            <input type="text" id="search-bar" placeholder="Buscar..." autocomplete="off">
        </div>

        <?php if ($cliente): ?>
            <div class="user-profile" onclick="toggleMenu(event)">
                <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Foto de Perfil">
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

    <?php if ($cliente): ?>
        <form id="logout-form" method="POST" style="display: none;">
            <input type="hidden" name="logout" value="1" />
        </form>
    <?php endif; ?>

    <script src="../JS/userMenu.js"></script>

    <header> 
        <h1><?= htmlspecialchars($nome, ENT_QUOTES | ENT_HTML5) ?></h1>
        <img src="<?= htmlspecialchars($imagem, ENT_QUOTES | ENT_HTML5) ?>" alt="<?= htmlspecialchars($nome, ENT_QUOTES | ENT_HTML5) ?>" />
        <p><?= nl2br(htmlspecialchars($descricaoCompleta, ENT_QUOTES | ENT_HTML5)) ?></p>
    </header>

    <main>
        <section>
            <div class="left-col">
                <div class="ingredientes">
                    <h3>Lista de Ingredientes:</h3>
                    <?php if (!empty($ingredientes)): ?>
                    <ul>
                        <?php foreach ($ingredientes as $ingrediente): ?>
                            <li><?= htmlspecialchars($ingrediente, ENT_QUOTES | ENT_HTML5) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <p>Sem ingredientes listados.</p>
                    <?php endif; ?>
                </div>

                <div class="tamanho">
                    <h2>Escolher tamanho da Pizza:</h2>
                    <button id="pq" onclick="selecionarTamanho(this)" data-preco="<?= number_format($preco * 0.8, 2, '.', '') ?>">Pequeno</button>
                    <button id="m" class="ativo" onclick="selecionarTamanho(this)" data-preco="<?= number_format($preco, 2, '.', '') ?>">Médio</button>
                    <button id="g" onclick="selecionarTamanho(this)" data-preco="<?= number_format($preco * 1.3, 2, '.', '') ?>">Grande</button>
                </div>
            </div>

            <!-- Preço formatado -->
            <div class="compra">
                <h2 id="preco-formatado" class="preco-formatado">R$ <?= number_format($preco, 2, ',', '.') ?></h2>
                
                <div class="entrega">
                    <h3>Escolher forma de entrega:</h3>
                    <button class="local space" onclick="selecionarEntrega(this)" data-entrega="local">Retirar no Local</button>
                    <button class="local ativo" onclick="selecionarEntrega(this)" data-entrega="casa">Receber em Casa</button>
                </div>

                <br/>
                <?php if ($cliente): ?>
                    <button class="addPedido" onclick="abrirModalQuantidade()">Adicionar ao Pedido</button>
                <?php else: ?>
                    <button class="addPedido" onclick="window.location.href='Login-Cadastro.php'">Adicionar ao Pedido</button>
                <?php endif; ?>

            </div>
        </section>
    </main>

    <footer>
        <div class="footer-container">
            <!-- Logo à esquerda -->
            <div class="footer-logo">
                <img src="..\IMG\Logo1.jpg" alt="Logo Della Vita">
            </div>

            <!-- Conteúdo à direita -->
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

    <div id="modal-quantidade" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Selecionar Quantidade</h2>
            <input type="number" id="quantidade" value="1" min="1" onchange="atualizarTotal()" />
            <p>Total: <span id="total-modal">R$ 0,00</span></p>
            <div class="modal-buttons">
                <button onclick="fecharModal()">Cancelar</button>
                <button onclick="confirmarAdicionar()">Confirmar</button>
            </div>
        </div>
    </div>
    <div id="modal-confirmacao" class="modal-pedido" style="display: none;">
        <div class="modal-content-pedido">
            <h2>Produto adicionado com sucesso!</h2>
            <p>Deseja continuar comprando ou ir para seu pedido?</p>
            <div class="modal-buttons-pedido">
                <button onclick="fecharModalConfirmacao()">Continuar Comprando</button>
                <button onclick="window.location.href='Pedidos.php'">Ir ao Pedido</button>
            </div>
        </div>
    </div>
    <script src="../JS/Produto.js"></script>
    <script src="../JS/busca-filter.js"></script>
</body>
</html>
