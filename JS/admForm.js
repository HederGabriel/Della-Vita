document.addEventListener("DOMContentLoaded", () => {
  const descricaoR = document.getElementById("descricao-r");
  const contadorResumo = document.getElementById("contador-resumo");
  const descricaoCompleta = document.getElementById("descricao_completa");
  const contadorCompleta = document.getElementById("contador-completa");
  const addIngredienteBtn = document.getElementById("add-ingrediente");
  const ingredientesContainer = document.getElementById("ingredientes-container");
  const formulario = document.querySelector("form");
  const uploadDiv = document.getElementById("upload-imagem");
  const inputImagem = document.getElementById("inputImagem");
  const btnEscolher = document.getElementById("btnEscolherImagem");
  const botaoSubmit = document.getElementById("adicionar-produto");

  descricaoR.addEventListener("input", () => {
    contadorResumo.textContent = `${descricaoR.value.length}/30`;
  });

  descricaoCompleta.addEventListener("input", () => {
    contadorCompleta.textContent = `${descricaoCompleta.value.length}/185`;
  });

  addIngredienteBtn.addEventListener("click", () => {
    const input = document.createElement("input");
    input.type = "text";
    input.name = "ingredientes[]";
    input.placeholder = "Ingrediente";
    input.required = true;
    ingredientesContainer.appendChild(input);
  });

  const mostrarImagem = (fileOrDataURL) => {
    const reader = new FileReader();
    reader.onload = () => {
      uploadDiv.innerHTML = `
        <div class="imagem-preview" style="position: relative; cursor: pointer;">
          <img src="${reader.result}" style="width:100%; height:100%; object-fit:cover; border-radius:8px;">
          <div class="hover-overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;
               background:rgba(0,0,0,0.5);display:flex;justify-content:center;align-items:center;
               color:white;font-size:24px;opacity:0;transition:opacity 0.3s;border-radius:8px;">
            ✎
          </div>
        </div>
      `;
      const preview = uploadDiv.querySelector(".imagem-preview");
      const overlay = uploadDiv.querySelector(".hover-overlay");
      preview.addEventListener("mouseenter", () => overlay.style.opacity = "1");
      preview.addEventListener("mouseleave", () => overlay.style.opacity = "0");
      preview.addEventListener("click", () => inputImagem.click());
    };
    if (fileOrDataURL instanceof File) {
      reader.readAsDataURL(fileOrDataURL);
    } else {
      uploadDiv.innerHTML = `
        <div class="imagem-preview" style="position: relative; cursor: pointer;">
          <img src="${fileOrDataURL}" style="width:100%; height:100%; object-fit:cover; border-radius:8px;">
          <div class="hover-overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;
               background:rgba(0,0,0,0.5);display:flex;justify-content:center;align-items:center;
               color:white;font-size:24px;opacity:0;transition:opacity 0.3s;border-radius:8px;">
            ✎
          </div>
        </div>
      `;
      const preview = uploadDiv.querySelector(".imagem-preview");
      const overlay = uploadDiv.querySelector(".hover-overlay");
      preview.addEventListener("mouseenter", () => overlay.style.opacity = "1");
      preview.addEventListener("mouseleave", () => overlay.style.opacity = "0");
      preview.addEventListener("click", () => inputImagem.click());
    }
  };

  uploadDiv.addEventListener("dragover", e => {
    e.preventDefault();
    uploadDiv.style.border = "2px dashed #000";
  });
  uploadDiv.addEventListener("dragleave", () => {
    uploadDiv.style.border = "";
  });
  uploadDiv.addEventListener("drop", e => {
    e.preventDefault();
    uploadDiv.style.border = "";
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith("image/")) {
      inputImagem.files = e.dataTransfer.files;
      mostrarImagem(file);
    }
  });
  btnEscolher.addEventListener("click", () => inputImagem.click());
  inputImagem.addEventListener("change", () => {
    const file = inputImagem.files[0];
    if (file && file.type.startsWith("image/")) {
      mostrarImagem(file);
    }
  });
  formulario.addEventListener("submit", e => {
    if (!inputImagem.files.length) {
      alert("Por favor, selecione uma imagem antes de enviar.");
      inputImagem.focus();
      e.preventDefault();
    }
  });

  // --- EDITAR PRODUTO ---
  document.querySelectorAll(".edit-button").forEach(btn => {
    btn.addEventListener("click", async () => {
      const id = btn.dataset.id;
      try {
        const res = await fetch(`../System/getProduto.php?id=${id}`);
        const produto = await res.json();
        if (!produto || produto.error) {
          alert(produto?.error || "Erro ao carregar o produto.");
          return;
        }

        document.getElementById("id_produto").value = produto.id_produto || "";
        document.getElementById("nome").value = produto.nome || "";
        document.getElementById("preco").value = produto.preco || "";
        descricaoR.value = produto.descricao_resumida || "";
        contadorResumo.textContent = `${produto.descricao_resumida?.length || 0}/30`;

        document.getElementById("tipo").value = produto.tipo || "";
        document.getElementById("categoria").value = produto.sabor || "";

        // limpa ingredientes
        ingredientesContainer.innerHTML = "";

        // se tiver dadosPagina, carregar JSON e extrair info
        if (produto.dadosPagina) {
          try {
            const resJson = await fetch(produto.dadosPagina);
            const dados = await resJson.json();

            descricaoCompleta.value = dados.descricao_completa || "";
            contadorCompleta.textContent = `${descricaoCompleta.value.length}/185`;

            (dados.ingredientes || []).forEach(ing => {
              const input = document.createElement("input");
              input.type = "text";
              input.name = "ingredientes[]";
              input.placeholder = "Ingrediente";
              input.required = true;
              input.value = ing;
              ingredientesContainer.appendChild(input);
            });
          } catch (err) {
            console.warn("Erro ao carregar dadosPagina:", err);
          }
        } else {
          descricaoCompleta.value = produto.descricao_completa || "";
          contadorCompleta.textContent = `${descricaoCompleta.value.length}/185`;

          (produto.ingredientes || []).forEach(ing => {
            const input = document.createElement("input");
            input.type = "text";
            input.name = "ingredientes[]";
            input.placeholder = "Ingrediente";
            input.required = true;
            input.value = ing;
            ingredientesContainer.appendChild(input);
          });
        }

        botaoSubmit.textContent = "Salvar Alterações";
        formulario.action = "../System/editarProduto.php";

        if (produto.imagem) {
          mostrarImagem(produto.imagem);
          const blob = await (await fetch(produto.imagem)).blob();
          const filename = produto.imagem.split("/").pop();
          const file = new File([blob], filename, { type: blob.type });
          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(file);
          inputImagem.files = dataTransfer.files;
        }

        window.scrollTo({ top: 0, behavior: "smooth" });
      } catch (err) {
        alert("Erro na requisição: " + err.message);
      }
    });
  });
  
  // --- MODAL DE CONFIRMAÇÃO PERSONALIZADO PARA EXCLUSÃO ---
  const criarModalConfirmacao = () => {
    if (document.getElementById("custom-confirm-modal")) return;

    const modal = document.createElement("div");
    modal.id = "custom-confirm-modal";
    modal.className = "custom-confirm-modal";
    modal.innerHTML = `
      <div class="custom-confirm-conteudo">
        <p id="custom-confirm-message" class="custom-confirm-mensagem">Tem certeza que deseja Excluir esse Produto?</p>
        <div class="custom-confirm-botoes">
          <button id="custom-confirm-no">Não</button>
          <button id="custom-confirm-yes">Sim</button>
        </div>
      </div>
    `;
    document.body.appendChild(modal);
  };

  criarModalConfirmacao();

  const modal = document.getElementById("custom-confirm-modal");
  const btnSim = document.getElementById("custom-confirm-yes");
  const btnNao = document.getElementById("custom-confirm-no");
  let idParaExcluir = null;

  document.querySelectorAll(".delete-button").forEach(btn => {
    btn.addEventListener("click", () => {
      idParaExcluir = btn.dataset.id;
      modal.style.display = "flex";
    });
  });

  btnNao.addEventListener("click", () => {
    modal.style.display = "none";
    idParaExcluir = null;
  });

  btnSim.addEventListener("click", async () => {
    if (!idParaExcluir) return;
    try {
      const res = await fetch(`../System/excluirProduto.php?id=${idParaExcluir}`, { method: "GET" });
      const result = await res.text();
      if (res.ok) {
        location.reload();
      } else {
        alert("Erro ao excluir o produto: " + result);
      }
    } catch (err) {
      alert("Erro ao excluir: " + err.message);
    }
  });

});
