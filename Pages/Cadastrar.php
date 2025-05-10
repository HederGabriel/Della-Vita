<?php 
session_start(); // Iniciar a sessão para gerenciar mensagens de erro
include '../System/db.php'; // Conexão com o banco de dados
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar</title>
    <link rel="stylesheet" href="/CSS/Cadastrar.css">
    <link rel="stylesheet" href="/CSS/font.css">
    <script src="../JS/cadastrar.js"></script>
</head>
<body>
    
    <main>
        <form method="post">
            <!-- Campo para o nome -->
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" autocomplete="off" required>
            <br>
            
            <!-- Campo para o email -->
            <label for="email">Email:</label> 
            <input type="email" name="email" id="email" autocomplete="off" required>
            <br>

            <!-- Campo para a senha -->
            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" autocomplete="off" minlength="6" maxlength="6" required>
            <br>

            <!-- Botões de ação -->
            <button type="submit">Cadastrar</button>
            <br>
            <button type="button" id="btn-voltar" onclick="window.location.href='Login-Cadastro.php'">Voltar</button>
        </form>

        <?php 
        // Processar o formulário de cadastro
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nomeUsuario = $_POST['nome'];
            $email = $_POST['email'];
            $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografar a senha

            // Verificar se o email já está cadastrado
            $checkEmailSql = "SELECT email FROM clientes WHERE email = :email";
            $stmt = $pdo->prepare($checkEmailSql);
            $stmt->execute(['email' => $email]);

            if ($stmt->rowCount() > 0) {
                // Email já cadastrado
                $_SESSION['error_message'] = "Erro: Este email já está cadastrado.";
                header("Location: Cadastrar.php");
                exit();
            } else {
                // Inserir novo usuário no banco de dados
                $sql = "INSERT INTO clientes (nome, email, senha) VALUES (:nome, :email, :senha)";
                $stmt = $pdo->prepare($sql);

                if ($stmt->execute(['nome' => $nomeUsuario, 'email' => $email, 'senha' => $senha])) {
                    $_SESSION['error_message'] = null; // Limpar mensagem de erro
                    header("Location: Logar.php");
                    exit();
                } else {
                    // Erro ao cadastrar
                    $_SESSION['error_message'] = "Erro ao cadastrar usuário.";
                    header("Location: Cadastrar.php");
                    exit();
                }
            }
        }

        // Exibir mensagem de erro, se existir, e removê-la
        if (isset($_SESSION['error_message']) && $_SESSION['error_message']) {
            echo "<p class='error-message'>{$_SESSION['error_message']}</p>";
            unset($_SESSION['error_message']); // Remover mensagem após exibição
        }
        ?>
    </main>

</body>
</html>

