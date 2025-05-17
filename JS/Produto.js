// Produto.js

// Seleciona o tamanho (botões estilizados como radio)
function selecionarTamanho(botao) {
    // Remove classe 'ativo' de todos os botões de tamanho
    document.querySelectorAll(".tamanho button").forEach(btn => btn.classList.remove("ativo"));
    
    // Adiciona 'ativo' só no botão clicado
    botao.classList.add("ativo");
    
    // Atualiza o preço exibido com base no data-preco do botão clicado
    const preco = parseFloat(botao.getAttribute("data-preco"));
    const precoFormatado = preco.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });

    const precoElement = document.getElementById("preco-formatado");
    if (precoElement) precoElement.innerText = precoFormatado;
}

// Seleciona o tipo de entrega (mesma lógica de botão ativo)
function selecionarEntrega(botao) {
    document.querySelectorAll(".entrega button").forEach(btn => btn.classList.remove("ativo"));
    botao.classList.add("ativo");

    const tipoEntrega = botao.getAttribute("data-entrega");
    console.log("Tipo de entrega selecionado:", tipoEntrega);
}

// Função para enviar pedido via fetch ao clicar no botão adicionar
function adicionarPedido() {
    // Pega o id do produto da URL
    const urlParams = new URLSearchParams(window.location.search);
    const id_produto = parseInt(urlParams.get('id'));
    if (!id_produto) {
        alert('Produto inválido');
        return;
    }

    // Botão de tamanho selecionado
    const botaoSelecionado = document.querySelector('.tamanho button.ativo');
    if (!botaoSelecionado) {
        alert('Por favor, selecione um tamanho.');
        return;
    }

    // Preço unitário do tamanho selecionado
    const precoUnitario = parseFloat(botaoSelecionado.getAttribute('data-preco'));
    if (isNaN(precoUnitario)) {
        alert('Preço inválido.');
        return;
    }

    // Quantidade padrão (pode ser substituída por input)
    const quantidade = 1;

    // Opcional: pegar tipo de entrega selecionado
    const botaoEntrega = document.querySelector('.entrega button.ativo');
    const tipoEntrega = botaoEntrega ? botaoEntrega.getAttribute('data-entrega') : null;

    // Monta os dados para enviar
    const dados = {
        id_produto,
        quantidade,
        preco_unitario: precoUnitario.toFixed(2),
        tipo_entrega: tipoEntrega
    };

    // Envia via fetch para backend
    fetch('../System/addPedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(dados)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Produto adicionado ao carrinho!');
            // Aqui pode atualizar a UI, limpar seleção, etc.
        } else {
            alert('Erro: ' + (data.error || 'Erro desconhecido'));
        }
    })
    .catch(() => alert('Erro ao comunicar com o servidor'));
}

// Evento para o botão adicionar pedido
document.querySelector('.addPedido').addEventListener('click', adicionarPedido);
