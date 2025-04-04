<?php 
session_start(); // Iniciar a sessão para acessar informações do cliente

// Verificar se o usuário veio da página Logar.php
if (isset($_SESSION['cliente'])) {
    $cliente = $_SESSION['cliente']; // Obter informações do cliente da sessão
}

// Logout do usuário
if (isset($_POST['logout'])) {
    session_destroy(); // Destruir a sessão
    header("Location: index.php"); // Redirecionar para a página inicial
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Della Vita</title>
    <link rel="stylesheet" href="../CSS/index.css"> <!-- Estilo externo -->
    <style>
        /* Estilo para o menu flutuante */
        #user-menu {
            display: none;
            position: absolute;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            z-index: 1000;
            padding: 10px;
        }
        #user-menu ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        #user-menu ul li {
            margin: 5px 0;
        }
        #user-menu ul li a {
            text-decoration: none;
            color: black;
        }
        /* Estilo para o modal de logout */
        #logout-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-radius: 8px;
            text-align: center;
        }
        #logout-modal button {
            margin: 5px;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        #logout-modal .confirm-btn {
            background-color: #f44336;
            color: white;
        }
        #logout-modal .cancel-btn {
            background-color: #ccc;
        }
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
    <script src="../JS/index.js"></script>
</head>
<body>
    <div id="user-menu">
        <ul>
            <li><a href="pedidos.php">Pedidos</a></li>
            <li><a href="opcoes.php">Opções</a></li>
            <li><a href="#" onclick="showLogoutModal()">Sair</a></li>
        </ul>
    </div>
    <div id="overlay" onclick="hideLogoutModal()"></div>
    <div id="logout-modal">
        <p>Tem certeza que deseja sair?</p>
        <button class="confirm-btn" onclick="document.getElementById('logout-form').submit()">Confirmar</button>
        <button class="cancel-btn" onclick="hideLogoutModal()">Cancelar</button>
    </div>

    <nav>
        <div class="logo">LOGO</div>
        <div class="nav-links">
            <a href="#">Página 1</a>
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

    <!-- Página inicial do sistema -->
    <h1>PAGINA INICIAL</h1>
    <?php if (isset($cliente)): ?>
        <form id="logout-form" method="POST" style="display: none;">
            <input type="hidden" name="logout" value="1">
        </form>
    <?php endif; ?>
</body>
</html>