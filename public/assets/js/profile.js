document.addEventListener('DOMContentLoaded', () => {
    // Инициализация модалок
    setupModal('profileModal', '#editProfileBtn', '.modal-close');
    setupModal('confirmModal', '#deleteAccountBtn', '.modal-close');

    // Удаление аккаунта
    document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
        alert('Аккаунт удален');
        document.getElementById('confirmModal').style.display = 'none';
    });
});