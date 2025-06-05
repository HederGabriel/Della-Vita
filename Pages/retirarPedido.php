<?php 
include_once '../System/session.php';
include_once '../System/db.php';

if (!isset($_SESSION['id_cliente'])) {
    header("Location: Login-Cadastro.php?redirect=retirarPedido.php");
    exit();
}

$id_cliente = $_SESSION['id_cliente'];

$stmt = $pdo->prepare("SELECT nome, avatar FROM clientes WHERE id_cliente = :id_cliente");
$stmt->execute(['id_cliente' => $id_cliente]);
$cliente = $stmt->fetch();

if ($cliente) {
    $_SESSION['nome'] = $cliente['nome'];
    $_SESSION['avatar'] = !empty($cliente['avatar']) ? $cliente['avatar'] : '../IMG/Profile/Default.png';
} else {
    session_destroy();
    header("Location: Login-Cadastro.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id_cliente = :id_cliente AND tipo_pedido = 'local' ORDER BY data_pedido DESC");
$stmt->execute(['id_cliente' => $id_cliente]);
$pedidos = $stmt->fetchAll();

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Della Vita - Retirar</title>
  <link rel="stylesheet" href="../CSS/nav.css" />
  <link rel="stylesheet" href="../CSS/acompanhar.css" />
  <link rel="stylesheet" href="/CSS/font.css" />
  <link rel="stylesheet" href="/CSS/footer.css" />
  <style>
    button.cancelar{
      margin-left: 175px;
    }
  </style>
</head>
<body>
  <nav>
    <img src="../IMG/Logo2.jpg" alt="Logo" class="logo" onclick="window.location.href='index.php'" />
    <div class="nav-links">
      <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">Início</a>
      <a href="Cardapio.php" class="<?= $current_page === 'Cardapio.php' ? 'active' : '' ?>">Cardápio</a>
      <a href="Destaque.php" class="<?= $current_page === 'Destaque.php' ? 'active' : '' ?>">Destaque</a>
    </div>
    <div class="nav-search">
      <input type="text" placeholder="Buscar..." />
    </div>
    <?php if (isset($_SESSION['id_cliente'])): ?>
      <div class="user-profile" onclick="toggleMenu(event)">
        <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Foto de Perfil" />
      </div>
    <?php else: ?>
      <button class="login-btn" onclick="window.location.href='Login-Cadastro.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search)">Entrar</button>
    <?php endif; ?>
  </nav>
  <div id="user-menu" style="display:none;">
    <ul>
      <li><a href="Perfil.php" class="<?= $current_page === 'Perfil.php' ? 'active' : '' ?>">Perfil</a></li>
      <li><a href="Pedidos.php" class="<?= $current_page === 'Pedidos.php' ? 'active' : '' ?>">Pedidos</a></li>
      <li><a href="#" onclick="showLogoutModal()">Sair</a></li>
    </ul>
  </div>
  <div id="overlay" onclick="hideLogoutModal()" style="display:none;"></div>
  <div id="logout-modal" style="display:none;">
    <p>Tem certeza que deseja sair?</p>
    <button class="confirm-btn" onclick="document.getElementById('logout-form').submit()">Confirmar</button>
    <button class="cancel-btn" onclick="hideLogoutModal()">Cancelar</button>
  </div>
  <form id="logout-form" method="POST" style="display: none;">
    <input type="hidden" name="logout" value="1" />
  </form>
  <script src="../JS/userMenu.js"></script>

  <main>
    <div class="conteiner">
      <div class="escolha">
        <a href="acompanharPedido.php">Acompanhar</a>
        <a class="ativo" href="retirarPedido.php">Retirar</a>
      </div>
      <h1 class="titulo">Retirar</h1>

      <div class="status">
        <div class="etapas">
          <div class="etapa ativo">Recebido</div>
          <div class="etapa">Em Preparo</div>
          <div class="etapa">Aguardando Retirada</div>
        </div>
      </div>

      <?php foreach ($pedidos as $pedido): ?>
        <?php
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM itens_pedido WHERE id_pedido = :id_pedido");
        $stmt->execute(['id_pedido' => $pedido['id_pedido']]);
        $total_itens = $stmt->fetchColumn();
        ?>
        <div class="pedido" data-id-pedido="<?= $pedido['id_pedido'] ?>">
          <img class="avatar" src="<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Foto de Perfil" />
          <div class="info">
            <p><strong>Cliente:</strong> <?= htmlspecialchars($_SESSION['nome']) ?></p>
            <p>Retirada no local</p>
          </div>
          <div class="resumo">
            <p><?= $total_itens ?> Itens<br>+<br>R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></p>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="acoes">
        <button class="cancelar" disabled>Cancelar</button>
      </div>
    </div>
  </main>

  <footer>
    <div class="footer-container">
      <div class="footer-logo">
        <img src="../IMG/Logo1.jpg" alt="Logo Della Vita" />
      </div>
      <div class="footer-conteudo">
        <div class="footer-topo"><h1>Explore mais</h1></div>
        <div class="footer-boxes">
          <div class="links">
            <h2>Principais Links</h2>
            <p onclick="window.location.href='index.php'">Início</p>
            <p onclick="window.location.href='Login-Cadastro.php'">Entrar/Cadastro</p>
            <p onclick="window.location.href='Perfil.php'">Meu Perfil</p>
            <p onclick="window.location.href='Cardapio.php'">Cardápio</p>
            <p><a href="Termo.php">Termos de Uso</a></p>
            <p><a href="politica.php">Política de Privacidade</a></p>
          </div>
          <div class="contato">
            <h2>Contatos</h2>
            <h3>Obrigado pela Preferência!</h3>
            <label for="t">Número:</label>
            <p id="t"><a href="https://wa.me/5562999772544" target="_blank">Falar no WhatsApp</a></p>
            <br />
            <label for="e">Gmail:</label>
            <p><a href="mailto:dellavitaenterprise@gmail.com">Enviar mensagem via Gmail</a></p>
          </div>
          <div class="social">
            <h2>Redes Sociais</h2>
            <h3>Siga-Nós</h3>
            <a href="https://www.instagram.com/della.vita.enterprise/" target="_blank"><img src="../IMG/Icons/instagram.svg" alt="Instagram" /></a>
            <a href="https://wa.me/5562999772544" target="_blank"><img src="../IMG/Icons/whatsapp.svg" alt="Whatsapp" /></a>
            <a href="https://www.facebook.com/share/1APRM1n7BA/" target="_blank"><img src="../IMG/Icons/facebook.svg" alt="Facebook" /></a>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <div id="custom-confirm-modal" class="custom-confirm-modal">
    <div class="custom-confirm-conteudo">
      <p id="custom-confirm-message" class="custom-confirm-mensagem">Tem certeza?</p>
      <div class="custom-confirm-botoes">
        <button id="custom-confirm-no">Não</button>
        <button id="custom-confirm-yes">Sim</button>
      </div>
    </div>
  </div>
  <div id="toast-alerta" style="display:none; position: fixed; bottom: 20px; right: 20px; background: #333; color: #fff; padding: 10px 20px; border-radius: 5px; z-index: 10000;">
    <span id="toast-alerta-texto"></span>
  </div>
  <script src="../JS/retirarPedido.js"></script>
</body>
</html>
