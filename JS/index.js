function toggleMenu(event) {
    const menu = document.getElementById('user-menu');
    const rect = event.target.getBoundingClientRect();
    menu.style.top = `${rect.bottom + window.scrollY}px`;
    menu.style.left = `${rect.left + window.scrollX}px`;
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

function hideMenu() {
    document.getElementById('user-menu').style.display = 'none';
}

document.addEventListener('click', function (event) {
    const menu = document.getElementById('user-menu');
    const profile = document.querySelector('.user-profile');
    if (!menu.contains(event.target) && !profile.contains(event.target)) {
        hideMenu();
    }
});

function showLogoutModal() {
    document.getElementById('logout-modal').style.display = 'block';
    document.getElementById('overlay').style.display = 'block';
}

function hideLogoutModal() {
    document.getElementById('logout-modal').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}
