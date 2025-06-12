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

<?php
// Carregar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../System/phpmailer/src/Exception.php';
require '../System/phpmailer/src/PHPMailer.php';
require '../System/phpmailer/src/SMTP.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci a Senha</title>
    <link rel="stylesheet" href="/CSS/Esqueci-Senha.css">
    <link rel="stylesheet" href="/CSS/font.css">
    <script src="../JS/esqueci-senha.js"></script>
    <link rel="shortcut icon" href="../IMG/favicon.ico" type="image/x-icon">
</head>
<body>
    
<main>
<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Verificar se o email está cadastrado
    $sql = "SELECT * FROM clientes WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        // Gerar código de recuperação
        $_SESSION['recovery_email'] = $email;
        $_SESSION['recovery_code'] = rand(100000, 999999);

        // Enviar o código via e-mail
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'dellavitaenterprise@gmail.com'; // remetente
            $mail->Password = 'efpb ltjf fhez cwvm'; // senha do e-mail (ou senha de app)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->CharSet = 'UTF-8';


            $mail->setFrom('dellavitaenterprise@gmail.com', 'Della Vita - Recuperação de Senha');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Código de recuperação de senha';
            $mail->Body    = "Olá! Seu código de recuperação é: <strong>{$_SESSION['recovery_code']}</strong>";
            $mail->AltBody = "Olá! Seu código de recuperação é: {$_SESSION['recovery_code']}";

            $mail->send();
            $_SESSION['error_message'] = "Um código de recuperação foi enviado para $email.";
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Erro ao enviar o e-mail: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error_message'] = "Erro: Email não encontrado.";
    }
}

// Verificar código
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['codigo'])) {
    $codigo = $_POST['codigo'];

    if (isset($_SESSION['recovery_code']) && $codigo == $_SESSION['recovery_code']) {
        $_SESSION['code_verified'] = true;
        $_SESSION['error_message'] = null;
    } else {
        $_SESSION['error_message'] = "Erro: Código inválido.";
    }
}

// Redefinir senha
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nova_senha']) && isset($_POST['confirme_senha'])) {
    $novaSenha = $_POST['nova_senha'];
    $confirmeSenha = $_POST['confirme_senha'];
    $email = $_SESSION['recovery_email'];

    if ($novaSenha === $confirmeSenha) {
        $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $sql = "UPDATE clientes SET senha = :senha WHERE email = :email";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute(['senha' => $novaSenhaHash, 'email' => $email])) {
            $_SESSION['error_message'] = "Senha redefinida com sucesso.";
            unset($_SESSION['recovery_email']);
            unset($_SESSION['recovery_code']);
            unset($_SESSION['code_verified']);
            header("Location: Logar.php");
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
<form method="post">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" autocomplete="off" required>
    <br>
    <button type="submit">Enviar</button>
</form>
<?php elseif (!isset($_SESSION['code_verified'])): ?>
<form method="post">
    <label for="codigo">Digite o código enviado para o email:</label>
    <input type="text" name="codigo" id="codigo" autocomplete="off" minlength="6" maxlength="6" required>
    <br>
    <button type="submit">Verificar Código</button>
</form>
<?php else: ?>
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
if (isset($_SESSION['error_message']) && $_SESSION['error_message']) {
    echo "<p class='error-message'>{$_SESSION['error_message']}</p>";
    unset($_SESSION['error_message']);
}
?>

<div class="button-container">
    <button type="button" id="btn-voltar" onclick="window.location.href='<?= $_SERVER['HTTP_REFERER'] ?? 'Login-Cadastro.php' ?>'">Voltar</button>
</div>
</main>

</body>
</html>
