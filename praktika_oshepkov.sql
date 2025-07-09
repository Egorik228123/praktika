-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июл 04 2025 г., 19:50
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `praktika_oshepkov`
--

-- --------------------------------------------------------

--
-- Структура таблицы `columns`
--

CREATE TABLE `columns` (
  `id` int NOT NULL COMMENT 'ID Столбца',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Название',
  `position` int NOT NULL COMMENT 'Расположение на доске',
  `project_id` int NOT NULL COMMENT 'ID Проекта'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `columns`
--

INSERT INTO `columns` (`id`, `name`, `position`, `project_id`) VALUES
(1, 'новые', 0, 5),
(2, 'в процессе', 1, 5),
(3, 'можно проверять', 2, 5),
(4, 'готово', 3, 5),
(5, 'новые', 0, 6),
(6, 'в процессе', 1, 6),
(7, 'можно проверять', 2, 6),
(8, 'готово', 3, 6),
(9, 'новые', 0, 7),
(10, 'в процессе', 1, 7),
(11, 'можно проверять', 2, 7),
(12, 'готово', 3, 7),
(13, 'новые', 0, 8),
(14, 'в процессе', 1, 8),
(15, 'можно проверять', 2, 8),
(16, 'готово', 3, 8),
(17, 'новые', 0, 9),
(18, 'в процессе', 1, 9),
(19, 'можно проверять', 2, 9),
(20, 'готово', 3, 9),
(37, 'ooggo', 1, 5),
(38, '123', 1, 5),
(39, '123', 2, 5),
(40, '123', 1, 1),
(41, 'New', 1, 1),
(49, 'gdfg', 1, 5),
(50, '123', 1, 5),
(51, '123', 2, 5),
(52, 'новые', 0, 11),
(53, 'в процессе', 1, 11),
(54, 'можно проверять', 2, 11),
(55, 'готово', 3, 11),
(56, 'новые', 0, 12),
(57, 'в процессе', 1, 12),
(58, 'можно проверять', 2, 12),
(59, 'готово', 3, 12),
(60, 'новые', 0, 13),
(61, 'в процессе', 1, 13),
(62, 'можно проверять', 2, 13),
(63, 'готово', 3, 13),
(64, 'новые', 0, 14),
(65, 'в процессе', 1, 14),
(66, 'можно проверять', 2, 14),
(67, 'готово', 3, 14),
(68, 'новые', 0, 15),
(69, 'в процессе', 1, 15),
(70, 'можно проверять', 2, 15),
(71, 'готово', 3, 15),
(72, 'новые', 0, 16),
(73, 'в процессе', 1, 16),
(74, 'можно проверять', 2, 16),
(75, 'готово', 3, 16),
(76, 'новые', 0, 17),
(77, 'в процессе', 1, 17),
(78, 'можно проверять', 2, 17),
(79, 'готово', 3, 17),
(80, 'новые', 0, 18),
(81, 'в процессе', 1, 18),
(82, 'можно проверять', 2, 18),
(83, 'готово', 3, 18),
(84, '123', 1, 18),
(85, 'gsdg', 2, 18),
(87, 'rgrsfvsr', 1, 18),
(88, 'lll', 2, 18),
(93, 'новые', 0, 19),
(94, 'в процессе', 1, 19),
(95, 'можно проверять', 2, 19),
(96, 'готово', 3, 19);

-- --------------------------------------------------------

--
-- Структура таблицы `projects`
--

CREATE TABLE `projects` (
  `id` int NOT NULL COMMENT 'ID Проекта',
  `name` varchar(200) NOT NULL COMMENT 'Название',
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Описание',
  `is_public` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Публичность проекта'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `projects`
--

INSERT INTO `projects` (`id`, `name`, `description`, `is_public`) VALUES
(1, 'Тест проек123', '1234', 1),
(18, 'dfsdf', 'sadfsadf', 1),
(19, '1234', '1234', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `project_roles`
--

CREATE TABLE `project_roles` (
  `id_project` int NOT NULL COMMENT 'ID Проекта',
  `id_user` int NOT NULL COMMENT 'ID Пользователя',
  `role` enum('user','creator','admin') NOT NULL DEFAULT 'user' COMMENT 'Роль'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `project_roles`
--

INSERT INTO `project_roles` (`id_project`, `id_user`, `role`) VALUES
(19, 7, 'user'),
(19, 11, 'user');

-- --------------------------------------------------------

--
-- Структура таблицы `subtasks`
--

CREATE TABLE `subtasks` (
  `id` int NOT NULL COMMENT 'ID Подзадачи',
  `name` varchar(200) NOT NULL COMMENT 'Название',
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Описание',
  `due_date` datetime DEFAULT NULL COMMENT 'Дата выполнения (дедлайн)',
  `task_id` int NOT NULL COMMENT 'ID Задачи'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `subtask_assignees`
--

CREATE TABLE `subtask_assignees` (
  `user_id` int NOT NULL COMMENT 'ID Пользователя',
  `subtask_id` int NOT NULL COMMENT 'ID Подзадачи'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tasks`
--

CREATE TABLE `tasks` (
  `id` int NOT NULL COMMENT 'ID Задачи',
  `name` varchar(200) NOT NULL COMMENT 'Название',
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Описание',
  `due_date` datetime DEFAULT NULL COMMENT 'Дата выполнения (дедлайн)',
  `column_id` int DEFAULT NULL COMMENT 'Столбец (статус)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `tasks`
--

INSERT INTO `tasks` (`id`, `name`, `description`, `due_date`, `column_id`) VALUES
(1, 'Тест задача', NULL, '2025-05-31 21:32:15', 1),
(11, 'fdd434', 'dff3434', '2011-01-01 00:00:00', 40),
(12, '1234', '1234', '2020-02-02 00:00:00', 93),
(13, '123', '123', '2020-02-02 19:48:30', 40);

-- --------------------------------------------------------

--
-- Структура таблицы `task_assignees`
--

CREATE TABLE `task_assignees` (
  `user_id` int NOT NULL COMMENT 'ID Пользователя',
  `task_id` int NOT NULL COMMENT 'ID Задачи'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `task_assignees`
--

INSERT INTO `task_assignees` (`user_id`, `task_id`) VALUES
(7, 1),
(11, 12);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL COMMENT 'ID Пользователя',
  `name` varchar(100) NOT NULL COMMENT 'Имя',
  `surname` varchar(100) NOT NULL COMMENT 'Фамилия',
  `middlename` varchar(100) NOT NULL COMMENT 'Отчество',
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Почта',
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Пароль',
  `bio` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Описание'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `middlename`, `email`, `password`, `bio`) VALUES
(7, 'egor', 'shadrn', 'egorovich', 'egor@egor.egor', '$2y$10$ism/SQnA4wodXyN54Iw87.N9czG.pYsFXzyN4KF6GOMg7y7WeYWou', NULL),
(11, 'Egor', 'Shadrin', 'Aleksandrovich', 'shadrinegor@gmail.com', '$2y$10$J03n/acIRKVCGtcyVwOVq.53a0aDZ.D30.y3ZUEahRa/bMhYO28Xi', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `columns`
--
ALTER TABLE `columns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Индексы таблицы `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `project_roles`
--
ALTER TABLE `project_roles`
  ADD PRIMARY KEY (`id_project`,`id_user`),
  ADD KEY `id_project` (`id_project`,`id_user`),
  ADD KEY `id_user` (`id_user`);

--
-- Индексы таблицы `subtasks`
--
ALTER TABLE `subtasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Индексы таблицы `subtask_assignees`
--
ALTER TABLE `subtask_assignees`
  ADD PRIMARY KEY (`user_id`,`subtask_id`),
  ADD KEY `user_id` (`user_id`,`subtask_id`),
  ADD KEY `subtask_id` (`subtask_id`);

--
-- Индексы таблицы `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `column_id` (`column_id`);

--
-- Индексы таблицы `task_assignees`
--
ALTER TABLE `task_assignees`
  ADD PRIMARY KEY (`user_id`,`task_id`),
  ADD KEY `user_id` (`user_id`,`task_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `columns`
--
ALTER TABLE `columns`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID Столбца', AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT для таблицы `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID Проекта', AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `subtasks`
--
ALTER TABLE `subtasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID Подзадачи';

--
-- AUTO_INCREMENT для таблицы `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID Задачи', AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID Пользователя', AUTO_INCREMENT=12;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `columns`
--
ALTER TABLE `columns`
  ADD CONSTRAINT `columns_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `project_roles`
--
ALTER TABLE `project_roles`
  ADD CONSTRAINT `project_roles_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_roles_ibfk_2` FOREIGN KEY (`id_project`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `subtasks`
--
ALTER TABLE `subtasks`
  ADD CONSTRAINT `subtasks_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `subtask_assignees`
--
ALTER TABLE `subtask_assignees`
  ADD CONSTRAINT `subtask_assignees_ibfk_1` FOREIGN KEY (`subtask_id`) REFERENCES `subtasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subtask_assignees_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`column_id`) REFERENCES `columns` (`id`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `task_assignees`
--
ALTER TABLE `task_assignees`
  ADD CONSTRAINT `task_assignees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_assignees_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
