// Produto.js

function selecionarTamanho(botao) {
    document.querySelectorAll(".tamanho button").forEach(btn => btn.classList.remove("ativo"));
    botao.classList.add("ativo");
    const preco = parseFloat(botao.getAttribute("data-preco"));
    const precoFormatado = preco.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    const precoElement = document.getElementById("preco-formatado");
    if (precoElement) precoElement.innerText = precoFormatado;
}

function selecionarEntrega(botao) {
    document.querySelectorAll(".entrega button").forEach(btn => btn.classList.remove("ativo"));
    botao.classList.add("ativo");
    const tipoEntrega = botao.getAttribute("data-entrega");
    console.log("Tipo de entrega selecionado:", tipoEntrega);
}

function adicionarPedido() {
    const urlParams = new URLSearchParams(window.location.search);
    const id_produto = parseInt(urlParams.get('id'));
    if (!id_produto) {
        alert('Produto inválido');
        return;
    }

    const botaoSelecionado = document.querySelector('.tamanho button.ativo');
    if (!botaoSelecionado) {
        alert('Por favor, selecione um tamanho.');
        return;
    }

    const precoUnitario = parseFloat(botaoSelecionado.getAttribute('data-preco'));
    if (isNaN(precoUnitario)) {
        alert('Preço inválido.');
        return;
    }

    const botaoEntrega = document.querySelector('.entrega button.ativo');
    if (!botaoEntrega) {
        alert('Por favor, selecione o tipo de entrega.');
        return;
    }

    const tipoEntrega = botaoEntrega.getAttribute('data-entrega');

    // Bloqueio para entrega local
    if (tipoEntrega === 'local') {
        alert('Entrega local ainda não está disponível. Função futura.');
        return;
    }

    // Só adiciona se for 'casa'
    if (tipoEntrega !== 'casa') {
        alert('Tipo de entrega inválido.');
        return;
    }

    const quantidade = 1;

    const dados = {
        id_produto,
        quantidade,
        preco_unitario: precoUnitario.toFixed(2),
        tipo_entrega: tipoEntrega
    };

    fetch('../System/addPedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(dados)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Produto adicionado ao carrinho!');
        } else {
            alert('Erro: ' + (data.error || 'Erro desconhecido'));
        }
    })
    .catch(() => alert('Erro ao comunicar com o servidor'));
}

document.querySelector('.addPedido').addEventListener('click', adicionarPedido);
