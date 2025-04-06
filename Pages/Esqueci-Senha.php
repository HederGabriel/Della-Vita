<?php 
session_start(); // Iniciar a sessão para gerenciar mensagens de erro
include '../System/db.php'; // Conexão com o banco de dados

// Resetar a página ao recarregar
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    unset($_SESSION['recovery_email']);
    unset($_SESSION['recovery_code']);
    unset($_SESSION['error_message']);
    unset($_SESSION['code_verified']); // Resetar verificação do código
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci a Senha</title>
    <link rel="stylesheet" href="/CSS/Esqueci-Senha.css"> <!-- Estilo da página -->
    <script src="../JS/esqueci-senha.js"></script>
</head>
<body>
    
    <main>
        <?php 
        // Processar o formulário de recuperação de senha
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
            $email = $_POST['email'];

            // Verificar se o email está cadastrado
            $sql = "SELECT * FROM clientes WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);

            if ($stmt->rowCount() > 0) {
                // Simular envio de código de recuperação
                $_SESSION['recovery_email'] = $email;
                $_SESSION['recovery_code'] = rand(100000, 999999); // Gerar código de 6 dígitos
                echo "<script>alert('Seu código de recuperação é: {$_SESSION['recovery_code']}');</script>";
                $_SESSION['error_message'] = "Um código de recuperação foi enviado para $email.";
            } else {
                // Email não encontrado
                $_SESSION['error_message'] = "Erro: Email não encontrado.";
            }
        }

        // Verificar se o código foi enviado
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['codigo'])) {
            $codigo = $_POST['codigo'];

            if (isset($_SESSION['recovery_code']) && $codigo == $_SESSION['recovery_code']) {
                $_SESSION['code_verified'] = true; // Marcar código como verificado
                $_SESSION['error_message'] = null; // Limpar mensagem de erro
            } else {
                $_SESSION['error_message'] = "Erro: Código inválido.";
            }
        }

        // Processar redefinição de senha
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nova_senha']) && isset($_POST['confirme_senha'])) {
            $novaSenha = $_POST['nova_senha'];
            $confirmeSenha = $_POST['confirme_senha'];
            $email = $_SESSION['recovery_email'];

            if ($novaSenha === $confirmeSenha) {
                $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT); // Criptografar a nova senha
                $sql = "UPDATE clientes SET senha = :senha WHERE email = :email";
                $stmt = $pdo->prepare($sql);

                if ($stmt->execute(['senha' => $novaSenhaHash, 'email' => $email])) {
                    $_SESSION['error_message'] = "Senha redefinida com sucesso.";
                    unset($_SESSION['recovery_email']);
                    unset($_SESSION['recovery_code']);
                    unset($_SESSION['code_verified']);
                    header("Location: Logar.php"); // Redirecionar para a página de login
                    exit();
                } else {
                    $_SESSION['error_message'] = "Erro ao redefinir a senha.";
                }
            } else {
                $_SESSION['error_message'] = "Erro: As senhas não coincidem.";
            }
        }
        ?>

        <?php if (!isset($_SESSION['recovery_email'])): ?>
        <!-- Formulário para solicitar código de recuperação -->
        <form method="post">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" autocomplete="off" required>
            <br>
            <button type="submit">Enviar</button>
        </form>
        <?php elseif (!isset($_SESSION['code_verified'])): ?>
        <!-- Formulário para inserir o código de recuperação -->
        <form method="post">
            <label for="codigo">Digite o código enviado para o email:</label>
            <input type="text" name="codigo" id="codigo" autocomplete="off" minlength="6" maxlength="6" required>
            <br>
            <button type="submit">Verificar Código</button>
        </form>
        <?php else: ?>
        <!-- Formulário para redefinir a senha -->
        <form method="post">
            <label for="nova_senha">Digite sua nova senha:</label>
            <input type="password" name="nova_senha" id="nova_senha" autocomplete="off" minlength="6" maxlength="6" required>
            <br>
            <label for="confirme_senha">Confirme sua nova senha:</label>
            <input type="password" name="confirme_senha" id="confirme_senha" autocomplete="off" minlength="6" maxlength="6" required>
            <br>
            <button type="submit">Redefinir Senha</button>
        </form>
        <?php endif; ?>

        <?php 
        // Exibir mensagem de erro ou sucesso, se existir
        if (isset($_SESSION['error_message']) && $_SESSION['error_message']) {
            echo "<p class='error-message'>{$_SESSION['error_message']}</p>";
            unset($_SESSION['error_message']); // Remover mensagem após exibição
        }
        ?>

        <div class="button-container">
            <button type="button" id="btn-voltar" onclick="window.location.href='<?= $_SERVER['HTTP_REFERER'] ?? 'Login-Cadastro.php' ?>'">Voltar</button>
        </div>
    </main>

</body>
</html>
