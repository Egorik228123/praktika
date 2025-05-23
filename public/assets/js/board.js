document.addEventListener('DOMContentLoaded', () => {
    // Инициализация модалок
    setupModal('taskModal', '#addTaskBtn', '.close');
    setupModal('columnModal', '#addColumnBtn', '.close');
    setupModal('taskDetailsModal', '.task-btn', '.close');
    setupModal('createTaskModal', '.add-column', '.close');

    // Добавление подзадачи
    document.getElementById('addSubtask').addEventListener('click', () => {
        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'subtask';
        input.placeholder = 'Название подзадачи';
        document.getElementById('subtasks').appendChild(input);
    });

    // Создание столбца
    document.getElementById('add-column').addEventListener('submit', (e) => {
        e.preventDefault();
        const columnName = document.getElementById('columnName').value;
        const columnHTML = `
            <div class="column" id="${columnName.toLowerCase().replace(/\s+/g, '-')}">
                <h2>${columnName}</h2>
            </div>
        `;
        document.querySelector('.board').insertAdjacentHTML('beforeend', columnHTML);
        document.getElementById('columnModal').style.display = 'none';
    });

    // Создание задачи
    document.getElementById('createTaskForm').addEventListener('submit', (e) => {
        e.preventDefault();
        const taskName = document.getElementById('createTaskName').value;
        const taskResponsible = document.getElementById('createTaskResponsible').value;
        const taskDescription = document.getElementById('createTaskDescription').value;
        const taskDeadline = document.getElementById('createTaskDeadline').value;
        
        const taskHTML = `
            <div class="task" draggable="true">
                <h3>${taskName}</h3>
                <p>Ответственный: ${taskResponsible}</p>
                <button class="task-btn">Описание задачи</button>
            </div>
        `;
        
        // Добавляем задачу в первый столбец (или другой по вашему выбору)
        document.querySelector('.column').insertAdjacentHTML('beforeend', taskHTML);
        document.getElementById('createTaskModal').style.display = 'none';
    });
});