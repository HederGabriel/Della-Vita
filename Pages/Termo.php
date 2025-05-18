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

 <html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Termos de Uso e Política de Privacidade | Della Vita</title>
  <link rel="stylesheet" href="../CSS/Termo.css">
    <link rel="stylesheet" href="/CSS/footer.css">
</head>

<body>
  <div class="container">
    <h1>Termos de Uso e Política de Privacidade</h1>
    <p><strong>Última atualização:</strong> 18 de maio de 2025</p>

    <div class="section">
      <h2>1. Apresentação</h2>
      <p>Bem-vindo ao site da <strong>Pizzaria Sabor da Vila</strong>, um serviço de delivery de pizzas localizado em <strong>Posse - GO</strong>. Estes Termos de Uso e a Política de Privacidade têm como objetivo esclarecer as condições de uso do nosso site e o tratamento das informações fornecidas pelos usuários.</p>
      <p>Ao acessar, navegar ou realizar pedidos neste site, você concorda com os termos e condições aqui descritos.</p>
    </div>

    <div class="section">
      <h2>2. Funcionamento do Serviço</h2>
      <p>Nosso site permite que o cliente:</p>
      <ul>
        <li>Visualize o cardápio atualizado com fotos, descrições e preços;</li>
        <li>Faça pedidos de forma prática e segura;</li>
        <li>Escolha a forma de pagamento (Pix, Dinheiro ou Cartão);</li>
        <li>Acompanhe o status do pedido;</li>
        <li>Receba a entrega no endereço informado dentro da área de cobertura.</li>
      </ul>
    </div>

    <div class="section">
      <h2>3. Cadastro e Informações do Usuário</h2>
      <p>Para realizar pedidos, o usuário poderá informar:</p>
      <ul>
        <li>Nome completo;</li>
        <li>Endereço de entrega;</li>
        <li>Telefone de contato;</li>
        <li>Forma de pagamento.</li>
      </ul>
      <p>Essas informações são essenciais para a entrega correta do pedido e comunicação com o cliente em caso de dúvidas ou imprevistos.</p>
    </div>

    <div class="section">
      <h2>4. Privacidade e Proteção de Dados</h2>
      <p>Nós respeitamos sua privacidade. Todas as informações fornecidas são utilizadas apenas para os seguintes fins:</p>
      <ul>
        <li>Processamento e entrega dos pedidos;</li>
        <li>Contato com o cliente, se necessário;</li>
        <li>Melhoria do atendimento e dos nossos serviços.</li>
      </ul>
      <p><strong>Não compartilhamos, vendemos ou divulgamos suas informações com terceiros.</strong> Seus dados são armazenados com segurança e somente enquanto forem necessários para os fins propostos.</p>
    </div>

    <div class="section">
      <h2>5. Pagamentos</h2>
      <p>Aceitamos pagamentos via:</p>
      <ul>
        <li>Pix (com chave ou QR Code);</li>
        <li>Dinheiro (com troco, se necessário);</li>
        <li>Cartão de crédito ou débito na entrega.</li>
      </ul>
      <p>Os pagamentos são processados com segurança, e nenhum dado bancário é armazenado pelo site.</p>
    </div>

    <div class="section">
      <h2>6. Entregas</h2>
      <ul>
        <li>As entregas são realizadas <strong>apenas dentro da cidade de Posse - GO</strong>;</li>
        <li>O tempo médio de entrega é estimado no momento do pedido;</li>
        <li>O cliente deve fornecer um endereço correto e completo. Não nos responsabilizamos por atrasos causados por informações incorretas.</li>
      </ul>
    </div>

    <div class="section">
      <h2>7. Cancelamentos e Reembolsos</h2>
      <p>O cliente pode cancelar o pedido antes do preparo. Após o início da produção, <strong>não será possível realizar o cancelamento</strong>.</p>
      <p>Em casos de erro no pedido ou problemas com a entrega, o cliente deve entrar em contato imediatamente para que possamos corrigir ou compensar o ocorrido conforme o caso.</p>
    </div>

    <div class="section">
      <h2>8. Responsabilidades do Usuário</h2>
      <p>Ao utilizar o site, o usuário concorda em:</p>
      <ul>
        <li>Fornecer informações verdadeiras e atualizadas;</li>
        <li>Não utilizar o site para fins ilícitos;</li>
        <li>Respeitar os termos aqui estabelecidos.</li>
      </ul>
    </div>

    <div class="section">
      <h2>9. Modificações dos Termos</h2>
      <p>A <strong>Pizzaria Della Vita</strong> reserva-se o direito de alterar estes Termos de Uso e Política de Privacidade a qualquer momento. Recomendamos que os usuários revisem periodicamente este conteúdo para se manterem atualizados.</p>
    </div>

    <div class="section">
      <h2>10. Contato</h2>
      <p>Em caso de dúvidas, sugestões ou solicitações relacionadas a estes termos, entre em contato conosco:</p>
      <ul>
        <li><strong>WhatsApp:</strong> (62) 99977-2544</li>
        <li><strong>E-mail:</strong> dellavitaenterprise@gmail.com</li>
        <li><strong>Instagram:</strong> <a href="https://www.instagram.com/della.vita.enterprise/profilecard/?igsh=aTk2Y2t4cHlwNHN4" target="_blank">@della.vita.enterprise</a></li>
      </ul>
    </div>

    <p><strong>Pizzaria Della Vita</strong> agradece a sua confiança e preferência. Boas compras e bom apetite!</p>
  </div>

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
                        <p><a onclick="window.location.href='#'">Termos de Uso</a></p>
                        <p><a onclick="window.location.href='#'">Política de Privacidade</a></p>
                    </div>
                    <div class="contato">
                        <h2>Contatos</h2>
                        <h3>Obrigado pela Preferência!</h3>
                        <label for="t">Número : </label>
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
</body>
</html>