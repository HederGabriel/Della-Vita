function atualizarStatus(selectElement, idPedido) {
    const novoStatus = selectElement.value;

    fetch('../System/cozinha-status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id_pedido=${idPedido}&status_pedido=${encodeURIComponent(novoStatus)}`
    })
    .then(response => response.text())
    .then(result => {
        if (result === 'OK') {
            // Atualiza o texto do span status-destaque na mesma div
            const pedidoDiv = selectElement.closest('.pedido-info');
            const spanStatus = pedidoDiv.querySelector('.status-destaque');

            // Mapear para o texto correto, igual ao PHP
            const mapaStatusExibicao = {
                'Recebido': 'Recebido',
                'Em Preparo': 'Em Preparo',
                'Enviado': selectElement.options[selectElement.selectedIndex].text,
                'Entregue': 'Retirado', // Para retirada local
            };

            let textoExibicao = mapaStatusExibicao[novoStatus] || novoStatus;
            spanStatus.textContent = textoExibicao;
        } else {
            console.warn('Erro ao atualizar o status:', result);
            alert('Erro ao atualizar o status!');
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        alert('Erro na requisição');
    });
}
