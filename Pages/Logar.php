<?php 
session_start(); // Iniciar a sessão para gerenciar mensagens de erro
include '../System/db.php'; // Conexão com o banco de dados
?>

<!-- Arquivo: Logar.php -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logar</title>
    <link rel="stylesheet" href="/CSS/Logar.css"> <!-- Estilo da página -->
    <link rel="stylesheet" href="/CSS/font.css">
    <script src="../JS/logar.js"></script>
</head>
<body>
    
    <main>
        <form method="post">
            <!-- Campo oculto para redirecionamento -->
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? $_POST['redirect'] ?? 'index.php') ?>">

            <!-- Campo para o email -->
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" autocomplete="off" required>
            <br>

            <!-- Campo para a senha -->
            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" autocomplete="off" minlength="6" maxlength="6" required>
            <br>

            <?php 
            // Processar o formulário de login
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $email = $_POST['email'];
                $senha = $_POST['senha'];

                // Verificar credenciais no banco de dados
                $sql = "SELECT * FROM clientes WHERE email = :email";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['email' => $email]);
                $cliente = $stmt->fetch();

                if ($cliente && password_verify($senha, $cliente['senha'])) {
                    $_SESSION['id_cliente'] = $cliente['id_cliente'];
                    $_SESSION['nome'] = $cliente['nome'];
                    $_SESSION['avatar'] = $cliente['avatar'] ?? '../IMG/Profile/Default.png';

                    $redirect = $_POST['redirect'] ?? 'index.php';
                    header("Location: " . $redirect);
                    exit();
                } else {
                    $_SESSION['error_message'] = "Erro: Email ou senha inválidos.";
                    header("Location: Logar.php?redirect=" . urlencode($_POST['redirect'] ?? 'index.php'));
                    exit();
                }
            }

            // Exibir mensagem de erro, se existir
            if (isset($_SESSION['error_message']) && $_SESSION['error_message']) {
                echo "<p class='error-message'>{$_SESSION['error_message']}</p>";
                unset($_SESSION['error_message']);
            }
            ?>

            <!-- Botões de ação -->
            <button type="submit" id="btn-entrar">Entrar</button>
            <br>
            <button type="button" id="btn-voltar" onclick="window.location.href='Login-Cadastro.php?redirect=<?= urlencode($_GET['redirect'] ?? 'index.php') ?>'">Voltar</button>
        </form>
    </main>

</body>
</html>
