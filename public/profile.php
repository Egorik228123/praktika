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
                    
                    
                    document.querySelector("#surnameInput").value = response.data.surname;
                    document.querySelector("#nameInput").value = response.data.name;
                    document.querySelector("#middlenameInput").value = response.data.middlename || '';
                    document.querySelector("#bioInput").value = response.data.bio || '';
                }
            });
        }
        
        function updateUser() {
            const password = document.querySelector("#passwordInput").value;
            const repeatPassword = document.querySelector("#repeatPasswordInput").value;

            if (password && password !== repeatPassword) {
                alert('Пароли не совпадают');
                return;
            }

            let Data = new FormData();
            Data.append('action', 'updateUser');
            Data.append('id', userId);
            Data.append('surname', document.querySelector("#surnameInput").value);
            Data.append('name', document.querySelector("#nameInput").value);
            Data.append('middlename', document.querySelector("#middlenameInput").value);
            Data.append('bio', document.querySelector("#bioInput").value);
            if (password) {
                Data.append('password', password);
            }
            
            ajax('../src/classes/controllers/UsersController.php', Data, function(response) {
                if(response.success) {
                    alert('Данные успешно обновлены');
                    getUser(); 
                    document.getElementById('profileModal').style.display = 'none';
                } else {
                    alert('Ошибка: ' + (response.errors ? response.errors.join(', ') : 'Неизвестная ошибка'));
                }
            });
        }
        
        function deleteUser() {
            let Data = new FormData();
            Data.append('action', 'deleteUser');
            Data.append('id', userId);
            
            ajax('../src/classes/controllers/UsersController.php', Data, function(response) {
                if(response.success) {
                    alert('Аккаунт успешно удален');
                    window.location.href = 'login.php';
                } else {
                    alert('Ошибка: ' + (response.errors ? response.errors.join(', ') : 'Неизвестная ошибка'));
                }
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            getUser();
            
            document.getElementById('editProfileBtn').addEventListener('click', function() {
                document.getElementById('profileModal').style.display = 'flex';
            });
            
            document.querySelector('#profileModal .btn-primary').addEventListener('click', function(e) {
                e.preventDefault();
                updateUser();
            });
            
            document.getElementById('deleteAccountBtn').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('confirmModal').style.display = 'block';
            });
            
            document.getElementById('confirmDeleteBtn').addEventListener('click', function(e) {
                e.preventDefault();
                deleteUser();
            });
            
            // Закрытие модальных окон при клике вне их
            window.addEventListener('click', function(event) {
                if (event.target.className === 'modal') {
                    event.target.style.display = 'none';
                }
            });
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
                <label>Фамилия</label>
                <input type="text" id="surnameInput" placeholder="Фамилия" required>
            </div>
            <div class="form-group">
                <label>Имя</label>
                <input type="text" id="nameInput" placeholder="Имя" required>
            </div>
            <div class="form-group">
                <label>Отчество</label>
                <input type="text" id="middlenameInput" placeholder="Отчество">
            </div>
            <div class="form-group">
                <label>Новый пароль</label>
                <input type="password" id="passwordInput" placeholder="Новый пароль">
            </div>
            <div class="form-group">
                <label>Повторите новый пароль</label>
                <input type="password" id="repeatPasswordInput" placeholder="Повторите новый пароль">
            </div>
            <div class="form-group">
                <label>Краткая информация</label>
                <textarea id="bioInput" placeholder="Расскажите о себе"></textarea>
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
                <button class="btn" onclick="document.getElementById('confirmModal').style.display = 'none'">Отмена</button>
            </div>
        </div>
    </div>
</body>
</html>