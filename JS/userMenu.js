function toggleMenu(event) {
    const menu = document.getElementById('user-menu');
    const profile = event.target.closest('.user-profile');
    if (!menu || !profile) return; // Ensure elements exist

    const rect = profile.getBoundingClientRect();
    menu.style.top = `${rect.bottom + window.scrollY}px`; // Position below the user-profile
    menu.style.left = `${rect.left + window.scrollX}px`; // Align with the user-profile
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block'; // Toggle visibility
    event.stopPropagation(); // Prevent click propagation
}

function hideMenu() {
    document.getElementById('user-menu').style.display = 'none';
}

function showLogoutModal() {
    // Adiciona o redirect dinamicamente ao formulário de logout
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

document.addEventListener('click', function (event) {
    const menu = document.getElementById('user-menu');
    const profile = document.querySelector('.user-profile');
    if (menu && profile && !menu.contains(event.target) && !profile.contains(event.target)) {
        menu.style.display = 'none'; // Hide menu if clicked outside
    }
});
