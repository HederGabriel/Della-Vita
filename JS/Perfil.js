// Alterna entre o espaço cinza e as opções de avatar
function toggleAvatarOptions() {
    const avatarOptions = document.getElementById('avatar-options');
    const profileOptions = document.querySelector('.profile-options');

    if (avatarOptions.style.display === 'flex') {
        avatarOptions.style.display = 'none'; // Oculta as opções
        profileOptions.style.marginTop = '0'; // Reseta a margem superior
    } else {
        avatarOptions.style.display = 'flex'; // Exibe as opções
        const avatarOptionsHeight = avatarOptions.offsetHeight; // Calcula a altura do avatar-options
        profileOptions.style.marginTop = `${avatarOptionsHeight + 20}px`; // Adiciona margem superior com espaçamento
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
    const profileOptions = document.querySelector('.profile-options');

    // Verifica se o clique foi fora do avatar-options e do botão de edição
    if (
        avatarOptions.style.display === 'flex' &&
        avatarOptions &&
        !avatarOptions.contains(event.target) &&
        pencilButton &&
        event.target !== pencilButton
    ) {
        avatarOptions.style.display = 'none'; // Oculta as opções
        profileOptions.style.marginTop = '0'; // Reseta a margem superior do profile-options
    }
});

// Seleciona um avatar
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

// Exibe o modal de exclusão de conta
function showDeleteModal() {
    const modal = document.getElementById('delete-account-modal');
    const overlay = document.getElementById('overlay-delete');
    modal.style.display = 'block'; // Exibe o modal
    overlay.style.display = 'block'; // Exibe o overlay
}

// Oculta o modal de exclusão de conta
function hideDeleteModal() {
    const modal = document.getElementById('delete-account-modal');
    const overlay = document.getElementById('overlay-delete');
    modal.style.display = 'none'; // Oculta o modal
    overlay.style.display = 'none'; // Oculta o overlay
}

// Exibe o modal de senha
function showPasswordModal() {
    const passwordModal = document.getElementById('password-modal');
    const deleteAccountModal = document.getElementById('delete-account-modal');
    const overlay = document.getElementById('overlay-delete');

    // Exibe o modal de senha e o overlay
    passwordModal.style.display = 'block';
    overlay.style.display = 'block';

    // Oculta o modal de exclusão de conta
    deleteAccountModal.style.display = 'none';
}

// Oculta o modal de senha
function hidePasswordModal() {
    const passwordModal = document.getElementById('password-modal');
    const overlay = document.getElementById('overlay-delete');

    // Oculta o modal de senha e o overlay
    passwordModal.style.display = 'none';
    overlay.style.display = 'none';

    // Remove mensagens de erro, se existirem
    const errorMessage = document.querySelector('#password-modal .error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
}

// Adiciona eventos ao carregar o DOM
document.addEventListener('DOMContentLoaded', function () {
    // Botão de exclusão de conta
    const deleteAccountBtn = document.querySelector('.delete-account-btn');
    if (deleteAccountBtn) {
        deleteAccountBtn.addEventListener('click', showDeleteModal);
    }

    // Botão "Cancelar" no modal de exclusão
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', hideDeleteModal);
    }

    // Botão "Confirmar" no modal de exclusão
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function () {
            showPasswordModal(); // Exibe o modal de senha
        });
    }

    // Formulário de exclusão de conta
    const deleteAccountForm = document.getElementById('delete-account-form');
    if (deleteAccountForm) {
        deleteAccountForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Impede o envio padrão do formulário

            const passwordInput = document.getElementById('password-input');
            const password = passwordInput.value;

            // Simulação de validação da senha
            fetch('Perfil.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `password=${encodeURIComponent(password)}`
            })
                .then(response => response.text())
                .then(data => {
                    // Remove mensagens de erro anteriores
                    const existingError = document.querySelector('#password-modal .error-message');
                    if (existingError) {
                        existingError.remove();
                    }

                    if (data.includes('Senha incorreta')) {
                        // Exibe mensagem de erro no modal
                        const errorMessage = document.createElement('p');
                        errorMessage.textContent = 'Senha incorreta. Tente novamente.';
                        errorMessage.className = 'error-message';
                        errorMessage.style.color = 'red';
                        errorMessage.style.marginTop = '10px';
                        const passwordModal = document.getElementById('password-modal');
                        passwordModal.appendChild(errorMessage);
                    } else {
                        // Redireciona ou atualiza a página após a exclusão
                        window.location.href = 'index.php';
                    }
                })
                .catch(error => console.error('Erro:', error));
        });
    }
});