// Alterna entre o espaço cinza e as opções de avatar
function toggleAvatarOptions() {
    const avatarOptions = document.getElementById('avatar-options');
    const placeholderBox = document.querySelector('.placeholder-box');

    if (avatarOptions.style.display === 'flex') {
        avatarOptions.style.display = 'none'; // Oculta as opções
        placeholderBox.style.display = 'block'; // Exibe o espaço cinza
    } else {
        avatarOptions.style.display = 'flex'; // Exibe as opções
        placeholderBox.style.display = 'none'; // Oculta o espaço cinza
    }
}

// Oculta as opções de avatar
function hideAvatarOptions() {
    const avatarOptions = document.getElementById('avatar-options');
    avatarOptions.style.display = 'none'; // Oculta as opções
}

// Fecha as opções ao clicar fora delas
document.addEventListener('click', function (event) {
    const avatarOptions = document.getElementById('avatar-options');
    const pencilButton = document.querySelector('.edit-avatar-btn');

    // Verifica se o clique foi fora do avatar-options e do botão de edição
    if (
        avatarOptions.style.display === 'flex' &&
        avatarOptions &&
        !avatarOptions.contains(event.target) &&
        pencilButton &&
        event.target !== pencilButton
    ) {
        hideAvatarOptions(); // Oculta as opções
    }
});

function selectAvatar(avatarPath) {
    // Remove a classe 'selected' de todas as opções
    document.querySelectorAll('.avatar-option').forEach(option => {
        option.classList.remove('selected');
    });

    // Adiciona a classe 'selected' à imagem clicada
    const selectedOption = document.querySelector(`img[src="${avatarPath}"]`);
    selectedOption.classList.add('selected');

    // Define o valor do avatar selecionado no campo oculto
    document.getElementById('selected-avatar').value = avatarPath;
}
