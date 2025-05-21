<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/project.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <title>Система управления проектами | Проекты</title>
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
                    <li>
                        <a href="tasks.php">
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
                <button class="btn">Создать проект</button>
            </div>
    
            <div class="projects-container">

                <div class="project-card">
                    <span class="project-number">№1</span>
                    <h3 class="project-title">Название проекта</h3>
                    <p class="project-creator">Создатель: Вы</p>
                    <button class="btn project-task-btn">Просмотреть задачи</button>
                </div>
                
                <div class="project-card">
                    <span class="project-number">№2</span>
                    <h3 class="project-title">Название проекта</h3>
                    <p class="project-creator">Создатель: User</p>
                    <button class="btn project-task-btn">Просмотреть задачи</button>
                </div>
            </div>
        </div>
    </div>    
</body>
</html>