<?php 
include_once '../System/session.php';
require_once '../System/db.php';

if (!isset($_SESSION['id_cliente'])) {
  header("Location: Login-Cadastro.php?redirect=Pedidos.php");
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

$stmt = $pdo->prepare("
  SELECT ip.*, p.nome AS nome_produto, p.imagem AS imagem_produto 
  FROM itens_pedido ip
  INNER JOIN produtos p ON ip.id_produto = p.id_produto
  WHERE ip.id_cliente = :id_cliente AND ip.id_pedido IS NULL
  ORDER BY ip.entrega, ip.id_item_pedido DESC
");
$stmt->execute(['id_cliente' => $id_cliente]);
$itens = $stmt->fetchAll();

$itens_entrega = [];
$itens_local = [];

foreach ($itens as $item) {
  $entrega_tipo = $item['entrega'] ?? '';
  if ($entrega_tipo === 'casa') {
    $itens_entrega[] = $item;
  } elseif ($entrega_tipo === 'local') {
    $itens_local[] = $item;
  }
}

if (isset($_POST['logout'])) {
  session_destroy();
  $redirect_url = $_POST['redirect'] ?? 'index.php';
  header("Location: " . $redirect_url);
  exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Della Vita - Pedidos</title>
  <link rel="stylesheet" href="../CSS/nav.css" />
  <link rel="stylesheet" href="../CSS/pedido.css" />
  <link rel="stylesheet" href="/CSS/font.css" />
  <link rel="stylesheet" href="/CSS/footer.css" />
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

  <main>
    <section class="pedidos">
      <div class="acompanhar-container">
        <button class="btn-acompanhar" onclick="window.location.href='acompanharPedido.php'">📦 Acompanhar Pedido</button>
      </div>

      <!-- Entrega em Casa -->
      <div class="titulo-pedidos">
        <label class="custom-checkbox" for="casa">
          <input type="checkbox" id="casa" name="tipo_pedido" value="casa" />
          <span class="checkmark"></span>
          <h2 class="h2-casa">Pedidos para Entrega em Casa</h2>
        </label>
      </div>

      <?php if (!empty($itens_entrega)): ?>
        <?php 
          $total_entrega = 0;
          foreach ($itens_entrega as $item): 
          $subtotal = $item['quantidade'] * $item['preco_unitario'];
          $total_entrega += $subtotal;
        ?>
          <div class="pedido-item" data-id-item="<?= (int)$item['id_item_pedido'] ?>" data-tipo="casa">
            <img src="<?= htmlspecialchars($item['imagem_produto']) ?>" class="produto-img" alt="Imagem Produto" />
            <div class="pedido-info">
              <p><strong>Produto:</strong> <?= htmlspecialchars($item['nome_produto']) ?></p>
              <p><strong>Tamanho:</strong> <?= strtoupper(htmlspecialchars($item['tamanho'])) ?></p>
              <p><strong>Preço Unitário:</strong> R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></p>
              <div class="quantidade-container">
                <strong>Quantidade:</strong>
                <button type="button" class="btn-menos" data-id="<?= $item['id_item_pedido'] ?>">-</button>
                <span class="quantidade"><?= (int)$item['quantidade'] ?></span>
                <button type="button" class="btn-mais" data-id="<?= $item['id_item_pedido'] ?>">+</button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <div class="total-container">
          <p><strong>Total:</strong> R$ <?= number_format($total_entrega, 2, ',', '.') ?></p>
        </div>
      <?php else: ?>
        <p>Não há pedidos para entrega em casa.</p>
      <?php endif; ?>

      <!-- Retirada no Local -->
      <div class="titulo-pedidos">
        <label class="custom-checkbox" for="local">
          <input type="checkbox" id="local" name="tipo_pedido" value="local" />
          <span class="checkmark"></span>
          <h2 class="h2-local">Pedidos para Retirada no Local</h2>
        </label>
      </div>

      <?php if (!empty($itens_local)): ?>
        <?php 
          $total_local = 0;
          foreach ($itens_local as $item): 
          $subtotal = $item['quantidade'] * $item['preco_unitario'];
          $total_local += $subtotal;
        ?>
          <div class="pedido-item" data-id-item="<?= (int)$item['id_item_pedido'] ?>" data-tipo="local">
            <img src="<?= htmlspecialchars($item['imagem_produto']) ?>" class="produto-img" alt="Imagem Produto" />
            <div class="pedido-info">
              <p><strong>Produto:</strong> <?= htmlspecialchars($item['nome_produto']) ?></p>
              <p><strong>Tamanho:</strong> <?= strtoupper(htmlspecialchars($item['tamanho'])) ?></p>
              <p><strong>Preço Unitário:</strong> R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></p>
              <div class="quantidade-container">
                <strong>Quantidade:</strong>
                <button type="button" class="btn-menos" data-id="<?= $item['id_item_pedido'] ?>">-</button>
                <span class="quantidade"><?= (int)$item['quantidade'] ?></span>
                <button type="button" class="btn-mais" data-id="<?= $item['id_item_pedido'] ?>">+</button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <div class="total-container">
          <p><strong>Total:</strong> R$ <?= number_format($total_local, 2, ',', '.') ?></p>
        </div>
      <?php else: ?>
        <p>Não há pedidos para consumo no local.</p>
      <?php endif; ?>

      <!-- Formulário Finalizar -->
      <form id="form-finalizar" method="POST" action="../System/finalizarPedido.php" onsubmit="return validarFormulario()">
        <input type="hidden" id="input-comentario-form" name="comentario">
        <input type="hidden" name="tipo_pedido" id="input-tipo-pedido" />
        <input type="hidden" name="rua" id="input-rua" />
        <input type="hidden" name="numero" id="input-numero" />
        <input type="hidden" name="setor" id="input-setor" />
        <input type="hidden" name="cep" id="input-cep" />
        <input type="hidden" name="complemento" id="input-complemento" />
        <input type="hidden" name="cidade" id="input-cidade" /> <!-- ADICIONADO -->

        <button type="button" class="finalizarPedido" id="btnFinalizar" disabled>Finalizar Pedido</button>
    </form>


      <!-- Modal Endereço -->
      <div id="modal-endereco" class="modal-endereco" style="display: none;">
        <div class="modal-content">
          <h2>Informe o Endereço de Entrega</h2>
          <input id="autocomplete" placeholder="Digite o endereço completo" type="text" />
          <input type="text" id="input-cep-modal" placeholder="CEP" required />
          <input type="text" id="input-cidade-modal" placeholder="Cidade" required />
          <input type="text" id="input-rua-modal" placeholder="Rua" required />
          <input type="text" id="input-numero-modal" placeholder="Número" required />
          <input type="text" id="input-setor-modal" placeholder="Bairro" required />
          <input type="text" id="input-complemento-modal" placeholder="Complemento (opcional)" />
          <button type="button" id="btnCancelarEndereco">Cancelar</button>
          <button type="button" id="btnConfirmarEndereco">Confirmar Endereço</button>
        </div>
      </div>
    </section>
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

  <div id="modal-comentario" class="modal-comentario">
    <div class="modal-comentario-conteudo">
      <h3>Comentário</h3>
      
      <!-- Textarea com maxlength -->
      <textarea
        id="input-comentario"
        placeholder="Digite um comentário (opcional)"
        rows="4"
        maxlength="200"
      ></textarea>
      
      <!-- Contador de caracteres -->
      <small id="contador-comentario">0/200</small>
      
      <div class="modal-comentario-botoes">
        <button id="btnCancelarComentario" type="button">Cancelar</button>
        <button id="btnConfirmarComentario" type="button">Enviar</button>
      </div>
    </div>
  </div>

  <div id="modal-remover-item" class="modal-remover">
    <div class="modal-remover-conteudo">
      <p id="texto-modal-remover">Deseja realmente remover este item do pedido?</p>
      <div class="modal-remover-botoes">
        <button id="btnConfirmarRemover" class="btn-confirmar">Confirmar</button>
        <button id="btnCancelarRemover" class="btn-cancelar">Cancelar</button>
      </div>
    </div>
  </div>

  <script src="../JS/userMenu.js"></script>
  <script src="../JS/Pedidos.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBGn0vqgAS22ZHzFENXQtDj1AqjgPUVjTo&libraries=places" async defer></script>
</body>
</html>
