<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar</title>
    <link rel="stylesheet" href="../CSS/Login-Cadastro.css"> 
    <script src="../JS/login-cadastro.js"></script>
    <link rel="stylesheet" href="/CSS/font.css">
</head>
<body>

    <main>
        <?php
            if (isset($_GET['action'])) {
                if ($_GET['action'] === 'login') {
                    Header("Location: /Pages/Logar.php");
                    exit();
                } elseif ($_GET['action'] === 'cadastro') {
                    Header("Location: /Pages/Cadastrar.php");
                    exit();
                } elseif ($_GET['action'] === 'esqueci') {
                    Header("Location: /Pages/Esqueci-Senha.php");
                    exit();
                }
            }
        ?>
        <form method="get">
            <button type="submit" name="action" value="login" id="btn-login">Entrar</button>
            <br>
            <button type="submit" name="action" value="cadastro" id="btn-cadastro">Cadastrar</button>
            <br>
            <button type="submit" name="action" value="esqueci" id="btn-esqueci">Esqueci a Senha</button>
            <br>
        </form>
    </main>

</body>
</html>