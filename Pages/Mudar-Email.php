<?php 
session_start();
include '../System/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    unset($_SESSION['email_change_code']);
    unset($_SESSION['email_code_verified']);
    unset($_SESSION['new_email']);
    unset($_SESSION['email_error_message']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Mudar Email</title>
    <link rel="stylesheet" href="/CSS/Esqueci-Senha.css">
</head>
<body>
<main>
<?php
if (!isset($_SESSION['id_cliente'])) {
    header("Location: login-Cadastro.php");
    exit();
}

$id_cliente = $_SESSION['id_cliente'];

// Etapa 1: Verificar email atual e enviar código
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email_atual'])) {
    $emailAtual = $_POST['email_atual'];

    // Verifica se é o email cadastrado
    $sql = "SELECT * FROM clientes WHERE id_cliente = :id AND email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_cliente, 'email' => $emailAtual]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['email_change_code'] = rand(100000, 999999);
        $_SESSION['email_atual_confirmado'] = $emailAtual;
        echo "<script>alert('Seu código de verificação é: {$_SESSION['email_change_code']}');</script>";
        $_SESSION['email_error_message'] = "Um código foi enviado para seu email atual.";
    } else {
        $_SESSION['email_error_message'] = "Erro: O email informado não corresponde ao seu email atual.";
    }
}

// Etapa 2: Verificar código enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    $codigo = $_POST['codigo'];

    if (isset($_SESSION['email_change_code']) && $codigo == $_SESSION['email_change_code']) {
        $_SESSION['email_code_verified'] = true;
        $_SESSION['email_error_message'] = null;
    } else {
        $_SESSION['email_error_message'] = "Erro: Código inválido.";
    }
}

// Etapa 3: Inserir novo email e atualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_email']) && isset($_SESSION['email_code_verified'])) {
    $novoEmail = $_POST['novo_email'];

    // Verifica se o novo email já existe
    $sql = "SELECT * FROM clientes WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $novoEmail]);

    if ($stmt->rowCount() == 0) {
        $sql = "UPDATE clientes SET email = :email WHERE id_cliente = :id";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute(['email' => $novoEmail, 'id' => $id_cliente])) {
            $_SESSION['email'] = $novoEmail;
            unset($_SESSION['email_change_code']);
            unset($_SESSION['email_code_verified']);
            unset($_SESSION['email_atual_confirmado']);
            $_SESSION['email_error_message'] = "Email atualizado com sucesso.";
            header("Location: meusDados.php");
            exit();
        } else {
            $_SESSION['email_error_message'] = "Erro ao atualizar o email.";
        }
    } else {
        $_SESSION['email_error_message'] = "Erro: Este email já está em uso.";
    }
}
?>

<!-- Etapa 1 -->
<?php if (!isset($_SESSION['email_atual_confirmado'])): ?>
    <form method="post">
        <label for="email_atual">Digite seu email atual:</label>
        <input type="email" name="email_atual" id="email_atual" required autocomplete="off">
        <br>
        <button type="submit">Enviar Código</button>
    </form>

<!-- Etapa 2 -->
<?php elseif (!isset($_SESSION['email_code_verified'])): ?>
    <form method="post">
        <label for="codigo">Digite o código enviado para o email:</label>
        <input type="text" name="codigo" id="codigo" maxlength="6" required autocomplete="off">
        <br>
        <button type="submit">Verificar Código</button>
    </form>

<!-- Etapa 3 -->
<?php else: ?>
    <form method="post">
        <label for="novo_email">Digite seu novo email:</label>
        <input type="email" name="novo_email" id="novo_email" required autocomplete="off">
        <br>
        <button type="submit">Alterar Email</button>
    </form>
<?php endif; ?>

<?php 
if (isset($_SESSION['email_error_message'])) {
    echo "<p class='error-message'>{$_SESSION['email_error_message']}</p>";
    unset($_SESSION['email_error_message']);
}
?>

<div class="button-container">
    <button type="button" onclick="window.location.href='meusDados.php'">Voltar</button>
</div>
</main>
</body>
</html>
