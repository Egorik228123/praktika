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
    <link rel="stylesheet" href="assets/css/project.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <script src="assets/js/common.js"></script>
    <script src="assets/js/projects.js"></script>
    <script src="assets/js/search.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../src/ajax.js"></script>
    <title>Система управления проектами | Проекты</title>

    <script>
        let allProjects = [];
        let currentUserId = <?= $_SESSION['user']['id'] ?? 'null' ?>;

        function getProjects() {
            const formData = new FormData();
           
            
            formData.append('action', 'getAllUserProjects');
            formData.append('user_id', currentUserId); 

            ajax('../src/classes/controllers/ProjectsController.php', formData, function(response) {
                if(response.success) {
                    allProjects = response.data;
                    renderProjects(allProjects);
                    
                    document.querySelector('.search-input').addEventListener('input', function(e) {
                        searchProjects(e.target.value); 
                    });
                } else {
                    console.error('Failed to load projects:', response.errors);
                }
            });
        }

        function renderProjects(projects) {
            const container = document.querySelector(".projects-container");
            container.innerHTML = '';

            if(projects.length === 0) {
                container.innerHTML = '<div class="no-results">Проекты не найдены</div>';
                return;
            }

            projects.forEach((project) => {
                const card = document.createElement('div');
                card.className = 'project-card';
                card.innerHTML = `
                    <span class="project-number">№${project.project_id}</span>
                    <h3 class="project-title">${project.name}</h3>
                    <p class="project-creator">${project.creator_id == currentUserId ? 'Вы' : (project.creator_name || 'Неизвестный создатель')}</p>
                    <p class="project-status">${project.is_public == 1 ? 'Публичный' : 'Приватный'}</p>
                    <div class="project-actions">
                        <button class="btn project-task-btn" onclick="openProject(${project.project_id})">Просмотреть задачи</button>
                        ${project.creator_id == currentUserId ? `<button class="btn btn-danger" onclick="deleteProject(${project.project_id})">Удалить</button>` : ''}
                    </div>
                `;
                container.appendChild(card);
            });
        }

        function openProject(projectId) {
            window.location.href = `tasks.php?projectId=${projectId}`;
        }

        function createProject() {
            const name = document.getElementById('projectName').value.trim();
            const description = document.getElementById('projectDescription').value.trim();
            const is_public_checkbox = document.getElementById('projectStatus');
            const is_public = is_public_checkbox.checked ? 1 : 0;

            if (!name) {
                alert('Название проекта обязательно!');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'createProject');
            formData.append('name', name);
            formData.append('description', description);
            formData.append('is_public', is_public);
            formData.append('user_id', currentUserId);

            ajax('../src/classes/controllers/ProjectsController.php', formData, function(response) {
                if(response.success) {
                    window.location.href = `tasks.php?projectId=${response.data.project_id}`;
                } else {
                    alert('Ошибка создания проекта: ' + (response.errors ? response.errors.join(', ') : 'Неизвестная ошибка'));
                }
            });
        }

        // Новая функция для удаления проекта
        function deleteProject(projectId) {
            if (confirm('Вы уверены, что хотите удалить этот проект? Все связанные данные (задачи, роли) будут удалены.')) {
                const formData = new FormData();
                formData.append('action', 'deleteProject');
                formData.append('project_id', projectId);
                formData.append('user_id', currentUserId); 
                
                ajax('../src/classes/controllers/ProjectsController.php', formData, function(response) {
                    if (response.success) {
                        alert('Проект успешно удален.');
                        getProjects(); // Обновление списка проектов
                    } else {
                        alert('Ошибка удаления проекта: ' + (response.errors ? response.errors.join(', ') : 'Неизвестная ошибка'));
                    }
                });
            }
        }

        function searchProjects(query) {
            const lowerCaseQuery = query.toLowerCase();
            const filteredProjects = allProjects.filter(project =>
                project.project_id.toString().includes(lowerCaseQuery) ||
                project.name.toLowerCase().includes(lowerCaseQuery) ||
                (project.creator_name && project.creator_name.toLowerCase().includes(lowerCaseQuery))
            );
            renderProjects(filteredProjects);
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            const createProjectBtn = document.getElementById('createProjectBtn');
            const createProjectModal = document.getElementById('createProjectModal');
            const closeSpan = document.querySelector('#createProjectModal .close');

            createProjectBtn.onclick = function() {
                createProjectModal.style.display = "block";
            }

            closeSpan.onclick = function() {
                createProjectModal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == createProjectModal) {
                    createProjectModal.style.display = "none";
                }
            }
            getProjects();
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
                        <a href="projects.php" class="active">
                            <img src="assets/img/icon-project.png" alt="Проекты">
                            <p>Проекты</p>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <div class="main-content">
            <div class="line-header">
                <h2>Проекты</h2>
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Поиск проекта...">
                </div>
                <button id="createProjectBtn" class="btn">Создать проект</button>
            </div>

            <div class="projects-container">

            </div>
        </div>

        <div id="createProjectModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Создать проект</h2>
                <form id="createProjectForm">
                    <div class="form-group">
                        <label for="projectName">Название проекта:</label>
                        <input type="text" id="projectName" required>
                    </div>
                    <div class="form-group">
                        <label for="projectDescription">Описание:</label>
                        <textarea id="projectDescription" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="projectStatus">Статус проекта:</label>
                        <div class="checkbox-container">
                            <input type="checkbox" id="projectStatus">
                            <label for="projectStatus">Публичный проект</label>
                        </div>
                    </div>
                    <button type="button" class="btn" onclick="createProject()">Создать</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>