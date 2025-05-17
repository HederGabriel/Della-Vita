<?php 
include_once '../System/session.php';
include_once '../System/db.php';

// Garantir que o usuário está logado para acessar a página
if (!isset($_SESSION['id_cliente'])) {
    header("Location: login-Cadastro.php");
    exit();
}

$id_cliente = $_SESSION['id_cliente'] ?? null;
$cliente = null;

if ($id_cliente) {
    $stmt = $pdo->prepare("SELECT nome, avatar FROM clientes WHERE id_cliente = :id_cliente");
    $stmt->execute(['id_cliente' => $id_cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($cliente) {
        // Atualizar dados da sessão se necessário
        $_SESSION['nome'] = $cliente['nome'];
        $_SESSION['avatar'] = $cliente['avatar'] ?? '../IMG/Profile/Default.png';
    }
}

// Logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);

// Validar parâmetro id do produto via GET
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    echo "Produto não encontrado.";
    exit();
}

$id_produto = (int)$_GET['id'];

// Buscar dados do produto com query preparada
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

// Gerar caminho absoluto seguro para o JSON
$jsonFullPath = realpath(__DIR__ . '/../Json/' . basename($dadosJsonPath));

if ($jsonFullPath && file_exists($jsonFullPath)) {
    $jsonContent = file_get_contents($jsonFullPath);
    $dadosExtra = json_decode($jsonContent, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $descricaoCompleta = 'Erro ao ler os dados JSON.';
        $ingredientes = [];
    } else {
        $descricaoCompleta = $dadosExtra['descricao_completa'] ?? 'Descrição não disponível';
        $ingredientes = $dadosExtra['ingredientes'] ?? [];
    }
} else {
    $descricaoCompleta = 'Arquivo de descrição não encontrado.';
    $ingredientes = [];
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
</head>
<body>
    <nav>
        <img src="../IMG/Logo2.jpg" alt="Logo" class="logo" onclick="window.location.href='index.php'">

        <div class="nav-links">
            <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">Início</a>
            <a href="Cardapio.php" class="<?= $current_page === 'Cardapio.php' ? 'active' : '' ?>">Cardápio</a>
            <a href="Destaque.php" class="<?= $current_page === 'Destaque.php' ? 'active' : '' ?>">Destaque</a>
        </div>

        <div class="nav-search">
            <input type="text" placeholder="Buscar...">
        </div>

        <?php if ($cliente): ?>
            <div class="user-profile" onclick="toggleMenu(event)">
                <img src="<?= htmlspecialchars($cliente['avatar'], ENT_QUOTES | ENT_HTML5) ?>" alt="Foto de Perfil">
            </div>
        <?php else: ?>
            <button class="login-btn" onclick="window.location.href='login-Cadastro.php'">Entrar</button>
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
                <button onclick="selecionarTamanho(this)" data-preco="<?= number_format($preco * 0.8, 2, '.', '') ?>">Pequeno</button>
                <button class="ativo" onclick="selecionarTamanho(this)" data-preco="<?= number_format($preco, 2, '.', '') ?>">Médio</button>
                <button onclick="selecionarTamanho(this)" data-preco="<?= number_format($preco * 1.3, 2, '.', '') ?>">Grande</button>
            </div>

            <div class="compra">
                <h2 class="preco-formatado">R$ <?= number_format($preco, 2, ',', '.') ?></h2>

                <div class="entrega">
                    <h3>Escolher forma de entrega:</h3>
                    <button class="local space" onclick="selecionarEntrega(this)" data-entrega="local">Retirar no Local</button>
                    <button class="local ativo" onclick="selecionarEntrega(this)" data-entrega="casa">Receber em Casa</button>
                </div>

                <br/>
                <button class="addPedido">Adicionar ao Pedido</button>
            </div>
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
                        <p onclick="window.location.href='login-Cadastro.php'">Entrar/Cadastro</p>
                        <p onclick="window.location.href='Perfil.php'">Meu Perfil</p>
                        <p onclick="window.location.href='Cardapio.php'">Cardápio</p>
                        <p><a href="#" onclick="return false;">Termos de Uso</a></p>
                        <p><a href="#" onclick="return false;">Política de Privacidade</a></p>
                    </div>
                    <div class="contato">
                        <h2>Contatos</h2>
                        <p>Telefone: (71) 99999-9999</p>
                        <p>Email: dellavita@gmail.com</p>
                        <p>Localização: Salvador, BA</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="../JS/Produto.js"></script>
</body>
</html>
