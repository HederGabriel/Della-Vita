// pedidos.js

function toggleCheckbox(clicked) {
  const casa = document.getElementById('casa');
  const local = document.getElementById('local');
  const h2Casa = document.querySelector('.h2-casa');
  const h2Local = document.querySelector('.h2-local');
  const btn = document.getElementById('btnFinalizar');
  const inputEntrega = document.getElementById('input-entrega');

  // Verificar se os elementos essenciais existem
  if (!casa || !local || !h2Casa || !h2Local || !btn || !inputEntrega) {
    console.error('Elementos essenciais não encontrados no DOM.');
    return;
  }

  // Função auxiliar para resetar estados
  function reset() {
    h2Casa.classList.remove('h2-active');
    h2Local.classList.remove('h2-active');
    btn.disabled = true;
    inputEntrega.value = '';
    casa.checked = false;
    local.checked = false;
  }

  if (clicked === casa && casa.checked) {
    local.checked = false;
    h2Casa.classList.add('h2-active');
    h2Local.classList.remove('h2-active');
    btn.disabled = false;
    inputEntrega.value = 'casa';
  } else if (clicked === local && local.checked) {
    casa.checked = false;
    h2Local.classList.add('h2-active');
    h2Casa.classList.remove('h2-active');
    btn.disabled = false;
    inputEntrega.value = 'local';
  } else {
    reset();
  }
}

// === Finalizar Pedido com fetch (sem redirecionamento) ===
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('form-finalizar');

  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault(); // Impede o envio padrão do formulário

      const tipoPedido = document.getElementById('input-entrega').value;

      if (!tipoPedido) {
        alert('Selecione uma opção de entrega ou retirada.');
        return;
      }

      const formData = new FormData();
      formData.append('tipo_pedido', tipoPedido);

      try {
        const response = await fetch('../System/finalizarPedido.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.success) {
          alert(result.message);
          console.log('ID do pedido:', result.id_pedido);

          // Aqui você pode redirecionar ou resetar o carrinho
          // window.location.href = "pagina-confirmacao.html";
        } else {
          alert('Erro ao finalizar o pedido.');
        }
      } catch (error) {
        console.error('Erro na requisição:', error);
        alert('Erro ao se conectar com o servidor.');
      }
    });
  }
});
