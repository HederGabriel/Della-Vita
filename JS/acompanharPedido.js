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
      if (!idPedido) return;

      limparSelecaoPedidos();
      pedido.classList.add("pedido-selecionado");
      pedidoSelecionado = pedido;

      acoesDiv.style.display = "block";
      btnCancelar.disabled = true;
      btnConfirmar.disabled = true;

      fetch(`../System/statusPedido.php?id_pedido=${encodeURIComponent(idPedido)}`)
        .then((response) => {
          if (!response.ok) throw new Error();
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
        .catch(() => {
          resetarStatusEtapas();
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
      .catch(() => {
        mostrarAlerta("Erro ao cancelar pedido. Tente novamente.");
      });
  });

  let notaSelecionada = 0;

  document.getElementById("btnEnviarNota").addEventListener("click", async () => {
    const modal = document.getElementById("modalAvaliacao");
    const idPedido = pedidoSelecionado.dataset.idPedido;

    modal.style.display = "none";

    // Tenta salvar a nota, mesmo que não tenha sido selecionada
    try {
      await fetch("../System/salvarNota.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id_pedido=${encodeURIComponent(idPedido)}&nota=${encodeURIComponent(notaSelecionada || '')}`
      });
    } catch {
      // Mesmo se falhar o envio da nota, segue com status archive
    }

    // Atualiza status para archive
    try {
      const resStatus = await fetch("../System/atualizarStatus.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id_pedido=${encodeURIComponent(idPedido)}&status=archive`
      });
      const jsonStatus = await resStatus.json();
      if (jsonStatus.success) {
        mostrarAlerta("Pedido finalizado e arquivado com sucesso!");
      } else {
        mostrarAlerta("Erro ao atualizar status.");
      }
    } catch {
      mostrarAlerta("Erro na comunicação ao atualizar status.");
    }

    // Sempre recarrega a página no final
    location.reload();
  });

  btnConfirmar.addEventListener("click", async () => {
    if (!pedidoSelecionado) return;

    const confirmado = await customConfirm("Deseja confirmar que recebeu esse pedido?");
    if (!confirmado) return;

    const modal = document.getElementById("modalAvaliacao");
    notaSelecionada = 0;

    document.querySelectorAll("#estrelas span").forEach(estrela => {
      estrela.classList.remove("selecionada");
      estrela.onclick = () => {
        notaSelecionada = parseInt(estrela.dataset.valor);
        document.querySelectorAll("#estrelas span").forEach((e, index) => {
          e.classList.toggle("selecionada", index < notaSelecionada);
        });
      };
    });

    modal.style.display = "block";
  });

  if (pedidos.length > 0) {
    pedidos[0].click();
  }

  const btnRetirar = document.getElementById("btnRetirar");
  if (btnRetirar) {
    btnRetirar.addEventListener("click", function (event) {
      event.preventDefault(); // impede o redirecionamento automático do <a>

      fetch("../System/verificarPedidosLocal.php")
        .then(response => {
          if (!response.ok) throw new Error("Erro na resposta da API");
          return response.json();
        })
        .then(data => {
          if (data && (data.temPedido === true || data.temPedido === "true" || data.temPedido == 1)) {
            window.location.href = "retirarPedido.php";
          } else {
            mostrarAlerta("Não há pedidos disponíveis para retirada.");
          }
        })
        .catch(() => {
          mostrarAlerta("Erro ao verificar pedidos para retirada.");
        });
    });
  }
});
