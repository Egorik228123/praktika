<?php
    session_start();

    if (isset($_SESSION['user'])) {
        header("Location: profile.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/auth.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../src/ajax.js"></script>
    <title>Система управления проектами | Регистрация</title>
    <script>
        function register() {
            const name = document.getElementById('name').value.trim();
            const surname = document.getElementById('surname').value.trim();
            const middlename = document.getElementById('middlename').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const repeatPassword = document.getElementById('repeat_password').value.trim();

            if (!name || !surname || !email || !password || !repeatPassword) {
                document.getElementById('notification').innerHTML = 
                    '<div class="error">Заполните все поля</div>';
                return;
            }

            if (password != repeatPassword) {
                document.getElementById('notification').innerHTML = 
                    '<div class="error">Пароли не совпадают</div>';
                return;
            }

            let Data = new FormData();
            Data.append('action', 'register');
            Data.append('name', name);
            Data.append('surname', surname);
            Data.append('middlename', middlename);
            Data.append('email', email);
            Data.append('password', password);
 
            ajax('../src/classes/controllers/UsersController.php', Data, function(response) {
                if(response.success) {
                    window.location.href = "profile.php";
                }
                else {
                    document.getElementById('notification').innerHTML = 
                        response.errors.map(err => `<div class="error">${err}</div>`).join('');
                }
            });
        }
    </script>
</head>
<body>
    <div id="notification"></div>
    <main>
        <div class="container">
            <h1>Система управления проектами</h1>
            <div class="form_container grid">
                <h2>Заполните свои личные данные</h2>
                <form action="" id="register">
                    <div>
                        <input type="text" id="surname" name="surname" placeholder="Фамилия">
                    </div>
                    <div>
                        <input type="text" id="name" name="name" placeholder="Имя">
                    </div>
                    <div>
                        <input type="text" id="middlename" name="middlename" placeholder="Отчество">
                    </div>
                    <div>
                        <input type="email" id="email" name="email" placeholder="Почта xx@xx.xx">
                    </div>
                    <div>
                        <input type="password" id="password" name="password" placeholder="Пароль">
                    </div>
                    <div>
                        <input type="password" id="repeat_password" name="repeat_password" placeholder="Повторите пароль">
                    </div>
                </form>
            </div>
            <button type="button" form="register" onclick="register()">Зарегистрироваться</button>
            <p>Есть аккаунт? <a href="login.php">Войти</a></p>
        </div>
    </main>
</body>
</html>