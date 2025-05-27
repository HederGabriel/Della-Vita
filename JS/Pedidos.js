document.addEventListener('DOMContentLoaded', () => {
  const casa = document.getElementById('casa');
  const local = document.getElementById('local');
  const h2Casa = document.querySelector('.h2-casa');
  const h2Local = document.querySelector('.h2-local');
  const btn = document.getElementById('btnFinalizar');
  const tipoPedidoInput = document.getElementById('input-tipo-pedido');

  const modalEndereco = document.getElementById('modal-endereco');
  
  // Inputs do modal para os dados completos
  const inputNumeroModal = document.getElementById('input-numero');
  const inputComplementoModal = document.getElementById('input-complemento');
  const inputBairroModal = document.getElementById('input-bairro');
  const inputSetorModal = document.getElementById('input-setor');
  const inputCidadeModal = document.getElementById('input-cidade');
  const inputCepModal = document.getElementById('input-cep');

  const btnConfirmarEndereco = document.getElementById('confirmar-endereco');
  const btnCancelarEndereco = modalEndereco.querySelector('.btn-cancelar');

  const formFinalizar = document.getElementById('form-finalizar');

  function reset() {
    h2Casa.classList.remove('h2-active');
    h2Local.classList.remove('h2-active');
    btn.disabled = true;
    tipoPedidoInput.value = '';
    casa.checked = false;
    local.checked = false;
  }

  function toggleCheckbox(clicked) {
    if (clicked === casa) {
      if (casa.checked) {
        local.checked = false;
        h2Casa.classList.add('h2-active');
        h2Local.classList.remove('h2-active');
        btn.disabled = false;
        tipoPedidoInput.value = 'casa';
      } else {
        reset();
      }
    } else if (clicked === local) {
      if (local.checked) {
        casa.checked = false;
        h2Local.classList.add('h2-active');
        h2Casa.classList.remove('h2-active');
        btn.disabled = false;
        tipoPedidoInput.value = 'local';
      } else {
        reset();
      }
    }
  }

  function abrirModalEndereco() {
    modalEndereco.style.display = 'flex';
    inputNumeroModal.value = '';
    inputComplementoModal.value = '';
    inputBairroModal.value = '';
    inputSetorModal.value = '';
    inputCidadeModal.value = '';
    inputCepModal.value = '';
  }

  function fecharModalEndereco() {
    modalEndereco.style.display = 'none';
  }

  casa.addEventListener('change', () => toggleCheckbox(casa));
  local.addEventListener('change', () => toggleCheckbox(local));

  btn.addEventListener('click', (e) => {
    const tipoPedido = tipoPedidoInput.value;

    if (!tipoPedido) {
      alert('Selecione uma opção de entrega ou retirada.');
      return;
    }

    if (tipoPedido === 'casa') {
      abrirModalEndereco();
    } else {
      formFinalizar.submit();
    }
  });

  btnConfirmarEndereco.addEventListener('click', (e) => {
    e.preventDefault();

    // Validação dos campos obrigatórios
    if (
      !inputNumeroModal.value.trim() ||
      !inputBairroModal.value.trim() ||
      !inputSetorModal.value.trim() ||
      !inputCidadeModal.value.trim() ||
      !inputCepModal.value.trim()
    ) {
      alert('Por favor, preencha todos os campos obrigatórios do endereço.');
      return;
    }

    // Preenche os inputs ocultos do formulário principal com os dados do modal
    preencherInputsEndereco({
      numero: inputNumeroModal.value.trim(),
      complemento: inputComplementoModal.value.trim(),
      bairro: inputBairroModal.value.trim(),
      setor: inputSetorModal.value.trim(), // ou crie inputSetorModal se separado
      cidade: inputCidadeModal.value.trim(),
      cep: inputCepModal.value.trim()
    });

    fecharModalEndereco();
    formFinalizar.submit();
  });

  btnCancelarEndereco.addEventListener('click', (e) => {
    e.preventDefault();
    fecharModalEndereco();
  });

  // Função para criar/atualizar inputs hidden no form principal
  function preencherInputsEndereco(endereco) {
    Object.entries(endereco).forEach(([key, value]) => {
      let inputHidden = formFinalizar.querySelector(`input[name="${key}"]`);
      if (!inputHidden) {
        inputHidden = document.createElement('input');
        inputHidden.type = 'hidden';
        inputHidden.name = key;
        formFinalizar.appendChild(inputHidden);
      }
      inputHidden.value = value;
    });
  }
});
