document.addEventListener('DOMContentLoaded', () => {
  const casa = document.getElementById('casa');
  const local = document.getElementById('local');
  const h2Casa = document.querySelector('.h2-casa');
  const h2Local = document.querySelector('.h2-local');

  const btnFinalizar = document.getElementById('btnFinalizar');
  const inputTipoPedido = document.getElementById('input-tipo-pedido');
  const formFinalizar = document.getElementById('form-finalizar');

  const modalEndereco = document.getElementById('modal-endereco');
  const btnConfirmarEndereco = document.getElementById('btnConfirmarEndereco');
  const btnCancelarEndereco = document.getElementById('btnCancelarEndereco');

  const inputRuaModal = document.getElementById('input-rua-modal');
  const inputNumeroModal = document.getElementById('input-numero-modal');
  const checkboxSemNumero = document.getElementById('checkbox-sem-numero');
  const inputSetorModal = document.getElementById('input-setor-modal');
  const inputCepModal = document.getElementById('input-cep-modal');
  const inputComplementoModal = document.getElementById('input-complemento-modal');
  const inputCidadeModal = document.getElementById('input-cidade-modal');

  const modalComentario = document.getElementById('modal-comentario');
  const btnConfirmarComentario = document.getElementById('btnConfirmarComentario');
  const btnCancelarComentario = document.getElementById('btnCancelarComentario');
  const inputComentario = document.getElementById('input-comentario');
  const contadorComentario = document.getElementById('contador-comentario');

  const modalRemoverItem = document.getElementById('modal-remover-item');
  const btnConfirmarRemover = document.getElementById('btnConfirmarRemover');
  const btnCancelarRemover = document.getElementById('btnCancelarRemover');
  const textoModalRemover = document.getElementById('texto-modal-remover');

  let itemParaRemoverId = null;
  let tipoSelecionado = '';
  let idsSelecionados = [];

  checkboxSemNumero.addEventListener('change', () => {
    if (checkboxSemNumero.checked) {
      inputNumeroModal.value = 'S/N';
      inputNumeroModal.disabled = true;
    } else {
      inputNumeroModal.value = '';
      inputNumeroModal.disabled = false;
      inputNumeroModal.focus();
    }
  });

  function mostrarAlerta(msg) {
    const toast = document.getElementById('toast-alerta');
    const texto = document.getElementById('toast-alerta-texto');

    texto.textContent = msg;
    toast.style.display = 'block';

    // Ocultar após 3 segundos
    setTimeout(() => {
      toast.style.display = 'none';
    }, 3000);
  }

  function resetSelecao() {
    btnFinalizar.disabled = true;
    inputTipoPedido.value = '';
    h2Casa.classList.remove('h2-active');
    h2Local.classList.remove('h2-active');
    casa.checked = false;
    local.checked = false;
  }

  function handleCheckboxClick(checkboxClicado) {
    if (checkboxClicado === casa) {
      if (casa.checked) {
        local.checked = false;
        h2Casa.classList.add('h2-active');
        h2Local.classList.remove('h2-active');
        inputTipoPedido.value = 'casa';
        btnFinalizar.disabled = false;
      } else {
        resetSelecao();
      }
    } else if (checkboxClicado === local) {
      if (local.checked) {
        casa.checked = false;
        h2Local.classList.add('h2-active');
        h2Casa.classList.remove('h2-active');
        inputTipoPedido.value = 'local';
        btnFinalizar.disabled = false;
      } else {
        resetSelecao();
      }
    }
  }

  casa.addEventListener('change', () => handleCheckboxClick(casa));
  local.addEventListener('change', () => handleCheckboxClick(local));

  function abrirModalEndereco() {
    inputRuaModal.value = '';
    inputNumeroModal.value = '';
    inputSetorModal.value = '';
    inputCepModal.value = '';
    inputComplementoModal.value = '';
    inputCidadeModal.value = '';
    modalEndereco.style.display = 'flex';
  }

  function fecharModalEndereco() {
    modalEndereco.style.display = 'none';
  }

  function abrirModalComentario() {
    inputComentario.value = '';
    contadorComentario.textContent = '0/200';
    modalComentario.style.display = 'flex';
  }

  function fecharModalComentario() {
    modalComentario.style.display = 'none';
  }

  inputComentario.addEventListener('input', () => {
    const limite = inputComentario.maxLength;
    const atual = inputComentario.value.length;
    contadorComentario.textContent = `${atual}/${limite}`;
  });

  function validarCamposEnderecoModal() {
    if (!inputCepModal.value.trim()) {
      mostrarAlerta('Por favor, preencha o campo CEP.');
      return false;
    }
    if (!inputCidadeModal.value.trim()) {
      mostrarAlerta('Por favor, preencha o campo Cidade.');
      return false;
    }
    if (!inputRuaModal.value.trim()) {
      mostrarAlerta('Por favor, preencha o campo Rua.');
      return false;
    }
    if (!inputNumeroModal.value.trim()) {
      mostrarAlerta('Por favor, preencha o campo Número.');
      return false;
    }
    return true;
  }

  function enviarFormularioAjax() {
    const formData = new FormData(formFinalizar);
    fetch('../System/finalizarPedido.php', {
      method: 'POST',
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          window.location.href = data.redirect;
        } else {
          mostrarAlerta(data.error || 'Erro ao finalizar pedido.');
        }
      });
  }

  btnFinalizar.addEventListener('click', (e) => {
    e.preventDefault();
    tipoSelecionado = inputTipoPedido.value.trim().toLowerCase();
    if (!tipoSelecionado) {
      mostrarAlerta('Por favor, selecione um tipo de pedido.');
      return;
    }

    const itensPedido = document.querySelectorAll('.pedido-item');
    if (itensPedido.length === 0) {
      mostrarAlerta('Não há itens no pedido.');
      return;
    }

    idsSelecionados = [];
    itensPedido.forEach(item => {
      if (item.dataset.tipo && item.dataset.tipo.toLowerCase() === tipoSelecionado) {
        if (item.dataset.idItem) {
          idsSelecionados.push(item.dataset.idItem);
        }
      }
    });

    if (idsSelecionados.length === 0) {
      mostrarAlerta(`Nenhum item encontrado para o tipo "${tipoSelecionado}".`);
      return;
    }

    abrirModalComentario();
  });

  btnConfirmarComentario.addEventListener('click', (e) => {
    e.preventDefault();

    let comentario = inputComentario.value.trim();
    if (!comentario) comentario = 'Sem Comentários';

    let inputComentarioForm = formFinalizar.querySelector('input[name="comentario"]');
    if (!inputComentarioForm) {
      inputComentarioForm = document.createElement('input');
      inputComentarioForm.type = 'hidden';
      inputComentarioForm.name = 'comentario';
      formFinalizar.appendChild(inputComentarioForm);
    }
    inputComentarioForm.value = comentario;

    let inputIds = formFinalizar.querySelector('input[name="ids_itens"]');
    if (!inputIds) {
      inputIds = document.createElement('input');
      inputIds.type = 'hidden';
      inputIds.name = 'ids_itens';
      formFinalizar.appendChild(inputIds);
    }
    inputIds.value = idsSelecionados.join(',');

    fecharModalComentario();

    if (tipoSelecionado === 'casa') {
      abrirModalEndereco();
    } else {
      enviarFormularioAjax();
    }
  });

  btnCancelarComentario.addEventListener('click', (e) => {
    e.preventDefault();
    fecharModalComentario();
  });

  btnConfirmarEndereco.addEventListener('click', (e) => {
    e.preventDefault();
    if (validarCamposEnderecoModal()) {
      document.getElementById('input-rua').value = inputRuaModal.value.trim();
      document.getElementById('input-numero').value = inputNumeroModal.value.trim();
      document.getElementById('input-cep').value = inputCepModal.value.trim();
      document.getElementById('input-setor').value = inputSetorModal.value.trim();
      document.getElementById('input-complemento').value = inputComplementoModal.value.trim();
      document.getElementById('input-cidade').value = inputCidadeModal.value.trim();

      fecharModalEndereco();
      enviarFormularioAjax();
    }
  });

  btnCancelarEndereco.addEventListener('click', (e) => {
    e.preventDefault();
    fecharModalEndereco();
  });

  btnFinalizar.disabled = true;

  function abrirModalRemoverItem(idItem) {
    itemParaRemoverId = idItem;
    textoModalRemover.textContent = 'Deseja realmente remover este item do pedido?';
    modalRemoverItem.style.display = 'flex';
  }

  function fecharModalRemoverItem() {
    modalRemoverItem.style.display = 'none';
    itemParaRemoverId = null;
  }

  function removerItem(idItem) {
    fetch('../System/removerItem.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id_item=${encodeURIComponent(idItem)}`
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.querySelector(`.pedido-item[data-id-item="${idItem}"]`)?.remove();
        } else {
          mostrarAlerta(data.error || 'Erro ao remover item.');
        }
      })
      .catch(() => {
        mostrarAlerta('Erro ao comunicar com o servidor para remover item.');
      });
  }

  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-mais') || e.target.classList.contains('btn-menos')) {
      const btn = e.target;
      const container = btn.closest('.quantidade-container');
      const spanQuantidade = container.querySelector('.quantidade');
      const idItem = btn.dataset.id;
      let quantidadeAtual = parseInt(spanQuantidade.textContent, 10);

      if (btn.classList.contains('btn-mais')) {
        quantidadeAtual++;
        spanQuantidade.textContent = quantidadeAtual;
        atualizarQuantidade(idItem, quantidadeAtual);
      } else if (btn.classList.contains('btn-menos')) {
        if (quantidadeAtual === 1) {
          abrirModalRemoverItem(idItem);
        } else if (quantidadeAtual > 1) {
          quantidadeAtual--;
          spanQuantidade.textContent = quantidadeAtual;
          atualizarQuantidade(idItem, quantidadeAtual);
        }
      }
    }
  });

  btnConfirmarRemover.addEventListener('click', (e) => {
    e.preventDefault();
    if (itemParaRemoverId) {
      removerItem(itemParaRemoverId);
      fecharModalRemoverItem();
    }
  });

  btnCancelarRemover.addEventListener('click', (e) => {
    e.preventDefault();
    fecharModalRemoverItem();
  });

  function atualizarQuantidade(idItem, novaQuantidade) {
    const formData = new FormData();
    formData.append('id_item', idItem);
    formData.append('quantidade', novaQuantidade);

    fetch('../System/atualizarQuantidade.php', {
      method: 'POST',
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        if (!data.success) {
          mostrarAlerta(data.error || 'Erro ao atualizar quantidade.');
        } else {
          window.location.reload();
        }
      })
      .catch(() => {
        mostrarAlerta('Erro ao comunicar com o servidor.');
      });
  }
});