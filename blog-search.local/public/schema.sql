-- Создаем базу данных, если она не существует
CREATE DATABASE IF NOT EXISTS blog_data CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog_data;

DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `posts`;

-- Создание таблицы для записей блога
CREATE TABLE `posts` (
    `id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `body` TEXT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Создание таблицы для комментариев
CREATE TABLE `comments` (
    `id` INT UNSIGNED NOT NULL,
    `post_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `body` TEXT NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_post_id` (`post_id`),
    FOREIGN KEY (`post_id`)
        REFERENCES `posts`(`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;