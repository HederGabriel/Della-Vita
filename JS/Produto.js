// Arquivo: Produto.js

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

    document.getElementById("preco-formatado").innerText = precoFormatado;
}
