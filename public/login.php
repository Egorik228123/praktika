<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/auth.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../src/ajax.js"></script>
    <script>
        function authorize() {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!email || !password) {
                document.getElementById('notification').innerHTML = 
                    '<div class="error">Заполните все поля</div>';
                return;
            }

            let Data = new FormData();
            Data.append('action', 'authorize');
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
    <title>Система управления проектами | Авторизация</title>
</head>
<body>
    <div id="notification"></div>
    <main>
        
        <div>
            <h1>Система управления проектами</h1>
            <div class="form_container">
                <h2>Заполните свои личные данные</h2>
                <form action="" id="login">
                    <div>
                        <input type="email" id="email" name="email" placeholder="Почта xx@xx.xx">
                    </div>
                    <div>
                        <input type="password" id="password" name="password" placeholder="Пароль">
                    </div>
                </form>
            </div>
            <button type="button" onclick="authorize()">Авторизоваться</button>
            <p>Нет аккаунта? <a href="register.php">Создать</a></p>
        </div>
    </main>
</body>
</html>