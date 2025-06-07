document.addEventListener("DOMContentLoaded", () => {
  const pedidos = document.querySelectorAll(".pedido");
  const etapas = document.querySelectorAll(".etapas .etapa");
  const acoesDiv = document.querySelector(".acoes");
  const btnCancelar = acoesDiv.querySelector(".cancelar");
  const btnConfirmar = acoesDiv.querySelector(".confirmar");

  let pedidoSelecionado = null;

  acoesDiv.style.display = "none";
  btnCancelar.disabled = true;
  btnConfirmar.disabled = true;

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

      modal.style.display = 'flex';  // Aparece com as animações CSS

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
      "Entregue": 3,
    };
    const index = mapaStatus[status];
    if (index !== undefined && etapas[index]) {
      etapas[index].classList.add("ativo");
    }
  }

  function atualizarBotoes(status) {
    btnCancelar.disabled = ["Enviado", "Entregue", "archive"].includes(status);
    btnConfirmar.disabled = ["Recebido", "Em Preparo", "Entregue", "archive"].includes(status);
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
      btnConfirmar.disabled = true;

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
          }
        })
        .catch((error) => {
          console.error("Erro ao buscar status:", error);
          resetarStatusEtapas();
        });
    });
  });

  btnCancelar.addEventListener("click", async () => {
    if (!pedidoSelecionado) {
      return;
    }

    const idPedido = pedidoSelecionado.dataset.idPedido;

    const confirmado = await customConfirm("Tem certeza que deseja cancelar este pedido?");
    if (!confirmado) {
      return;
    }

    fetch("../System/cancelarPedido.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `id_pedido=${encodeURIComponent(idPedido)}`
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          pedidoSelecionado.remove();
          pedidoSelecionado = null;
          acoesDiv.style.display = "none";
          resetarStatusEtapas();
          location.reload();  // reload só após o pedido ser cancelado com sucesso
        } else {
          mostrarAlerta(data.message);
        }
      })
      .catch((error) => {
        console.error("Erro ao cancelar pedido:", error);
        mostrarAlerta("Erro ao cancelar pedido. Tente novamente.");
      });
  });

  btnConfirmar.addEventListener("click", async () => {
    if (!pedidoSelecionado) return;

    const idPedido = pedidoSelecionado.dataset.idPedido;
    const confirmado = await customConfirm("Deseja confirmar que recebeu esse pedido?");

    if (!confirmado) return;

    fetch("../System/atualizarStatus.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        id_pedido: idPedido,
        status: "Entregue"
      })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          mostrarAlerta("Status atualizado para 'Entregue'");
          resetarStatusEtapas();
          destacarEtapa("Entregue");
          atualizarBotoes("Entregue");
        } else {
          mostrarAlerta("Erro ao atualizar status.");
          console.error(data.message || "Erro desconhecido.");
        }
      })
      .catch(error => {
        mostrarAlerta("Erro de conexão com o servidor.");
        console.error("Erro:", error);
      });
  });

  if (pedidos.length > 0) {
    pedidos[0].click();
  }

  /*window.addEventListener("beforeunload", () => {
    navigator.sendBeacon('../System/archivePedidos.php');
  });*/
});
