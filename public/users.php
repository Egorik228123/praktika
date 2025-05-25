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
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/project.css">
    <script src="assets/js/search.js"></script>
    <title>Система управления проектами | Сотрудники</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../src/ajax.js"></script>
    <script>
        let allUsers = [];

        function getUser() {
            const Data = new FormData();
            Data.append('action', 'getAllUsers');
            
            ajax('../src/classes/controllers/UsersController.php', Data, function(response) {
                if(response.success) {
                    allUsers = response.data;
                    renderUsers(allUsers);
                }
            });
        }

        function renderUsers(users) {
            const container = document.querySelector(".projects-container");
            container.innerHTML = '';
            
            if(users.length === 0) {
                container.innerHTML = '<div class="no-results">Сотрудники не найдены</div>';
                return;
            }
            
            users.forEach((user) => {
                const card = document.createElement('div');
                card.className = 'project-card';
                card.innerHTML = `
                    <span class="project-number">№${user.id}</span>
                    <h3 class="project-title">${user.name} ${user.surname} ${user.middlename}</h3>
                    <button class="btn project-task-btn">Перейти в профиль</button>
                `;
                container.appendChild(card);
            });
        }
        getUser();
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
                        <a href="users.php" class="active">
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
        
        <main class="main-content">
            <div class="main-content">
                <div class="line-header">
                    <h2>Сотрудники</h2>
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Поиск по ID, имени, фамилии...">
                    </div>
                </div>
    
                <div class="projects-container">
                    
                </div>
            </div>
        </main>
    </div>
</body>
</html>