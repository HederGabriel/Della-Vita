function toggleMenu(event) {
    const menu = document.getElementById('user-menu');
    const profile = event.target.closest('.user-profile');
    if (!menu || !profile) return;

    const rect = profile.getBoundingClientRect();
    menu.style.top = `${rect.bottom + window.scrollY}px`;
    menu.style.left = `${rect.left + window.scrollX}px`;
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    event.stopPropagation();
}

function hideMenu() {
    const menu = document.getElementById('user-menu');
    if (menu) {
        menu.style.display = 'none';
    }
}

function showLogoutModal() {
    const logoutForm = document.getElementById('logout-form');

    let redirectInput = logoutForm.querySelector('input[name="redirect"]');
    if (!redirectInput) {
        redirectInput = document.createElement('input');
        redirectInput.type = 'hidden';
        redirectInput.name = 'redirect';
        logoutForm.appendChild(redirectInput);
    }

    redirectInput.value = window.location.pathname + window.location.search;

    document.getElementById('overlay').style.display = 'block';
    document.getElementById('logout-modal').style.display = 'block';
}

function hideLogoutModal() {
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('logout-modal').style.display = 'none';
}

function renderUserMenu() {
    const userProfile = document.getElementById('user-profile');
    const userEmail = userProfile?.dataset.email;
    const menu = document.getElementById('user-menu');

    if (!menu || !userEmail) return;

    const ul = menu.querySelector('ul');
    if (!ul) return;

    // Limpa o conteúdo atual do menu (dentro da ul)
    ul.innerHTML = '';

    if (userEmail === 'dellavitaenterprise@gmail.com') {
        // Apenas ADM e Sair
        ul.innerHTML = `
            <li><a href="../Pages/adm-cozinha.php">ADM</a></li>
            <li><a href="javascript:void(0);" onclick="showLogoutModal()">Sair</a></li>
        `;
    } else {
        // Outras opções normais (exemplo)
        ul.innerHTML = `
            <li><a href="Perfil.php" class="<?= $current_page === 'Perfil.php' ? 'active' : '' ?>">Perfil</a></li>
            <li><a href="Pedidos.php" class="<?= $current_page === 'Pedidos.php' ? 'active' : '' ?>">Pedidos</a></li>
            <li><a href="#" onclick="showLogoutModal()">Sair</a></li>
        `;
    }
}

// Executa ao carregar a página
document.addEventListener('DOMContentLoaded', renderUserMenu);

// Fecha o menu ao clicar fora dele
document.addEventListener('click', function (event) {
    const menu = document.getElementById('user-menu');
    const profile = document.querySelector('.user-profile');
    if (menu && profile && !menu.contains(event.target) && !profile.contains(event.target)) {
        menu.style.display = 'none';
    }
});
