// Função para selecionar o tamanho (componente radio-button estilo botão)
function selecionarTamanho(botao) {
    // Remove 'ativo' de todos os botões
    const botoes = document.querySelectorAll(".tamanho button");
    botoes.forEach(btn => btn.classList.remove("ativo"));

    // Adiciona 'ativo' só no botão clicado
    botao.classList.add("ativo");

    // Atualiza o preço exibido com base no data-preco do botão clicado
    const preco = parseFloat(botao.getAttribute("data-preco"));
    const precoFormatado = preco.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });

    const precoElement = document.getElementById("preco-formatado");
    if (precoElement) {
        precoElement.innerText = precoFormatado;
    }
}

// Função para selecionar tipo de entrega (mesma lógica)
function selecionarEntrega(botao) {
    const botoes = document.querySelectorAll(".entrega button");
    botoes.forEach(btn => btn.classList.remove("ativo"));
    botao.classList.add("ativo");
    const tipoEntrega = botao.getAttribute("data-entrega");
    console.log("Tipo de entrega selecionado:", tipoEntrega);
}

// Evento para botão adicionar pedido
document.querySelector('.addPedido').addEventListener('click', () => {
    // Pega o id do produto da URL
    const urlParams = new URLSearchParams(window.location.search);
    const id_produto = parseInt(urlParams.get('id'));
    if (!id_produto) {
        alert('Produto inválido');
        return;
    }

    // Pega o botão de tamanho selecionado
    const botaoSelecionado = document.querySelector('.tamanho button.ativo');
    if (!botaoSelecionado) {
        alert('Por favor, selecione um tamanho.');
        return;
    }

    // Pega o preço ajustado do botão selecionado
    const precoUnitario = parseFloat(botaoSelecionado.getAttribute('data-preco'));
    if (isNaN(precoUnitario)) {
        alert('Preço inválido.');
        return;
    }

    // Quantidade padrão, pode ser um input futuramente
    const quantidade = 1;

    // Envia os dados via fetch para API (assumindo endpoint correto)
    fetch('../System/addPedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            id_produto,
            quantidade,
            preco_unitario: precoUnitario.toFixed(2)
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Produto adicionado ao carrinho!');
            // Atualizar UI se quiser
        } else {
            alert('Erro: ' + (data.error || 'Erro desconhecido'));
        }
    })
    .catch(() => alert('Erro ao comunicar com o servidor'));
});
