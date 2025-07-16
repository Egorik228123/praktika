document.addEventListener('DOMContentLoaded', () => {
    // Инициализация модалок
    setupModal('profileModal', '#editProfileBtn', '.modal-close');
    setupModal('confirmModal', '#deleteAccountBtn', '.modal-close');
});