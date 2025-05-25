document.addEventListener('DOMContentLoaded', () => {
    // Инициализация модалок
    setupModal('taskModal', '#addTaskBtn', '.close');
    setupModal('columnModal', '.add-column', '.close');
    setupModal('taskDetailsModal', '.task-btn', '.close');
    setupModal('createTaskModal', '.add-task-btn', '.close');

    // // Добавление подзадачи
    // document.getElementById('addSubtask')?.addEventListener('click', () => {
    //     const input = document.createElement('input');
    //     input.type = 'text';
    //     input.className = 'subtask';
    //     input.placeholder = 'Название подзадачи';
    //     document.getElementById('subtasks').appendChild(input);
    // });

    // // Создание столбца
    // document.getElementById('columnForm')?.addEventListener('submit', (e) => {
    //     e.preventDefault();
    //     const columnName = document.getElementById('columnName').value;
    //     const columnHTML = `
    //         <div class="column" data-status="${columnName}">
    //             <h2>${columnName}</h2>
    //             <div class="tasks-container"></div>
    //         </div>
    //     `;
    //     document.querySelector('.columns-container').insertAdjacentHTML('beforeend', columnHTML);
    //     document.getElementById('columnModal').style.display = 'none';
    //     document.getElementById('columnName').value = '';
    //     setupDragAndDrop();
    // });

    // // Создание задачи
    // document.getElementById('createTaskForm')?.addEventListener('submit', (e) => {
    //     e.preventDefault();
    //     const taskName = document.getElementById('createTaskName').value;
    //     const taskResponsible = document.getElementById('createTaskResponsible').value;
    //     const taskDescription = document.getElementById('createTaskDescription').value;
    //     const taskDeadline = document.getElementById('createTaskDeadline').value;
        
    //     const taskId = Date.now(); // Генерируем уникальный ID для задачи
    //     const taskHTML = `
    //         <div class="task" draggable="true" data-task-id="${taskId}">
    //             <h3>${taskName}</h3>
    //             <p>Ответственный: ${taskResponsible}</p>
    //             <button class="task-btn">Подробнее</button>
    //             <div class="task-status">
    //                 <select class="status-select">
    //                     <option value="Новые">Новые</option>
    //                     <option value="В процессе">В процессе</option>
    //                     <option value="Можно проверять">Можно проверять</option>
    //                     <option value="Готово">Готово</option>
    //                 </select>
    //             </div>
    //         </div>
    //     `;
        
    //     // Добавляем задачу в первый столбец
    //     const firstColumn = document.querySelector('.column .tasks-container');
    //     if (firstColumn) {
    //         firstColumn.insertAdjacentHTML('beforeend', taskHTML);
    //     } else {
    //         // Если нет столбцов, создаем новый
    //         const columnHTML = `
    //             <div class="column" data-status="Новые">
    //                 <h2>Новые</h2>
    //                 <div class="tasks-container">${taskHTML}</div>
    //             </div>
    //         `;
    //         document.querySelector('.columns-container').insertAdjacentHTML('afterbegin', columnHTML);
    //     }
        
    //     // Создаем детали задачи
    //     const taskDetailsHTML = `
    //         <div class="task-details-container" data-task-id="${taskId}">
    //             <h3>${taskName}</h3>
    //             <div class="task-info">
    //                 <p><strong>Ответственный:</strong> ${taskResponsible}</p>
    //                 <p><strong>Описание:</strong> ${taskDescription}</p>
    //                 <p><strong>Дедлайн:</strong> ${taskDeadline}</p>
    //             </div>
    //         </div>
    //     `;
    //     document.querySelector('.tasks-preview').insertAdjacentHTML('afterbegin', taskDetailsHTML);
        
    //     document.getElementById('createTaskModal').style.display = 'none';
    //     resetTaskForm();
    //     setupDragAndDrop();
    // });

    // // Показ деталей задачи
    // document.addEventListener('click', (e) => {
    //     if (e.target.classList.contains('task-btn')) {
    //         const task = e.target.closest('.task');
    //         const taskId = task.dataset.taskId;
            
    //         // Скрываем все детали задач
    //         document.querySelectorAll('.task-details-container').forEach(detail => {
    //             detail.style.display = 'none';
    //         });
            
    //         // Показываем только выбранную задачу
    //         const taskDetail = document.querySelector(`.task-details-container[data-task-id="${taskId}"]`);
    //         if (taskDetail) {
    //             taskDetail.style.display = 'block';
    //         }
    //     }
    // });

    // // Обработчик изменения статуса задачи
    // // document.addEventListener('change', (e) => {
    // //     if (e.target.classList.contains('status-select')) {
    // //         const task = e.target.closest('.task');
    // //         const newStatus = e.target.value;
            
    // //         // Находим столбец с соответствующим статусом
    // //         const targetColumn = document.querySelector(`.column[data-status="${newStatus}"] .tasks-container`);
    // //         if (targetColumn) {
    // //             // Перемещаем задачу в нужный столбец
    // //             targetColumn.appendChild(task);
    // //         }
    // //     }
    // // });

    
    // function resetTaskForm() {
    //     document.getElementById('createTaskName').value = '';
    //     document.getElementById('createTaskDescription').value = '';
    //     document.getElementById('createTaskResponsible').selectedIndex = 0;
    //     document.getElementById('createTaskDeadline').value = '';
    // }
});