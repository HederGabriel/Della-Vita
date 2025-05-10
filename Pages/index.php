<?php 
include_once '../System/session.php'; // Inclui o arquivo de sessão
include_once '../System/db.php'; // Inclui o arquivo de conexão com o banco de dados

// Verificar se o usuário está logado
if (isset($_SESSION['id_cliente'])) {
    $id_cliente = $_SESSION['id_cliente'];
    $stmt = $pdo->prepare("SELECT nome, avatar FROM clientes WHERE id_cliente = :id_cliente");
    $stmt->execute(['id_cliente' => $id_cliente]);
    $cliente = $stmt->fetch();
    if ($cliente) {
        $_SESSION['nome'] = $cliente['nome'];
        $_SESSION['avatar'] = $cliente['avatar'] ?? '../IMG/Profile/Default.png';
    }
}

// Logout do usuário
if (isset($_POST['logout'])) {
    session_destroy(); // Destruir a sessão
    header("Location: index.php"); // Redirecionar para a página inicial
    exit();
}

// Página atual
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Della Vita</title>
    <link rel="stylesheet" href="../CSS/nav.css"> <!-- Estilo do nav -->
    <link rel="stylesheet" href="../CSS/index.css"> <!-- Estilo específico do index -->
    <link rel="stylesheet" href="/CSS/font.css">

</head>
<body>
    <nav>
        <div class="logo">
            <a href="index.php">LOGO</a>
        </div>
        <div class="nav-links">
            <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">Início</a>
            <a href="Cardapio.php" class="<?= $current_page === 'Cardapio.php' ? 'active' : '' ?>">Cardápio</a>
            <a href="#" class="<?= $current_page === 'pagina3.php' ? 'active' : '' ?>">Página 3</a>
        </div>
        <div class="nav-search">
            <input type="text" placeholder="Buscar...">
        </div>
        <?php if (isset($_SESSION['id_cliente'])): ?>
            <!-- Exibir perfil do usuário -->
            <div class="user-profile" onclick="toggleMenu(event)">
                <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Foto de Perfil">
            </div>
        <?php else: ?>
            <!-- Botão de login -->
            <button class="login-btn" onclick="window.location.href='login-Cadastro.php'">Entrar</button>
        <?php endif; ?>
    </nav>
    <div id="user-menu">
        <ul>
            <li><a href="Perfil.php" class="<?= $current_page === 'Perfil.php' ? 'active' : '' ?>">Perfil</a></li>
            <li><a href="#" class="<?= $current_page === 'Pedidos.php' ? 'active' : '' ?>">Pedidos</a></li>
            <li><a href="#" onclick="showLogoutModal()">Sair</a></li>
        </ul>
    </div>
    <div id="overlay" onclick="hideLogoutModal()"></div>
    <div id="logout-modal">
        <p>Tem certeza que deseja sair?</p>
        <button class="confirm-btn" onclick="document.getElementById('logout-form').submit()">Confirmar</button>
        <button class="cancel-btn" onclick="hideLogoutModal()">Cancelar</button>
    </div>
    
    <?php if (isset($cliente)): ?>
        <form id="logout-form" method="POST" style="display: none;">
            <input type="hidden" name="logout" value="1">
        </form>
    <?php endif; ?>
    <script src="../JS/userMenu.js"></script>

    <Header> 
        <h1>Lorem ipsum dolor sit amet consectetur, adipisicing elit. </h1>
        <img src="..\IMG\header.jpg" alt="Pizza">
    </Header>

    <main>
        <?php 
            // Buscar 4 pizzas aleatórias do tipo normal
            $stmtPizzas = $pdo->prepare("SELECT nome, preco, imagem, descricao_resumida FROM produtos WHERE tipo = 'normal' ORDER BY RAND() LIMIT 4");
            $stmtPizzas->execute();
            $produtos = $stmtPizzas->fetchAll();

            // Buscar 1 combo aleatório
            $stmtCombo = $pdo->prepare("SELECT nome, preco, imagem, descricao_resumida FROM produtos WHERE tipo = 'combo' ORDER BY RAND() LIMIT 1");
            $stmtCombo->execute();
            $combo = $stmtCombo->fetch();
        ?>

        <section class="combo">
            <?php if ($combo): ?>
                <img src="<?= htmlspecialchars($combo['imagem']) ?>" alt="<?= htmlspecialchars($combo['descricao_resumida']) ?>">
                <div class="combo-info">
                    <h1><?= htmlspecialchars($combo['nome']) ?> - R$ <?= number_format($combo['preco'], 2, ',', '.') ?></h1>
                    <p><?= htmlspecialchars($combo['descricao_resumida']) ?></p>
                    <button onclick="window.location.href='#'">Ver Combo</button>
                </div>
            <?php else: ?>
                <p>Nenhum combo disponível no momento.</p>
            <?php endif; ?>
        </section>

        <section class="pizza">
            <h1 class="t-categoria">Categorias</h1>
            <div class="categoria"><p>Pizzas Tradicionais</p></div>
            <div class="categoria"><p>Pizzas Doces</p></div>
            <div class="categoria"><p>Pizzas Especiais</p></div>
            <div class="categoria"><p>Bebidas</p></div>

            <h1 class="t-pizza">Pizzas Populares</h1>
            <div class="grid-espaco"></div>
            <button a href="" class="btn-ver-mais">Ver mais</button>

            <?php foreach ($produtos as $produto): ?>
                <div class="produto-card">
                    <img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                    <h3><?= htmlspecialchars($produto['nome']) ?></h3>
                    <p><?= htmlspecialchars($produto['descricao_resumida']) ?></p>
                    <strong>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></strong>
                    <br>
                    <button a href="#">Escolher</button>
                </div>
            <?php endforeach; ?>
        </section>

    </main>

    <footer>
        <img src="..\IMG\Logo.jpg" alt="Logo Della Vita">
        <h1>Explore mais</h1>

        <div class="social">
            <h2>Redes Sociais</h2>
            <h3>Siga-Nós</h3>

            <img src="..\IMG\Icons\instagram.svg" alt="Instagram">
      

            <img src="..\IMG\Icons\whatsapp.svg" alt="Whatsapp">
          
            
            <img src="..\IMG\Icons\facebook.svg" alt="">

        </div>

        <div class="contato">
            <h2>Contatos</h2>

            <h3>Obrigado pela Preferencia!</h3>

            <label for="t">Número <img src="..\IMG\Icons\whatsapp.svg" alt="Whatsapp"> : </label>
            <p id="t">
                <a href="https://wa.me/5562999772544?text=Olá%2C%20gostaria%20de%20mais%20informações" target="_blank">
                Falar no WhatsApp
                </a>
            </p>
            <br>

            <label for="e"><img src="..\IMG\Icons\google.svg" alt="Google">mail: </label>
            <p>
                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=dellavitaenterprise@gmail.com&su=Olá%20Della+Vita&body=Gostaria%20de%20mais%20informações%20sobre%20seus%20produtos." target="_blank">
                    Enviar e-mail via Gmail
                </a>
            </p>
        </div>

        <div class="links"></div>
    </footer>
</body>
</html>