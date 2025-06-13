<?php 
include_once '../System/session.php'; // Inclui o arquivo de sessão
include_once '../System/db.php'; // Inclui o arquivo de conexão com o banco de dados

// Verificar se o usuário está logado
if (isset($_SESSION['id_cliente'])) {
    $id_cliente = $_SESSION['id_cliente'];
    $stmt = $pdo->prepare("SELECT nome, avatar FROM clientes WHERE id_cliente = :id_cliente");
    $stmt->execute(['id_cliente' => $id_cliente]);
    $cliente = $stmt->fetch();
    if ($cliente) {
        $_SESSION['nome'] = $cliente['nome'];
        $_SESSION['avatar'] = $cliente['avatar'] ?? '../IMG/Profile/Default.png';
    }
}

if (isset($_POST['logout'])) {
    session_destroy(); // Destrói a sessão

    // Redireciona de volta para a mesma página
    $redirect_url = $_POST['redirect'] ?? 'index.php';
    header("Location: " . $redirect_url);
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
    <title>Della Vita - ADM</title>
    <link rel="stylesheet" href="..\CSS\font.css">
    <link rel="stylesheet" href="..\CSS\adm-cozinha.css">
    <link rel="shortcut icon" href="../IMG/favicon.ico" type="image/x-icon">
</head>
<body>
    <section id="cozinha" onclick="window.location.href='Cozinha.php'">
        <button onclick="window.location.href='../Pages/index.php' " id="Btn-sair">Sair</button>
        Cozinha
    </section>
    <section id="adm" onclick="window.location.href='ADM.php'">
        Página de Gerenciamento
    </section>
</body>

</html>