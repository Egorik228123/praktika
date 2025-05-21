<?php
    session_start();
    if(!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }

    include __DIR__ . "../../src/classes/controllers/UsersController.php";
    $controller = new UsersController();
    
    $user = $controller->GetUserById($_SESSION['user']['id']);
    if(!$user['success']) {
        foreach($user['errors'] as $error) {
            echo "<p>$error</p>";
        }
    }
    else {
        $user = $user['data'];
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система управления проектами | Профиль</title>
    <link rel="stylesheet" href="assets/css/profile.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <script src="assets/js/common.js"></script>
    <script src="assets/js/board.js"></script>
    <script src="assets/js/profile.js"></script>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="company-name">Система управления проектами</div>
            <div class="user-section">
                
                <ul class="nav-menu">
                    <li>
                        <a href="profile.php" class="active">
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
                        <a href="tasks.php">
                            <img src="assets/img/icon-task.png" alt="Мои задачи">
                            <p>Мои задачи</p>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        
        <main class="main-content">
            <div class="profile-info">
                <img src="assets/img/image.jpg" class="avatar">
                <div class="profile-text">
                    <h3><?= $user->surname . ' ' . $user->name . ' ' . $user->middlename?></h3>
                    <span class="email"><?= $user->email?></span>
                    <span class="last-activity">Последняя активность mm:hh dd.mm.yyyy</span>
                </div>
            </div>
            
            <div class="about-section">
                <h3>О себе</h3>
                <p><?= $user->bio ?></p>
            </div>
            
            <div class="edit-profile">
                <h3 id="editProfileBtn">Редактировать профиль</h3>
                <button class="btn" onclick="">Выход</button>
            </div>
        </main>
    </div>

    <div class="modal" id="profileModal">
        <div class="modal-content">
            <h2>Изменение личных данных</h2>
            <div class="photo-actions">
                <button class="btn">Обновить фото</button>
                <button class="btn btn-danger">Удалить</button>
            </div>
            <div class="form-group">
                <label>Вы</label>
                <input type="text" placeholder="Фамилия">
                <input type="text" placeholder="Имя">
                <input type="text" placeholder="Отчество">
            </div>
            <div class="form-group">
                <label>Данные для входа</label>
                <input type="email" placeholder="Почта">
                <input type="password" placeholder="Пароль">
            </div>
            <div class="form-group">
                <label>Краткая информация</label>
                <textarea placeholder="Расскажите о себе">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</textarea>
            </div>
            <div class="modal-actions">
                <button class="btn btn-danger" id="deleteAccountBtn">Удалить аккаунт</button>
                <div>
                    <button class="btn btn-secondary" id="cancelEditBtn">Отмена</button>
                    <button class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="confirmModal">
        <div class="modal-content confirm-modal">
            <h3>Вы уверены, что хотите удалить аккаунт?</h3>
            <p>Это действие нельзя отменить. Все ваши данные будут удалены.</p>
            <div class="modal-actions">
                <button class="btn btn-secondary" id="cancelDeleteBtn">Отмена</button>
                <button class="btn btn-danger" id="confirmDeleteBtn">Удалить</button>
            </div>
        </div>
    </div>
</body>
</html>