<?php
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
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

        function renderColumns(columns) {
            const container = document.querySelector(".board");
            container.innerHTML = '';
            
            if(columns.length === 0) {
                container.innerHTML = '<div class="no-results">Столбцы не найдены. Создайте новый</div>';
                return;
            }
            
            columns.forEach((column) => {
                const card = document.createElement('div');
                card.className = 'column';
                card.innerHTML = `<h2>${column.name}</h2>`;
                container.appendChild(card);
                getTasks(column.id);
            });
        }

        function renderTask(tasks) {
            const container = document.querySelector(".column");
            
            if(tasks.length === 0) {
                return;
            }
            
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
            Data.append('project_id', <?=$_GET['projectId']?>);
            
            ajax('../src/classes/controllers/ColumnsController.php', Data, function(response) {
                if(response.success) {
                    renderColumns(response.data);
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
                    <li>
                        <a href="tasks.php" class="active">
                            <img src="assets/img/icon-task.png" alt="Мои задачи">
                            <p>Мои задачи</p>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        <div class="main-content">
            <div class="line-header">
                <h2>Проекты</h2>
                <button id="addTaskBtn" class="btn">Добавить задачу</button>
            </div>

            <div class="board">
                <!-- Существующие столбцы -->
                <div class="column">
                    <h2>Новые</h2>
                    <div class="task" draggable="true">
                        <h3>Наименование задачи</h3>
                        <p>Ответственный: Сидоров Сидор Сидорович</p>
                        <button class="task-btn">Описание задачи</button>
                    </div>
                </div>

                <!-- Кнопка добавления столбца -->
                <div id="add-column" class="add-column">
                    <span>+</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Модальное окно добавления задачи -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Добавить задачу</h2>
            <form id="taskForm">
                <label for="taskName">Наименование:</label>
                <input type="text" id="taskName" required>

                <label for="taskResponsible">Ответственный:</label>
                <select id="taskResponsible">
                    <option>Иванов Иван Иванович</option>
                    <option>Петров Петр Петрович</option>
                </select>

                <label>Подзадачи:</label>
                <div id="subtasks">
                    <input type="text" class="subtask" placeholder="Название подзадачи">
                </div>
                <button type="button" id="addSubtask">Добавить подзадачу</button>

                <button type="submit">Сохранить</button>
            </form>
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
                <button type="submit">Создать</button>
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
    <div id="createTaskModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Создать задачу</h2>
            <form id="createTaskForm">
                <div class="task-details">
                    <label for="createTaskName">Название:</label>
                    <input type="text" id="createTaskName" required>
                    
                    <label for="createTaskDescription">Описание:</label>
                    <textarea id="createTaskDescription" required></textarea>
                    
                    <label for="createTaskResponsible">Ответственный:</label>
                    <select id="createTaskResponsible" required>
                        <option>Иванов Иван Иванович</option>
                        <option>Петров Петр Петрович</option>
                        <option>Сидоров Сидор Сидорович</option>
                    </select>
                    
                    <label for="createTaskDeadline">Дедлайн:</label>
                    <input type="date" id="createTaskDeadline" required>
                    
                    <button type="submit">Создать задачу</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>