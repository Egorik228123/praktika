<?php
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    if(isset($_POST['logout'])) {
        session_destroy();
        header("Location: login.php");
        exit;
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../src/ajax.js"></script>
    <script>
        const userId = <?=$_SESSION['user']['id']?>;
        function getUser() {
            let Data = new FormData();
            Data.append('action', 'getUserById');
            Data.append('id', userId);
            ajax('../src/classes/controllers/UsersController.php', Data, function(response) {
                if(response.success) {
                    document.querySelector(".profile-text h3").textContent = `${response.data.name} ${response.data.surname} ${response.data.middlename}`;
                    document.querySelector(".profile-text .email").textContent = response.data.email;  
                    document.querySelector(".about-section p").textContent = response.data.bio;  
                }
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
                </ul>
            </div>
        </aside>
        
        <main class="main-content">
            <div class="profile-info">
                <img src="assets/img/image.jpg" class="avatar">
                <div class="profile-text">
                    <h3></h3>
                    <span class="email"></span>
                    <span class="last-activity">Последняя активность mm:hh dd.mm.yyyy</span>
                </div>
            </div>
            
            <div class="about-section">
                <h3>О себе</h3>
                <p></p>
            </div>
            
            <div class="edit-profile">
                <h4 id="editProfileBtn">Редактировать профиль</h4>
                <form action="" method="post">
                    <button class="btn" name="logout">Выход</button>
                </form>
            </div>
        </main>
    </div>

    <div class="modal" id="profileModal">
        <div class="modal-content">
            <h2>Изменение личных данных</h2>
            <div class="form-group">
                <label>Вы</label>
                <input type="text" placeholder="Фамилия">
                <input type="text" placeholder="Имя">
                <input type="text" placeholder="Отчество">
            </div>
            <div class="form-group">
                <label>Краткая информация</label>
                <textarea placeholder="Расскажите о себе"></textarea>
            </div>
            <div class="modal-actions">
                <button class="btn btn-danger" id="deleteAccountBtn">Удалить аккаунт</button>
                <button class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>

    <div class="modal" id="confirmModal">
        <div class="modal-content confirm-modal">
            <h3>Вы уверены, что хотите удалить аккаунт?</h3>
            <p>Это действие нельзя отменить. Все ваши данные будут удалены.</p>
            <div class="modal-actions">
                <button class="btn btn-danger" id="confirmDeleteBtn">Удалить</button>
            </div>
        </div>
    </div>
</body>
</html>