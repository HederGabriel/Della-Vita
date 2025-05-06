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
    header("Location: login-Cadastro.php"); // Redireciona para login se não estiver logado
    exit();
}

// Obtém os dados do cliente do banco de dados
$id_cliente = $_SESSION['id_cliente'];
$stmt = $pdo->prepare("SELECT nome, avatar FROM clientes WHERE id_cliente = ?");
$stmt->execute([$id_cliente]);
$cliente = $stmt->fetch();

if ($cliente) {
    $_SESSION['nome'] = $cliente['nome'];
    $_SESSION['avatar'] = $cliente['avatar'] ?? '../IMG/Profile/Default.png';
}

// Atualizar avatar
if (isset($_POST['avatar'])) {
    $avatar = $_POST['avatar'];
    $stmt = $pdo->prepare("UPDATE clientes SET avatar = ? WHERE id_cliente = ?");
    $stmt->execute([$avatar, $id_cliente]);
    $_SESSION['avatar'] = $avatar; // Atualiza o avatar na sessão
}

// Limpar avatar (resetar para o padrão)
if (isset($_POST['reset_avatar'])) {
    $stmt = $pdo->prepare("UPDATE clientes SET avatar = NULL WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    $_SESSION['avatar'] = '../IMG/Profile/Default.png'; // Reseta para o avatar padrão
}

// Verifica se o formulário de exclusão de conta foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $password = $_POST['password'];

    // Verifica se a senha está correta
    $stmt = $pdo->prepare("SELECT senha FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    $cliente = $stmt->fetch();

    if ($cliente && password_verify($password, $cliente['senha'])) {
        // Exclui a conta do cliente
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id_cliente = ?");
        $stmt->execute([$id_cliente]);

        // Destroi a sessão e redireciona para a página inicial
        session_destroy();
        echo "Conta excluída com sucesso.";
        exit();
    } else {
        echo "Senha incorreta.";
        exit();
    }
}

// Página atual
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="../CSS/nav.css"> <!-- Estilo do nav -->
    <link rel="stylesheet" href="../CSS/Perfil.css"> <!-- Estilo específico do perfil -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"> <!-- Google Material Symbols -->
    <script src="../JS/Perfil.js"></script> <!-- Script externo -->
</head>
<body>
    <nav>
        <div class="logo">
            <a href="index.php">LOGO</a>
        </div>
        <div class="nav-links">
            <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">Início</a>
            <a href="#" class="<?= $current_page === 'pagina2.php' ? 'active' : '' ?>">Página 2</a>
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
            <span class="edit-icon">edit</span> <!-- Ícone posicionado atrás -->
            <button class="edit-avatar-btn" onclick="toggleAvatarOptions()"></button> <!-- Botão transparente -->
        </div>
    </header>

    <main>
        <!-- Avatar Options -->
        <div id="avatar-options" style="display: none;">
            <form method="POST" id="avatar-form">
                <div class="avatar-options-container">
                    <img src="../IMG/Profile/01.png" alt="Avatar 1" class="avatar-option" onclick="selectAvatar('../IMG/Profile/01.png')">
                    <img src="../IMG/Profile/02.png" alt="Avatar 2" class="avatar-option" onclick="selectAvatar('../IMG/Profile/02.png')">
                    <img src="../IMG/Profile/03.png" alt="Avatar 3" class="avatar-option" onclick="selectAvatar('../IMG/Profile/03.png')">
                    <img src="../IMG/Profile/04.png" alt="Avatar 4" class="avatar-option" onclick="selectAvatar('../IMG/Profile/04.png')">
                </div>
                <input type="hidden" name="avatar" id="selected-avatar">
                <div class="avatar-buttons">
                    <button type="submit">Salvar</button>
                    <button type="submit" name="reset_avatar">Padrão</button>
                </div>
            </form>
        </div>

        <!-- Profile Options -->
        <section class="profile-options">
            <button onclick="window.location.href='MeusDados.php'" class="profile-option-btn">
                <span class="material-symbols-outlined icon-left">person</span>
                Meus Dados
                <span class="material-symbols-outlined">arrow_forward_ios</span>
            </button>
            <button onclick="window.location.href='Esqueci-Senha.php'" class="profile-option-btn">
                <span class="material-symbols-outlined icon-left">key</span>
                Alterar Senha
                <span class="material-symbols-outlined">arrow_forward_ios</span>
            </button>
            <button onclick="window.location.href=''" class="profile-option-btn">
                <span class="material-symbols-outlined icon-left">palette</span>
                Tema
                <span class="material-symbols-outlined">arrow_forward_ios</span>
            </button>
            <button class="profile-option-btn delete-account-btn">
                <span class="material-symbols-outlined icon-left">delete</span>
                Excluir Conta
                <span class="material-symbols-outlined">arrow_forward_ios</span>
            </button>
        </section>

        <!-- Overlay para o modal -->
        <div id="overlay-delete" onclick="hideDeleteModal()"></div>

        <!-- Modal de exclusão de conta -->
        <div id="delete-account-modal">
            <p>Tem certeza de que deseja excluir sua conta?</p>
            <div class="modal-buttons">
                <button id="confirm-delete-btn" class="confirm-btn">Confirmar</button>
                <button id="cancel-delete-btn" class="cancel-btn" onclick="hideDeleteModal()">Cancelar</button>
            </div>
        </div>

        <!-- Modal para digitar a senha -->
        <div id="password-modal" style="display: none;">
            <p>Digite sua senha para confirmar a exclusão:</p>
            <form method="POST" id="delete-account-form">
                <input type="password" name="password" id="password-input" placeholder="Senha" minlength="6" maxlength="6" required>
                <div class="modal-buttons">
                    <button type="submit" class="confirm-btn">Confirmar</button>
                    <button type="button" class="cancel-btn" onclick="hidePasswordModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </main>

    <script src="../JS/userMenu.js"></script> <!-- Script para o user-menu -->
</body>
</html>
