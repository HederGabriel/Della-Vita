document.addEventListener("DOMContentLoaded", function () {
    const filterBtn = document.querySelector(".filter");
    const filterModal = document.getElementById("filter-modal");
    const filterForm = document.getElementById("filter-form");
    const searchInput = document.getElementById("search-bar");

    // Elemento para mostrar a mensagem de "Nenhuma pizza encontrada"
    // Você deve ter esse elemento no seu HTML, por exemplo:
    // <p id="mensagem-nenhuma-pizza" style="display:none; color:red; font-weight:bold; margin-top:20px;">
    //   Nenhuma pizza encontrada.
    // </p>
    const mensagemNenhumaPizza = document.getElementById("no-results");

    // Mostrar/ocultar modal de filtro
    filterBtn?.addEventListener("click", () => {
        filterModal.classList.toggle("hidden");
    });

    // Submissão do filtro
    filterForm?.addEventListener("submit", function (e) {
        e.preventDefault();
        aplicarFiltroEBusca();
    });

    // Aplica filtro e busca combinados
    function aplicarFiltroEBusca() {
        const saboresSelecionados = [...document.querySelectorAll('input[name="sabor"]:checked')]
            .map(cb => cb.value);
        const ordem = document.getElementById("ordem")?.value;
        const termoBusca = searchInput?.value.trim().toLowerCase() || '';

        const todasCategorias = ['trad', 'doce', 'esp'];
        const categorias = saboresSelecionados.length > 0 ? saboresSelecionados : todasCategorias;

        let algumCardVisivel = false; // Para controlar se existe algum card visível

        todasCategorias.forEach(tipo => {
            const secao = document.getElementById(tipo);
            if (!secao) return;

            if (categorias.includes(tipo)) {
                secao.style.display = 'block';

                const container = secao.querySelector(".pizza-list");
                const cards = Array.from(container.querySelectorAll(".pizza-card"));

                // Ordenar os cards
                cards.sort((a, b) => {
                    const nomeA = a.querySelector("h2").textContent.trim().toLowerCase();
                    const nomeB = b.querySelector("h2").textContent.trim().toLowerCase();
                    return ordem === 'desc' ? nomeB.localeCompare(nomeA) : nomeA.localeCompare(nomeB);
                });

                cards.forEach(card => container.appendChild(card));

                // Aplicar busca após o filtro
                let algumVisivelNaSecao = false;
                cards.forEach(card => {
                    const titulo = card.querySelector("h2").textContent.toLowerCase();
                    const corresponde = titulo.includes(termoBusca);
                    card.style.display = corresponde ? "block" : "none";
                    if (corresponde) algumVisivelNaSecao = true;
                });

                secao.style.display = algumVisivelNaSecao ? 'block' : 'none';

                if (algumVisivelNaSecao) algumCardVisivel = true;

            } else {
                secao.style.display = 'none';
            }
        });

        // Mostrar ou esconder mensagem de "Nenhuma pizza encontrada"
        if (mensagemNenhumaPizza) {
            mensagemNenhumaPizza.style.display = algumCardVisivel ? "none" : "block";
        }
    }

    // Comportamento da barra de busca
    if (searchInput) {
        const isCardapio = window.location.pathname.includes("Cardapio.php");

        if (isCardapio) {
            // Input dinâmico
            searchInput.addEventListener("input", () => {
                aplicarFiltroEBusca();
            });

            // Aplicar busca automaticamente se veio de URL
            const urlParams = new URLSearchParams(window.location.search);
            const buscaInicial = urlParams.get("busca");
            if (buscaInicial) {
                searchInput.value = buscaInicial;
                aplicarFiltroEBusca();
            }
        } else {
            // Redireciona com ENTER
            searchInput.addEventListener("keypress", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    const termo = searchInput.value.trim();
                    if (termo !== "") {
                        window.location.href = `Cardapio.php?busca=${encodeURIComponent(termo)}`;
                    }
                }
            });
        }
    }

    // Inicializa com tudo visível
    if (window.location.pathname.includes("Cardapio.php")) {
        aplicarFiltroEBusca();
    }
});
