<?php 
include_once '../System/session.php'; // Inclui o arquivo de sessão
include_once '../System/db.php'; // Inclui o arquivo de conexão com o banco de dados

// Verifica se o cliente clicou em "Sair"
if (isset($_POST['logout'])) {
    session_destroy(); // Destruir a sessão
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/'); // Remover cookie de sessão
    }
    header("Location: index.php"); // Redirecionar para a página inicial
    exit();
}

// Verifica se a sessão está ativa
if (!isset($_SESSION['id_cliente'])) {
    header("Location: Login-Cadastro.php"); // Redireciona para login se não estiver logado
    exit();
}

// Obtém os dados do cliente do banco de dados
$id_cliente = $_SESSION['id_cliente'];
$stmt = $pdo->prepare("SELECT nome, avatar, telefone, email FROM clientes WHERE id_cliente = ?");
$stmt->execute([$id_cliente]);
$cliente = $stmt->fetch();

if ($cliente) {
    $_SESSION['nome'] = $cliente['nome'];
    $_SESSION['avatar'] = $cliente['avatar'] ?? '../IMG/Profile/Default.png';
    $_SESSION['telefone'] = $cliente['telefone'] ?? '';
    $_SESSION['email'] = $cliente['email'] ?? ''; 
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="../CSS/nav.css"> <!-- Estilo do nav -->
    <link rel="stylesheet" href="../CSS/meusDados.css"> <!-- Estilo específico do perfil -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"> <!-- Google Material Symbols -->
    <script src="../JS/userMenu.js"></script> 
    <link rel="stylesheet" href="/CSS/font.css">
    <link rel="stylesheet" href="/CSS/footer.css">
    <link rel="shortcut icon" href="../IMG/favicon.ico" type="image/x-icon">
</head>

<body>
    <nav>
        <div class="logo">
            <img src="..\IMG\Logo2.jpg" alt="Logo" class="logo" onclick="window.location.href='index.php'">
        </div>
        <div class="nav-links">
            <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">Início</a>
            <a href="Cardapio.php" class="<?= $current_page === 'Cardapio.php' ? 'active' : '' ?>">Cardápio</a>
        </div>
        <div class="nav-search">
            <input type="text" id="search-bar" placeholder="Buscar..." autocomplete="off">
        </div>
        <?php if (isset($_SESSION['id_cliente'])): ?>
            <!-- Exibir perfil do usuário -->
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
    <div id="logout-modal" class="centered">
        <p>Tem certeza que deseja sair?</p>
        <button class="confirm-btn" onclick="document.getElementById('logout-form').submit()">Confirmar</button>
        <button class="cancel-btn" onclick="hideLogoutModal()">Cancelar</button>
    </div>
    <form id="logout-form" method="POST" style="display: none;">
        <input type="hidden" name="logout" value="1">
    </form>

    <header>
        <div class="avatar-container">
            <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Avatar" class="avatar">
            <div class="client-name">
                <?= htmlspecialchars($_SESSION['nome']) ?>
            </div>
        </div>
    </header>

    <main>

        <form  method="post">

            <label for="nome">Nome: </label>
            <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($_SESSION['nome']) ?>" required><br>

            <label for="telefone">Telefone: </label>
            <input type="text" name="telefone" id="telefone" value="<?= htmlspecialchars($_SESSION['telefone']) ?>"><br>

            <button type="button" onclick="window.location.href='Mudar-Email.php'">Alterar Email</button><br>


            <button type="submit">Alterar</button>
            <br>
            <button type="button" id="btn-voltar" onclick="window.location.href='Perfil.php'">Voltar</button>

        </form>
        <?php 
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['logout'])) {
                $novo_nome = $_POST['nome'] ?? '';
                $novo_telefone = trim($_POST['telefone']) !== '' ? $_POST['telefone'] : null;
            
                // Atualiza os dados no banco de dados
                $stmt = $pdo->prepare("UPDATE clientes SET nome = :nome, telefone = :telefone WHERE id_cliente = :id_cliente");
                $stmt->execute([
                    'nome' => $novo_nome,
                    'telefone' => $novo_telefone,
                    'id_cliente' => $id_cliente
                ]);
            
                // Atualiza a sessão com os novos dados
                $_SESSION['nome'] = $novo_nome;
                $_SESSION['telefone'] = $novo_telefone ?? '';
            }

            // Obtém os dados atualizados do cliente
            $stmt = $pdo->prepare("SELECT nome, avatar, telefone, email FROM clientes WHERE id_cliente = ?");
            $stmt->execute([$id_cliente]);
            $cliente = $stmt->fetch();

            if ($cliente) {
                $_SESSION['nome'] = $cliente['nome'];
                $_SESSION['avatar'] = $cliente['avatar'] ?? '../IMG/Profile/Default.png';
                $_SESSION['telefone'] = $cliente['telefone'] ?? '';
                $_SESSION['email'] = $cliente['email'] ?? ''; 
            }
        ?>

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

    <script src="../JS/busca-filter.js"></script>
</body>

</html>