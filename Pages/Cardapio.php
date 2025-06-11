<?php 
include_once '../System/session.php'; 
include_once '../System/db.php'; 

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
    session_destroy();
    $redirect_url = $_POST['redirect'] ?? 'Cardapio.php';
    header("Location: " . $redirect_url);
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);

// Filtros
$saboresSelecionados = $_GET['sabor'] ?? [];
$ordem = $_GET['ordem'] ?? 'asc';
$busca = $_GET['busca'] ?? '';

function buscarPizzas($pdo, $sabor, $ordem, $busca) {
    $query = "SELECT id_produto, nome, imagem FROM produtos WHERE sabor = :sabor";
    $params = ['sabor' => $sabor];

    if (!empty($busca)) {
        $query .= " AND nome LIKE :busca";
        $params['busca'] = '%' . $busca . '%';
    }

    $query .= " ORDER BY nome " . ($ordem === 'desc' ? 'DESC' : 'ASC');
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Della Vita - Menu</title>
    <link rel="stylesheet" href="../CSS/nav.css">
    <link rel="stylesheet" href="../CSS/cardapio.css"> 
    <link rel="stylesheet" href="/CSS/font.css">
    <link rel="stylesheet" href="/CSS/footer.css">
</head>
<body>
    <nav>
        <img src="..\IMG\Logo2.jpg" alt="Logo" class="logo" onclick="window.location.href='index.php'">

        <div class="nav-links">
            <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">Início</a>
            <a href="Cardapio.php" class="<?= $current_page === 'Cardapio.php' ? 'active' : '' ?>">Cardápio</a>
        </div>
        <div class="nav-search">
            <input type="text" id="search-bar" placeholder="Buscar..." autocomplete="off">
            <div class="filter-container" style="position: relative; display: inline-block;">
                <img class="filter" src="..\IMG\filter.svg" alt="Filtro">

                <div id="filter-modal" class="hidden">
                    <form id="filter-form">
                        <label>
                            <input type="checkbox" name="sabor" value="trad"> Tradicionais
                        </label><br>
                        <label>
                            <input type="checkbox" name="sabor" value="doce"> Doces
                        </label><br>
                        <label>
                            <input type="checkbox" name="sabor" value="esp"> Especiais
                        </label><br>

                        <label for="ordem">Ordenar por:</label>
                        <select id="ordem" name="ordem">
                            <option value="asc">A-Z</option>
                            <option value="desc">Z-A</option>
                        </select>

                        <button type="submit">Aplicar Filtros</button>
                    </form>
                </div>
            </div>
        </div>


        <?php if (isset($_SESSION['id_cliente'])): ?>
            <!-- Exibir perfil do usuário -->
            <div class="user-profile" onclick="toggleMenu(event)">
                <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Foto de Perfil">
            </div>
        <?php else: ?>
            <button class="login-btn" onclick="window.location.href='Login-Cadastro.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search)">Entrar</button>
        <?php endif; ?>
    </nav>
    <div id="user-menu">
        <ul>
            <li><a href="Perfil.php" class="<?= $current_page === 'Perfil.php' ? 'active' : '' ?>">Perfil</a></li>
            <li><a href="Pedidos.php" class="<?= $current_page === 'Pedidos.php' ? 'active' : '' ?>">Pedidos</a></li>
            <li><a href="#" onclick="showLogoutModal()">Sair</a></li>
        </ul>
    </div>
    <div id="overlay" onclick="hideLogoutModal()"></div>
    <div id="logout-modal">
        <p>Tem certeza que deseja sair?</p>
        <button class="confirm-btn" onclick="document.getElementById('logout-form').submit()">Confirmar</button>
        <button class="cancel-btn" onclick="hideLogoutModal()">Cancelar</button>
    </div>
    
    <?php if (isset($cliente)): ?>
        <form id="logout-form" method="POST" style="display: none;">
            <input type="hidden" name="logout" value="1">
        </form>
    <?php endif; ?>
    <script src="../JS/userMenu.js"></script>

    <main>
        <section id="trad">
            <h1>Pizzas Tradicionais</h1>
            <div class="pizza-list">
                <?php
                $stmt = $pdo->prepare("SELECT id_produto, nome, imagem FROM produtos WHERE sabor = 'trad' ORDER BY nome ASC");
                $stmt->execute();
                $pizzasTrad = $stmt->fetchAll();

                if (count($pizzasTrad) === 0): ?>
                    <p>Nenhuma pizza encontrada nesta categoria.</p>
                <?php else:
                    foreach ($pizzasTrad as $pizza):
                        $imgSrc = !empty($pizza['imagem']) ? htmlspecialchars($pizza['imagem']) : '../IMG/img.jpg';
                ?>
                    <div class="pizza-card" onclick="window.location.href='Produto.php?id=<?= $pizza['id_produto'] ?>'">
                        <img class="pizza-img" src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($pizza['nome']) ?>">
                        <h2><?= htmlspecialchars($pizza['nome']) ?></h2>
                    </div>
                <?php endforeach;
                endif; ?>
            </div>
        </section>

        <section id="doce">
            <h1>Pizzas Doces</h1>
            <div class="pizza-list">
                <?php
                $stmt = $pdo->prepare("SELECT id_produto, nome, imagem FROM produtos WHERE sabor = 'doce' ORDER BY nome ASC");
                $stmt->execute();
                $pizzasDoce = $stmt->fetchAll();

                if (count($pizzasDoce) === 0): ?>
                    <p>Nenhuma pizza encontrada nesta categoria.</p>
                <?php else:
                    foreach ($pizzasDoce as $pizza):
                        $imgSrc = !empty($pizza['imagem']) ? htmlspecialchars($pizza['imagem']) : '../IMG/img.jpg';
                ?>
                    <div class="pizza-card" onclick="window.location.href='Produto.php?id=<?= $pizza['id_produto'] ?>'">
                        <img class="pizza-img" src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($pizza['nome']) ?>">
                        <h2><?= htmlspecialchars($pizza['nome']) ?></h2>
                    </div>
                <?php endforeach;
                endif; ?>
            </div>
        </section>

        <section id="esp">
            <h1>Pizzas Especiais</h1>
            <div class="pizza-list">
                <?php
                $stmt = $pdo->prepare("SELECT id_produto, nome, imagem FROM produtos WHERE sabor = 'esp' ORDER BY nome ASC");
                $stmt->execute();
                $pizzasEspecial = $stmt->fetchAll();

                if (count($pizzasEspecial) === 0): ?>
                    <p>Nenhuma pizza encontrada nesta categoria.</p>
                <?php else:
                    foreach ($pizzasEspecial as $pizza):
                        $imgSrc = !empty($pizza['imagem']) ? htmlspecialchars($pizza['imagem']) : '../IMG/img.jpg';
                ?>
                    <div class="pizza-card" onclick="window.location.href='Produto.php?id=<?= $pizza['id_produto'] ?>'">
                        <img class="pizza-img" src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($pizza['nome']) ?>">
                        <h2><?= htmlspecialchars($pizza['nome']) ?></h2>
                    </div>
                <?php endforeach;
                endif; ?>
            </div>
        </section>
        <p id="no-results" style="display: none; text-align: center; font-size: 18px; margin-top: 20px; color: gray;">
            Nenhuma pizza encontrada.
        </p>


    </main>
    
    <footer>
        <div class="footer-container">
            <!-- Logo à esquerda -->
            <div class="footer-logo">
                <img src="..\IMG\Logo1.jpg" alt="Logo Della Vita">
            </div>

            <!-- Conteúdo à direita -->
            <div class="footer-conteudo">
                <div class="footer-topo">
                    <h1>Explore mais</h1>
                </div>
                <div class="footer-boxes">
                    <div class="links">
                        <h2>Principais Links</h2>
                        <p onclick="window.location.href='index.php'">Início</p>
                        <p onclick="window.location.href='Login-Cadastro.php'">Entrar/Cadastro</p>
                        <p onclick="window.location.href='Perfil.php'">Meu Perfil</p>
                        <p onclick="window.location.href='Cardapio.php'">Cardápio</p>
                        <p><a onclick="window.location.href='Termo.php'">Termos de Uso</a></p>
                        <p><a onclick="window.location.href='politica.php'">Política de Privacidade</a></p>
                    </div>
                    <div class="contato">
                        <h2>Contatos</h2>
                        <h3>Obrigado pela Preferência!</h3>
                        <label for="t">Número: </label>
                        <p id="t">
                            <a href="https://wa.me/5562999772544?text=Olá%2C%20gostaria%20de%20mais%20informações" target="_blank">
                            Falar no WhatsApp
                            </a>
                        </p>
                        <br>
                        <label for="e">Gmail: </label>
                        <p>
                            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=dellavitaenterprise@gmail.com&su=Olá%20Della+Vita&body=Gostaria%20de%20mais%20informações%20sobre%20seus%20produtos." target="_blank">
                            Enviar mensagem via Gmail
                            </a>
                        </p>
                    </div>
                    <div class="social">
                        <h2>Redes Sociais</h2>
                        <h3>Siga-Nós</h3>
                        <a href="https://www.instagram.com/della.vita.enterprise/profilecard/?igsh=aTk2Y2t4cHlwNHN4" target="_blank"><img src="..\IMG\Icons\instagram.svg" alt="Instagram"></a>
                        <a href="https://wa.me/5562999772544?text=Olá%2C%20gostaria%20de%20mais%20informações" target="_blank"><img src="..\IMG\Icons\whatsapp.svg" alt="Whatsapp"></a>
                        <a href="https://www.facebook.com/share/1APRM1n7BA/" target="_blank"><img src="..\IMG\Icons\facebook.svg" alt="Facebook"></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="../JS/busca-filter.js"></script>
</body>
</html>
