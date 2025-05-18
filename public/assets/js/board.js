document.addEventListener('DOMContentLoaded', () => {
    // Инициализация модалок
    setupModal('taskModal', '#addTaskBtn', '.close');
    setupModal('columnModal', '#addColumnBtn', '.close');

    // Добавление подзадачи
    document.getElementById('addSubtask').addEventListener('click', () => {
        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'subtask';
        input.placeholder = 'Название подзадачи';
        document.getElementById('subtasks').appendChild(input);
    });

    // Создание столбца
    document.getElementById('columnForm').addEventListener('submit', (e) => {
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

    // Drag and drop
    const initDragAndDrop = () => {
        document.querySelectorAll('.task').forEach(task => {
            task.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', e.target.id);
            });
        });

        document.querySelectorAll('.column').forEach(column => {
            column.addEventListener('dragover', (e) => e.preventDefault());
            column.addEventListener('drop', (e) => {
                e.preventDefault();
                const taskId = e.dataTransfer.getData('text/plain');
                e.currentTarget.appendChild(document.getElementById(taskId));
            });
        });
    };
    initDragAndDrop();
});