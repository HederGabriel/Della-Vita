document.addEventListener("DOMContentLoaded", () => {
  const pedidos = document.querySelectorAll(".pedido");
  const etapas = document.querySelectorAll(".etapas .etapa");
  const acoesDiv = document.querySelector(".acoes");
  const btnCancelar = acoesDiv.querySelector(".cancelar");

  let pedidoSelecionado = null;

  // Inicializa botões e ações
  acoesDiv.style.display = "none";
  btnCancelar.disabled = true;

  function mostrarAlerta(msg) {
    const toast = document.getElementById('toast-alerta');
    const texto = document.getElementById('toast-alerta-texto');

    texto.textContent = msg;
    toast.style.display = 'block';

    setTimeout(() => {
      toast.style.display = 'none';
    }, 3000);
  }

  function customConfirm(message) {
    return new Promise((resolve) => {
      const modal = document.getElementById('custom-confirm-modal');
      const messageEl = document.getElementById('custom-confirm-message');
      const btnYes = document.getElementById('custom-confirm-yes');
      const btnNo = document.getElementById('custom-confirm-no');

      messageEl.textContent = message;
      modal.style.display = 'flex';

      function cleanup() {
        modal.style.display = 'none';
        btnYes.removeEventListener('click', onYes);
        btnNo.removeEventListener('click', onNo);
      }

      function onYes() {
        cleanup();
        resolve(true);
      }

      function onNo() {
        cleanup();
        resolve(false);
      }

      btnYes.addEventListener('click', onYes);
      btnNo.addEventListener('click', onNo);
    });
  }

  function resetarStatusEtapas() {
    etapas.forEach((etapa) => etapa.classList.remove("ativo"));
  }

  function destacarEtapa(status) {
    const mapaStatus = {
      "Recebido": 0,
      "Em Preparo": 1,
      "Enviado": 2,
    };
    const index = mapaStatus[status];
    if (index !== undefined && etapas[index]) {
      etapas[index].classList.add("ativo");
    }
  }

  function atualizarBotoes(status) {
    // Desabilita cancelar se status estiver em "Aguardando Retirada"
    btnCancelar.disabled = status === "Aguardando Retirada";
  }

  function limparSelecaoPedidos() {
    pedidos.forEach((pedido) => pedido.classList.remove("pedido-selecionado"));
  }

  pedidos.forEach((pedido) => {
    pedido.style.cursor = "pointer";

    pedido.addEventListener("click", () => {
      const idPedido = pedido.dataset.idPedido;
      if (!idPedido) {
        console.error("ID do pedido não encontrado no elemento.");
        return;
      }

      limparSelecaoPedidos();
      pedido.classList.add("pedido-selecionado");
      pedidoSelecionado = pedido;

      acoesDiv.style.display = "block";
      btnCancelar.disabled = true;

      fetch(`../System/statusPedido.php?id_pedido=${encodeURIComponent(idPedido)}`)
        .then((response) => {
          if (!response.ok) {
            throw new Error(`Erro HTTP! Status: ${response.status}`);
          }
          return response.json();
        })
        .then((data) => {
          if (data.status) {
            resetarStatusEtapas();
            destacarEtapa(data.status);
            atualizarBotoes(data.status);
          } else {
            resetarStatusEtapas();
            btnCancelar.disabled = true;
          }
        })
        .catch((error) => {
          console.error("Erro ao buscar status:", error);
          resetarStatusEtapas();
          btnCancelar.disabled = true;
        });
    });
  });

  btnCancelar.addEventListener("click", async () => {
    if (!pedidoSelecionado) return;

    const idPedido = pedidoSelecionado.dataset.idPedido;

    const confirmado = await customConfirm("Tem certeza que deseja cancelar este pedido?");
    if (!confirmado) return;

    fetch("../System/cancelarPedido.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `id_pedido=${encodeURIComponent(idPedido)}`
    })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        pedidoSelecionado.remove();
        pedidoSelecionado = null;
        acoesDiv.style.display = "none";
        resetarStatusEtapas();
        location.reload();
      } else {
        mostrarAlerta(data.message);
      }
    })
    .catch((error) => {
      console.error("Erro ao cancelar pedido:", error);
      mostrarAlerta("Erro ao cancelar pedido. Tente novamente.");
    });
  });

  // Se houver pedidos, selecione o primeiro automaticamente
  if (pedidos.length > 0) {
    pedidos[0].click();
  }

    /*window.addEventListener("beforeunload", () => {
      navigator.sendBeacon('../System/archivePedidos.php');
    });*/
});
