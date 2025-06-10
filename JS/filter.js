document.addEventListener("DOMContentLoaded", function () {
    const filterBtn = document.querySelector(".filter");
    const filterModal = document.getElementById("filter-modal");

    filterBtn.addEventListener("click", () => {
        filterModal.classList.toggle("hidden");
    });

    document.getElementById("filter-form").addEventListener("submit", function (e) {
        e.preventDefault();

        const saboresSelecionados = [...document.querySelectorAll('input[name="sabor"]:checked')]
            .map(cb => cb.value);
        const ordem = document.getElementById("ordem").value;

        filtrarSabores(saboresSelecionados, ordem);
    });

    function filtrarSabores(sabores, ordem) {
        const todas = ['trad', 'doce', 'esp'];

        const categorias = sabores.length > 0 ? sabores : todas;

        todas.forEach(tipo => {
            const secao = document.getElementById(tipo);
            if (categorias.includes(tipo)) {
                secao.style.display = 'block';
                ordenarSecao(secao, ordem);
            } else {
                secao.style.display = 'none';
            }
        });
    }

    function ordenarSecao(secao, ordem) {
        const container = secao.querySelector(".pizza-list");
        const cards = Array.from(container.querySelectorAll(".pizza-card"));

        cards.sort((a, b) => {
            const nomeA = a.querySelector("h2").textContent.trim();
            const nomeB = b.querySelector("h2").textContent.trim();

            return ordem === 'asc'
                ? nomeA.localeCompare(nomeB)
                : nomeB.localeCompare(nomeA);
        });

        cards.forEach(card => container.appendChild(card));
    }
});
