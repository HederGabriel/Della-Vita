const searchInput = document.getElementById("search-bar");
const currentPage = window.location.pathname.includes("Cardapio.php");

if (currentPage) {
    // Busca dinâmica na Cardapio.php
    searchInput.addEventListener("input", function () {
        const termo = searchInput.value.toLowerCase();
        const todasPizzas = document.querySelectorAll(".pizza-card");
        let algumaVisivel = false;

        todasPizzas.forEach(card => {
            const nome = card.querySelector("h2").textContent.toLowerCase();
            if (nome.includes(termo)) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });

        ['trad', 'doce', 'esp'].forEach(id => {
            const secao = document.getElementById(id);
            const visiveis = secao.querySelectorAll(".pizza-card:not([style*='display: none'])");
            secao.style.display = visiveis.length > 0 ? "block" : "none";
            if (visiveis.length > 0) algumaVisivel = true;
        });

        const msg = document.getElementById("no-results");
        msg.style.display = algumaVisivel ? "none" : "block";
    });
} else {
    // Redireciona ao pressionar Enter se não estiver em Cardapio.php
    searchInput.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            const termo = searchInput.value.trim();
            if (termo !== "") {
                window.location.href = `Cardapio.php?search=${encodeURIComponent(termo)}`;
            }
        }
    });
}
