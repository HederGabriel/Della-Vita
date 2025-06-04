document.addEventListener("DOMContentLoaded", () => {
  const pedidos = document.querySelectorAll(".pedido");
  const etapas = document.querySelectorAll(".etapas .etapa");
  const acoesDiv = document.querySelector(".acoes");
  const btnCancelar = acoesDiv.querySelector(".cancelar");
  const btnConfirmar = acoesDiv.querySelector(".confirmar");

  // Inicializa: oculta os botões e deixa desabilitados
  acoesDiv.style.display = "none";
  btnCancelar.disabled = true;
  btnConfirmar.disabled = true;

  // Resetar visual dos blocos de status
  function resetarStatusEtapas() {
    etapas.forEach(etapa => etapa.classList.remove("ativo"));
  }

  // Marcar a etapa correspondente como ativa
  function destacarEtapa(status) {
    const mapaStatus = {
      "Recebido": 0,
      "Em Preparo": 1,
      "Enviado": 2,
      "Entregue": 3
    };
    const index = mapaStatus[status];
    if (index !== undefined && etapas[index]) {
      etapas[index].classList.add("ativo");
    }
  }

  // Resetar destaque visual dos pedidos
  function limparSelecaoPedidos() {
    pedidos.forEach(pedido => pedido.classList.remove("pedido-selecionado"));
  }

  // Torna os pedidos clicáveis
  pedidos.forEach(pedido => {
    pedido.style.cursor = "pointer";

    pedido.addEventListener("click", () => {
      const idPedido = pedido.dataset.idPedido;
      if (!idPedido) {
        console.error("ID do pedido não encontrado no elemento.");
        return;
      }

      limparSelecaoPedidos();
      pedido.classList.add("pedido-selecionado");

      // Exibir e habilitar botões ao selecionar um pedido
      acoesDiv.style.display = "block";
      btnCancelar.disabled = false;
      btnConfirmar.disabled = false;

      // → AQUI ESTÁ A CORREÇÃO: usar "../System/…"
      fetch(`../System/statusPedido.php?id_pedido=${encodeURIComponent(idPedido)}`)
        .then(response => {
          if (!response.ok) {
            throw new Error(`Erro HTTP! Status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          console.log("Status recebido:", data.status);
          if (data.status) {
            resetarStatusEtapas();
            destacarEtapa(data.status);
          } else {
            console.warn("Status não retornado ou é nulo.");
            resetarStatusEtapas();
          }
        })
        .catch(error => {
          console.error("Erro ao buscar status:", error);
          resetarStatusEtapas();
        });
    });
  });
});
