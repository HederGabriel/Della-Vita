document.addEventListener("DOMContentLoaded", function () {
    const filterBtn = document.querySelector(".filter");
    const filterModal = document.getElementById("filter-modal");
    const filterForm = document.getElementById("filter-form");
    const searchInput = document.getElementById("search-bar");
    const mensagemNenhumaPizza = document.getElementById("mensagem-nenhuma-pizza");
    const noRespost = document.getElementById("no-results");

    filterBtn?.addEventListener("click", () => {
        filterModal.classList.toggle("hidden");
    });

    document.addEventListener("click", (e) => {
        if (!filterModal.classList.contains("hidden")) {
            if (
                !filterModal.contains(e.target) &&
                !filterBtn.contains(e.target)
            ) {
                filterModal.classList.add("hidden");
            }
        }
    });

    filterForm?.addEventListener("submit", function (e) {
        e.preventDefault();
        aplicarFiltroEBusca();
    });

    function aplicarFiltroEBusca() {
        const saboresSelecionados = [...document.querySelectorAll('input[name="sabor"]:checked')]
            .map(cb => cb.value);
        const ordem = document.getElementById("ordem")?.value;
        const termoBusca = searchInput?.value.trim().toLowerCase() || '';

        const todasCategorias = ['trad', 'doce', 'esp'];
        const categorias = saboresSelecionados.length > 0 ? saboresSelecionados : todasCategorias;

        let algumCardVisivel = false;

        todasCategorias.forEach(tipo => {
            const secao = document.getElementById(tipo);
            if (!secao) return;

            if (categorias.includes(tipo)) {
                secao.style.display = 'block';

                const container = secao.querySelector(".pizza-list");
                const cards = Array.from(container.querySelectorAll(".pizza-card"));

                cards.sort((a, b) => {
                    const nomeA = a.querySelector("h2").textContent.trim().toLowerCase();
                    const nomeB = b.querySelector("h2").textContent.trim().toLowerCase();
                    return ordem === 'desc' ? nomeB.localeCompare(nomeA) : nomeA.localeCompare(nomeB);
                });

                cards.forEach(card => container.appendChild(card));

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

        // Exibe a mensagem no-respost se nenhum card estiver visível
        if (noRespost) {
            noRespost.style.display = algumCardVisivel ? "none" : "block";
        }

        if (mensagemNenhumaPizza) {
            mensagemNenhumaPizza.style.display = algumCardVisivel ? "none" : "block";
        }
    }

    if (searchInput) {
        const isCardapio = window.location.pathname.includes("Cardapio.php");

        if (isCardapio) {
            searchInput.addEventListener("input", () => {
                aplicarFiltroEBusca();

                if (searchInput.value.trim() === "") {
                    const url = new URL(window.location);
                    url.searchParams.delete("busca");
                    url.searchParams.delete("search");
                    window.history.replaceState({}, "", url);
                }
            });

            const urlParams = new URLSearchParams(window.location.search);
            const buscaInicial = urlParams.get("busca") || urlParams.get("search");
            if (buscaInicial) {
                searchInput.value = buscaInicial;
                aplicarFiltroEBusca();
            }
        } else {
            const urlParams = new URLSearchParams(window.location.search);
            const buscaAtual = urlParams.get("busca") || urlParams.get("search");
            if (buscaAtual && searchInput) {
                searchInput.value = buscaAtual;
            }

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

    if (window.location.pathname.includes("Cardapio.php")) {
        aplicarFiltroEBusca();
    }
});
