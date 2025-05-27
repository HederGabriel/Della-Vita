document.addEventListener('DOMContentLoaded', () => {
  const casa = document.getElementById('casa');
  const local = document.getElementById('local');
  const h2Casa = document.querySelector('.h2-casa');
  const h2Local = document.querySelector('.h2-local');
  const btn = document.getElementById('btnFinalizar');
  const inputEntrega = document.getElementById('input-entrega');
  const tipoPedidoInput = document.getElementById('input-tipo-pedido'); // NOVO

  const modalEndereco = document.getElementById('modal-endereco');
  const inputModalEndereco = document.getElementById('input-modal-endereco');
  const btnConfirmarEndereco = document.getElementById('confirmar-endereco');

  const formFinalizar = document.getElementById('form-finalizar');
  const hiddenEndereco = document.getElementById('hidden-endereco');

  function reset() {
    h2Casa.classList.remove('h2-active');
    h2Local.classList.remove('h2-active');
    btn.disabled = true;
    inputEntrega.value = '';
    tipoPedidoInput.value = ''; // zera o tipo_pedido
    casa.checked = false;
    local.checked = false;
    hiddenEndereco.value = '';
  }

  function toggleCheckbox(clicked) {
    if (clicked === casa) {
      if (casa.checked) {
        local.checked = false;
        h2Casa.classList.add('h2-active');
        h2Local.classList.remove('h2-active');
        btn.disabled = false;
        inputEntrega.value = 'casa';
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
        console.log('Botão habilitado');
        inputEntrega.value = 'local';
        tipoPedidoInput.value = 'local';
      } else {
        reset();
      }
    }
  }

  function initAutocompleteModal() {
    if (inputModalEndereco && window.google) {
      new google.maps.places.Autocomplete(inputModalEndereco, {
        types: ['geocode'],
        componentRestrictions: { country: 'br' }
      });
    }
  }

  function abrirModalEndereco() {
    modalEndereco.style.display = 'flex';
    inputModalEndereco.value = hiddenEndereco.value || '';
    inputModalEndereco.focus();
    initAutocompleteModal();
  }

  function fecharModalEndereco() {
    modalEndereco.style.display = 'none';
  }

  // Desabilita botão inicialmente
  btn.disabled = true;

  // Eventos de seleção de tipo de entrega
  casa.addEventListener('change', () => toggleCheckbox(casa));
  local.addEventListener('change', () => toggleCheckbox(local));

  // Clique no botão de finalizar
  btn.addEventListener('click', (e) => {
    e.preventDefault();
    const tipoPedido = inputEntrega.value;

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

  // Confirmação do endereço
  btnConfirmarEndereco.addEventListener('click', (e) => {
    e.preventDefault();

    const endereco = inputModalEndereco.value.trim();
    if (!endereco) {
      alert('Por favor, informe um endereço válido.');
      inputModalEndereco.focus();
      return;
    }

    hiddenEndereco.value = endereco;

    // Preenchimento dos campos ocultos para envio (pode ser adaptado com parsing real)
    document.getElementById('input-rua').value = endereco;
    document.getElementById('input-numero').value = '123';
    document.getElementById('input-bairro').value = 'Centro';
    document.getElementById('input-setor').value = '';
    document.getElementById('input-cep').value = '74800-000';
    document.getElementById('input-complemento').value = '';

    fecharModalEndereco();
    formFinalizar.submit();
  });
});
