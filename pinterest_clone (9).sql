-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-5.7
-- Время создания: Фев 26 2025 г., 02:55
-- Версия сервера: 5.7.44
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `pinterest_clone`
--

-- --------------------------------------------------------

--
-- Структура таблицы `awards`
--

CREATE TABLE `awards` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `awards`
--

INSERT INTO `awards` (`id`, `name`, `description`, `icon`, `created_at`) VALUES
(1, 'ЗБТ Участник', 'Участник закрытого бета-тестирования с 2 по 30 марта', 'zbt_award.png', '2025-02-24 16:42:58'),
(2, 'Первые шаги', 'Выложил первую работу', 'first_work.svg', '2025-02-25 23:18:15'),
(3, 'Активный автор', 'Выложил 25 работ', 'active_author.svg', '2025-02-25 23:18:15'),
(4, 'Мастер контента', 'Выложил 50 работ', 'content_master.svg', '2025-02-25 23:18:15'),
(5, 'Легенда Canvas', 'Выложил 100 работ', 'canvas_legend.svg', '2025-02-25 23:18:15'),
(6, 'Золотая работа', 'Работа заняла первое место', 'gold_work.svg', '2025-02-25 23:18:15'),
(7, 'Серебряная работа', 'Работа заняла второе место', 'silver_work.svg', '2025-02-25 23:18:15'),
(8, 'Бронзовая работа', 'Работа заняла третье место', 'bronze_work.svg', '2025-02-25 23:18:15'),
(9, 'VIP Статус', 'Премиум подписка активирована', 'vip_status.svg', '2025-02-25 23:18:15'),
(10, 'ЗБТ Участник', 'Участник закрытого бета-тестирования с 2 по 30 марта', 'first_work.svg', '2025-02-24 16:42:58'),
(11, 'ЗБТ Участник', 'Участник закрытого бета-тестирования с 2 по 30 марта', '25_work.png', '2025-02-24 16:42:58'),
(12, 'ЗБТ Участник', 'Участник закрытого бета-тестирования с 2 по 30 марта', '50_work.svg', '2025-02-24 16:42:58'),
(13, 'ЗБТ Участник', 'Участник закрытого бета-тестирования с 2 по 30 марта', '100_work.svg\r\n', '2025-02-24 16:42:58'),
(14, 'ЗБТ Участник', 'Участник закрытого бета-тестирования с 2 по 30 марта', 'топ 1.svg\r\n', '2025-02-24 16:42:58'),
(15, 'ЗБТ Участник', 'Участник закрытого бета-тестирования с 2 по 30 марта', 'топ 2.svg', '2025-02-24 16:42:58'),
(16, 'ЗБТ Участник', 'Участник закрытого бета-тестирования с 2 по 30 марта', 'топ 3.svg', '2025-02-24 16:42:58');

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('primary','secondary') COLLATE utf8mb4_unicode_ci DEFAULT 'secondary',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`, `slug`, `type`, `created_at`) VALUES
(1, '3D', NULL, '3d', 'primary', '2025-02-19 18:23:50'),
(2, '2D', NULL, '2d', 'primary', '2025-02-19 18:23:50'),
(3, 'Web', NULL, 'web', 'secondary', '2025-02-19 18:23:50'),
(4, 'Иллюстрации', NULL, 'illustrations', 'secondary', '2025-02-19 18:23:50'),
(5, 'Брендинг', NULL, 'branding', 'secondary', '2025-02-19 18:23:50'),
(6, 'UI/UX', NULL, 'uiux', 'secondary', '2025-02-19 18:23:50'),
(7, 'Анимация', NULL, 'animation', 'secondary', '2025-02-19 18:23:50');

-- --------------------------------------------------------

--
-- Структура таблицы `designs`
--

CREATE TABLE `designs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `layout_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'grid',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `moderator_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `primary_category_id` int(11) DEFAULT NULL,
  `secondary_category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `designs`
--

INSERT INTO `designs` (`id`, `user_id`, `title`, `description`, `image_path`, `created_at`, `rejection_reason`, `layout_type`, `status`, `moderator_id`, `category_id`, `primary_category_id`, `secondary_category_id`) VALUES
(70, 10, 'Ванна', 'классный дизайн ванны', '67be46034539c_sovremennyy-dom-v-sliyanii-s-prirodoy-svetlana-gerasimova-img~8c6183d809d11074_9-6236-1-24f8606.jpg', '2025-02-25 22:36:51', NULL, 'grid', 'approved', NULL, NULL, 1, 4),
(71, 10, 'еще одна', 'пппппппда', '67be51a16bd46_77158f746f827a0262bf10171fd64b72.jpg', '2025-02-25 23:26:25', NULL, 'grid', 'approved', NULL, NULL, 1, 4),
(72, 10, 'ТУТ Я УТОПИЛ СВОЮ БЫВШУЮ', 'ДА ДА Я', '67be52ef5abd8_MG_1886.jpg', '2025-02-25 23:31:59', NULL, 'grid', 'approved', NULL, NULL, 1, 4);

-- --------------------------------------------------------

--
-- Структура таблицы `design_comments`
--

CREATE TABLE `design_comments` (
  `id` int(11) NOT NULL,
  `design_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `design_images`
