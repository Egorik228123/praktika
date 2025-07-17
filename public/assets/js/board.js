const TaskManager = (() => {
    let currentTaskId = null;
    let currentProjectId = null;
    let allUsers = [];
    let columns = [];
    let firstColumnId = 0;
    
    // Инициализация
    function init(projectId) {
        currentProjectId = projectId;
        initEventListeners();
        loadUsers();
        getProject();
        getColumns();
    }
    
    // Загрузка пользователей
    async function loadUsers() {
        const formData = new FormData();
        formData.append('action', 'getAllUsers');
        
        try {
            const response = await ajaxRequest('../src/classes/controllers/UsersController.php', formData);
            if (response.success) {
                allUsers = response.data;
                renderUserSelects();
            }
        } catch (error) {
            console.error('Ошибка загрузки пользователей:', error);
        }
    }
    
    // Рендер пользователей в выпадающих списках
    function renderUserSelects() {
        const selects = [
            document.getElementById('assigneeSelect'),
            document.getElementById('editAssigneeSelect'),
            document.getElementById('memberSelect')
        ];
        
        selects.forEach(select => {
            if (!select) return;
            
            select.innerHTML = '';
            // Добавляем пустую опцию по умолчанию
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Выберите пользователя';
            select.appendChild(defaultOption);

            allUsers.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.surname} ${user.name}`;
                select.appendChild(option);
            });
        });
    }
    
    // Загрузка данных проекта
    async function getProject() {
        const formData = new FormData();
        formData.append('action', 'getProjectById');
        formData.append('id', currentProjectId);
        
        try {
            const response = await ajaxRequest('../src/classes/controllers/ProjectsController.php', formData);
            if (response.success) {
                document.getElementById('projectName').textContent = response.data.name;
            }
        } catch (error) {
            console.error('Ошибка загрузки проекта:', error);
        }
    }
    
    // Загрузка столбцов
    async function getColumns() {
        const formData = new FormData();
        formData.append('action', 'getColumnsByProject');
        formData.append('project_id', currentProjectId);
        
        try {
            const response = await ajaxRequest('../src/classes/controllers/ColumnsController.php', formData);
            if (response.success) {
                columns = response.data;
                renderColumns(columns);
                if (columns.length > 0) {
                    firstColumnId = columns[0].id;
                }
            }
        } catch (error) {
            console.error('Ошибка загрузки столбцов:', error);
        }
    }
    
    // Рендер столбцов
    function renderColumns(columns) {
        const container = document.querySelector(".board");
        container.innerHTML = '';
        
        if (columns.length === 0) {
            container.innerHTML = `
                <div id="add-column" class="add-column">
                    <span>+</span>
                </div>
            `;
            return;
        }
        
        columns.forEach(column => {
            const card = document.createElement('div');
            card.className = 'column';
            card.dataset.columnId = column.id;
            card.innerHTML = `
                <div class="column-header">
                    <h2>${column.name}</h2>
                    <button class="delete-column-btn" data-column-id="${column.id}">×</button>
                </div>
                <div class="tasks-container" ondragover="TaskManager.handleDragOver(event)" ondrop="TaskManager.handleDrop(event, ${column.id})"></div>
            `;
            container.appendChild(card);
            getTasks(column.id);
        });
        
        const createColumn = document.createElement('div');
        createColumn.id = 'add-column';
        createColumn.className = 'add-column';
        createColumn.innerHTML = `<span>+</span>`;
        container.appendChild(createColumn);
    }
    
    // Загрузка задач для столбца
    async function getTasks(columnId) {
        const formData = new FormData();
        formData.append('action', 'getTasksByColumn');
        formData.append('column_id', columnId);
        
        try {
            const response = await ajaxRequest('../src/classes/controllers/TasksController.php', formData);
            if (response.success) {
                renderTasks(response.data, columnId);
            }
        } catch (error) {
            console.error('Ошибка загрузки задач:', error);
        }
    }
    
    // Рендер задач
    function renderTasks(tasks, columnId) {
        const container = document.querySelector(`.column[data-column-id="${columnId}"] .tasks-container`);
        if (!container) return;
        
        container.innerHTML = '';
        
        tasks.forEach(task => {
            const card = document.createElement('div');
            card.className = 'task';
            card.dataset.taskId = task.id;
            card.draggable = true; // Сделать задачу перетаскиваемой
            card.ondragstart = (event) => TaskManager.handleDragStart(event, task.id); // Обработчик начала перетаскивания
            card.innerHTML = `
                <h3>${task.name}</h3>
                <p>Ответственный: ${task.assignee || 'Не назначен'}</p>
                <button class="task-btn">Просмотреть задачу</button>
            `;
            container.appendChild(card);
        });
    }
    
    // Создание столбца
    async function createColumn() {
        const columnName = document.getElementById("columnName").value.trim();
        if (!columnName) return;
        
        const position = columns.length;
        const formData = new FormData();
        formData.append('action', 'createColumn');
        formData.append('name', columnName);
        formData.append('project_id', currentProjectId);
        formData.append('position', position);
        
        try {
            const response = await ajaxRequest('../src/classes/controllers/ColumnsController.php', formData);
            if (response.success) {
                document.getElementById("columnName").value = '';
                hideModal('columnModal');
                getColumns();
            }
        } catch (error) {
            console.error('Ошибка создания столбца:', error);
        }
    }
    
    // Удаление столбца
    async function deleteColumn(columnId) {
        if (!confirm('Вы уверены, что хотите удалить этот столбец? Все задачи будут перемещены в первый столбец.')) {
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'deleteColumn');
        formData.append('column_id', columnId);
        formData.append('project_id', currentProjectId);

        try {
            const response = await ajaxRequest('../src/classes/controllers/ColumnsController.php', formData);
            if (response.success) {
                getColumns();
            } else {
                console.error('Ошибка удаления столбца:', response.errors);
            }
        } catch (error) {
            console.error('Ошибка удаления:', error);
        }
    }
    
    // Создание задачи
    async function createTask() {
        const name = document.getElementById('taskName').value;
        const description = document.getElementById('taskDescription').value;
        const deadline = document.getElementById('taskDeadline').value;
        
        if (!name) {
            alert('Название задачи обязательно');
            return;
        }
        
        const assignees = Array.from(document.querySelectorAll('#assigneesList .assignee-item'))
            .map(item => item.dataset.userId);
        
        const formData = new FormData();
        formData.append('action', 'createTask');
        formData.append('name', name);
        formData.append('description', description);
        formData.append('due_date', deadline);
        formData.append('column_id', firstColumnId);
        formData.append('assignees', JSON.stringify(assignees));
        
        try {
            const response = await ajaxRequest('../src/classes/controllers/TasksController.php', formData);
            if (response.success) {
                hideModal('taskModal');
                getTasks(firstColumnId);
                resetTaskForm();
            } else {
                console.error('Ошибка создания задачи:', response.errors);
            }
        } catch (error) {
            console.error('Ошибка создания задачи:', error);
        }
    }
    
    // Обновление задачи
    async function updateTask() {
        const name = document.getElementById('editTaskName').value;
        const description = document.getElementById('editTaskDescription').value;
        const deadline = document.getElementById('editTaskDeadline').value;
        
        if (!name) {
            alert('Название задачи обязательно');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'updateTask');
        formData.append('task_id', currentTaskId);
        formData.append('name', name);
        formData.append('description', description);
        formData.append('due_date', deadline);
        
        // Ответственные
        const assignees = Array.from(
            document.querySelectorAll('#editAssigneesList .assignee-item')
        ).map(el => el.dataset.userId);
        formData.append('assignees', JSON.stringify(assignees));
        
        // Подзадачи
        const subtasks = Array.from(
            document.querySelectorAll('#subtasksList .subtask-item')
        ).map(el => ({
            id: el.dataset.subtaskId && !isNaN(parseInt(el.dataset.subtaskId)) ? parseInt(el.dataset.subtaskId) : 0, // Set to 0 if new, parse if existing
            name: el.querySelector('.subtask-name').textContent,
            description: el.querySelector('.subtask-description')?.textContent || ''
        }));
        formData.append('subtasks', JSON.stringify(subtasks));

        try {
            const response = await ajaxRequest(
                '../src/classes/controllers/TasksController.php', 
                formData
            );
            if (response.success) {
                hideModal('taskDetailsModal');
                // Перезагружаем все задачи на доске
                columns.forEach(column => getTasks(column.id));
            } else {
                console.error('Ошибка обновления задачи:', response.errors);
            }
        } catch (error) {
            console.error('Ошибка обновления:', error);
        }
    }
    
    // Удаление задачи
    async function deleteTask() {
        if (!confirm('Вы уверены, что хотите удалить эту задачу?')) return;
        
        const formData = new FormData();
        formData.append('action', 'deleteTask');
        formData.append('task_id', currentTaskId);
        
        try {
            const response = await ajaxRequest('../src/classes/controllers/TasksController.php', formData);
            if (response.success) {
                hideModal('taskDetailsModal');
                // Перезагружаем все задачи на доске
                columns.forEach(column => getTasks(column.id));
            } else {
                console.error('Ошибка удаления задачи:', response.errors);
            }
        } catch (error) {
            console.error('Ошибка удаления задачи:', error);
        }
    }
    
    // Открытие деталей задачи
    async function openTaskDetails(taskId) {
        currentTaskId = taskId;
        const formData = new FormData();
        formData.append('action', 'getTaskDetails');
        formData.append('task_id', taskId);

        try {
            const response = await ajaxRequest('../src/classes/controllers/TasksController.php', formData);
            if (response.success) {
                populateTaskModal(response.data);
                showModal('taskDetailsModal');
            } else {
                console.error('Ошибка загрузки задачи:', response.errors);
            }
        } catch (error) {
            console.error('Ошибка загрузки задачи:', error);
        }
    }

        // Загрузка данных проекта для модального окна
    async function initProjectModal() {
        try {
            // Загрузка данных проекта
            const project = await getProjectDetails(currentProjectId);
            if (!project) {
                console.error('Не удалось загрузить данные проекта');
                return;
            }
            
            document.getElementById('projectNameInput').value = project.name;
            document.getElementById('projectDescriptionInput').value = project.description || '';
            document.getElementById('projectPublic').checked = project.is_public == 1;

            // Загрузка участников
            const members = await getProjectMembers(currentProjectId);
            renderMembers(members);
        } catch (error) {
            console.error('Ошибка инициализации модалки проекта:', error);
        }
    }

    async function getProjectDetails(projectId) {
        const formData = new FormData();
        formData.append('action', 'getProjectById');
        formData.append('id', projectId);
        
        try {
            const response = await ajaxRequest('../src/classes/controllers/ProjectsController.php', formData);
            if (response.success) {
                return response.data;
            } else {
                console.error('Ошибка загрузки проекта:', response.errors);
            }
        } catch (error) {
            console.error('Ошибка загрузки проекта:', error);
        }
        return null;
    }

    async function getProjectMembers(projectId) {
        const formData = new FormData();
        formData.append('action', 'getProjectMembers');
        formData.append('project_id', projectId);
        
        try {
            const response = await ajaxRequest('../src/classes/controllers/ProjectsController.php', formData);
            if (response.success) {
                return response.data;
            } else {
                console.error('Ошибка загрузки участников:', response.errors);
            }
        } catch (error) {
            console.error('Ошибка загрузки участников:', error);
        }
        return [];
    }

    function renderMembers(members) {
        const container = document.getElementById('projectMembersList');
        container.innerHTML = '';
        
        members.forEach(member => {
            const memberEl = document.createElement('div');
            memberEl.className = 'member-item';
            memberEl.dataset.userId = member.id;
            memberEl.innerHTML = `
                <span>${member.surname} ${member.name}</span>
                <button class="remove-member">×</button>
            `;
            container.appendChild(memberEl);
        });
    }

    // Сохранение изменений проекта
    async function saveProject() {
        const name = document.getElementById('projectNameInput').value;
        const description = document.getElementById('projectDescriptionInput').value;
        const isPublic = document.getElementById('projectPublic').checked ? 1 : 0;

        const formData = new FormData();
        formData.append('action', 'updateProject');
        formData.append('project_id', currentProjectId);
        formData.append('name', name);
        formData.append('description', description);
        formData.append('is_public', isPublic);

        try {
            const response = await ajaxRequest('../src/classes/controllers/ProjectsController.php', formData);
            if (response.success) {
                // Обновляем название проекта в заголовке
                document.getElementById('projectName').textContent = name;
                hideModal('projectModal');
            } else {
                console.error('Ошибка обновления проекта:', response.errors);
            }
        } catch (error) {
            console.error('Ошибка обновления проекта:', error);
        }
    }

    // Добавление участника
    async function addProjectMember() {
        const select = document.getElementById('memberSelect');
        const userId = select.value;
        if (!userId) return;

        const formData = new FormData();
        formData.append('action', 'addProjectMember');
        formData.append('project_id', currentProjectId);
        formData.append('user_id', userId);

        try {
            const response = await ajaxRequest('../src/classes/controllers/ProjectsController.php', formData);
            if (response.success) {
                // Перезагружаем участников
                const members = await getProjectMembers(currentProjectId);
                renderMembers(members);
            } else {
                console.error('Ошибка добавления участника:', response.errors);
            }
        } catch (error) {
            console.error('Ошибка добавления участника:', error);
        }
    }

    // Удаление участника
    async function removeProjectMember(userId) {
        if (!confirm('Удалить участника из проекта?')) return;

        const formData = new FormData();
        formData.append('action', 'removeMember');
        formData.append('project_id', currentProjectId);
        formData.append('user_id', userId);

        try {
            const response = await ajaxRequest('../src/classes/controllers/ProjectsController.php', formData);
            if (response.success) {
                // Перезагружаем участников
                const members = await getProjectMembers(currentProjectId);
                renderMembers(members);
            } else {
                console.error('Ошибка удаления участника:', response.errors);
            }
        } catch (error) {
            console.error('Ошибка удаления участника:', error);
        }
    }
    
    // Заполнение модального окна задачи
    function populateTaskModal(data) {
        document.getElementById('editTaskName').value = data.task.name || '';
        document.getElementById('editTaskDescription').value = data.task.description || '';
        
        // Отображаем только дату (без времени) для редактирования
        if (data.task.due_date) {
            const [datePart] = data.task.due_date.split(' ');
            document.getElementById('editTaskDeadline').value = datePart;
        } else {
            document.getElementById('editTaskDeadline').value = '';
        }
        
        // Заполнение ответственных
        const assigneesContainer = document.getElementById('editAssigneesList');
        assigneesContainer.innerHTML = '';
        data.assignees.forEach(assignee => {
            const user = allUsers.find(u => u.id == assignee.id); // Исправлено: assignee.id вместо assignee.user_id
            if (user) {
                const div = document.createElement('div');
                div.className = 'assignee-item';
                div.dataset.userId = user.id; // Используем user.id
                div.innerHTML = `
                    <span>${user.surname} ${user.name}</span>
                    <button class="remove-assignee">×</button>
                `;
                assigneesContainer.appendChild(div);
            }
        });
        
        // Заполнение подзадач
        const subtasksContainer = document.getElementById('subtasksList');
        subtasksContainer.innerHTML = '';
        data.subtasks.forEach(subtask => {
            const div = document.createElement('div');
            div.className = 'subtask-item';
            div.dataset.subtaskId = subtask.id;
            div.innerHTML = `
                <div class="subtask-info">
                    <div class="subtask-name">${subtask.name}</div>
                    <div class="subtask-description">${subtask.description || ''}</div>
                </div>
                <button class="remove-subtask">×</button>
            `;
            subtasksContainer.appendChild(div);
        });
    }
    
    // Добавление ответственного
    function addAssignee(containerId, selectId) {
        const select = document.getElementById(selectId);
        const userId = select.value;
        const userName = select.options[select.selectedIndex].text;
        
        if (!userId) return;
        
        const container = document.getElementById(containerId);
        const existing = container.querySelector(`.assignee-item[data-user-id="${userId}"]`);
        if (existing) return;
        
        const div = document.createElement('div');
        div.className = 'assignee-item';
        div.dataset.userId = userId;
        div.innerHTML = `
            <span>${userName}</span>
            <button class="remove-assignee">×</button>
        `;
        container.appendChild(div);
        select.value = ''; // Очищаем выбор после добавления
    }
    
    // Добавление подзадачи
    function addSubtask() {
        const name = document.getElementById('newSubtaskName').value;
        const description = document.getElementById('newSubtaskDescription').value;
        
        if (!name) return;
        
        const container = document.getElementById('subtasksList');
        // New subtasks should not have an ID or have id: 0 to be treated as new on the server
        const div = document.createElement('div');
        div.className = 'subtask-item';
        // Do NOT set data-subtaskId for new subtasks, or set it to 0
        // div.dataset.subtaskId = 0; // Or just omit it. The server checks for id > 0 for updates.
        div.innerHTML = `
            <div class="subtask-info">
                <div class="subtask-name">${name}</div>
                <div class="subtask-description">${description || ''}</div>
            </div>
            <button class="remove-subtask">×</button>
        `;
        container.appendChild(div);
        
        // Очистка полей
        document.getElementById('newSubtaskName').value = '';
        document.getElementById('newSubtaskDescription').value = '';
    }
    
    // Сброс формы задачи
    function resetTaskForm() {
        document.getElementById('taskName').value = '';
        document.getElementById('taskDescription').value = '';
        document.getElementById('taskDeadline').value = '';
        document.getElementById('assigneesList').innerHTML = '';
        document.getElementById('assigneeSelect').value = ''; // Очистка селекта
    }
    
    // Показ модального окна
    function showModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    // Скрытие модального окна
    function hideModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Drag and Drop
    let draggedTaskId = null;

    function handleDragStart(event, taskId) {
        draggedTaskId = taskId;
        event.dataTransfer.setData('text/plain', taskId);
        event.dataTransfer.effectAllowed = 'move';
    }

    function handleDragOver(event) {
        event.preventDefault(); // Разрешить перетаскивание
        event.dataTransfer.dropEffect = 'move';
    }

    async function handleDrop(event, newColumnId) {
        event.preventDefault();
        const taskId = event.dataTransfer.getData('text/plain');
        
        if (taskId && newColumnId) {
            try {
                const formData = new FormData();
                formData.append('action', 'moveTask');
                formData.append('task_id', taskId);
                formData.append('new_column_id', newColumnId);

                const response = await ajaxRequest('../src/classes/controllers/TasksController.php', formData);
                if (response.success) {
                    // Перезагрузить задачи для обоих столбцов
                    columns.forEach(column => getTasks(column.id));
                } else {
                    console.error('Ошибка перемещения задачи:', response.errors);
                }
            } catch (error) {
                console.error('Ошибка перемещения задачи:', error);
            }
        }
        draggedTaskId = null;
    }
    
    // Инициализация обработчиков событий
    function initEventListeners() {
        // Открытие модалки проекта
        document.getElementById('projectName').addEventListener('click', async () => {
            await initProjectModal();
            showModal('projectModal');
        });
        
        // Открытие модалки создания задачи
        document.getElementById('addTaskBtn').addEventListener('click', () => {
            resetTaskForm(); // Сброс формы при открытии модалки создания
            showModal('taskModal');
        });
        
        // Создание столбца
        document.getElementById('createColumnBtn').addEventListener('click', createColumn);
        
        // Сохранение задачи
        document.getElementById('saveTaskBtn').addEventListener('click', createTask);
        
        // Обновление задачи
        document.getElementById('updateTaskBtn').addEventListener('click', updateTask);
        
        // Удаление задачи
        document.getElementById('deleteTaskBtn').addEventListener('click', deleteTask);
        
        // Добавление ответственных
        document.getElementById('addAssigneeBtn').addEventListener('click', () => {
            addAssignee('assigneesList', 'assigneeSelect');
        });
        
        document.getElementById('editAddAssigneeBtn').addEventListener('click', () => {
            addAssignee('editAssigneesList', 'editAssigneeSelect');
        });
        
        // Добавление подзадачи
        document.getElementById('addSubtaskBtn').addEventListener('click', addSubtask);
        
        // Удаление ответственных и подзадач (делегирование)
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-assignee')) {
                e.target.closest('.assignee-item').remove();
            }
            
            if (e.target.classList.contains('remove-subtask')) {
                e.target.closest('.subtask-item').remove();
            }
        });
        
        // Обработка кликов по задачам (делегирование)
        document.body.addEventListener('click', (e) => {
            if (e.target.classList.contains('task-btn')) {
                const taskId = e.target.closest('.task').dataset.taskId;
                openTaskDetails(taskId);
            }
            
            if (e.target.classList.contains('delete-column-btn')) {
                const columnId = e.target.dataset.columnId;
                deleteColumn(columnId);
            }
            
            if (e.target.id === 'add-column' || e.target.closest('#add-column')) {
                showModal('columnModal');
            }
        });
        
        // Закрытие модалок по клику вне области
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    hideModal(modal.id);
                }
            });
        });

        // Сохранение проекта
        document.getElementById('saveProjectBtn').addEventListener('click', saveProject);
        
        // Добавление участника
        document.getElementById('addMemberBtn').addEventListener('click', addProjectMember);
        
        // Удаление участника (делегирование)
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-member')) {
                const memberItem = e.target.closest('.member-item');
                const userId = memberItem.dataset.userId;
                removeProjectMember(userId);
            }
        });
    }
    
    // Обертка для AJAX-запросов
    async function ajaxRequest(url, data) {
        return new Promise((resolve) => {
            ajax(url, data, function(response) {
                resolve(response);
            });
        });
    }
    
    return { init, handleDragStart, handleDragOver, handleDrop };
})();