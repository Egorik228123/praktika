document.addEventListener('DOMContentLoaded', () => {
    // Инициализация модалок
    setupModal('projectTasksModal', '.project-task-btn', '.close');
    setupModal('createProjectModal', '#createProjectBtn', '.close');

    // Обработка создания проекта
    document.getElementById('createProjectForm').addEventListener('submit', (e) => {
        e.preventDefault();
        
        const projectName = document.getElementById('projectName').value;
        const projectDescription = document.getElementById('projectDescription').value;
        const projectStatus = document.getElementById('projectStatus').value;
        
        // Закрываем модальное окно
        document.getElementById('createProjectModal').style.display = 'none';
        
        // Очищаем форму
        e.target.reset();
    });
});