--

CREATE TABLE `design_images` (
  `id` int(11) NOT NULL,
  `design_id` int(11) NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `is_main` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `design_images`
--

INSERT INTO `design_images` (`id`, `design_id`, `image_path`, `position`, `is_main`) VALUES
(41, 70, '67be460346fd3_sovremennyy-dom-v-sliyanii-s-prirodoy-svetlana-gerasimova-img~8c6183d809d11074_9-6236-1-24f8606.jpg', 0, 1),
(42, 70, '67be460347bc1_bathroom-remodeling-ideas-arrowhead-remodeling-and-design-inc-img~faf1e52509a6fe13_9-7126-1-03f6571.jpg', 1, 0),
(43, 70, '67be46034840d_1a240835a8cb8d93f7fd0c66a00af30e.jpg', 2, 0),
(44, 71, '67be51a16ccc5_77158f746f827a0262bf10171fd64b72.jpg', 0, 1),
(45, 72, '67be52ef5b6b3_MG_1886.jpg', 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `followers`
--

CREATE TABLE `followers` (
  `id` int(11) NOT NULL,
  `follower_id` int(11) DEFAULT NULL,
  `following_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hidden_designs`
--

CREATE TABLE `hidden_designs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `design_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `design_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `profile_views`
--

CREATE TABLE `profile_views` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `viewer_id` int(11) DEFAULT NULL,
  `viewed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `design_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `follower_id` int(11) DEFAULT NULL,
  `following_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tutorials`
--

CREATE TABLE `tutorials` (
  `id` int(11) NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `video_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difficulty_level` enum('beginner','intermediate','expert') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `views_count` int(11) DEFAULT '0',
  `likes_count` int(11) DEFAULT '0',
  `status` enum('draft','published') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `tutorials`
--

INSERT INTO `tutorials` (`id`, `author_id`, `category_id`, `title`, `description`, `video_path`, `thumbnail_path`, `difficulty_level`, `duration`, `views_count`, `likes_count`, `status`, `created_at`) VALUES
(1, 10, 5, 'Погружение', 'intresting', '1740513794_Погружение в Blender 3D (уроки).mp4', '1740513794_blender_community_logo_black.png', 'beginner', NULL, 0, 0, 'draft', '2025-02-25 20:03:14');

-- --------------------------------------------------------

--
-- Структура таблицы `tutorial_categories`
--

CREATE TABLE `tutorial_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `tutorial_categories`
--

INSERT INTO `tutorial_categories` (`id`, `name`, `slug`, `icon_path`, `created_at`) VALUES
(1, 'Adobe Photoshop', 'adobe-photoshop', 'photoshop-icon.svg', '2025-02-25 05:35:23'),
(2, 'Adobe Illustrator', 'adobe-illustrator', 'illustrator-icon.svg', '2025-02-25 05:35:23'),
(3, 'Adobe XD', 'adobe-xd', 'xd-icon.svg', '2025-02-25 05:35:23'),
(4, 'Figma', 'figma', 'figma-icon.svg', '2025-02-25 05:35:23'),
(5, 'Blender', 'blender', 'blender-icon.jpg', '2025-02-25 05:35:23'),
(6, 'Cinema 4D', 'cinema-4d', 'cinema4d-icon.svg', '2025-02-25 05:35:23'),
(7, 'After Effects', 'after-effects', 'ae-icon.svg', '2025-02-25 05:35:23'),
(8, 'Premiere Pro', 'premiere-pro', 'premiere-icon.svg', '2025-02-25 05:35:23');

-- --------------------------------------------------------

--
-- Структура таблицы `tutorial_comments`
--

CREATE TABLE `tutorial_comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tutorial_id` int(11) DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tutorial_likes`
--

CREATE TABLE `tutorial_likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tutorial_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tutorial_progress`
--

CREATE TABLE `tutorial_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tutorial_id` int(11) DEFAULT NULL,
  `watched_duration` int(11) DEFAULT NULL,
  `completed` tinyint(1) DEFAULT '0',
  `last_watched` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tutorial_tags`
--

CREATE TABLE `tutorial_tags` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tutorial_tag_relations`
--

CREATE TABLE `tutorial_tag_relations` (
  `tutorial_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_moderator` tinyint(1) DEFAULT '0',
  `role` enum('user','moderator','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `is_admin` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `location`, `occupation`, `created_at`, `bio`, `avatar`, `banner`, `is_moderator`, `role`, `is_admin`) VALUES
(10, 'Иван', 'poliko', 'polikogame@gmail.com', '$2y$10$j2Q8WjR9Eq/XqaDCmU8Q..DPc4gPwPwLMoROB2j3ocDdAp.vT5Omm', 'Ижевск', '', '2025-02-24 16:28:38', '', '1740424997_6299d107dfd54f2d02e300492d2997ed.jpg', '1740424997_1675373744_new_preview_kot.jpg', 1, 'moderator', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `user_awards`
--

CREATE TABLE `user_awards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `award_id` int(11) NOT NULL,
  `awarded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_visible` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `user_awards`
--

INSERT INTO `user_awards` (`id`, `user_id`, `award_id`, `awarded_at`, `is_visible`) VALUES
(1, 10, 1, '2025-02-24 16:43:05', 1),
(9, 10, 10, '2025-02-24 16:43:05', 1),
(10, 10, 12, '2025-02-24 16:43:05', 1),
(11, 10, 11, '2025-02-24 16:43:05', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `views`
--

CREATE TABLE `views` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `design_id` int(11) DEFAULT NULL,
  `user_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `viewed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `views`
--

INSERT INTO `views` (`id`, `user_id`, `design_id`, `user_ip`, `viewed_at`) VALUES
(202, 10, 70, '127.0.0.1', '2025-02-25 22:37:21'),
(203, 10, 71, '127.0.0.1', '2025-02-25 23:53:10'),
(204, 10, 72, '127.0.0.1', '2025-02-25 23:53:42');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `awards`
--
ALTER TABLE `awards`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Индексы таблицы `designs`
--
ALTER TABLE `designs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `primary_category_id` (`primary_category_id`),
  ADD KEY `secondary_category_id` (`secondary_category_id`);

--
-- Индексы таблицы `design_comments`
--
ALTER TABLE `design_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `design_id` (`design_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `design_images`
--
ALTER TABLE `design_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `design_id` (`design_id`);

--
-- Индексы таблицы `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_follow` (`follower_id`,`following_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Индексы таблицы `hidden_designs`
--
ALTER TABLE `hidden_designs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_hide` (`user_id`,`design_id`);

--
-- Индексы таблицы `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`design_id`),
  ADD KEY `design_id` (`design_id`);

--
-- Индексы таблицы `profile_views`
--
ALTER TABLE `profile_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`),
  ADD KEY `viewer_id` (`viewer_id`);

--
-- Индексы таблицы `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rating` (`user_id`,`design_id`),
  ADD KEY `design_id` (`design_id`);

--
-- Индексы таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_subscription` (`follower_id`,`following_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Индексы таблицы `tutorials`
--
ALTER TABLE `tutorials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Индексы таблицы `tutorial_categories`
--
ALTER TABLE `tutorial_categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tutorial_comments`
--
ALTER TABLE `tutorial_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tutorial_id` (`tutorial_id`);

--
-- Индексы таблицы `tutorial_likes`
--
ALTER TABLE `tutorial_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tutorial_id` (`tutorial_id`);

--
-- Индексы таблицы `tutorial_progress`
--
ALTER TABLE `tutorial_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tutorial_id` (`tutorial_id`);

--
-- Индексы таблицы `tutorial_tags`
--
ALTER TABLE `tutorial_tags`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tutorial_tag_relations`
--
ALTER TABLE `tutorial_tag_relations`
  ADD PRIMARY KEY (`tutorial_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `user_awards`
--
ALTER TABLE `user_awards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `award_id` (`award_id`);

--
-- Индексы таблицы `views`
--
ALTER TABLE `views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `design_id` (`design_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `awards`
--
ALTER TABLE `awards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `designs`
--
ALTER TABLE `designs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT для таблицы `design_comments`
--
ALTER TABLE `design_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `design_images`
--
ALTER TABLE `design_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT для таблицы `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `hidden_designs`
--
ALTER TABLE `hidden_designs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `profile_views`
--
ALTER TABLE `profile_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tutorials`
--
ALTER TABLE `tutorials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `tutorial_categories`
--
ALTER TABLE `tutorial_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `tutorial_comments`
--
ALTER TABLE `tutorial_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tutorial_likes`
--
ALTER TABLE `tutorial_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tutorial_progress`
--
ALTER TABLE `tutorial_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tutorial_tags`
--
ALTER TABLE `tutorial_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `user_awards`
--
ALTER TABLE `user_awards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `views`
--
ALTER TABLE `views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `designs`
--
ALTER TABLE `designs`
  ADD CONSTRAINT `designs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `designs_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `designs_ibfk_3` FOREIGN KEY (`primary_category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `designs_ibfk_4` FOREIGN KEY (`secondary_category_id`) REFERENCES `categories` (`id`);

--
-- Ограничения внешнего ключа таблицы `design_comments`
--
ALTER TABLE `design_comments`
  ADD CONSTRAINT `design_comments_ibfk_1` FOREIGN KEY (`design_id`) REFERENCES `designs` (`id`),
  ADD CONSTRAINT `design_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `design_images`
--
ALTER TABLE `design_images`
  ADD CONSTRAINT `design_images_ibfk_1` FOREIGN KEY (`design_id`) REFERENCES `designs` (`id`);

--
-- Ограничения внешнего ключа таблицы `followers`
--
ALTER TABLE `followers`
  ADD CONSTRAINT `followers_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `followers_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`design_id`) REFERENCES `designs` (`id`);

--
-- Ограничения внешнего ключа таблицы `profile_views`
--
ALTER TABLE `profile_views`
  ADD CONSTRAINT `profile_views_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `profile_views_ibfk_2` FOREIGN KEY (`viewer_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`design_id`) REFERENCES `designs` (`id`);

--
-- Ограничения внешнего ключа таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `tutorials`
--
ALTER TABLE `tutorials`
  ADD CONSTRAINT `tutorials_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `tutorial_categories` (`id`),
  ADD CONSTRAINT `tutorials_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `tutorial_comments`
--
ALTER TABLE `tutorial_comments`
  ADD CONSTRAINT `tutorial_comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tutorial_comments_ibfk_2` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorials` (`id`);

--
-- Ограничения внешнего ключа таблицы `tutorial_likes`
--
ALTER TABLE `tutorial_likes`
  ADD CONSTRAINT `tutorial_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tutorial_likes_ibfk_2` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorials` (`id`);

--
-- Ограничения внешнего ключа таблицы `tutorial_progress`
--
ALTER TABLE `tutorial_progress`
  ADD CONSTRAINT `tutorial_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tutorial_progress_ibfk_2` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorials` (`id`);

--
-- Ограничения внешнего ключа таблицы `tutorial_tag_relations`
--
ALTER TABLE `tutorial_tag_relations`
  ADD CONSTRAINT `tutorial_tag_relations_ibfk_1` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorials` (`id`),
  ADD CONSTRAINT `tutorial_tag_relations_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tutorial_tags` (`id`);

--
-- Ограничения внешнего ключа таблицы `user_awards`
--
ALTER TABLE `user_awards`
  ADD CONSTRAINT `user_awards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_awards_ibfk_2` FOREIGN KEY (`award_id`) REFERENCES `awards` (`id`);

--
-- Ограничения внешнего ключа таблицы `views`
--
ALTER TABLE `views`
  ADD CONSTRAINT `views_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `views_ibfk_2` FOREIGN KEY (`design_id`) REFERENCES `designs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
