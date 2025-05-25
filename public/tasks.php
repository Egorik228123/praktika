<?php
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }
    if($_GET == null) {
        header("Location: projects.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система управления проектами | Доска задач</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/board.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../src/ajax.js"></script>
    <script src="assets/js/common.js"></script>
    <script src="assets/js/board.js"></script>
    <script>
        let allUsers = [];
        let countColumns = 0;
        let firstColumn = 0;
        let projectId = <?=$_GET['projectId'] ?? 0?>;
        function getTasks(id) {
            const Data = new FormData();
            Data.append('action', 'getTasksByColumn');
            Data.append('column_id', id);
            
            ajax('../src/classes/controllers/TasksController.php', Data, function(response) {
                if(response.success) {
                    renderTask(response.data);
                }
            });
        }

        function getProject() {
            const projectName = document.getElementById('projectName');
            projectName.textContent = '';
            const Data = new FormData();
            Data.append('action', 'getProjectById');
            Data.append('id', projectId);

            ajax('../src/classes/controllers/ProjectsController.php', Data, function(response) {
                if(response.success)
                    projectName.textContent = `Проект ${response.data.name}`;
            });
        }
        

        function createColumn() {
            const columnName = document.getElementById("columnName").value.trim();
            if (columnName == '') return;

            const Data = new FormData();
            Data.append('action', 'createColumn');
            Data.append('name', columnName);
            Data.append('project_id', projectId);
            Data.append('position', countColumns);

            ajax('../src/classes/controllers/ColumnsController.php', Data, function(response) {
                if (response.success) {
                    document.getElementById("columnName").value = ''; // Очистить поле
                    getColumns(); // Обновить столбцы
                    closeModal('columnModal'); // Закрыть модальное окно
                }
            });
        }

        function renderColumns(columns) {
            const container = document.querySelector(".board");
            container.innerHTML = '';
            if(columns.length === 0) {
                container.innerHTML = `
                    <div id="add-column" class="add-column">
                        <span>+</span>
                    </div>
                `;
                setupModal('columnModal', '.add-column', '.close');
                return;
            }
            
            columns.forEach((column) => {
                const card = document.createElement('div');
                card.className = 'column';
                card.innerHTML = `<h2>${column.name}</h2>`;
                container.appendChild(card);
                getTasks(column.id);
            });
            const createColumn = document.createElement('div');
            createColumn.id = 'add-column';
            createColumn.className = 'add-column';
            createColumn.innerHTML = `<span>+</span>`;
            container.appendChild(createColumn);
            setupModal('columnModal', '.add-column', '.close');
            getProject();
            return;
        }

        function renderTask(tasks) {  
            if(tasks.length === 0) {
                return;
            }
            const container = document.querySelector(".column");
            tasks.forEach((task) => {
                const card = document.createElement('div');
                card.className = 'task';
                card.draggable = 'true';
                card.innerHTML = `
                        <h3>${task.name}</h3>
                        <p>Ответственный: ${task.assignee}</p>
                        <button class="task-btn">Описание задачи ${task.id}</button>
                `;
                container.appendChild(card);
            });
        }

        function getColumns() {
            const Data = new FormData();
            Data.append('action', 'getColumnsByProject');
            Data.append('project_id', projectId);
            
            ajax('../src/classes/controllers/ColumnsController.php', Data, function(response) {
                if(response.success) {
                    countColumns += 1;
                    if(firstColumn == null) {
                        firstColumn = response.data.id;
                    }
                    renderColumns(response.data);
                }
            });
        }

        function createTask() {
            const name = document.getElementById('createTaskName').value;
            const description = document.getElementById('createTaskDescription').value;
            const createTaskDeadline = document.getElementById('createTaskDeadline').value;
            const Data = new FormData();
            Data.append('action', 'getColumnsByProject');
            Data.append('name', name);
            Data.append('description', description);
            Data.append('due_date', createTaskDeadline);
            Data.append('column_id', firstColumn);
            ajax('../src/classes/controllers/TasksController.php', Data, function(response) {
                if(response.success) {
                    getTasks(firstColumn);
                }
            });
        }
        getColumns();
    </script>
</head>
<body> 
    <div class="container"> 
        <aside class="sidebar">
            <div class="company-name">Система управления проектами</div>
            <div class="user-section">
                <ul class="nav-menu">
                    <li>
                        <a href="profile.php">
                            <img src="assets/img/icon-profile.png" alt="Профиль">
                            <p>Профиль</p>
                        </a>
                    </li>
                    <li>
                        <a href="users.php">
                            <img src="assets/img/icon-users.png" alt="Все сотрудники">
                            <p>Все сотрудники</p>
                        </a>
                    </li>
                    <li>
                        <a href="projects.php">
                            <img src="assets/img/icon-project.png" alt="Проекты">
                            <p>Проекты</p>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        <div class="main-content">
            <div class="line-header">
                <h2 id="projectName">Проект</h2>
                <button id="addTaskBtn" class="btn">Добавить задачу</button>
            </div>

            <div class="board">

            </div>
        </div>
    </div>

    <!-- Модальное окно добавления столбца -->
    <div id="columnModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Добавить столбец</h2>
            <form id="columnForm">
                <label for="columnName">Название столбца:</label>
                <input type="text" id="columnName" required>
                <button type="button" class="btn" onclick="createColumn()">Создать</button>
            </form>
        </div>
    </div>

    <!-- Модальное окно просмотра задачи -->
    <div id="taskDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Детали задачи</h2>
            <div class="task-details">
                <div>
                    <label>Название:</label>
                    <p>Наименование задачи</p>
                </div>
                <div>
                    <label>Описание:</label>
                    <p>Подробное описание задачи с деталями выполнения и требованиями</p>
                </div>
                <div>
                    <label>Ответственный:</label>
                    <p>Сидоров Сидор Сидорович</p>
                </div>
                <div>
                    <label>Дедлайн:</label>
                    <p>15.06.2023</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно создания задачи -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Создать задачу</h2>
            <form id="createTaskForm">
                <div class="task-details">
                    <label for="createTaskName">Название:</label>
                    <input type="text" id="createTaskName" required>
                    
                    <label for="createTaskDescription">Описание:</label>
                    <textarea id="createTaskDescription"></textarea>
                    
                    <label for="createTaskResponsible">Ответственный:</label>
                    <select id="createTaskResponsible">
                        <option>Иванов Иван Иванович</option>
                        <option>Петров Петр Петрович</option>
                        <option>Сидоров Сидор Сидорович</option>
                    </select>
                    
                    <label for="createTaskDeadline">Дедлайн:</label>
                    <input type="date" id="createTaskDeadline">
                    
                    <button class="btn">Создать задачу</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>