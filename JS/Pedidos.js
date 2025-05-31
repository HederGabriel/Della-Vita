document.addEventListener('DOMContentLoaded', () => {
  const casa = document.getElementById('casa');
  const local = document.getElementById('local');
  const h2Casa = document.querySelector('.h2-casa');
  const h2Local = document.querySelector('.h2-local');
  const btn = document.getElementById('btnFinalizar');
  const tipoPedidoInput = document.getElementById('input-tipo-pedido');

  const modalEndereco = document.getElementById('modal-endereco');

  // Inputs do modal
  const inputEnderecoCompleto = document.getElementById('input-endereco-completo');
  const inputRuaModal = document.getElementById('input-rua-modal'); // NOVO
  const inputNumeroModal = document.getElementById('input-numero-modal');
  const inputSetorModal = document.getElementById('input-setor-modal');
  const inputCidadeModal = document.getElementById('input-cidade-modal');
  const inputCepModal = document.getElementById('input-cep-modal');
  const inputComplementoModal = document.getElementById('input-complemento-modal');

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
    inputEnderecoCompleto.value = '';
    inputRuaModal.value = '';
    inputNumeroModal.value = '';
    inputComplementoModal.value = '';
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

    if (
      !inputEnderecoCompleto.value.trim() ||
      !inputRuaModal.value.trim() ||
      !inputNumeroModal.value.trim() ||
      !inputSetorModal.value.trim() ||
      !inputCidadeModal.value.trim() ||
      !inputCepModal.value.trim()
    ) {
      alert('Por favor, preencha todos os campos obrigatórios do endereço.');
      return;
    }

    preencherInputsEndereco({
      enderecoCompleto: inputEnderecoCompleto.value.trim(),
      rua: inputRuaModal.value.trim(),
      numero: inputNumeroModal.value.trim(),
      complemento: inputComplementoModal.value.trim(),
      setor: inputSetorModal.value.trim(),
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

  // ===== Google Places Autocomplete (novo SDK) =====

  const autocompleteElement = document.createElement('gmpx-placeautocomplete');
  autocompleteElement.setAttribute('placeholder', 'Digite seu endereço');
  autocompleteElement.style.width = '100%';

  const containerAutocomplete = document.getElementById('autocomplete-container');
  if (containerAutocomplete) {
    containerAutocomplete.innerHTML = '';
    containerAutocomplete.appendChild(autocompleteElement);
  } else {
    inputEnderecoCompleto.style.display = 'none';
    inputEnderecoCompleto.parentElement.appendChild(autocompleteElement);
  }

  autocompleteElement.addEventListener('gmpx-placeautocomplete-placechange', (event) => {
    const place = event.detail;

    let rua = '';
    let numero = '';
    let bairro = '';
    let cidade = 'Posse'; // padrão
    let cep = '';
    let enderecoCompleto = place.formatted_address || '';

    if (place.address_components) {
      place.address_components.forEach(component => {
        if (component.types.includes('route')) rua = component.long_name;
        if (component.types.includes('street_number')) numero = component.long_name;
        if (component.types.includes('neighborhood')) bairro = component.long_name;
        if (component.types.includes('postal_code')) cep = component.long_name;
        if (component.types.includes('locality') || component.types.includes('administrative_area_level_2')) {
          cidade = component.long_name;
        }
      });
    }

    inputRuaModal.value = rua;
    inputNumeroModal.value = numero;
    inputSetorModal.value = bairro;
    inputCidadeModal.value = cidade;
    inputCepModal.value = cep;
    inputEnderecoCompleto.value = enderecoCompleto;
  });

});
