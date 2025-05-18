<!-- Arquivo: Login-Cadastro.php -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar</title>
    <link rel="stylesheet" href="../CSS/Login-Cadastro.css"> 
    <link rel="stylesheet" href="/CSS/font.css">
</head>
<body>

    <main>
        <?php
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';

            if (isset($_GET['action'])) {
                if ($_GET['action'] === 'login') {
                    header("Location: /Pages/Logar.php?redirect=" . urlencode($redirect));
                    exit();
                } elseif ($_GET['action'] === 'cadastro') {
                    header("Location: /Pages/Cadastrar.php?redirect=" . urlencode($redirect));
                    exit();
                } elseif ($_GET['action'] === 'esqueci') {
                    header("Location: /Pages/Esqueci-Senha.php?redirect=" . urlencode($redirect));
                    exit();
                }
            }
        ?>
        <form method="get">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
            <button type="submit" name="action" value="login" id="btn-login">Entrar</button>
            <br>
            <button type="submit" name="action" value="cadastro" id="btn-cadastro">Cadastrar</button>
            <br>
            <button type="submit" name="action" value="esqueci" id="btn-esqueci">Esqueci a Senha</button>
            <br>
            <button type="button" id="btn-cancelar">Cancelar</button>
        </form>

        <script>
            const btnCancelar = document.getElementById('btn-cancelar');
            btnCancelar.addEventListener('click', () => {
                // Redireciona para a URL original armazenada em redirect
                const redirectUrl = <?= json_encode($redirect) ?>;
                window.location.href = redirectUrl;
            });
        </script>
    </main>
</body>
</html>
