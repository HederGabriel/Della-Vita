document.addEventListener("DOMContentLoaded", () => { 
  const pedidos = document.querySelectorAll(".pedido");
  const etapas = document.querySelectorAll(".etapas .etapa");
  const acoesDiv = document.querySelector(".acoes");
  const btnCancelar = acoesDiv.querySelector(".cancelar");
  const btnConfirmar = acoesDiv.querySelector(".confirmar");

  let pedidoSelecionado = null;
  let notaSelecionada = 0;

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
    btnConfirmar.disabled = ["Recebido", "Em Preparo", "Enviado", "archive"].includes(status);
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

    const confirmado = confirm("Tem certeza que deseja cancelar este pedido?");
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

  document.getElementById("btnEnviarNota").addEventListener("click", async () => {
    const modal = document.getElementById("modalAvaliacao");
    const idPedido = pedidoSelecionado.dataset.idPedido;

    modal.style.display = "none";

    try {
      await fetch("../System/salvarNota.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id_pedido=${encodeURIComponent(idPedido)}&nota=${encodeURIComponent(notaSelecionada || '')}`
      });
    } catch (e) {
      console.error("Erro ao salvar nota:", e);
      mostrarAlerta("Erro ao salvar a nota.");
      return;
    }

    try {
      const resStatus = await fetch("../System/atualizarStatus.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id_pedido=${encodeURIComponent(idPedido)}&status=archive`
      });

      const jsonStatus = await resStatus.json();
      console.log("Resposta atualizarStatus:", jsonStatus);

      if (jsonStatus.success === true || jsonStatus.success === "true") {
        mostrarAlerta("Pedido finalizado e arquivado com sucesso!");

        // Aguarda 1 segundo antes de continuar
        setTimeout(async () => {
          try {
            const resLocal = await fetch("../System/verificarPedidosLocal.php");
            const jsonLocal = await resLocal.json();

            if (jsonLocal.temPedido) {
              // Se ainda houver pedido local, permanece em retirarPedido.php
              window.location.href = "retirarPedido.php";
            } else {
              const resCasa = await fetch("../System/verificarPedidosCasa.php");
              const jsonCasa = await resCasa.json();

              if (jsonCasa.temPedido) {
                // Se houver pedido para entrega em casa, vai para acompanharPedido.php
                window.location.href = "acompanharPedido.php";
              } else {
                // Caso contrário, redireciona para a página inicial
                window.location.href = "index.php";
              }
            }
          } catch (e) {
            console.error("Erro ao verificar próximos pedidos:", e);
            mostrarAlerta("Erro ao verificar os próximos pedidos.");
          }
        }, 1000);
      } else {
        mostrarAlerta("Erro ao atualizar status.");
        console.error("Erro no retorno do status:", jsonStatus);
      }
    } catch (e) {
      console.error("Erro na comunicação ao atualizar status:", e);
      mostrarAlerta("Erro na comunicação ao atualizar status.");
    }
  });

  btnConfirmar.addEventListener("click", () => {
    if (!pedidoSelecionado) return;

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

  const btnAcompanhar = document.getElementById("btnAcompanhar");
  if (btnAcompanhar) {
    btnAcompanhar.addEventListener("click", function (event) {
      event.preventDefault(); // impede o redirecionamento automático do <a>

      fetch("../System/verificarPedidosCasa.php")
        .then(response => {
          if (!response.ok) throw new Error("Erro na resposta da API");
          return response.json();
        })
        .then(data => {
          if (data && (data.temPedido === true || data.temPedido === "true" || data.temPedido == 1)) {
            window.location.href = "acompanharPedido.php";
          } else {
            mostrarAlerta("Não há pedidos para acompanhamento.");
          }
        })
        .catch(() => {
          mostrarAlerta("Erro ao verificar pedidos para acompanhamento.");
        });
    });
  }
});
