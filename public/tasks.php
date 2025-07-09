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
        let projectId = <?=$_GET['projectId'] ?? 0?>;
        document.addEventListener('DOMContentLoaded', () => {
            TaskManager.init(projectId);
        });
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
                <h2 id="projectName" style="cursor: pointer; text-decoration: underline;">Проект</h2>
                <button id="addTaskBtn" class="btn">Добавить задачу</button>
            </div>

            <div class="board"></div>
        </div>
    </div>

    <!-- Модальное окно добавления столбца -->
    <div id="columnModal" class="modal">
        <div class="modal-content">
            <h2>Добавить столбец</h2>
            <form id="columnForm">
                <label for="columnName">Название столбца:</label>
                <input type="text" id="columnName" required>
                <button type="button" id="createColumnBtn" class="btn">Создать</button>
            </form>
        </div>
    </div>

    <!-- Модальное окно просмотра задачи -->
    <div id="taskDetailsModal" class="modal">
        <div class="modal-content">
            <h2>Детали задачи</h2>
            <form id="taskDetailsForm">
                <div class="form-group">
                    <label for="editTaskName">Название:</label>
                    <input type="text" id="editTaskName" required>
                </div>
                
                <div class="form-group">
                    <label for="editTaskDescription">Описание:</label>
                    <textarea id="editTaskDescription" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Ответственные:</label>
                    <div class="scrollable-container" style="max-height: 150px;" id="editAssigneesList"></div>
                    <div class="assignee-controls">
                        <select id="editAssigneeSelect" class="searchable-select">
                            <!-- Users will be loaded dynamically -->
                        </select>
                        <button type="button" id="editAddAssigneeBtn">+ Добавить</button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Подзадачи:</label>
                    <div class="scrollable-container" style="max-height: 150px;" id="subtasksList"></div>
                    <div class="subtask-controls">
                        <input type="text" id="newSubtaskName" placeholder="Название подзадачи">
                        <textarea id="newSubtaskDescription" placeholder="Описание подзадачи" rows="2"></textarea>
                        <button type="button" id="addSubtaskBtn">+ Добавить</button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="editTaskDeadline">Дедлайн:</label>
                    <input type="date" id="editTaskDeadline">
                </div>
                
                <div class="task-actions">
                    <button type="button" id="updateTaskBtn" class="btn">Обновить задачу</button>
                    <button type="button" id="deleteTaskBtn" class="btn btn-danger">Удалить задачу</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Модальное окно создания задачи -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <h2 id="taskModalTitle">Создать задачу</h2>
            <form id="taskForm">
                <div class="form-group">
                    <label for="taskName">Название:</label>
                    <input type="text" id="taskName" required>
                </div>
                
                <div class="form-group">
                    <label for="taskDescription">Описание:</label>
                    <textarea id="taskDescription" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Ответственные:</label>
                    <div class="scrollable-container" style="max-height: 150px;" id="assigneesList"></div>
                    <div class="assignee-controls">
                        <select id="assigneeSelect" class="searchable-select">
                            <!-- Users will be loaded dynamically -->
                        </select>
                        <button type="button" id="addAssigneeBtn">+ Добавить</button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="taskDeadline">Дедлайн:</label>
                    <input type="date" id="taskDeadline">
                </div>
                
                <button type="button" id="saveTaskBtn" class="btn">Сохранить</button>
            </form>
        </div>
    </div>

    <!-- Модальное окно данных проекта -->
    <div id="projectModal" class="modal">
        <div class="modal-content">
            <h2>Настройки проекта</h2>
            <form id="projectForm">
                <div class="form-group">
                    <label for="projectNameInput">Название:</label>
                    <input type="text" id="projectNameInput" required>
                </div>
                
                <div class="form-group">
                    <label for="projectDescriptionInput">Описание:</label>
                    <textarea id="projectDescriptionInput" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="projectPublic">Публичный проект:</label>
                    <input type="checkbox" id="projectPublic">
                </div>
                
                <div class="form-group">
                    <label>Участники проекта:</label>
                    <div class="scrollable-container" style="max-height: 200px;" id="projectMembersList"></div>
                    <div class="member-controls">
                        <select id="memberSelect" class="searchable-select">
                            <!-- Users will be loaded dynamically -->
                        </select>
                        <button type="button" id="addMemberBtn">+ Добавить</button>
                    </div>
                </div>
                
                <button type="button" id="saveProjectBtn" class="btn">Сохранить</button>
            </form>
        </div>
    </div>
</body>
</html>