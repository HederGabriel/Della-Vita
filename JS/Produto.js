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

document.querySelectorAll('.addPedido').forEach(botao => {
    botao.addEventListener('click', () => {
        const produtoDiv = botao.closest('.produto');
        if (!produtoDiv) return;

        const id_produto = produtoDiv.dataset.idProduto;
        const preco_unitario = parseFloat(produtoDiv.dataset.preco);
        const quantidade = 1; // pode adaptar depois para input do usuário
        const total = preco_unitario * quantidade;

        fetch('adicionar_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id_produto=${encodeURIComponent(id_produto)}&quantidade=${quantidade}&preco_unitario=${preco_unitario}&total=${total}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            } else {
                alert("Erro: " + data.message);
            }
        })
        .catch(() => alert("Erro na conexão."));
    });
});
