<?php 
include_once '../System/session.php'; // Inclui o arquivo de sessão

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
    <title>Perfil</title>
    <link rel="stylesheet" href="../CSS/nav.css"> <!-- Estilo do nav -->
    <link rel="stylesheet" href="../CSS/Perfil.css"> <!-- Estilo específico do perfil -->
    <script src="../JS/index.js"></script>
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
        <?php if (isset($cliente)): ?>
            <!-- Exibir perfil do usuário -->
            <div class="user-profile" onclick="toggleMenu(event)">
                <img src="../IMG/Profile/Default.png" alt="Foto de Perfil">
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
        <img src="../IMG/Profile/Default.png" alt="Avatar" class="avatar">
        <div class="client-name">
            <?= isset($cliente['nome']) ? htmlspecialchars($cliente['nome']) : 'Nome do Cliente'; ?>
        </div>
        <div class="placeholder-box"></div>
    </main>

</body>
</html>
