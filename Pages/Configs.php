<?php 
include_once '../session.php'; // Inclui o arquivo de sessão
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações</title>
    <link rel="stylesheet" href="../CSS/nav.css"> <!-- Estilo do nav -->
    <link rel="stylesheet" href="../CSS/config.css"> <!-- Estilo específico do configs -->
</head>
<body>
    <nav>
        <div class="logo">
            <a href="index.php">LOGO</a>
        </div>
        <div class="nav-links">
            <a href="index.php">Início</a>
            <a href="#">Página 2</a>
            <a href="#">Página 3</a>
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
    <h1>Configs</h1>
</body>
</html>