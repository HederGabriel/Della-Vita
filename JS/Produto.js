// Marca o botão de tamanho selecionado
function selecionarTamanho(botao) {
    const botoes = document.querySelectorAll(".tamanho button");
    botoes.forEach(btn => btn.classList.remove("ativo"));
    botao.classList.add("ativo");

    const preco = parseFloat(botao.getAttribute("data-preco"));
    const precoFormatado = preco.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });

    // Usar ID pois o h2 no HTML precisa ter id="preco-formatado"
    const precoElement = document.getElementById("preco-formatado");
    if (precoElement) {
        precoElement.innerText = precoFormatado;
    }
}

function selecionarEntrega(botao) {
    const botoes = document.querySelectorAll(".entrega button");
    botoes.forEach(btn => btn.classList.remove("ativo"));
    botao.classList.add("ativo");

    const tipoEntrega = botao.getAttribute("data-entrega");
    console.log("Tipo de entrega selecionado:", tipoEntrega);
}

// arquivo: Produto.js

document.querySelector('.addPedido').addEventListener('click', () => {
    // Pega o id_produto da URL
    const urlParams = new URLSearchParams(window.location.search);
    const id_produto = parseInt(urlParams.get('id'));
    if (!id_produto) {
        alert('Produto inválido');
        return;
    }

    // Pega o preço do elemento com id preco-formatado
    const precoFormatado = document.getElementById('preco-formatado').innerText;
    const precoLimpo = parseFloat(precoFormatado.replace(/[R$\s\.]/g, '').replace(',', '.'));

    if (isNaN(precoLimpo)) {
        alert('Preço inválido');
        return;
    }

    const quantidade = 1; // Pode ajustar para pegar do input se tiver

    fetch('../API/addPedido.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            id_produto,
            quantidade,
            preco_unitario: precoLimpo
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Produto adicionado ao carrinho!');
            // Atualizar UI ou redirecionar conforme necessário
        } else {
            alert('Erro: ' + (data.error || 'Erro desconhecido'));
        }
    })
    .catch(() => alert('Erro ao comunicar com o servidor'));
});
