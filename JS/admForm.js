document.addEventListener("DOMContentLoaded", () => {
    const descricaoR = document.getElementById("descricao-r");
    const contadorResumo = document.getElementById("contador-resumo");
    const descricaoCompleta = document.getElementById("descricao_completa");
    const contadorCompleta = document.getElementById("contador-completa");
    const addIngredienteBtn = document.getElementById("add-ingrediente");
    const ingredientesContainer = document.getElementById("ingredientes-container");
    const formulario = document.querySelector("form");


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

    // Upload de imagem
    const uploadDiv = document.getElementById("upload-imagem");
    const inputImagem = document.getElementById("inputImagem");
    const btnEscolher = document.getElementById("btnEscolherImagem");

    const mostrarImagem = (file) => {
        const reader = new FileReader();
        reader.onload = () => {
            uploadDiv.innerHTML = `
                <div class="imagem-preview" style="position: relative; cursor: pointer;">
                    <img src="${reader.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                    <div class="hover-overlay" style="
                        position: absolute; top: 0; left: 0; 
                        width: 100%; height: 100%;
                        background: rgba(0,0,0,0.5);
                        display: flex; justify-content: center; align-items: center;
                        color: white; font-size: 24px; opacity: 0;
                        transition: opacity 0.3s;
                        border-radius: 8px;
                    ">
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
        reader.readAsDataURL(file);
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

    formulario.addEventListener("submit", (e) => {
        if (!inputImagem.files.length) {
            alert("Por favor, selecione uma imagem antes de enviar.");
            inputImagem.focus();
            e.preventDefault();
        }
    });
});
