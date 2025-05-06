<?php 
session_start(); // Iniciar a sessão para gerenciar mensagens de erro
include '../System/db.php'; // Conexão com o banco de dados
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logar</title>
    <link rel="stylesheet" href="/CSS/Logar.css"> <!-- Estilo da página -->
</head>
<body>
    
    <main>
        <form method="post">
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

                // Usar prepared statement com PDO
                $stmt = $pdo->prepare("SELECT * FROM clientes WHERE email = ? AND senha = ?");
                $stmt->execute([$email, $senha]);
                $usuario = $stmt->fetch();

                if ($usuario) {
                    // Login bem-sucedido
                    $_SESSION['error_message'] = null; // Limpar mensagem de erro
                    header("Location: index.php"); // Redirecionar para a página inicial
                    exit();
                } else {
                    // Credenciais inválidas
                    $_SESSION['error_message'] = "Erro: Email ou senha inválidos.";
                    header("Location: Logar.php"); // Recarregar a página
                    exit();
                }
            }

            // Exibir mensagem de erro, se existir, e removê-la
            if (isset($_SESSION['error_message']) && $_SESSION['error_message']) {
                echo "<p class='error-message'>{$_SESSION['error_message']}</p>";
                unset($_SESSION['error_message']); // Remover mensagem após exibição
            }
            ?>

            <!-- Botões de ação -->
            <button type="submit" id="btn-entrar">Entrar</button>
            <br>
            <button type="button" id="btn-voltar" onclick="window.location.href='Login-Cadastro.php'">Voltar</button>
        </form>
    </main>

</body>
</html>
