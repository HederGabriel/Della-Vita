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

  const inputRua = document.getElementById('input-rua');
  const inputNumero = document.getElementById('input-numero');
  const inputSetor = document.getElementById('input-setor');
  const inputCep = document.getElementById('input-cep');
  const inputComplemento = document.getElementById('input-complemento');
  const inputCidade = document.getElementById('input-cidade');

  const inputRuaModal = document.getElementById('input-rua-modal');
  const inputNumeroModal = document.getElementById('input-numero-modal');
  const inputSetorModal = document.getElementById('input-setor-modal');
  const inputCepModal = document.getElementById('input-cep-modal');
  const inputComplementoModal = document.getElementById('input-complemento-modal');
  const inputCidadeModal = document.getElementById('input-cidade-modal');

  const modalRemoverItem = document.getElementById('modal-remover-item');
  const btnConfirmarRemover = document.getElementById('btnConfirmarRemover');
  const btnCancelarRemover = document.getElementById('btnCancelarRemover');
  const textoModalRemover = document.getElementById('texto-modal-remover');

  let itemParaRemoverId = null;

  function mostrarAlerta(msg) {
    console.log('Alerta:', msg);
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
    })
    .catch(error => {
      console.error('Erro na requisição:', error);
      mostrarAlerta('Erro inesperado ao enviar pedido.');
    });
  }

  btnFinalizar.addEventListener('click', (e) => {
    e.preventDefault();

    const tipo = inputTipoPedido.value.trim().toLowerCase();
    if (!tipo) {
      mostrarAlerta('Por favor, selecione um tipo de pedido.');
      return;
    }

    const itensPedido = document.querySelectorAll('.pedido-item');
    if (itensPedido.length === 0) {
      mostrarAlerta('Não há itens no pedido.');
      return;
    }

    const idsSelecionados = [];
    itensPedido.forEach(item => {
      if (item.dataset.tipo && item.dataset.tipo.toLowerCase() === tipo) {
        if (item.dataset.idItem) {
          idsSelecionados.push(item.dataset.idItem);
        }
      }
    });

    if (idsSelecionados.length === 0) {
      mostrarAlerta(`Nenhum item encontrado para o tipo "${tipo}".`);
      return;
    }

    let inputIds = formFinalizar.querySelector('input[name="ids_itens"]');
    if (!inputIds) {
      inputIds = document.createElement('input');
      inputIds.type = 'hidden';
      inputIds.name = 'ids_itens';
      formFinalizar.appendChild(inputIds);
    }
    inputIds.value = idsSelecionados.join(',');

    if (tipo === 'casa') {
      abrirModalEndereco();
    } else {
      enviarFormularioAjax();
    }
  });

  btnConfirmarEndereco.addEventListener('click', (e) => {
    e.preventDefault();

    if (
      !inputRuaModal.value.trim() ||
      !inputNumeroModal.value.trim() ||
      !inputSetorModal.value.trim() ||
      !inputCepModal.value.trim() ||
      !inputCidadeModal.value.trim()
    ) {
      mostrarAlerta('Por favor, preencha todos os campos obrigatórios do endereço.');
      return;
    }

    inputRua.value = inputRuaModal.value.trim();
    inputNumero.value = inputNumeroModal.value.trim();
    inputSetor.value = inputSetorModal.value.trim();
    inputCep.value = inputCepModal.value.trim();
    inputComplemento.value = inputComplementoModal.value.trim();
    if (inputCidade) inputCidade.value = inputCidadeModal.value.trim();

    fecharModalEndereco();
    enviarFormularioAjax();
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
        // Recarrega a página para atualizar o total e o conteúdo
        window.location.reload();
      }
    })
    .catch(() => {
      mostrarAlerta('Erro ao comunicar com o servidor.');
    });
  }
});
