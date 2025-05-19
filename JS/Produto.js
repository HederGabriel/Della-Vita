// Produto.js

// Função para selecionar o tamanho e atualizar o preço formatado
function selecionarTamanho(botao) {
    document.querySelectorAll(".tamanho button").forEach(btn => btn.classList.remove("ativo"));
    botao.classList.add("ativo");

    const preco = parseFloat(botao.getAttribute("data-preco"));
    const precoFormatado = preco.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });
    const precoElement = document.getElementById("preco-formatado");
    if (precoElement) precoElement.innerText = precoFormatado;
}

// Função para selecionar o tipo de entrega
function selecionarEntrega(botao) {
    document.querySelectorAll(".entrega button").forEach(btn => btn.classList.remove("ativo"));
    botao.classList.add("ativo");

    const tipoEntrega = botao.getAttribute("data-entrega");
    console.log("Tipo de entrega selecionado:", tipoEntrega);
}

function abrirModalQuantidade() {
    const botaoEntrega = document.querySelector('.entrega button.ativo');
    if (!botaoEntrega) {
        alert('Por favor, selecione o tipo de entrega.');
        return;
    }

    const tipoEntrega = botaoEntrega.getAttribute('data-entrega');

    const modal = document.getElementById('modal-quantidade');
    if (modal) {
        modal.style.display = 'flex';
        atualizarTotal(); // Garante que o total é atualizado ao abrir
    }
}

// Função para fechar o modal
function fecharModal() {
    const modal = document.getElementById('modal-quantidade');
    if (modal) {
        modal.style.display = 'none';

        // Resetar a quantidade para 1
        const inputQuantidade = document.getElementById('quantidade');
        if (inputQuantidade) {
            inputQuantidade.value = 1;
            atualizarTotal();
        }
    }
}

// Função para atualizar o valor total no modal
function atualizarTotal() {
    const input = document.getElementById('quantidade');
    const quantidade = parseInt(input.value);
    const precoUnitario = parseFloat(document.querySelector('.tamanho button.ativo').getAttribute('data-preco'));

    const total = quantidade * precoUnitario;
    const totalFormatado = total.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });

    document.getElementById('total-modal').innerText = totalFormatado;
}

// Função para confirmar e adicionar o pedido
function confirmarAdicionar() {
    const quantidade = parseInt(document.getElementById('quantidade').value);
    if (isNaN(quantidade) || quantidade < 1) {
        alert('Quantidade inválida.');
        return;
    }

    const botaoSelecionado = document.querySelector('.tamanho button.ativo');
    if (!botaoSelecionado) {
        alert('Por favor, selecione um tamanho.');
        return;
    }

    const precoUnitario = parseFloat(botaoSelecionado.getAttribute('data-preco'));
    const tamanhoSelecionado = botaoSelecionado.id; // <- Pega o ID do botão (pq, m ou g)

    const botaoEntrega = document.querySelector('.entrega button.ativo');
    if (!botaoEntrega) {
        alert('Por favor, selecione o tipo de entrega.');
        return;
    }

    const tipoEntrega = botaoEntrega.getAttribute('data-entrega');

    const urlParams = new URLSearchParams(window.location.search);
    const id_produto = parseInt(urlParams.get('id'));

    if (!id_produto) {
        alert('Produto inválido');
        return;
    }

    const dados = {
        id_produto,
        quantidade,
        preco_unitario: precoUnitario.toFixed(2),
        tipo_entrega: tipoEntrega,
        tamanho: tamanhoSelecionado // <- Envia o tamanho
    };

    fetch('../System/addPedido.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(dados)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            fecharModal();
        } else {
            alert('Erro: ' + (data.error || 'Erro desconhecido'));
        }
    })
    .catch(() => alert('Erro ao comunicar com o servidor'));
}

// Adiciona os eventos quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    const btnAddPedido = document.querySelector('.addPedido');
    const btnConfirmar = document.getElementById('confirmarAdd');
    const btnCancelar = document.getElementById('cancelarAdd');
    const inputQuantidade = document.getElementById('quantidade');

    if (btnAddPedido && btnConfirmar && btnCancelar) {
        btnAddPedido.addEventListener('click', () => {
            abrirModalQuantidade();
        });

        btnConfirmar.addEventListener('click', () => {
            confirmarAdicionar();
        });

        btnCancelar.addEventListener('click', () => {
            fecharModal();
        });
    }

    document.querySelectorAll('.tamanho button').forEach(btn => {
        btn.addEventListener('click', () => selecionarTamanho(btn));
    });

    document.querySelectorAll('.entrega button').forEach(btn => {
        btn.addEventListener('click', () => selecionarEntrega(btn));
    });

    if (inputQuantidade) {
        inputQuantidade.addEventListener('input', atualizarTotal);
    }
});
