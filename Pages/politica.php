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

if (isset($_POST['logout'])) {
    session_destroy(); // Destrói a sessão

    // Redireciona de volta para a mesma página
    $redirect_url = $_POST['redirect'] ?? 'index.php';
    header("Location: " . $redirect_url);
    exit();
}

// Página atual
$current_page = basename($_SERVER['PHP_SELF']);
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Política de Privacidade | Pizzaria Sabor da Vila</title>
    <link rel="stylesheet" href="../CSS/politica.css">
    <link rel="stylesheet" href="/CSS/footer.css">
    <link rel="stylesheet" href="../CSS/nav.css">
    <script src="..\JS\userMenu.js"></script>
    <link rel="shortcut icon" href="../IMG/favicon.ico" type="image/x-icon">
</head>
<body>
    <nav>
        <img src="..\IMG\Logo2.jpg" alt="Logo" class="logo" onclick="window.location.href='index.php'">

        <div class="nav-links">
            <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">Início</a>
            <a href="Cardapio.php" class="<?= $current_page === 'Cardapio.php' ? 'active' : '' ?>">Cardápio</a>
        </div>
        <div class="nav-search">
            <input type="text" id="search-bar" placeholder="Buscar..." autocomplete="off">
        </div>
        <script src="..\JS\"></script>
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

    <div class="container">
        <h1>Política de Privacidade</h1>
        <p><strong>Última atualização:</strong> 18 de maio de 2025</p>

        <h2>1. Coleta de Informações</h2>
        <p>Coletamos apenas as informações essenciais para processar seu pedido, como nome, telefone e endereço. Esses dados são fornecidos diretamente pelo cliente no momento da compra.</p>

        <h2>2. Uso das Informações</h2>
        <p>Os dados coletados são utilizados para:</p>
        <ul>
        <li>Entregar os pedidos corretamente;</li>
        <li>Entrar em contato em caso de dúvidas ou alterações;</li>
        <li>Melhorar nosso atendimento e serviços.</li>
        </ul>

        <h2>3. Compartilhamento de Dados</h2>
        <p>Seus dados <strong>nunca são vendidos, alugados ou compartilhados</strong> com terceiros. O acesso é restrito apenas aos colaboradores envolvidos na preparação e entrega do pedido.</p>

        <h2>4. Armazenamento e Segurança</h2>
        <p>Adotamos medidas de segurança para proteger suas informações contra acessos não autorizados. Os dados são armazenados pelo tempo necessário ao atendimento e posteriormente descartados com segurança.</p>

        <h2>5. Contato</h2>
        <p>Em caso de dúvidas sobre nossa política de privacidade, fale conosco:</p>
        <ul>
        <li><strong>WhatsApp:</strong><a href="https://wa.me/5562999772544?text=Olá%2C%20gostaria%20de%20mais%20informações" target="_blank"> (62) 99977-2544</a></li>
        <li><strong>E-mail:</strong> <a href="https://mail.google.com/mail/?view=cm&fs=1&to=dellavitaenterprise@gmail.com&su=Olá%20Della+Vita&body=Gostaria%20de%20mais%20informações%20sobre%20seus%20produtos." target="_blank">
            dellavitaenterprise@gmail.com</a></li>
        <li><strong>Instagram:</strong><a href="https://www.instagram.com/della.vita.enterprise/profilecard/?igsh=aTk2Y2t4cHlwNHN4" target="_blank"> @della.vita.emterprise</a></li>
        </a></li>
        </ul>

        <p><strong>Della Vita</strong> protege sua privacidade com responsabilidade e transparência.</p>
        <p><strong>Della Vita</strong> agradece sua confiança e preferência!</p>
    </div>

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
