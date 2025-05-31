document.addEventListener('DOMContentLoaded', () => {
  const casa = document.getElementById('casa');
  const local = document.getElementById('local');
  const h2Casa = document.querySelector('.h2-casa');
  const h2Local = document.querySelector('.h2-local');
  const btn = document.getElementById('btnFinalizar');
  const tipoPedidoInput = document.getElementById('input-tipo-pedido');
  const formFinalizar = document.getElementById('form-finalizar');

  const modalEndereco = document.getElementById('modal-endereco');
  const btnConfirmarEndereco = document.getElementById('confirmar-endereco');
  const btnCancelarEndereco = document.querySelector('.btn-cancelar');

  // Inputs do formulário principal
  const inputRua = document.getElementById('input-rua');
  const inputNumero = document.getElementById('input-numero');
  const inputSetor = document.getElementById('input-setor');
  const inputCep = document.getElementById('input-cep');
  const inputComplemento = document.getElementById('input-complemento');

  // Inputs do modal
  const inputRuaModal = document.getElementById('input-rua-modal');
  const inputNumeroModal = document.getElementById('input-numero-modal');
  const inputSetorModal = document.getElementById('input-setor-modal');
  const inputCepModal = document.getElementById('input-cep-modal');
  const inputComplementoModal = document.getElementById('input-complemento-modal');
  const inputCidadeModal = document.getElementById('input-cidade-modal');
  const inputEnderecoCompleto = document.getElementById('input-endereco-completo');

  function toggleCheckbox(clicked) {
    if (clicked === casa) {
      if (casa.checked) {
        local.checked = false;
        h2Casa.classList.add('h2-active');
        h2Local.classList.remove('h2-active');
        tipoPedidoInput.value = 'casa';
        btn.disabled = false;
      } else {
        reset();
      }
    } else if (clicked === local) {
      if (local.checked) {
        casa.checked = false;
        h2Local.classList.add('h2-active');
        h2Casa.classList.remove('h2-active');
        tipoPedidoInput.value = 'local';
        btn.disabled = false;
      } else {
        reset();
      }
    }
  }

  casa.addEventListener('change', () => toggleCheckbox(casa));
  local.addEventListener('change', () => toggleCheckbox(local));

  btn.addEventListener('click', () => {
    const tipo = tipoPedidoInput.value;

    if (!tipo) {
      alert('Selecione o tipo de pedido.');
      return;
    }

    if (tipo === 'casa') {
      abrirModalEndereco();
    } else {
      formFinalizar.submit();
    }
  });

  btnConfirmarEndereco.addEventListener('click', () => {
    const rua = inputRuaModal.value.trim();
    const numero = inputNumeroModal.value.trim();
    const setor = inputSetorModal.value.trim();
    const cep = inputCepModal.value.trim();

    if (!rua || !numero || !setor || !cep) {
      alert("Preencha todos os campos obrigatórios.");
      return;
    }

    // Passar os valores para os inputs ocultos do formulário
    inputRua.value = rua;
    inputNumero.value = numero;
    inputSetor.value = setor;
    inputCep.value = cep;
    inputComplemento.value = inputComplementoModal.value.trim();

    fecharModalEndereco();
    formFinalizar.submit();
  });

  btnCancelarEndereco.addEventListener('click', (e) => {
    e.preventDefault();
    fecharModalEndereco();
  });

  function abrirModalEndereco() {
    modalEndereco.style.display = 'flex';
    inputRuaModal.value = '';
    inputNumeroModal.value = '';
    inputSetorModal.value = '';
    inputCepModal.value = '';
    inputComplementoModal.value = '';
  }

  function fecharModalEndereco() {
    modalEndereco.style.display = 'none';
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
