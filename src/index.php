<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <button class="user-btn" data-action="delete" data-id="2">Удалить</button>
    <button class="user-btn" data-action="view" data-id="2">Просмотр</button>

    <h2>Добавление</h2>
    <form id="userForm">
        <input type="hidden" name="id" id="userId">
        <input type="text" name="name" placeholder="Имя" required>
        <input type="text" name="surname" placeholder="Фамилия">
        <input type="text" name="middlename" placeholder="Отчество">
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="password" placeholder="Пароль">
        <textarea name="bio" placeholder="О себе"></textarea>

        <button type="button" class="user-btn" data-action="create">Создать</button>
        <button type="button" class="user-btn" data-action="edit">Редактировать</button>
    </form>

    <div id="response"></div>   

    <script src="usersAjax.js"></script>
</body>     
</html>