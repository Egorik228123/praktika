document.addEventListener('DOMContentLoaded', () => {
    // Инициализация модалок
    setupModal('profileModal', '#editProfileBtn', '.modal-close');
    setupModal('confirmModal', '#deleteAccountBtn', '.modal-close');

    // Обработчик для кнопки подтверждения удаления аккаунта
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', () => {
            const userId = confirmDeleteBtn.dataset.userId; // Получаем ID пользователя из data-атрибута
            if (userId) {
                deleteAccount(userId);
            } else {
                console.error("User ID not found for deletion.");
            }
        });
    }
});

function setupModal(modalId, openBtnSelector, closeBtnSelector) {
    const modal = document.getElementById(modalId);
    const openBtn = document.querySelector(openBtnSelector);
    const closeBtns = modal.querySelectorAll(closeBtnSelector);

    if (openBtn) {
        openBtn.addEventListener('click', () => {
            modal.style.display = 'flex';
        });
    }

    closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    });

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}

function deleteAccount(userId) {
    let Data = new FormData();
    Data.append('action', 'deleteUser');
    Data.append('id', userId);

    ajax('../src/classes/controllers/UsersController.php', Data, function(response) {
        if (response.success) {
            alert('Аккаунт успешно удален.');
            window.location.href = 'login.php';
        } else {
            alert('Ошибка при удалении аккаунта: ' + response.errors.join(', '));
        }
    });
}