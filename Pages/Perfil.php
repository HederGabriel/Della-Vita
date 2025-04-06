<?php 
include_once '../System/session.php'; // Inclui o arquivo de sessão
include_once '../System/db.php'; // Inclui o arquivo de conexão com o banco de dados

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Bootstrap Icons -->
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
    
    <main>
        <div class="avatar-container">
            <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Avatar" class="avatar">
            <div class="client-name">
                <?= htmlspecialchars($_SESSION['nome']) ?>
            </div>
            <span class="edit-icon">edit</span> <!-- Ícone posicionado atrás -->
            <button class="edit-avatar-btn" onclick="toggleAvatarOptions()"></button> <!-- Botão transparente -->
        </div>
        <div class="placeholder-box"></div> <!-- Espaço cinza exibido por padrão -->
        <div id="avatar-options" style="display: none;"> <!-- Opções escondidas por padrão -->
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
    </main>
    <script src="../JS/userMenu.js"></script> <!-- Script para o user-menu -->
    <script src="../JS/Perfil.js"></script>
</body>
</html>
